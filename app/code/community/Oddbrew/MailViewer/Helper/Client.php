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
 * Class Oddbrew_MailViewer_Helper_Client
 *
 * @package                Oddbrew_MailViewer
 * @author                 Alexandre Fayette <alexandre.fayette@gmail.com>
 * @copyright              Copyright (c) 2018 Alexandre Fayette
 * @license                https://opensource.org/licenses/BSD-3-Clause   3-Clause BSD License (BSD-3-Clause)
 * @website                https://github.com/OddBrew
 */
class Oddbrew_MailViewer_Helper_Client extends Mage_Core_Helper_Abstract
{

    protected  $_clientEmailTemplatesConfigPaths = array(
        Mage_Customer_Model_Customer::XML_PATH_REGISTER_EMAIL_TEMPLATE,
        Mage_Customer_Model_Customer::XML_PATH_REMIND_EMAIL_TEMPLATE,
        Mage_Customer_Model_Customer::XML_PATH_FORGOT_EMAIL_TEMPLATE,
        Mage_Customer_Model_Customer::XML_PATH_CONFIRM_EMAIL_TEMPLATE,
        Mage_Customer_Model_Customer::XML_PATH_CONFIRMED_EMAIL_TEMPLATE,
        Mage_Customer_Model_Customer::XML_PATH_CHANGED_PASSWORD_OR_EMAIL_TEMPLATE
    );


    public function getAllCustomerEmailTemplatesConfigs($storeId = null)
    {
        $system = Mage::getConfig()->loadModulesConfiguration('system.xml');

        $config = new Varien_Data_Collection();
        foreach ($this->_clientEmailTemplatesConfigPaths as $path) {
            $pathParts = explode('/', $path);
            $node = $system->getNode('sections/' . $pathParts[0] . '/groups/' . $pathParts[1] . '/fields/' . $pathParts[2]);
            $configValue = Mage::getStoreConfig($path, $storeId);
            $item = new Varien_Object(array(
                'label' => Mage::helper('adminhtml')->__((string)$node->label),
                'template' => $this->getMailTemplateCode($configValue, $storeId)
            ));
            $config->addItem($item);
        }

        return $config;
    }

    protected function getMailTemplateCode($identifier, $storeId)
    {
        if($identifier)

        /** @var Mage_Core_Model_Email_Template $template */
        $template = Mage::helper('oddbrew_mailviewer')->getMailTemplateByIdentifier($identifier, $storeId);

        return $template->getTemplateCode();
    }
}