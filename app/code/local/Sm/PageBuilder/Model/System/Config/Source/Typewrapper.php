<?php
/**
 * Created by PhpStorm.
 * User: Vu Van Phan
 * Date: 06-11-2015
 * Time: 09:48
 */
class Sm_PageBuilder_Model_System_Config_Source_Typewrapper extends Varien_Object
{
	const CONTAINER         = 'container';
	const CONTAINER_SMALL   = 'container-small';
	const CONTAINER_LARGE   = 'container-large';
	const CONTAINER_FLUID   = 'container-fluid';
	const MAIN_CONTAINER    = 'main-container';

	static public function getOptionArray(){
		return array(
			null 		            => Mage::helper('pagebuilder')->__('-- Select Class --'),
			self::CONTAINER 		=> Mage::helper('pagebuilder')->__('Container'),
			self::CONTAINER_SMALL   => Mage::helper('pagebuilder')->__('Container-Small'),
			self::CONTAINER_LARGE	=> Mage::helper('pagebuilder')->__('Container-Large'),
			self::CONTAINER_FLUID	=> Mage::helper('pagebuilder')->__('Container-Fluid'),
			self::MAIN_CONTAINER    => Mage::helper('pagebuilder')->__('Main-Container')
		);
	}

	static public function setOptionArray(){
		return array(
			array(
				'value'     => null,
				'label'     => Mage::helper('pagebuilder')->__('-- Select Class --')
			),
			array(
				'value'     => self::CONTAINER,
				'label'     => Mage::helper('pagebuilder')->__('Container')
			),
			array(
				'value'     => self::CONTAINER_SMALL,
				'label'     => Mage::helper('pagebuilder')->__('Container-Small')
			),
			array(
				'value'     => self::CONTAINER_LARGE,
				'label'     => Mage::helper('pagebuilder')->__('Container-Large')
			),
			array(
				'value'     => self::CONTAINER_FLUID,
				'label'     => Mage::helper('pagebuilder')->__('Container-Fluid')
			),
			array(
				'value'     => self::MAIN_CONTAINER,
				'label'     => Mage::helper('pagebuilder')->__('Main-Container')
			)
		);
	}
}