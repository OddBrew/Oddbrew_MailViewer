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
 * Class Oddbrew_MailViewer_Block_Adminhtml_Customer_Edit_Tab_Mails
 *
 * @package                Oddbrew_MailViewer
 * @author                 Alexandre Fayette <alexandre.fayette@gmail.com>
 * @copyright              Copyright (c) 2018 Alexandre Fayette
 * @license                https://opensource.org/licenses/BSD-3-Clause   3-Clause BSD License (BSD-3-Clause)
 * @website                https://github.com/OddBrew
 */
class Oddbrew_MailViewer_Block_Adminhtml_Customer_Edit_Tab_Mails extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('oddbrew_mailviewer_customer_mails_grid');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = $this->helper('oddbrew_mailviewer/client')->getAllCustomerEmailTemplatesConfigs();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('path', array(
            'header'    => Mage::helper('customer')->__('Path'),
            'width'     => '100',
            'index'     => 'path',
        ));

        $this->addColumn('label', array(
            'header'    => Mage::helper('customer')->__('Config'),
            'width'     => '100',
            'index'     => 'label',
        ));

        $this->addColumn('value', array(
            'header'    => Mage::helper('customer')->__('Value'),
            'width'     => '100',
            'index'     => 'value',
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/oddbrew_mailviewer_customer/mails', array('_current' => true));
    }

}