<?php
/**
 * Created by PhpStorm.
 * User: Vu Van Phan
 * Date: 14-11-2015
 * Time: 11:05
 */

class Sm_Pagebuilder_Block_Adminhtml_Addwidget_Store_Switcher_Form_Renderer_Fieldset_Element extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element implements Varien_Data_Form_Element_Renderer_Interface
{
	protected $_element;

	protected function _construct()
	{
		$this->setTemplate('sm/pagebuilder/widget/store/switcher/form/renderer/fieldset/element.phtml');
	}

	public function getElement()
	{
		return $this->_element;
	}

	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		$this->_element = $element;
		return $this->toHtml();
	}

//	public function getHintHtml()
//	{
//		return Mage::getBlockSingleton('adminhtml/store_switcher')->getHintHtml();
//	}
}