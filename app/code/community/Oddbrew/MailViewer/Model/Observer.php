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

    /**
     * Fire all functions linked to controller_action_layout_render_before_adminhtml_sales_order_view event
     *
     * @param Varien_Event_Observer $observer
     */
    public function controllerActionLayoutRenderBeforeAdminhtmlSalesOrderView(Varien_Event_Observer $observer)
    {
        $this->_addPreviewButtonToOrderView($observer);
    }

    /**
     * Fire all functions attached to adminhtml_block_html_before event
     *
     * @param Varien_Event_Observer $observer
     */
    public function adminhtmlBlockHtmlBefore(Varien_Event_Observer $observer)
    {
        $this->_addPreviewButtonsToOrderViewGrids($observer);
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

        $block->addButton('oddbrew_mailviewer_order_preview', [
            'label' => $this->_getHelper()->__('Preview Order Mail'),
            'class' => 'oddbrew-mailviewer-button',
            'onclick' => "popWin('{$url}','_blank','width=800,height=700,resizable=1,scrollbars=1');return false;"
        ]);
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

        $block->addColumn('oddbrew_mailviewer_preview_' . $entityType, [
            'header' => $this->_getHelper()->__('MailViewer'),
            'sortable' => false,
            'filter' => false,
            'align' => 'center',
            'header_css_class' => 'a-center',
            'width' => '80px',
            'renderer' => Mage::app()->getLayout()->createBlock('oddbrew_mailviewer/adminhtml_system_email_template_grid_renderer_action_preview',
                'oddbrew_mailviewer_renderer_preview_' . $entityType,
                ['entity_type' => $entityType])
        ]);

        return true;
    }
}