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
 * Class Oddbrew_MailViewer_Block_Adminhtml_System_Email_Template_Preview
 *
 * @package                Oddbrew_MailViewer
 * @author                 Alexandre Fayette <alexandre.fayette@gmail.com>
 * @copyright              Copyright (c) 2018 Alexandre Fayette
 * @license                https://opensource.org/licenses/BSD-3-Clause   3-Clause BSD License (BSD-3-Clause)
 * @website                https://github.com/OddBrew
 */
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