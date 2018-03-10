<?php

class Oddbrew_MailViewer_Block_Adminhtml_System_Email_Template_Preview extends Mage_Adminhtml_Block_Template
{

    protected function _toHtml()
    {
        parent::_toHtml();

        $mailType = $this->getRequest()->getParam('mail_type');
        $mainEntity = $this->_getMainEntity($mailType);
        $mainEntity->load($this->getRequest()->getParam('entity_id'));

        /** @var Mage_Core_Model_App_Emulation $appEmulation */
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($mainEntity->getStoreId());

        /** @var $template Mage_Core_Model_Email_Template */
        $template = Mage::helper('oddbrew_mailviewer')->getTemplateForMailType($mailType);

        /* @var $filter Mage_Core_Model_Input_Filter_MaliciousCode */
        $filter = Mage::getSingleton('core/input_filter_maliciousCode');

        $template->setTemplateText(
            $filter->filter($template->getTemplateText())
        );

        Varien_Profiler::start("email_template_proccessing");
        $vars = $this->_getVars($mailType, $mainEntity);

        $templateProcessed = $template->getProcessedTemplate($vars);

        if ($template->isPlain()) {
            $templateProcessed = "<pre>" . htmlspecialchars($templateProcessed) . "</pre>";
        }

        Varien_Profiler::stop("email_template_proccessing");

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        return $templateProcessed;
    }

    protected function _getVars($mailType, $mainEntity)
    {
        $vars = [];

        switch($mailType){
            case 'new_order' :
            case 'new_order_guest' :
                /** @var Mage_Sales_Model_Order $order */
                $vars['order'] = $mainEntity;
                $vars['store'] = $mainEntity->getStore();
                /** @var Mage_Core_Block_Template $paymentBlock */
                $paymentBlock = Mage::helper('payment')->getInfoBlock($mainEntity->getPayment())
                    ->setIsSecureMode(true);
                $paymentBlock->getMethod()->setStore($mainEntity->getStoreId());
                $vars['payment_html'] = $paymentBlock->toHtml();
                $vars['billing'] = $mainEntity->getBillingAddress();
                break;
            case 'invoice':
            case 'invoice_guest':
                $vars['invoice'] = $mainEntity;
                $vars['order'] = $mainEntity->getOrder();
                $vars['store'] = $mainEntity->getStore();
                $vars['billing'] = $mainEntity->getBillingAddress();
            /** @var Mage_Core_Block_Template $paymentBlock */
            $paymentBlock = Mage::helper('payment')->getInfoBlock($mainEntity->getOrder()->getPayment())
                ->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($mainEntity->getStoreId());
            $vars['payment_html'] = $paymentBlock->toHtml();
                break;
            case 'shipment':
            case 'shipment_guest':
                $vars['shipment'] = $mainEntity;
                $vars['order'] = $mainEntity->getOrder();
                $vars['store'] = $mainEntity->getStore();
                $vars['billing'] = $mainEntity->getBillingAddress();
                /** @var Mage_Core_Block_Template $paymentBlock */
                $paymentBlock = Mage::helper('payment')->getInfoBlock($mainEntity->getOrder()->getPayment())
                    ->setIsSecureMode(true);
                $paymentBlock->getMethod()->setStore($mainEntity->getStoreId());
                $vars['payment_html'] = $paymentBlock->toHtml();
                break;
            case 'creditmemo':
            case 'creditmemo_guest':
                $vars['creditmemo'] = $mainEntity;
                $vars['order'] = $mainEntity->getOrder();
                $vars['store'] = $mainEntity->getStore();
                $vars['billing'] = $mainEntity->getBillingAddress();
                /** @var Mage_Core_Block_Template $paymentBlock */
                $paymentBlock = Mage::helper('payment')->getInfoBlock($mainEntity->getOrder()->getPayment())
                    ->setIsSecureMode(true);
                $paymentBlock->getMethod()->setStore($mainEntity->getStoreId());
                $vars['payment_html'] = $paymentBlock->toHtml();
                break;
        }

        return $vars;
    }

    /**
     * @param $mailType
     * @return Mage_Core_Model_Abstract
     */
    protected function _getMainEntity($mailType)
    {
        switch ($mailType) {
            case 'new_order':
            case 'new_order_guest':
                return Mage::getModel('sales/order');
            case 'invoice':
            case 'invoice_guest':
                return Mage::getModel('sales/order_invoice');
            case 'shipment':
            case 'shipment_guest':
                return Mage::getModel('sales/order_shipment');
            case 'creditmemo':
            case 'creditmemo_guest':
                return Mage::getModel('sales/order_creditmemo');
        }
    }
}