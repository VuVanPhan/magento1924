<?php
/**
 * Created by PhpStorm.
 * User: Vu Van Phan
 * Date: 10-10-2015
 * Time: 00:18
 */
class Sm_Pagebuilder_Block_Adminhtml_Pagebuilder extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_pagebuilder';
		$this->_blockGroup = 'pagebuilder';
		$this->_headerText = '<i class="fa fa-folder-open"></i>'.Mage::helper('pagebuilder')->__('Manager Pagebuilder');
		$this->_addButtonLabel = Mage::helper('pagebuilder')->__('Add New Page');
		parent::__construct();
	}
}