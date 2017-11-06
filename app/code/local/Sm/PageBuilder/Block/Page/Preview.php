<?php
/**
 * Created by PhpStorm.
 * User: Vu Van Phan
 * Date: 12-11-2015
 * Time: 09:47
 */
class Sm_PageBuilder_Block_Page_Preview extends Mage_Catalog_Block_Product_Abstract implements Mage_Widget_Block_Interface
{
	protected $_config = null;
	protected $hash = null;

	protected function _construct()
	{
		parent::_construct();
		$this->_config = $this->_getCfg();
	}

	public function _getCfg($attr = null)
	{
		// get default config.xml
		$defaults = array();
		$def_cfgs = Mage::getConfig()
			->loadModulesConfiguration('config.xml')
			->getNode('default/pagebuilder')->asArray();
		if (empty($def_cfgs)) return;
		$groups = array();
		foreach ($def_cfgs as $def_key => $def_cfg) {
			$groups[] = $def_key;
			foreach ($def_cfg as $_def_key => $cfg) {
				$defaults[$_def_key] = $cfg;
			}
		}

		// get configs after change
		$_configs = (array)Mage::getStoreConfig("pagebuilder");
		if (empty($_configs)) return;
		$cfgs = array();

		foreach ($groups as $group) {
			$_cfgs = Mage::getStoreConfig('pagebuilder/' . $group . '');
			foreach ($_cfgs as $_key => $_cfg) {
				$cfgs[$_key] = $_cfg;
			}
		}

		// get output config
		$configs = array();
		foreach ($defaults as $key => $def) {
			if (isset($defaults[$key])) {
				$configs[$key] = $cfgs[$key];
			} else {
				unset($cfgs[$key]);
			}
		}
		$this->_config = ($attr != null) ? array_merge($configs, $attr) : $configs;
		return $this->_config;
	}

	public function _getConfig($name = null, $value_def = null)
	{
		if (is_null($this->_config)) $this->_getCfg();
		if (!is_null($name)) {
			$value_def = isset($this->_config[$name]) ? $this->_config[$name] : $value_def;
			return $value_def;
		}
		return $this->_config;
	}


	public function _setConfig($name, $value = null)
	{

		if (is_null($this->_config)) $this->_getCfg();
		if (is_array($name)) {
			$this->_config = array_merge($this->_config, $name);

			return;
		}
		if (!empty($name) && isset($this->_config[$name])) {
			$this->_config[$name] = $value;
		}
		return true;
	}

	protected function _prepareLayout()
	{
		if(Mage::helper('pagebuilder')->enablePageBuilder())
		{
			$head = $this->getLayout()->getBlock('head');
			if (Mage::app()->getRequest()->getActionName() == 'preview') {
				$head->addJs('sm/pagebuilder/js/jquery-2.1.4.min.js');
				$head->addJs('sm/pagebuilder/js/jquery-migrate-1.2.1.min.js');
				$head->addJs('sm/pagebuilder/js/jquery-noconflict.js');
			}
			return parent::_prepareLayout();
		}
	}

	protected function _toHtml()
	{
		if (!$this->_getConfig('isenabled', 1)) return;
		$use_cache = (int)$this->_getConfig('use_cache');
		$cache_time = (int)$this->_getConfig('cache_time');
		$folder_cache = Mage::getBaseDir('cache');
		$folder_cache = $folder_cache.'/Sm/PageBuilder/';
		if(!file_exists($folder_cache))
			mkdir ($folder_cache, 0777, true);
		if (!class_exists('Cache_Lite'))
			require_once($this->getBaseDir() .  'lib' . DS .  'Sm' .DS . 'PageBuilder' .DS . 'Cache_Lite' . DS . 'Lite.php');
		$options = array(
			'cacheDir' => $folder_cache,
			'lifeTime' => $cache_time
		);
		$Cache_Lite = new Cache_Lite($options);

		if ($use_cache){
			$this->hash = md5( serialize($this->_getConfig()) );
			if ($data = $Cache_Lite->get($this->hash)) {
				return  $data;
			}
			else
			{
				$template_file = $this->getTemplate();
				$template_file = (!empty($template_file)) ? $template_file : "sm/pagebuilder/default.phtml";
				$this->setTemplate($template_file);
				$data = parent::_toHtml();
				$Cache_Lite->save($data);
			}
		}else{
			if(file_exists($folder_cache))
				$Cache_Lite->_cleanDir($folder_cache);
			$template_file = $this->getTemplate();
			$template_file = (!empty($template_file)) ? $template_file : "sm/pagebuilder/default.phtml";
			$this->setTemplate($template_file);
		}
		return parent::_toHtml();
	}

	public function renderPageBuilder(){
		if (Mage::helper('pagebuilder')->enablePageBuilder())
		{
			$page_id = $this->getData('id');
			$model = Mage::getModel('pagebuilder/page');
			$collection = $model->load($page_id);
			/*$page_id = $this->getRequest()->getParam('id');*/
		}
		return $collection;
	}
}