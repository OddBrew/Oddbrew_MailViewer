<?php

class Oddbrew_MailViewer_Adminhtml_Oddbrew_Mailviewer_PreviewController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Entry point for all mail previews actions
     */
    public function baseAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}