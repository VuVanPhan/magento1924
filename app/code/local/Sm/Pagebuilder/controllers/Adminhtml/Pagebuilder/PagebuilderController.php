<?php
/**
 * Created by PhpStorm.
 * User: Vu Van Phan
 * Date: 10-10-2015
 * Time: 00:25
 */
class Sm_Pagebuilder_Adminhtml_Pagebuilder_PagebuilderController extends Mage_Adminhtml_Controller_Action
{
	private function _initAction()
	{
		$this->loadLayout()
			->_setActiveMenu('pagebuilder/managepagebuilder')
			->_addBreadcrumb(
				Mage::helper('adminhtml')->__('Pagebuilder Manager'),
				Mage::helper('adminhtml')->__('Pagebuilder Manager')
			);

		return $this;
	}

	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('pagebuilder');
	}

	public function _getCollectionBlock(){
		return "pagebuilder/page";
	}

	public function indexAction()
	{
		$this->_initAction();
		$this->_title(Mage::helper('pagebuilder')->__('Pagebuilder Manager'));
		$this->renderLayout();
	}

	public function newAction()
	{
		$this->_forward('edit');
	}

	public function editAction(){
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel($this->_getCollectionBlock())->load($id);

		if ($model->getPageId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if(!empty($data))
			{
				$model->setData($data);
			}

			Mage::register('page', $model);
			$this->_initAction();
			$this->_title(Mage::helper('pagebuilder')->__('Pagebuilder Manager'));

			if($model->getPageId()) {
				$this->_title($model->getTitle());
			}else{
				$this->_title(Mage::helper('pagebuilder')->__('Add Items'));
			}
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addLeft($this->getLayout()->createBlock('pagebuilder/adminhtml_pagebuilder_edit_tabs'))
				->_addContent($this->getLayout()->createBlock('pagebuilder/adminhtml_pagebuilder_edit'));
//			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);

			$this->renderLayout();
		}
		else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('pagebuilder')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}

	protected function _filterPostData($data)
	{
		$data = $this->_filterDates($data, array('custom_theme_from', 'custom_theme_to'));
		return $data;
	}

	protected function _validatePostData($data)
	{
		$errorNo = true;
		if (!empty($data['layout_update_xml']) || !empty($data['custom_layout_update_xml'])) {
			/** @var $validatorCustomLayout Mage_Adminhtml_Model_LayoutUpdate_Validator */
			$validatorCustomLayout = Mage::getModel('adminhtml/layoutUpdate_validator');
			if (!empty($data['layout_update_xml']) && !$validatorCustomLayout->isValid($data['layout_update_xml'])) {
				$errorNo = false;
			}
			if (!empty($data['custom_layout_update_xml'])
				&& !$validatorCustomLayout->isValid($data['custom_layout_update_xml'])) {
				$errorNo = false;
			}
			foreach ($validatorCustomLayout->getMessages() as $message) {
				$this->_getSession()->addError($message);
			}
		}
		return $errorNo;
	}

	public function _filterData(){
		if($data = $this->getRequest()->getPost())
		{
			if (isset($data['form_key']))
				unset($data['form_key']);

			if($data['customer_group'])
				$data['customer_group'] = implode(',', $data['customer_group']);

			$data['settings'] = array();
			$data['settings']['custom_css'] = isset($data['custom_css'])?$data['custom_css']:'';
			$data['settings']['custom_js'] = isset($data['custom_js'])?$data['custom_js']:'';
			$data['settings']['enable_wrapper'] = isset($data['enable_wrapper'])?$data['enable_wrapper']:'2';
			$data['settings']['select_wrapper'] = isset($data['select_wrapper'])?$data['select_wrapper']:'';
			$data['settings']['wrapper_class'] = isset($data['wrapper_class'])?$data['wrapper_class']:'';
			$data['settings']['template_settings'] = isset($data['template_settings'])?$data['template_settings']:'';

			$data['settings'] = serialize($data['settings']);

			if($this->getRequest()->getParam("id")) {
				$data['update_time'] = date( 'Y-m-d H:i:s' );
			} else {
				$data['creation_time'] = date( 'Y-m-d H:i:s' );
			}

			if($data['stores'])
				$data['stores_id'] = implode(',', $data['stores']);

			$data['identifier'] = $data['page_code'];
			$data['content_heading'] = '';
			$filterData['is_active'] = $data['status'];
			$filterData['sort_order'] = 0;
			$filterData['published_revision_id'] = 0;
			$filterData['website_root'] = 1;
			$filterData['under_version_control'] = 0;
		}
		return $data;
	}
	public function saveAction(){
		if ($data = $this->getRequest()->getPost()) {
			$filterData = $this->_filterData();
			$model = Mage::getModel('pagebuilder/page');

			if ($id = $this->getRequest()->getParam('id'))
			{
				$model->setData($filterData)->setId($id);
			}
			else
			{
				$model->setData($filterData);
			}

			try{
				$model->save();
				$pageid = $model->getPageId();

				if(!$this->getRequest()->getParam('id'))
				{
					Mage::dispatchEvent('pagebuilder_render_shortCode', array('pagebuilder'=>$model));
				}

				$mode_cms = Mage::getModel('cms/page')->load($data['page_code'], "identifier");
				$settings = array();
				$settings['template'] = isset($data['template_settings'])?$data['template_settings']:'';
				$settings['shortcode'] = isset($data['page_code'])?$data['page_code']:'';

				$filterData['content'] = Mage::helper('pagebuilder')->renderShortCode('pagebuilder/page_preview', $pageid, $settings);

				if($id_cms = $mode_cms->getPageId())
				{
					$mode_cms->setData($filterData)->setId($id_cms);
				}
				else
				{
					$mode_cms->setData($filterData);
				}

				$mode_cms->save();

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('pagebuilder')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($data['back']) {
//					$this->_redirect('*/*/edit', array(
//						'id' => $model->getPageId(),
//						'activeTab' => $this->getRequest()->getParam('activeTab')
//					));
					$url = $this->getUrl('*/*/edit', array(
						'id' => $model->getPageId(),
						'_current' => true,
//						'activeTab' => $this->getRequest()->getParam('activeTab')
					));
					$this->getResponse()->setBody($url);
					return;
				}
				$url = $this->getUrl('*/*/');
				$this->getResponse()->setBody($url);
//				$this->_redirect('*/*/');
				return;
			}
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('_current'=>true)));
				$url = $this->getUrl('*/*/edit', array(
					'id' => $model->getPageId(),
					'_current' => true,
//					'activeTab' => $this->getRequest()->getParam('activeTab')
				));
				$this->getResponse()->setBody($url);
				return;
				return;
			}
			catch (Exception $e)
			{
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('pagebuilder')->__('Unable to find item to save'));
		$this->_redirect('*/*/');
	}

	public function deleteAction(){
		if($page_id = $this->getRequest()->getParam('id'))
		{
			$model = Mage::getModel('pagebuilder/page')->load($page_id);
			$identifier = $model->getData('page_code');
			$model_cms = Mage::getModel('cms/page')->load($identifier, "identifier");
			try{
				$model->delete();
				$model_cms->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('adminhtml')->__(
						'Total of %d record(s) were successfully deleted', count($page_id)
					)
				);
				$this->getResponse()->setRedirect($this->getUrl('*/*/', array('_current'=>true, 'id'=>null)));
			}
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('_current'=>true)));
				return;
			}
			catch(Exception $e)
			{
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('pagebuilder')->__('An error occurred while trying to delete the items.'));
				$this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('_current'=>true)));
				return;
			}
		}
		$this->getResponse()->setRedirect($this->getUrl('*/*/', array('_current'=>true, 'id'=>null)));
	}

	public function massDeleteAction(){
		if($page_id = $this->getRequest()->getParam('page_id'))
		{
			try{
				foreach($page_id as $b)
				{
					$model = Mage::getModel('pagebuilder/page')->load($b);
					$identifier = $model->getData('page_code');
					$model_cms = Mage::getModel('cms/page')->load($identifier, 'identifier');
					$model->delete();
					$model_cms->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('adminhtml')->__(
						'Total of %d record(s) were successfully deleted', count($page_id)
					)
				);
			}
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('_current'=>true)));
				return;
			}
			catch(Exception $e)
			{
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('pagebuilder')->__('An error occurred while trying to delete the items.'));
				$this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('_current'=>true)));
				return;
			}
		}
		$this->getResponse()->setRedirect($this->getUrl('*/*/', array('_current'=>true, 'id'=>null)));
	}

	public function massStatusAction(){
		if($page_id = $this->getRequest()->getParam('page_id')) {
			try {
				foreach ($page_id as $b) {
					$model = Mage::getModel('pagebuilder/page')
						->load($b)
						->setStatus($this->getRequest()->getParam('status'))
						->setIsMassupdate(true);
					$identifier = $model->getData('page_code');
					$model_cms = Mage::getModel('cms/page')
						->load($identifier, 'identifier')
						->setIsActive($this->getRequest()->getParam('status'))
						->setIsMassupdate(true);
					$model->save();
					$model_cms->save();
				}
				$this->_getSession()->addSuccess(
					$this->__('Total of %d record(s) were successfully updated change status', count($page_id))
				);
			}
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->getResponse()->setRedirect($this->getUrl('*/*/index', array('_current'=>true)));
				return;
			}
			catch(Exception $e)
			{
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('pagebuilder')->__('An error occurred while trying to change status the items.'));
				$this->getResponse()->setRedirect($this->getUrl('*/*/index', array('_current'=>true)));
				return;
			}
		}
		$this->getResponse()->setRedirect($this->getUrl('*/*/', array('_current'=>true, 'id'=>null)));
	}

	public function exportCsvAction(){
		die('exportCsv');
	}

	public function exportXmlAction(){
		die('exportCsv');
	}

	/**
	 * Wisywyg widget plugin main page
	 */
	public function loadIndexAction() {
		$this->loadLayout('overlay_popup');
        $block = $this->getLayout()->createBlock('pagebuilder/adminhtml_pagebuilder_addwidget', 'adminhtml_pagebuilder_addwidget');
        $this->_addContent($block);
		$this->renderLayout();
	}

	/**
	 * Ajax responder for loading plugin options form
	 */
	public function loadOptionsAction()
	{
		try {
			$this->loadLayout('empty');
			if ($paramsJson = $this->getRequest()->getParam('widget')) {
				$request = Mage::helper('core')->jsonDecode($paramsJson);
				if (is_array($request)) {
					$optionsBlock = $this->getLayout()->getBlock('pagebuilder.wysiwyg_widget.options');
					if (isset($request['widget_type'])) {
						$optionsBlock->setWidgetType($request['widget_type']);
					}
					if (isset($request['values'])) {
						$optionsBlock->setWidgetValues($request['values']);
					}
				}
				$this->renderLayout();
			}
		} catch (Mage_Core_Exception $e) {
			$result = array('error' => true, 'message' => $e->getMessage());
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		}
	}

	/**
	 * Format widget pseudo-code for inserting into wysiwyg editor
	 */
	public function buildWidgetAction()
	{
		$type = $this->getRequest()->getPost('widget_type');
		$params = $this->getRequest()->getPost('parameters', array());
		$asIs = $this->getRequest()->getPost('as_is');
		$html = Mage::getSingleton('widget/widget')->getWidgetDeclaration($type, $params, $asIs);
		$this->getResponse()->setBody($html);
	}
}