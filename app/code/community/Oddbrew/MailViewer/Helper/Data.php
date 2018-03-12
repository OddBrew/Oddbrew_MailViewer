<?php

class Oddbrew_MailViewer_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function getTransactionalMailTemplateForEntity(Mage_Sales_Model_Abstract $entity, $storeId = null)
    {
        if ($entity instanceof Mage_Sales_Model_Order) {
            $order = $entity;
        } else {
            $order = $entity->getOrder();
        }

        $emailConfigTemplatePath = $entity::XML_PATH_EMAIL_TEMPLATE;
        if ($order->getCustomerIsGuest()) {
            $emailConfigTemplatePath = $entity::XML_PATH_EMAIL_GUEST_TEMPLATE;
        }

        $configValue = Mage::getStoreConfig($emailConfigTemplatePath, $storeId);

        /** @var Mage_Core_Model_Email_Template $template */
        $template = Mage::getModel('core/email_template');
        if (is_numeric($configValue)) {
            $template->load($configValue);
        } else {
            $localeCode = $storeId ? Mage::getStoreConfig('general/locale/code', $storeId) : null;
            $template->loadDefault($configValue, $localeCode);
        }

        return $template;
    }

    public function getTransactionalMailPreviewUrlFromEntity(Mage_Sales_Model_Abstract $entity)
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/oddbrew_mailviewer_preview/base', ['entity_id' => $entity->getId(), 'entity_type' => $entity::HISTORY_ENTITY_NAME]);
    }

    public function getTransactionalMailPreviewUrl($entityType, $entityId)
    {
        $entity = $this->getTransactionalEntityModel($entityType);

        if (!$entity) {
            Mage::throwException('The specified entity type is invalid : ' . $entityType);
        }
        $entity->load($entityId);

        if (!$entity->getId()) {
            Mage::throwException('Failed to load the entity ' . $entityType . ' with the ID : ' . $entityId);
        }

        return $this->getTransactionalMailPreviewUrlFromEntity($entity);
    }

    /**
     * @param $entityType
     * @return Mage_Sales_Model_Abstract|false
     */
    public function getTransactionalEntityModel($entityType)
    {
        switch ($entityType) {
            case Mage_Sales_Model_Order::HISTORY_ENTITY_NAME:
                return Mage::getModel('sales/order');
            case Mage_Sales_Model_Order_Invoice::HISTORY_ENTITY_NAME:
                return Mage::getModel('sales/order_invoice');
            case Mage_Sales_Model_Order_Shipment::HISTORY_ENTITY_NAME:
                return Mage::getModel('sales/order_shipment');
            case Mage_Sales_Model_Order_Creditmemo::HISTORY_ENTITY_NAME:
                return Mage::getModel('sales/order_creditmemo');
        }

        return false;
    }

    public function getEntityTransactionalMailVars(Mage_Sales_Model_Abstract $mainEntity)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $mainEntity;
        if (!$mainEntity instanceof Mage_Sales_Model_Order) {
            $order = $mainEntity->getOrder();
        }

        /** @var Mage_Core_Block_Template $paymentBlock */
        $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
            ->setIsSecureMode(true);
        $paymentBlock->getMethod()->setStore($mainEntity->getStoreId());

        $vars = [
            $mainEntity::HISTORY_ENTITY_NAME => $mainEntity,
            'payment_html' => $paymentBlock->toHtml(),
            'order' => $order,
            'billing' => $mainEntity->getBillingAddress(),
            'store' => $mainEntity->getStore(),
        ];

        return $vars;
    }
}