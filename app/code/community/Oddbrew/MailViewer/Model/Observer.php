<?php

class Oddbrew_MailViewer_Model_Observer
{
    /** @var  Oddbrew_Mailviewer_Helper_Data */
    protected $_helper;

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

    public function controllerActionLayoutRenderBeforeAdminhtmlSalesOrderView(Varien_Event_Observer $observer)
    {
        $this->_addPreviewButtonToOrderView($observer);
    }

    public function adminhtmlBlockHtmlBefore(Varien_Event_Observer $observer)
    {
        $this->_addPreviewButtonToInvoiceGrid($observer);
    }

    protected function _addPreviewButtonToOrderView(Varien_Event_Observer $observer)
    {
        /** @var Mage_Adminhtml_Block_Sales_Order_View $block */
        $block = Mage::app()->getLayout()->getBlock('sales_order_edit');
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::registry('current_order');
        if (!$block || !$order) {
            return;
        }

        /** @var string $url */
        $url = Mage::helper('adminhtml')->getUrl('*/oddbrew_mailviewer_preview/base', ['entity_id' => $order->getId(), 'mail_type' => 'new_order']);

        $block->addButton('oddbrew_mailviewer_order_preview', [
            'label' => $this->_getHelper()->__('Preview Order Mail'),
            'class' => 'oddbrew-mailviewer-button',
            'onclick' => "popWin('{$url}','_blank','width=800,height=700,resizable=1,scrollbars=1');return false;"
        ]);
    }

    protected function _addPreviewButtonToInvoiceGrid(Varien_Event_Observer $observer)
    {
        /** @var Mage_Adminhtml_Block_Template $block */
        $block = $observer->getEvent()->getBlock();
        if(!$block instanceof Mage_Adminhtml_Block_Sales_Order_View_Tab_Invoices) {
            return;
        }

        $block->addColumn('oddbrew_mailviewer_preview_invoice', [
            'header' => $this->_getHelper()->__('MailViewer'),
            'index' => 'increment_id',
            'sortable'  => false,
            'filter' 	=> false,
            'renderer'  => 'oddbrew_mailviewer/adminhtml_system_email_template_grid_renderer_action_invoice'
        ]);
    }
}