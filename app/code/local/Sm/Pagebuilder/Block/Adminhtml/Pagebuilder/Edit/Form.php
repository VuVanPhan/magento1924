<?php
/**
 * Created by PhpStorm.
 * User: Vu Van Phan
 * Date: 04-11-2015
 * Time: 15:34
 */
class Sm_Pagebuilder_Block_Adminhtml_Pagebuilder_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(array(
			'id' => 'edit_form',
			'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
			'method' => 'post',
			'enctype' => 'multipart/form-data'
		));

		$form->setUseContainer(true);
		$this->setForm($form);
		return parent::_prepareForm();
	}

	/**
	 * Prepare layout.
	 * Add files to use dialog windows
	 *
	 * @return Mage_Adminhtml_Block_System_Email_Template_Edit_Form
	 */
	protected function _prepareLayout()
	{
		if ($head = $this->getLayout()->getBlock("head")) {
			$head->addItem('js', 'prototype/window.js')
				->addItem('js', 'mage/adminhtml/wysiwyg/widget.js')
				->addItem('js_css', 'prototype/windows/themes/default.css')
				->addCss('lib/prototype/windows/themes/magento.css')
				->addCss('sm/pagebuilder/css/pagebuilder.css')
				->addCss('sm/pagebuilder/css/jquery.pagedesignmanager.css')
				->addCss('sm/pagebuilder/css/font-awesome.css')
				->addCss('sm/pagebuilder/css/jquery.modal.min.css')
				->addItem('js', 'sm/pagebuilder/js/jquery-2.1.4.min.js')
				->addItem('js', 'sm/pagebuilder/js/jquery-noconflict.js')
				->addItem('js', 'sm/pagebuilder/js/jquery-ui.js')
				->addItem('js', 'sm/pagebuilder/js/jquery.modal.min.js')
				->addItem('js', 'mage/adminhtml/variables.js')
				->addItem('js', 'sm/pagebuilder/jsplugin/renderercontentdesign.js')
				->addItem('js', 'sm/pagebuilder/jsplugin/rendererhelper.js')
				->addItem('js', 'sm/pagebuilder/jsplugin/rendererwidget.js')
				->addItem('js', 'sm/pagebuilder/jsplugin/pagebuilder.js');
		}
		return parent::_prepareLayout();
	}
}