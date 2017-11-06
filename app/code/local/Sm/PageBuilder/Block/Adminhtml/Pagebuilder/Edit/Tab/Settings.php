<?php
/**
 * Created by PhpStorm.
 * User: Vu Van Phan
 * Date: 04-11-2015
 * Time: 15:59
 */
class Sm_PageBuilder_Block_Adminhtml_Pagebuilder_Edit_Tab_Settings extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	protected function _prepareForm(){
		$model = Mage::registry('page');

		if ($this->_isAllowedAction('save')) {
			$isElementDisabled = false;
		} else {
			$isElementDisabled = true;
		}

		$form = new Varien_Data_Form();
		$form->setHtmlIdPrefix('pagebuilder_');
		$dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
		$builder_type_label = 'Page';

		$fieldset = $form->addFieldset('customcssjs_fieldset', array(
			'legend'    => "<i class='fa fa-cog'></i>".Mage::helper('pagebuilder')->__('Custorm CSS / JS'),
			'class'  => 'fieldset-wide',
			'disabled'  => $isElementDisabled
		));

		$fieldset->addField('custom_css', 'textarea', array(
			'class'     => 'field-pagebuilder',
			'name'      => 'custom_css',
			'label'     => Mage::helper('pagebuilder')->__('Custom CSS'),
			'style'     => 'height:24em;',
			'note'   => 'Your custom CSS code here, will be outputted only on this particular page.',
			'disabled'  => $isElementDisabled
		));

		$fieldset->addField('custom_js', 'textarea', array(
			'class'     => 'field-pagebuilder',
			'name'      => 'custom_js',
			'label'     => Mage::helper('pagebuilder')->__('Custom Js'),
			'style'     => 'height:24em;',
			'note'   => 'Your custom JS code here, will be outputted only on this particular page.',
			'disabled'  => $isElementDisabled
		));

		$fieldset1 = $form->addFieldset('wrapper_fieldset', array(
			'legend'    => "<i class='fa fa-cog'></i>".Mage::helper('pagebuilder')->__('Wraper For Page Builder'),
			'disabled'  => $isElementDisabled
		));

		$fieldset1->addField('enable_wrapper', 'select', array(
			'class'     => 'field-pagebuilder',
			'label'     => Mage::helper('pagebuilder')->__('Enable Wrapper '.$builder_type_label),
			'title'     => Mage::helper('pagebuilder')->__('Enable Wrapper '.$builder_type_label),
			'name'      => 'wrapper_page',
			'required'  => false,
			'options'   => Mage::getSingleton('pagebuilder/system_config_source_status')->getOptionArray(),
			'value'     => $model->getData('enable_wrapper')?$model->getData('enable_wrapper'):'2',
			'disabled'  => $isElementDisabled
		));

		$fieldset1->addField('select_wrapper', 'select', array(
			'class'     => 'field-pagebuilder',
			'label'     => Mage::helper('pagebuilder')->__('Select Wrapper Class'),
			'title'     => Mage::helper('pagebuilder')->__('Select Wrapper Class'),
			'name'      => 'select_wrapper',
			'required'  => false,
			'options'   => Mage::getSingleton('pagebuilder/system_config_source_typewrapper')->getOptionArray(),
			'note'      => 'Choice a container class',
			'disabled'  => $isElementDisabled
		));

		$fieldset1->addField('wrapper_class', 'text', array(
			'class'     => 'field-pagebuilder',
			'label'     => Mage::helper('pagebuilder')->__('Custom Wrapper Class'),
			'title'     => Mage::helper('pagebuilder')->__('Custom Wrapper Class'),
			'name'      => 'wrapper_class',
			'required'  => false,
			'note'      => 'Enter wrapper class code here. Example: container, ...',
			'disabled'  => $isElementDisabled
		));

		$fieldset2 = $form->addFieldset('customtemp_fieldset', array(
			'legend'    => "<i class='fa fa-cog'></i>".Mage::helper('pagebuilder')->__('Page Builder Template Settings'),
			'disabled'  => $isElementDisabled
		));

		$fieldset2->addField('template_settings', 'text', array(
			'class'     => 'field-pagebuilder',
			'label'     => Mage::helper('pagebuilder')->__('Custom Template Settings'),
			'title'     => Mage::helper('pagebuilder')->__('Custom Template Settings'),
			'name'      => 'template_settings',
			'required'  => false,
			'note'      => 'Input custom module template file path. For example: sm/pagebuilder/default.phtml, ... Empty for default',
			'disabled'  => $isElementDisabled
		));

		if ($model->getPageId())
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
		return Mage::helper('pagebuilder')->__('Settings');
	}

	/**
	 * Prepare title for tab
	 *
	 * @return string
	 */
	public function getTabTitle()
	{
		return Mage::helper('pagebuilder')->__('Settings');
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
		return Mage::getSingleton('admin/session')->isAllowed('pagebuilder/pagebuilder/' . $action);
	}
}