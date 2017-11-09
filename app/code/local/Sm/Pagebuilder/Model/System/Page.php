<?php
/**
 * Created by PhpStorm.
 * User: Vu Van Phan
 * Date: 11-11-2015
 * Time: 17:39
 */
class Sm_Pagebuilder_Model_System_Page
{
	public function toOptionArray()
	{
		$collection = Mage::getModel('pagebuilder/page')->getCollection();
		$array      = array();
		foreach($collection as $c)
		{
			$array[]    = array(
				'value' => $c->getData('page_id'),
				'label' => $c->getTitle()
			);
		}
		return $array;
	}
}