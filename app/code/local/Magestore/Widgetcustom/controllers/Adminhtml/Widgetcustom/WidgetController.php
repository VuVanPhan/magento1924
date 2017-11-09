<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_SolutionPartner
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Widget custom Adminhtml Controller
 *
 * @category    Magestore
 * @package     Magestore_Widgetcustom
 * @author      Magestore Developer
 */
class Magestore_Widgetcustom_Adminhtml_Widgetcustom_WidgetController extends Mage_Adminhtml_Controller_Action {
    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Widgetcustom_Adminhtml_Widgetcustom_WidgetController
     */
    public function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu("widgetcustom/widget")
            ->_addBreadcrumb(
                Mage::helper("adminhtml")->__("Widget Manager"),
                Mage::helper("adminhtml")->__("Widget Manager")
            );
        $this->getLayout()->getBlock("head")->setTitle($this->__("Widget Manager"));
        return $this;
    }

    /**
     * index action
     * */
    public function indexAction() {
        $this->_initAction()
            ->renderLayout();
    }

    public function girdAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody( $this->getLayout()->createBlock( 'widgetcustom/adminhtml_widget_grid' )->toHtml() );
    }

    /**
     * view and edit item action
     */
    public function editAction()
    {
        $widgetId   = $this->getRequest()->getParam('id');
        $model      = Mage::getModel('widgetcustom/widget')->load($widgetId);

        if ($model->getId() || $widgetId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('widget_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('adminhtml/widgetcustom_widget');

            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Widget Manager'),
                Mage::helper('adminhtml')->__('Wisget Manager')
            );
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Widget News'),
                Mage::helper('adminhtml')->__('Widget News')
            );

            if ($model->getId())
                $this->_title($model->getName());
            else
                $this->_title(Mage::helper('adminhtml')->__('Add New Widget'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addLeft($this->getLayout()->createBlock('widgetcustom/adminhtml_widget_edit_tabs'))
                ->_addContent($this->getLayout()->createBlock('widgetcustom/adminhtml_widget_edit'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('widgetcustom')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction() {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            //init model and set data
            $model = Mage::getModel('widgetcustom/widget');

            if ($id = $this->getRequest()->getParam('widget_id')) {
                $model->load($id);
            }

            echo "<pre>";
            var_dump($data);
            die('dfdfd');
            $model->setData($data);
        }
    }

    /**
     * delete item action
     */
    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('widgetcustom/widget');
                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Item was successfully deleted')
                );
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * mass delete item(s) action
     */
    public function massDeleteAction()
    {
        $widgetIds = $this->getRequest()->getParam('widget');
        if (!is_array($widgetIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select widget(s)'));
        } else {
            try {
                foreach ($widgetIds as $widgetId) {
                    $solutionpartner = Mage::getModel('widgetcustom/widget')->load($widgetId);
                    $solutionpartner->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted',
                        count($widgetIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction()
    {
        $fileName   = 'widget.csv';
        $content    = $this->getLayout()
            ->createBlock('widgetcustom/adminhtml_widget_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName   = 'widget.xml';
        $content    = $this->getLayout()
            ->createBlock('widgetcustom/adminhtml_widget_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('widget');
    }

    /*popup index widget custom*/
    /**
     * load index action
     * */
    public function loadIndexAction() {
        $this->loadLayout("overlay_popup");
        $block = $this->getLayout()->createBlock("widgetcustom/adminhtml_widget_addwidget", "adminhtml_widget_addwidget");
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
                    $optionsBlock = $this->getLayout()->getBlock('widgetcustom.wysiwyg_widget.options');
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