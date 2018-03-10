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
        $url = Mage::helper('adminhtml')->getUrl('adminhtml/oddbrew_mailviewer_preview/base', ['entity_id' => $order->getId(), 'mail_type' => 'new_order']);

        $block->addButton('oddbrew_mailviewer_order_preview', [
            'label' => $this->_getHelper()->__('Preview Order Mail'),
            'class' => 'oddbrew-mailviewer-button',
            'onclick' => "popWin('{$url}','_blank','width=800,height=700,resizable=1,scrollbars=1');return false;"
        ]);
    }
}