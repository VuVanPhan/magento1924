<?php
/**
 * Created by PhpStorm.
 * User: Vu Van Phan
 * Date: 06-11-2015
 * Time: 10:57
 */
class Sm_Pagebuilder_Block_Adminhtml_Pagebuilder_Form_Renderer_Fieldset_Element extends Mage_Adminhtml_Block_Template
	implements Varien_Data_Form_Element_Renderer_Interface
{
	protected $_element;

	protected function _construct()
	{
		$this->setTemplate('sm/pagebuilder/widget/form/renderer/fieldset/content.phtml');
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

	public function getAddWidgetUrl() {
		return $this->getUrl('adminhtml/pagebuilder_pagebuilder/loadIndex');
	}
}