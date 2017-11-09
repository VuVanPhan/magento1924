<?php
/**
 * Created by PhpStorm.
 * User: Vu Van Phan
 * Date: 04-11-2015
 * Time: 09:16
 */
class Sm_Pagebuilder_Model_Page extends Mage_Core_Model_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('pagebuilder/page');
	}

	public function _beforeSave()
	{
		if ( is_array( $this->getData('params') ) )
		{
			$this->setData( 'params', Mage::helper( 'core' )->jsonEncode($this->getParams()));
		}

		if ( is_array( $this->getData('settings') ) )
		{
			$this->setData( 'settings', Mage::helper( 'core' )->jsonEncode($this->getSettings()));
		}
		return parent::_beforeSave();
	}

	public function _afterLoad()
	{
		if ($this->getParams())
			$this->setParams( (array) Mage::helper( 'core' )->jsonDecode( $this->getParams() ) );
		return parent::_afterLoad();
	}
}