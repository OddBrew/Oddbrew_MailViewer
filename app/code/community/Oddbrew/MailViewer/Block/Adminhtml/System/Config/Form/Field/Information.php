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
 * Class Oddbrew_MailViewer_Block_Adminhtml_System_Config_Form_Field_Information
 *
 * @package                Oddbrew_MailViewer
 * @author                 Alexandre Fayette <alexandre.fayette@gmail.com>
 * @copyright              Copyright (c) 2018 Alexandre Fayette
 * @license                https://opensource.org/licenses/BSD-3-Clause   3-Clause BSD License (BSD-3-Clause)
 * @website                https://github.com/OddBrew
 */
class Oddbrew_MailViewer_Block_Adminhtml_System_Config_Form_Field_Information extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    /** @var array Informations to display in BO, depending on config key */
    protected $_informations = array(
        'oddbrew_mailviewer_about_author' => "Alexandre Fayette <a href='mailto:alexandre.fayette@gmail.com' target='_top'>alexandre.fayette@gmail.com</a>",
        'oddbrew_mailviewer_about_extension_website' => "<a href='https://www.github.com/OddBrew/Oddbrew_Mailviewer' target='_blank'>Oddbrew_MailViewer</a>",
        'oddbrew_mailviewer_about_copyright' => "Copyright (c) 2018, Alexandre Fayette",
        'oddbrew_mailviewer_about_donate'   => "<a href='https://paypal.me/AlexandreFayette' target='_blank'><img src='https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_37x23.jpg' alt ='Paypal'/></a>"
    );

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        if (!isset($this->_informations[$element->getHtmlId()])){
            return '';
        }

        return $this->_informations[$element->getHtmlId()];
    }
}
