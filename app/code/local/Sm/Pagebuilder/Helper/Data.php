<?php
/**
 * Created by PhpStorm.
 * User: Vu Van Phan
 * Date: 10-10-2015
 * Time: 00:10
 */
class Sm_Pagebuilder_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_ENABLED_PAGEBUILDER  = 'pagebuilder/general/isenabled';
	const XML_INCLUDE_JQUERY       = 'pagebuilder/advanced/include_jquery';

	public function enablePageBuilder($store = null){
		return Mage::getStoreConfigFlag(self::XML_ENABLED_PAGEBUILDER, $store);
	}

	public function getIncludeJquery($store = null)
	{
		return Mage::getStoreConfig(self::XML_INCLUDE_JQUERY, $store);
	}

	public function getInlucdeJQquery()
	{
		if (!(int)$this->enablePageBuilder()) return;
		if (!defined('MAGENTECH_JQUERY') && (int)$this->getIncludeJquery()) {
			define('MAGENTECH_JQUERY', 1);
			$_jquery_libary = 'sm/pagebuilder/js/jquery-2.1.4.min.js';
			return $_jquery_libary;
		}
	}

	public function getInlucdeNoconflict()
	{
		if (!(int)$this->enablePageBuilder()) return;
		if (!defined('MAGENTECH_JQUERY_NOCONFLICT') && (int)$this->getIncludeJquery()) {
			define('MAGENTECH_JQUERY_NOCONFLICT', 1);
			$_jquery_noconflict = 'sm/pagebuilder/js/jquery-noconflict.js';
			return $_jquery_noconflict;
		}
	}

	public function getInlucdeMigrate()
	{
		if (!(int)$this->enablePageBuilder()) return;
		if (!defined('MAGENTECH_JQUERY_MIGRATE') && (int)$this->getIncludeJquery()) {
			define('MAGENTECH_JQUERY_MIGRATE', 1);
			$_jquery_noconflict = 'sm/pagebuilder/js/jquery-migrate-1.2.1.min.js';
			return $_jquery_noconflict;
		}
	}

	public function getInlucdeJQqueryAdmin()
	{
		if (!defined('MAGENTECH_JQUERY')) {
			define('MAGENTECH_JQUERY', 1);
			$_jquery_libary = 'sm/pagebuilder/js/jquery-2.1.4.min.js';
			return $_jquery_libary;
		}
	}

	public function getInlucdeJQqueryUiAdmin()
	{
		if (!defined('MAGENTECH_JQUERY_UI')) {
			define('MAGENTECH_JQUERY_UI', 1);
			$_jquery_libary = 'sm/pagebuilder/js/jquery-ui.js';
			return $_jquery_libary;
		}
	}

	public function getInlucdeModalAdmin()
	{
		if (!defined('MAGENTECH_JQUERY_MODAL')) {
			define('MAGENTECH_JQUERY_MODAL', 1);
			$_jquery_libary = 'sm/pagebuilder/js/jquery.modal.min.js';
			return $_jquery_libary;
		}
	}

	public function getInlucdeNoconflictAdmin()
	{
		if (!defined('MAGENTECH_JQUERY_NOCONFLICT')) {
			define('MAGENTECH_JQUERY_NOCONFLICT', 1);
			$_jquery_noconflict = 'sm/pagebuilder/js/jquery-noconflict.js';
			return $_jquery_noconflict;
		}
	}

	public function getInlucdeMigrateAdmin()
	{
		if (!defined('MAGENTECH_JQUERY_MIGRATE')) {
			define('MAGENTECH_JQUERY_MIGRATE', 1);
			$_jquery_migrate = 'sm/pagebuilder/js/jquery-migrate-1.2.1.min.js';
			return $_jquery_migrate;
		}
	}

	public function renderShortCode($type, $pageid="", $settings = array()){
		if($type) {
			$options = array();
			if($settings) {
				foreach($settings as $k => $v) {
					if(trim($v)) {
						$options[] = trim($k). '="'.trim($v).'"';
					}
				}
			}
			$page_id = '';
			if($pageid) {
				$page_id = 'page_id="'.trim($pageid).'"';
			}
			return '{{widget type="'.trim($type).'" '.$page_id.' '.implode(" ", $options).'}}';
		}
		return  ;
	}
}