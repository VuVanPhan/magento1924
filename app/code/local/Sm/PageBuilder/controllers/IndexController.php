<?php
/**
 * Created by PhpStorm.
 * User: Vu Van Phan
 * Date: 10-10-2015
 * Time: 00:05
 */
class Sm_PageBuilder_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

	public function previewAction()
	{
		$id = $this->getRequest()->getParam('id');
		$this->loadLayout();
		$this->getLayout()->getBlock('root')->setTemplate('page/empty.phtml');
		$page = $this->getLayout()->createBlock('pagebuilder/page_preview', '', array(
			'id'    => $id
		));
		$this->getLayout()->getBlock('content')->append($page);
		$this->_title(Mage::helper('pagebuilder')->__('Sm Page Builder'))
			->_title(Mage::helper('pagebuilder')->__('Preview Page'));
		$this->renderLayout();
	}
}