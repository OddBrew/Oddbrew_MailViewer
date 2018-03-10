<?php

abstract class Oddbrew_MailViewer_Block_Adminhtml_System_Email_Template_Grid_Renderer_Action_Abstract extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{

    protected $_mailType;

    public function render(Varien_Object $row)
    {
        $actions = array();

        $actions[] = array(
            'url'		=>  $this->getUrl('*/oddbrew_mailviewer_preview/base', array('entity_id'=>$row->getId(), 'mail_type' => $this->_mailType)),
            'popup'     =>  true,
            'caption'	=>	$this->__('Preview')
        );

        $this->getColumn()->setActions($actions);

        return parent::render($row);
    }
}
