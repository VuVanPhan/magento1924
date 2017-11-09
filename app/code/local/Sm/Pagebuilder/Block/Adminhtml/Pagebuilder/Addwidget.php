<?php
/**
 * Created by PhpStorm.
 * User: vuvanphan
 * Date: 22/03/2017
 * Time: 14:27
 */

class Sm_Pagebuilder_Block_Adminhtml_Pagebuilder_Addwidget extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = "pagebuilder";
        $this->_controller = "adminhtml_pagebuilder";
        $this->_mode = 'addwidget';
        $this->_headerText = $this->helper('pagebuilder')->__('Widget Insertion');

        $this->removeButton('reset');
        $this->removeButton('back');
        $this->_updateButton('save', 'label', $this->helper('pagebuilder')->__('Insert Widget'));
        $this->_updateButton('save', 'class', 'add-widget');
        $this->_updateButton('save', 'id', 'insert_button');
        $this->_updateButton('save', 'onclick', 'pbWidget.insertWidget()');

        $this->_formScripts[] = 'pbWidget = new WysiwygWidgetCustom.Widget('
            . '"widget_options_form", "select_widget_type", "widget_options", "'
            . $this->getUrl('*/*/loadOptions') .'", "' . $this->getRequest()->getParam('widget_target_id') . '");';
//
//        $this->_formScripts[] = 'pbWidget = new _PdmWysiwygWidget.Widget('
//            . '"widget_options_form", "select_widget_type", "widget_options", "'
//            . $this->getUrl('*/*/loadOptions') . '", "' . $this->getRequest()->getParam('widget_target_id') . '");';

//        $this->_formScripts[] = 'pbWidget = new _PdmWysiwygWidget.Widget('
//            . '"widget_options_form", "select_widget_type", "widget_options", "'
//            . $this->getUrl('*/widget/loadOptions') .'", "' . $this->getRequest()->getParam('widget_target_id') . '");';
    }
}