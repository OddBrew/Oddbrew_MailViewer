<?php

/*
BSD 3-Clause License

Copyright (c) 2018, Alexandre Fayette
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:

 * Redistributions of source code must retain the above copyright notice, this
list of conditions and the following disclaimer.

 * Redistributions in binary form must reproduce the above copyright notice,
this list of conditions and the following disclaimer in the documentation
and/or other materials provided with the distribution.

 * Neither the name of the copyright holder nor the names of its
contributors may be used to endorse or promote products derived from
this software without specific prior written permission.

 * Redistributions in any form must not change the Oddbrew_MailViewer namespace,
and the module name must remain Oddbrew_MailViewer.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * Class Oddbrew_MailViewer_Helper_Data
 *
 * @package                Oddbrew_MailViewer
 * @author                 Alexandre Fayette <alexandre.fayette@gmail.com>
 * @copyright              Copyright (c) 2018 Alexandre Fayette
 * @license                https://opensource.org/licenses/BSD-3-Clause   3-Clause BSD License (BSD-3-Clause)
 * @website                https://github.com/OddBrew
 */
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

    /**
     * Activate the template hints for the current script execution, on the given (optional) store
     *
     * @param $storeId
     *
     * @return void
     */
    public function enableTemplateDebugMode($storeId = null)
    {
        Mage::app()->getStore($storeId)
            ->setConfig('dev/debug/template_hints', 1)
            ->setConfig('dev/debug/template_hints_blocks', 1);
    }

    /**
     * @return bool
     */
    public function getIsMailTemplateDebugActivated()
    {
        return (bool) Mage::getStoreConfigFlag('oddbrew_mailviewer/settings/debug_mail_template');
    }

    /**
     * @return string
     */
    public function getModuleVersion()
    {
        $moduleName = $this->_getModuleName();

        return (string)Mage::getConfig()->getModuleConfig($moduleName)->version;
    }
}