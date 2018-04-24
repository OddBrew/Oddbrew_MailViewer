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
 * Class Oddbrew_MailViewer_Model_Observer
 *
 * @package                Oddbrew_MailViewer
 * @author                 Alexandre Fayette <alexandre.fayette@gmail.com>
 * @copyright              Copyright (c) 2018 Alexandre Fayette
 * @license                https://opensource.org/licenses/BSD-3-Clause   3-Clause BSD License (BSD-3-Clause)
 * @website                https://github.com/OddBrew
 */
class Oddbrew_MailViewer_Model_Observer
{
    /** @var  Oddbrew_Mailviewer_Helper_Data */
    protected $_helper;

    protected $_customerGridAdded = false;

    /**
     * @return Oddbrew_Mailviewer_Helper_Data
     */
    protected function _getHelper()
    {
        if (!$this->_helper) {
            $this->_helper = Mage::helper('oddbrew_mailviewer');
        }

        return $this->_helper;
    }

    /**
     * Fire all functions linked to controller_action_layout_render_before_adminhtml_sales_order_view event
     *
     * @param Varien_Event_Observer $observer
     */
    public function controllerActionLayoutRenderBeforeAdminhtmlSalesOrderView(Varien_Event_Observer $observer)
    {
        $this->_addPreviewButtonToOrderView($observer);
    }

    public function controllerActionLayoutRenderBeforeAdminhtmlCustomerEdit(Varien_Event_Observer $observer)
    {
        $this->_addPreviewGridToCustomerDetails($observer);
    }

    /**
     * Fire all functions attached to adminhtml_block_html_before event
     *
     * @param Varien_Event_Observer $observer
     */
    public function adminhtmlBlockHtmlBefore(Varien_Event_Observer $observer)
    {
        $this->_addPreviewButtonsToOrderViewGrids($observer);
        $this->_addPreviewGridToCustomerDetails($observer);
    }

    /**
     * Add order mail preview button on order view page
     *
     * @param Varien_Event_Observer $observer
     * @return bool
     */
    protected function _addPreviewButtonToOrderView(Varien_Event_Observer $observer)
    {
        /** @var Mage_Adminhtml_Block_Sales_Order_View $block */
        $block = Mage::app()->getLayout()->getBlock('sales_order_edit');
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::registry('current_order');
        if (!$block || !$order) {
            return false;
        }

        /** @var string $url */
        $url = $this->_getHelper()->getTransactionalMailPreviewUrlFromEntity($order);

        $block->addButton('oddbrew_mailviewer_order_preview', array(
            'label' => $this->_getHelper()->__('Preview Order Mail'),
            'class' => 'oddbrew-mailviewer-button',
            'onclick' => "popWin('{$url}','_blank','width=800,height=700,resizable=1,scrollbars=1');return false;"
        ));

        return true;
    }

    /**
     * Add mail preview buttons to invoices, shipments and creditmemos grids on order view page
     *
     * @param Varien_Event_Observer $observer
     * @return bool
     */
    protected function _addPreviewButtonsToOrderViewGrids(Varien_Event_Observer $observer)
    {
        /** @var Mage_Adminhtml_Block_Template $block */
        $block = $observer->getEvent()->getBlock();

        if (Mage::app()->getFrontController()->getAction()->getFullActionName() != 'adminhtml_sales_order_view' || !$block instanceof Mage_Adminhtml_Block_Widget_Grid) {
            return false;
        }

        $entityType = null;
        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View_Tab_Invoices) {
            $entityType = Mage_Sales_Model_Order_Invoice::HISTORY_ENTITY_NAME;
        } else if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View_Tab_Shipments) {
            $entityType = Mage_Sales_Model_Order_Shipment::HISTORY_ENTITY_NAME;
        } else if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View_Tab_Creditmemos) {
            $entityType = Mage_Sales_Model_Order_Creditmemo::HISTORY_ENTITY_NAME;
        }

        if (!$entityType) {
            return false;
        }

        $block->addColumn('oddbrew_mailviewer_preview_' . $entityType, array(
            'header'   => 'MailViewer',
            'sortable' => false,
            'filter' => false,
            'align' => 'center',
            'header_css_class' => 'a-center',
            'width' => '80px',
            'renderer' => Mage::app()->getLayout()->createBlock('oddbrew_mailviewer/adminhtml_system_email_template_grid_renderer_action_preview',
                'oddbrew_mailviewer_renderer_preview_' . $entityType,
                array('entity_type' => $entityType))
        ));

        return true;
    }

    protected function _addPreviewGridToCustomerDetails(Varien_Event_Observer $observer)
    {
        /** @var Mage_Adminhtml_Block_Customer_Edit_Tabs $block */
        $block = Mage::app()->getLayout()->getBlock('customer_edit_tabs');

        if (!$block) {
            return false;
        }

        $block->addTab('mails', array(
            'label' => 'Mail Previews',
            'class' => 'ajax',
            'url'   => $block->getUrl('*/oddbrew_mailviewer_customer/mails', array('_current' => true))
        ));

        $this->_customerGridAdded = true;
    }
}