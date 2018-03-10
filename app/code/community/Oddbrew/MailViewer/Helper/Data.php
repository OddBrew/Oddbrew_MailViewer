<?php

class Oddbrew_MailViewer_Helper_Data extends Mage_Core_Helper_Abstract
{

    protected $_emailConfigTemplatePaths = [
        'new_order' => Mage_Sales_Model_Order::XML_PATH_EMAIL_TEMPLATE,
        'new_order_guest' => Mage_Sales_Model_Order::XML_PATH_EMAIL_GUEST_TEMPLATE,
        'invoice' => Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_TEMPLATE,
        'invoice_guest' => Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_GUEST_TEMPLATE,
        'shipment' => Mage_Sales_Model_Order_Shipment::XML_PATH_EMAIL_TEMPLATE,
        'shipment_guest' => Mage_Sales_Model_Order_Shipment::XML_PATH_EMAIL_GUEST_TEMPLATE,
        'creditmemo' => Mage_Sales_Model_Order_Creditmemo::XML_PATH_EMAIL_TEMPLATE,
        'credimemo_guest' => Mage_Sales_Model_Order_Creditmemo::XML_PATH_EMAIL_GUEST_TEMPLATE
    ];

    public function getTemplateForMailType($mailType, $storeId = null)
    {
        if (!isset($this->_emailConfigTemplatePaths[$mailType])){
            return false;
        }

        $configValue = Mage::getStoreConfig($this->_emailConfigTemplatePaths[$mailType], $storeId);

        /** @var Mage_Core_Model_Email_Template $template */
        $template = Mage::getModel('core/email_template');
        if(is_numeric($configValue)){
            $template->load($configValue);
        }
        else {
            $localeCode = $storeId ? Mage::getStoreConfig('general/locale/code', $storeId) : null;
            $template->loadDefault($configValue, $localeCode);
        }

        return $template;
    }
}