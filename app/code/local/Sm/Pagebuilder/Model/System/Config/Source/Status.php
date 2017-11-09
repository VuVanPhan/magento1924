<?php
/**
 * Created by PhpStorm.
 * User: Vu Van Phan
 * Date: 04-11-2015
 * Time: 14:06
 */
class Sm_Pagebuilder_Model_System_Config_Source_Status extends Varien_Object
{
	const STATUS_ENABLED	= 1;
	const STATUS_DISABLED	= 0;

	static public function getOptionArray()
	{
		return array(
			self::STATUS_ENABLED    => Mage::helper('pagebuilder')->__('Enabled'),
			self::STATUS_DISABLED   => Mage::helper('pagebuilder')->__('Disabled')
		);
	}

	static public function toOptionArray()
	{
		return array(
			array(
				'value'     => self::STATUS_ENABLED,
				'label'     => Mage::helper('pagebuilder')->__('Enabled'),
			),
			array(
				'value'     => self::STATUS_DISABLED,
				'label'     => Mage::helper('pagebuilder')->__('Disabled'),
			),
		);
	}
}