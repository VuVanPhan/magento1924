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
class Magestore_Widgetcustom_Block_Adminhtml_Widget_Addwidget extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = "widgetcustom";
        $this->_controller = "adminhtml_widget";
        /* name block current*/
        $this->_mode = 'addwidget';
        $this->_headerText = $this->helper('adminhtml')->__('Widget Insertion');

        $this->removeButton('reset');
        $this->removeButton('back');
        $this->_updateButton('save', 'label', $this->helper('adminhtml')->__('Insert Widget'));
        $this->_updateButton('save', 'class', 'add-widgetff');
        $this->_updateButton('save', 'id', 'insert_buttonff');
        $this->_updateButton('save', 'onclick', 'pbWidget.insertWidget()');

        $this->_formScripts[] = 'pbWidget = new WysiwygWidgetCustom.Widget('
            . '"widget_options_form", "select_widget_type", "widget_options", "'
            . $this->getUrl('*/*/loadOptions') .'", "' . $this->getRequest()->getParam('widget_target_id') . '");';
    }
}