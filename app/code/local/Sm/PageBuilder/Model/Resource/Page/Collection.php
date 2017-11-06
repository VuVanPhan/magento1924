<?php
/**
 * Created by PhpStorm.
 * User: Vu Van Phan
 * Date: 04-11-2015
 * Time: 09:46
 */
class Sm_PageBuilder_Model_Resource_Page_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
	protected $_previewFlag;

	public function _construct(){
		parent::_construct();
		$this->_init('pagebuilder/page');
	}

	public function setFirstStoreFlag($flag = false)
	{
		$this->_previewFlag = $flag;
		return $this;
	}

	protected function _afterLoad()
	{
		if ($this->_previewFlag) {
			$items = $this->getColumnValues('page_id');
			$connection = $this->getConnection();
			if (count($items)) {
				$select = $connection->select()
					->from(array('pb'=>$this->getTable('pagebuilder/page')))
					->where('pb.page_id IN (?)', $items);

				if ($result = $connection->fetchPairs($select)) {
					foreach ($this as $item) {
						if (!isset($result[$item->getData('page_id')])) {
							continue;
						}
						if ($result[$item->getData('page_id')] == 0) {
							$stores = Mage::app()->getStores(false, true);
							$storeId = current($stores)->getId();
							$storeCode = key($stores);
						} else {
							$storeId = $result[$item->getData('page_id')];
							$storeCode = Mage::app()->getStore($storeId)->getCode();
						}
						$item->setData('_first_store_id', $storeId);
						$item->setData('store_code', $storeCode);
					}
				}
			}
		}

		return parent::_afterLoad();
	}
}