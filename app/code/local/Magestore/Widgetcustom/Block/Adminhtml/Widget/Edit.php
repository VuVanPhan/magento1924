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
 * @category    Magestore
 * @package     Magestore_SolutionPartner
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Widget Edit Block
 *
 * @category     Magestore
 * @package     Magestore_Widgetcustom
 * @author      Magestore Developer
 */
class Magestore_Widgetcustom_Block_Adminhtml_Widget_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'widgetcustom';
        $this->_controller = 'adminhtml_widget';

        $this->_updateButton('save', 'label', Mage::helper('widgetcustom')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('widgetcustom')->__('Delete'));

        $this->_addButton('saveandcontinue', array(
            'label'        => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'    => 'saveAndContinueEdit()',
            'class'        => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('widget_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'widget_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'widget_content');
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('widget_data')
            && Mage::registry('widget_data')->getId()
        ) {
            $name = Mage::registry('widget_data')->getName();
            $title = Mage::helper('widgetcustom')->truncate($name, 150);
            return Mage::helper('widgetcustom')->__("Edit '%s'",
                $this->htmlEscape($title)
            );
        }
        return Mage::helper('widgetcustom')->__('Add Widget');
    }
}