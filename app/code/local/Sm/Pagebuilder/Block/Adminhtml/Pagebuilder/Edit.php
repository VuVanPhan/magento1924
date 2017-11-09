<?php
/**
 * Created by PhpStorm.
 * User: Vu Van Phan
 * Date: 10-10-2015
 * Time: 11:56
 */
class Sm_Pagebuilder_Block_Adminhtml_Pagebuilder_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();
		$this->_objectId    = 'page_id';
		$this->_blockGroup  = 'pagebuilder';
		$this->_controller  = 'adminhtml_pagebuilder';
		$this->_form       = 'edit';
		$page = Mage::registry('page');

		$this->_formScripts[]   = "editForm = new varienForm('edit_form', '');";
		$this->_formScripts[] = "var PG = new PageBuilder(editForm);";

		if(is_array($page->getParams()))
		{
			foreach ($page->getParams() as $p) {
				$this->_formScripts[] = "PG.addLayer(".Mage::helper('core')->jsonEncode($p).");";
			}
		}
	}

	public function getHeaderText()
	{
		if (Mage::registry('page') && Mage::registry('page')->getPageId()) {
			return "<i class='fa fa-pencil-square'></i>".Mage::helper('pagebuilder')->__("Edit Page '%s'", $this->htmlEscape(Mage::registry('page')->getTitle()));
		} else {
			return "<i class='fa fa-plus-circle'></i>".Mage::helper('pagebuilder')->__('Add New Items');
		}
	}

	protected function _prepareLayout()
	{
		parent::_prepareLayout();
		$this->updateButton('save', 'onclick', 'PG.save();');
		$this->_addButton('saveandcontinue', array(
			'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
			'onclick' => 'PG.save(true)',
			'class' => 'save',
		), -100);
		return $this;
	}
}