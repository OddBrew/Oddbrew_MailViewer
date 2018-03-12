<?php

class Oddbrew_MailViewer_Block_Adminhtml_System_Email_Template_Preview extends Mage_Adminhtml_Block_Template
{

    /**
     * Generates the whole mail preview
     *
     * @return string
     */
    protected function _toHtml()
    {
        parent::_toHtml();

        /** @var Oddbrew_MailViewer_Helper_Data $helper */
        $helper = Mage::helper('oddbrew_mailviewer');

        /** @var Mage_Sales_Model_Abstract $mainEntity */
        $mainEntity = $helper->getTransactionalEntityModel($this->getRequest()->getParam('entity_type'));
        $mainEntity->load($this->getRequest()->getParam('entity_id'));

        /** @var Mage_Core_Model_App_Emulation $appEmulation */
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($mainEntity->getStoreId());

        /** @var $template Mage_Core_Model_Email_Template */
        $template = $helper->getTransactionalMailTemplateForEntity($mainEntity);

        /* @var $filter Mage_Core_Model_Input_Filter_MaliciousCode */
        $filter = Mage::getSingleton('core/input_filter_maliciousCode');

        $template->setTemplateText(
            $filter->filter($template->getTemplateText())
        );

        $vars = $helper->getEntityTransactionalMailVars($mainEntity);

        $templateProcessed = $this->_getProcessedTemplate($template, $vars);

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        return $templateProcessed;
    }

    /**
     * Retrieve the final HTML resulting from the template
     *
     * @param Mage_Core_Model_Email_Template $template
     * @param array $vars
     * @return string
     */
    protected function _getProcessedTemplate(Mage_Core_Model_Email_Template $template, array $vars)
    {
        Varien_Profiler::start("email_template_proccessing");
        $templateProcessed = $template->getProcessedTemplate($vars);
        if ($template->isPlain()) {
            $templateProcessed = "<pre>" . htmlspecialchars($templateProcessed) . "</pre>";
        }
        Varien_Profiler::stop("email_template_proccessing");

        return $templateProcessed;
    }
}