<?php
/**
 * Created by PhpStorm.
 * User: Vu Van Phan
 * Date: 04-11-2015
 * Time: 15:58
 */
class Sm_Pagebuilder_Block_Adminhtml_Pagebuilder_Edit_Tab_Design extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
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
		$wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
			array('tab_id' => $this->getTabId())
		);
		$builder_type_label = 'Page';

		$group = $form->addFieldset('design_fieldset', array(
			'legend'    => "<i class='fa fa-cubes'></i>".Mage::helper('pagebuilder')->__('Design Page'),
			'disabled'  => $isElementDisabled
		));

		$fieldset = $group->addFieldset('class_fieldset', array(
			'class'     => 'no-spacing',
			'disabled'  => $isElementDisabled
		));

		$fieldset->addField('prefix_class', 'text', array(
			'class'     => 'field-pagebuilder',
			'label' => Mage::helper('pagebuilder')->__('Prefix Class'),
			'title' => Mage::helper('pagebuilder')->__('Prefix Class'),
			'name'  => 'prefix_class',
			'required' => false,
			'disabled'  => $isElementDisabled
		));

		$fieldset->addField('container', 'select', array(
			'class'     => 'field-pagebuilder',
			'label'     => Mage::helper('pagebuilder')->__('Enable Container'),
			'title'     => Mage::helper('pagebuilder')->__('Enable Container'),
			'name'      => 'container',
			'required'  => false,
			'options'   => Mage::getSingleton('pagebuilder/system_config_source_status')->getOptionArray(),
			'disabled'  => $isElementDisabled
		));

		$backup_layout = count($this->getBackupLayout());
		if ($backup_layout>0)
		{
			$fieldset->addField('backup_layout', 'select', array(
				'class'     => 'field-pagebuilder',
				'label'     => Mage::helper('pagebuilder')->__('Use '.$builder_type_label.' Layout'),
				'title'     => Mage::helper('pagebuilder')->__('Use '.$builder_type_label.' Layout'),
				'name'      => 'backup_layout',
				'required'  => false,
				'values'   => $this->getBackupLayout(),
				'disabled'  => $isElementDisabled
			));
		}

		$fieldset1 = $group->addFieldset('content_fieldset', array(
			'class'=>'fieldset-wide no-spacing',
			'disabled'  => $isElementDisabled
		));

		$contentField = $fieldset1->addField('content', 'editor', array(
			'name'      => 'content',
			'style'     => 'height:36em;',
			'required'  => true,
			'disabled'  => $isElementDisabled,
		));

		$renderer = $this->getLayout()->createBlock('pagebuilder/adminhtml_pagebuilder_form_renderer_fieldset_content')
			->setTemplate('sm/pagebuilder/widget/form/renderer/fieldset/content.phtml');
		$contentField->setRenderer($renderer);

		if ($model->getPageId())
		{
			$form->setValues($model->getData());
		}
		$this->setForm($form);

		return parent::_prepareForm();
	}

	public function _getCollectionBlock(){
		return "pagebuilder/page";
	}

	public function getBackupLayout(){
		$data = Mage::getModel($this->_getCollectionBlock())->getCollection();
		$collection = $data->getData();
		$array[] = array(
			'value' =>	'',
			'label' =>	Mage::helper('pagebuilder')->__('-- Load a Layout --'),
		);
		if (count($collection)>0)
		{
			foreach($collection as $c)
			{
				$page_id = $c['page_id'];
				$title = $c['title'];
				$array[] = array(
					'value'			=>	$page_id,
					'label'     	=>	Mage::helper('pagebuilder')->__($title),
				);
			}
		}
		return $array;
	}
	
	/**
	 * Prepare label for tab
	 *
	 * @return string
	 */
	public function getTabLabel()
	{
		return Mage::helper('pagebuilder')->__('Design Page');
	}

	/**
	 * Prepare title for tab
	 *
	 * @return string
	 */
	public function getTabTitle()
	{
		return Mage::helper('pagebuilder')->__('Design Page');
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