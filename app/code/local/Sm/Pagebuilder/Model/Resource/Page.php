<?php
/**
 * Created by PhpStorm.
 * User: Vu Van Phan
 * Date: 04-11-2015
 * Time: 09:46
 */
class Sm_Pagebuilder_Model_Resource_Page extends Mage_Core_Model_Resource_Db_Abstract
{
	public function _construct(){
		$this->_init('pagebuilder/page', 'page_id');
	}

	/**
	 * Get store ids to which specified item is assigned
	 *
	 * @param int $id
	 * @return array
	 */
	public function lookupStoreIds($pageId)
	{
		$adapter = $this->_getReadAdapter();

		$select  = $adapter->select()
			->from($this->getTable('cms/page_store'), 'store_id')
			->where('page_id = ?',(int)$pageId);

		return $adapter->fetchCol($select);
	}

	/**
	 * Perform operations after object load
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return Mage_Cms_Model_Resource_Page
	 */
	protected function _afterLoad(Mage_Core_Model_Abstract $object)
	{
		$page_code = $object->getData('page_code');
		$model_cms = Mage::getModel('cms/page')->load($page_code, 'identifier');
		if ($model_cms->getPageId()) {
			$stores = $this->lookupStoreIds($model_cms->getPageId());

			$object->setData('store_id', $stores);
			$object->setData('root_template', $model_cms->getData('root_template'));
			$object->setData('layout_update_xml', $model_cms->getData('layout_update_xml'));
			$object->setData('custom_theme_from', $model_cms->getData('custom_theme_from'));
			$object->setData('custom_theme_to', $model_cms->getData('custom_theme_to'));
			$object->setData('custom_theme', $model_cms->getData('custom_theme'));
			$object->setData('custom_root_template', $model_cms->getData('custom_root_template'));
			$object->setData('custom_layout_update_xml', $model_cms->getData('custom_layout_update_xml'));
			$object->setData('meta_keywords', $model_cms->getData('meta_keywords'));
			$object->setData('meta_description', $model_cms->getData('meta_description'));
		}

		if($settings = $object->getData("settings")) {
			$settings = unserialize($settings);
			if($settings) {
				foreach($settings as $key => $val) {
					$object->setData($key, $val);
				}
			}
		}

		return parent::_afterLoad($object);
	}

	public function _beforeSave(Mage_Core_Model_Abstract $object)
	{
		foreach (array('custom_theme_from', 'custom_theme_to') as $field) {
			$value = !$object->getData($field) ? null : $object->getData($field);
			$object->setData($field, $this->formatDate($value));
		}

		if (!$this->getIsUniquePageBuilder($object)) {
			Mage::throwException(Mage::helper('pagebuilder')->__('A page "Page Code" for specified store already exists.'));
		}

		if(!$object->getPageId())
		{
			if (!$this->getIsUniquePageToStores($object)) {
				Mage::throwException(Mage::helper('pagebuilder')->__('A page URL key for specified store already exists.'));
			}

			if (!$this->isValidPageIdentifier($object)) {
				Mage::throwException(Mage::helper('pagebuilder')->__('The page URL key contains capital letters or disallowed symbols.'));
			}

			if ($this->isNumericPageIdentifier($object)) {
				Mage::throwException(Mage::helper('pagebuilder')->__('The page URL key cannot consist only of numbers.'));
			}
		}
		// modify create / update dates
		if ($object->isObjectNew() && !$object->hasCreationTime()) {
			$object->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
		}

		$object->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());

		return parent::_beforeSave($object);
	}

	/**
	 *  Check whether page identifier is numeric
	 *
	 * @date Wed Mar 26 18:12:28 EET 2008
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return bool
	 */
	protected function isNumericPageIdentifier(Mage_Core_Model_Abstract $object)
	{
		return preg_match('/^[0-9]+$/', $object->getData('identifier'));
	}

	/**
	 *  Check whether page identifier is valid
	 *
	 *  @param    Mage_Core_Model_Abstract $object
	 *  @return   bool
	 */
	protected function isValidPageIdentifier(Mage_Core_Model_Abstract $object)
	{
		return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('identifier'));
	}

	/**
	 * Retrieve load select with filter by identifier, store and activity
	 *
	 * @param string $identifier
	 * @param int|array $store
	 * @param int $isActive
	 * @return Varien_Db_Select
	 */
	protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
	{
		$select = $this->_getReadAdapter()->select()
			->from(array('cp' => $this->getTable('cms/page')))
			->join(
				array('cps' => $this->getTable('cms/page_store')),
				'cp.page_id = cps.page_id',
				array())
			->where('cp.identifier = ?', $identifier)
			->where('cps.store_id IN (?)', $store);

		if (!is_null($isActive)) {
			$select->where('cp.is_active = ?', $isActive);
		}

		return $select;
	}

	/**
	 * Retrieve load select with filter by identifier, store and activity
	 *
	 * @param string $identifier
	 * @param int|array $store
	 * @param int $isActive
	 * @return Varien_Db_Select
	 */
	protected function _getLoadByPageCodeSelect($pagecode, $isActive = null)
	{
		$select = $this->_getReadAdapter()->select()
			->from(array('pp' => $this->getTable('pagebuilder/page')))
			->where('pp.page_code = ?', $pagecode);

		if (!is_null($isActive)) {
			$select->where('pp.is_active = ?', $isActive);
		}

		return $select;
	}

	/**
	 * Check for unique of identifier of page to selected store(s).
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return bool
	 */
	public function getIsUniquePageToStores(Mage_Core_Model_Abstract $object)
	{
		if (!$object->hasStores()) {
			$stores = array(Mage_Core_Model_App::ADMIN_STORE_ID);
		} else {
			$stores = (array)$object->getData('stores');
		}

		$select = $this->_getLoadByIdentifierSelect($object->getData('identifier'), $stores);

		if ($object->getPageId()) {
			$select->where('cps.page_id <> ?', $object->getPageId());
		}

		if ($this->_getWriteAdapter()->fetchRow($select)) {
			return false;
		}

		return true;
	}

	/**
	 * Check for unique of identifier of page to selected store(s).
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return bool
	 */
	public function getIsUniquePageBuilder(Mage_Core_Model_Abstract $object)
	{
		$select = $this->_getLoadByPageCodeSelect($object->getData('page_code'));

		if ($object->getPageId()) {
			$select->where('pp.page_id <> ?', $object->getPageId());
		}

		if ($this->_getWriteAdapter()->fetchRow($select)) {
			return false;
		}

		return true;
	}
}