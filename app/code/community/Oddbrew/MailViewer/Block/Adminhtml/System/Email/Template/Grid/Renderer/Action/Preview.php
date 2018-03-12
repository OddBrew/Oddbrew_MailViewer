<?php

class Oddbrew_MailViewer_Block_Adminhtml_System_Email_Template_Grid_Renderer_Action_Preview extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{

    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $actions = [];
        $actions[] = [
            'url' => Mage::helper('oddbrew_mailviewer')->getTransactionalMailPreviewUrl($this->getEntityType(), $row->getId()),
            'popup' => true,
            'caption' => $this->__('Preview')
        ];
        $this->getColumn()->setActions($actions);

        return parent::render($row);
    }
}
