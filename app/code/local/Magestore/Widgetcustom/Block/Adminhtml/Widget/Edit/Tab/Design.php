<?php
class Magestore_Widgetcustom_Block_Adminhtml_Widget_Edit_Tab_Design extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm(){
        /** @var $model Magestore_Widgetcustom_Model_Widget */
        $model = Mage::registry('cms_page');

        /*
         * Checking if user have permissions to save information
         */
//        if ($this->_isAllowedAction('save')) {
//            $isElementDisabled = false;
//        } else {
//            $isElementDisabled = true;
//        }

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('widgetcustom_');
        
        $builder_type_label = 'Widget';

        $fieldset = $form->addFieldset('link_form', array(
            'class' => 'no-spacing',
            'legend'=>Mage::helper('widgetcustom')->__('Widget Information')
        ));
        $fieldset->addField('name', 'text', array(
            'label'         => Mage::helper('widgetcustom')->__('Name'),
            'class'         => 'required-entry',
            'required'      => true,
            'name'          => 'name',
        ));
        echo "<pre>";
        var_dump($model->getData());
        die('54455555');

        $contentField = $fieldset->addField('content', 'editor', array(
            'name'      => 'content',
            'style'     => 'height:36em;width:1173px',
            'required'  => true,
//            'disabled'  => $isElementDisabled,
        ));

        echo "<pre>";
        var_dump($model->getData());
        die('54455555');
        // Setting custom renderer for content field to remove label column
        $renderer = $this->getLayout()->createBlock('widgetcustom/adminhtml_custom_form_renderer_fieldset_element')
            ->setTemplate('widgetcustom/widget/page/edit/form/renderer/content.phtml');
        $contentField->setRenderer($renderer);

        echo "<pre>";
        var_dump($model->getData());
        die();
        if ($model->getWidgetId())
        {
            $form->setValues($model->getData());
        }
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('widgetcustom')->__('Design Widget');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('widgetcustom')->__('Design Widget');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('adminhtml/widgetcustom_widget/' . $action);
    }
}