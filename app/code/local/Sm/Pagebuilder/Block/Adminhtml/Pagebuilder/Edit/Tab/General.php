<?php
/**
 * Created by PhpStorm.
 * User: Vu Van Phan
 * Date: 04-11-2015
 * Time: 15:35
 */
class Sm_Pagebuilder_Block_Adminhtml_Pagebuilder_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	protected function _prepareLayout()
	{
		if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
		}
		return parent::_prepareLayout();
	}

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

		$fieldset = $form->addFieldset('base_fieldset', array(
			'legend'    => "<i class='fa fa-sun-o'></i> ".Mage::helper('pagebuilder')->__('General Options'),
			'disabled'  => $isElementDisabled
		));

//		echo "<pre>";
//		var_dump($model->getData());
//		die();

		if($model->getPageId())
		{
			$fieldset->addField('page_id', 'hidden', array(
				'name'  => 'page_id'
			));
		}

		$fieldset->addField('title', 'text', array(
			'label' => Mage::helper('pagebuilder')->__('Title'),
			'title' => Mage::helper('pagebuilder')->__('Title'),
			'name'  => 'title',
			'class' => 'required-entry field-pagebuilder',
			'required' => true,
			'disabled'  => $isElementDisabled
		));

		$fieldset->addField('page_code', 'text', array(
			'label' => Mage::helper('pagebuilder')->__($builder_type_label.' Code'),
			'title' => Mage::helper('pagebuilder')->__($builder_type_label.' Code'),
			'name'  => 'page_code',
			'class' => 'validate-xml-identifier required-entry field-pagebuilder',
			'required' => true,
			'disabled'  => $isElementDisabled
		));

		if (!Mage::app()->isSingleStoreMode()) {
			$field = $fieldset->addField('store_id', 'multiselect', array(
				'class'     => 'field-pagebuilder',
				'name'      => 'stores[]',
				'label'     => Mage::helper('pagebuilder')->__('Store View'),
				'title'     => Mage::helper('pagebuilder')->__('Store View'),
				'required'  => true,
				'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
				'value'     => $model->getData('store_id')?$model->getData('store_id'):'0',
				'disabled'  => $isElementDisabled
			));
//			$renderer = $this->getLayout()->createBlock('adminhtml/widget_store_switcher_form_renderer_fieldset_element');
//			$field->setRenderer($renderer);
		}
		else {
			$fieldset->addField('store_id', 'hidden', array(
				'class'     => 'field-pagebuilder',
				'name'      => 'stores[]',
				'value'     => Mage::app()->getStore(true)->getId()
			));
			$model->setStoreId(Mage::app()->getStore(true)->getId());
		}

		$fieldset->addField('status', 'select', array(
			'class'     => 'field-pagebuilder',
			'label'     => Mage::helper('pagebuilder')->__('Status'),
			'title'     => Mage::helper('pagebuilder')->__('Page Status'),
			'name'      => 'status',
			'required'  => false,
			'options'   => Mage::getSingleton('pagebuilder/system_config_source_status')->getOptionArray(),
			'disabled'  => $isElementDisabled
		));

		if (!$model->getPageId()) {
			$model->setData('status', $isElementDisabled ? '0' : '1');
		}

		$fieldset->addField('show_from', 'date', array(
			'label' => Mage::helper('pagebuilder')->__('Display '.$builder_type_label.' From Date'),
			'name' => 'show_from',
			'class' => 'validate-date validate-date-range date-range-custom_theme-from field-pagebuilder',
			'required'  => false,
			'image'  => $this->getSkinUrl('images/grid-cal.gif'),
			'input_format' => $dateFormatIso,
			'format'       => $dateFormatIso,
			'disabled'  => $isElementDisabled
		));

		$fieldset->addField('show_to', 'date', array(
			'label' => Mage::helper('pagebuilder')->__('Display '.$builder_type_label.' To Date'),
			'class' => 'validate-date validate-date-range date-range-custom_theme-to field-pagebuilder',
			'required'  => false,
			'name' => 'show_to',
			'image'  => $this->getSkinUrl('images/grid-cal.gif'),
			'input_format' => $dateFormatIso,
			'format'       => $dateFormatIso,
			'disabled'  => $isElementDisabled
		));

		$fieldset->addField('customer_group', 'multiselect', array(
			'class'     => 'field-pagebuilder',
			'name'      => 'customer_group[]',
			'label'     => Mage::helper('pagebuilder')->__('Enable '.$builder_type_label.' For Certain Customer Groups'),
			'title'     => Mage::helper('pagebuilder')->__('Enable '.$builder_type_label.' For Certain Customer Groups'),
			'required'  => false,
			'values'    => $this->getCustomerGroups(),
			'disabled'  => $isElementDisabled
		));

		$fieldset->addField('position', 'text', array(
			'label' => Mage::helper('pagebuilder')->__('Position'),
			'class' => 'validate-zero-or-greater required-entry validate-number field-pagebuilder',
			'required' => true,
			'value' => $model->getData('position') ? $model->getData('position') : '0',
			'name' => 'position',
			'disabled'  => $isElementDisabled
		));


		if ($model->getPageId())
		{
			$form->setValues($model->getData());
		}
		$this->setForm($form);

		return parent::_prepareForm();
	}

	public function getCustomerGroups()
	{
		$data_array = array();
		$customer_groups = Mage::getModel('customer/group')->getCollection();;

		foreach ($customer_groups as $item_group) {
			$data_array[] = array(
				'value' => $item_group->getCustomerGroupId(),
				'label' => $item_group->getData('customer_group_code')
			);
		}
		return ($data_array);

	}

	/**
	 * Prepare label for tab
	 *
	 * @return string
	 */
	public function getTabLabel()
	{
		return Mage::helper('pagebuilder')->__('Page Information');
	}

	/**
	 * Prepare title for tab
	 *
	 * @return string
	 */
	public function getTabTitle()
	{
		return Mage::helper('pagebuilder')->__('Page Information');
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