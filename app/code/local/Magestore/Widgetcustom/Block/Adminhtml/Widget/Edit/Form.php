<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category     Magestore
 * @package     Magestore_SolutionPartner
 * @copyright     Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Widget Edit Form Block
 *
 * @category    Magestore
 * @package     Magestore_Widgetcustom
 * @author      Magestore Developer
 */
class Magestore_Widgetcustom_Block_Adminhtml_Widget_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare form's information for block
     *
     * @return Magestore_Widgetcustom_Block_Adminhtml_Widget_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save', array(
                'id'    => $this->getRequest()->getParam('id')
            )),
            'method'    => 'post',
            'enctype'   => 'multipart/form-data'
        ));

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('widgetcustom/widget/' . $action);
    }

    /**
     * Prepare layout.
     * Add files to use dialog windows
     *
     * @return Mage_Adminhtml_Block_System_Email_Template_Edit_Form
     */
    protected function _prepareLayout()
    {
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->addItem('js', 'prototype/window.js')
                ->addItem('js', 'mage/adminhtml/wysiwyg/widget.js')
                ->addItem('js_css', 'prototype/windows/themes/default.css')
                ->addCss('lib/prototype/windows/themes/magento.css')
                ->addCss('css/widgetcustom/widgetcustom.css')
                /* begin add jQuery and noconflict */
                ->addItem('js', 'lib/jquery/jquery-1.10.2.min.js')
                ->addItem('js', 'lib/jquery/noconflict.js')
                /* end add jQuery and noconflict */
                ->addItem('js', 'mage/adminhtml/variables.js')
                ->addItem('js', 'magestore/widgetcustom/adminhtml/wysiwyg/widgetcustom.js');
        }
        return parent::_prepareLayout();
    }
}