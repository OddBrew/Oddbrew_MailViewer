<?php

class Oddbrew_MailViewer_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Retrieves transactional mail template for a given entity
     * (Ex : if given an order, will return the 'new_order' mail template)
     *
     * @param Mage_Sales_Model_Abstract $entity Can be an order, an invoice, a shipment or a creditmemo
     * @param null $storeId Optional, for choosing the store on which to retrieve the template
     * @return Mage_Core_Model_Email_Template
     */
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

    /**
     * Generate a mail preview url for the given entity
     *
     * @param Mage_Sales_Model_Abstract $entity Can be an order, an invoice, a shipment or a creditmemo
     * @return mixed
     */
    public function getTransactionalMailPreviewUrlFromEntity(Mage_Sales_Model_Abstract $entity)
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/oddbrew_mailviewer_preview/base', ['entity_id' => $entity->getId(), 'entity_type' => $entity::HISTORY_ENTITY_NAME]);
    }

    /**
     * Generate a mail preview url for the given entity type and entity id
     *
     * @see Oddbrew_MailViewer_Helper_Data::getTransactionalEntityModel() for the possible values
     *
     * @param $entityType
     * @param $entityId
     * @return mixed
     */
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
     * Retrieve the model corresponding to the given entity type
     *
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

    /**
     * Retrieve all the variables needed to inject in the transactional mail template, depending on the given entity
     *
     * @param Mage_Sales_Model_Abstract $mainEntity
     * @return array
     */
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