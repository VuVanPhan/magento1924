<?php
/**
 * Created by PhpStorm.
 * User: Vu Van Phan
 * Date: 04-11-2015
 * Time: 15:58
 */
class Sm_Pagebuilder_Block_Adminhtml_Pagebuilder_Edit_Tab_Cmspage extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	public function __construct()
	{
		parent::__construct();
		$this->setShowGlobalIcon(true);
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

		$fieldset = $form->addFieldset('pagelayout_fieldset', array(
			'legend'    => "<i class='fa fa-tags'></i> ".Mage::helper('pagebuilder')->__($builder_type_label.' Layout'),
			'class'  => 'fieldset-wide',
			'disabled'  => $isElementDisabled
		));

		$fieldset->addField('root_template', 'select', array(
			'class'    => 'field-pagebuilder',
			'name'     => 'root_template',
			'label'    => Mage::helper('pagebuilder')->__('Layout'),
			'required' => true,
			'values'   => Mage::getSingleton('page/source_layout')->toOptionArray(),
			'disabled' => $isElementDisabled
		));
		if (!$model->getId()) {
			$model->setRootTemplate(Mage::getSingleton('page/source_layout')->getDefaultValue());
		}

		$fieldset->addField('layout_update_xml', 'textarea', array(
			'class'    => 'field-pagebuilder',
			'name'      => 'layout_update_xml',
			'label'     => Mage::helper('pagebuilder')->__('Layout Update XML'),
			'style'     => 'height:24em;',
			'disabled'  => $isElementDisabled
		));

		$fieldset1 = $form->addFieldset('customer_fieldset', array(
			'legend'    => "<i class='fa fa-tags'></i> ".Mage::helper('pagebuilder')->__('Custom Design'),
			'class'  => 'fieldset-wide',
			'disabled'  => $isElementDisabled
		));

		$fieldset1->addField('custom_theme_from', 'date', array(
			'name'      => 'custom_theme_from',
			'label'     => Mage::helper('pagebuilder')->__('Custom Design From'),
			'image'     => $this->getSkinUrl('images/grid-cal.gif'),
			'format'    => $dateFormatIso,
			'disabled'  => $isElementDisabled,
			'class'     => 'validate-date validate-date-range date-range-custom_theme-from field-pagebuilder'
		));

		$fieldset1->addField('custom_theme_to', 'date', array(
			'name'      => 'custom_theme_to',
			'label'     => Mage::helper('pagebuilder')->__('Custom Design To'),
			'image'     => $this->getSkinUrl('images/grid-cal.gif'),
			'format'    => $dateFormatIso,
			'disabled'  => $isElementDisabled,
			'class'     => 'validate-date validate-date-range date-range-custom_theme-to field-pagebuilder'
		));

		$fieldset1->addField('custom_theme', 'select', array(
			'class'     => 'field-pagebuilder',
			'name'      => 'custom_theme',
			'label'     => Mage::helper('pagebuilder')->__('Custom Theme'),
			'values'    => Mage::getModel('core/design_source_design')->getAllOptions(),
			'disabled'  => $isElementDisabled
		));


		$fieldset1->addField('custom_root_template', 'select', array(
			'class'     => 'field-pagebuilder',
			'name'      => 'custom_root_template',
			'label'     => Mage::helper('pagebuilder')->__('Custom Layout'),
			'values'    => Mage::getSingleton('page/source_layout')->toOptionArray(true),
			'disabled'  => $isElementDisabled
		));

		$fieldset1->addField('custom_layout_update_xml', 'textarea', array(
			'class'     => 'field-pagebuilder',
			'name'      => 'custom_layout_update_xml',
			'label'     => Mage::helper('pagebuilder')->__('Custom Layout Update XML'),
			'style'     => 'height:24em;',
			'disabled'  => $isElementDisabled
		));

		$fieldset2 = $form->addFieldset('meta_fieldset', array(
			'legend' => "<i class='fa fa-link'></i> ".Mage::helper('pagebuilder')->__('Meta Data'),
			'class' => 'fieldset-wide',
			'disabled'  => $isElementDisabled
		));

		$fieldset2->addField('meta_keywords', 'textarea', array(
			'class'     => 'field-pagebuilder',
			'name' => 'meta_keywords',
			'label' => Mage::helper('pagebuilder')->__('Keywords'),
			'title' => Mage::helper('pagebuilder')->__('Meta Keywords'),
			'disabled'  => $isElementDisabled
		));

		$fieldset2->addField('meta_description', 'textarea', array(
			'class'     => 'field-pagebuilder',
			'name' => 'meta_description',
			'label' => Mage::helper('pagebuilder')->__('Description'),
			'title' => Mage::helper('pagebuilder')->__('Meta Description'),
			'disabled'  => $isElementDisabled
		));

		if (!$model->getPageId()) {
			$model->setData('status', $isElementDisabled ? '0' : '1');
		}

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
		return Mage::helper('pagebuilder')->__('CMS Page Information');
	}

	/**
	 * Prepare title for tab
	 *
	 * @return string
	 */
	public function getTabTitle()
	{
		return Mage::helper('pagebuilder')->__('CMS Page Information');
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