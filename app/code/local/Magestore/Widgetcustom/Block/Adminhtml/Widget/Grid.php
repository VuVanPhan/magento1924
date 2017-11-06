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
 * Widget custom Grid Block
 *
 * @category    Magestore
 * @package     Magestore_Widgetcustom
 * @author      Magestore Developer
 */
class Magestore_Widgetcustom_Block_Adminhtml_Widget_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    public function __construct()
    {
        parent::__construct();
        $this->setId("widgetCustomGrid");
        $this->setDefaultSort('widget_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_Widgetcustom_Block_Adminhtml_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('widgetcustom/widget')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     *
     * @return Magestore_Widgetcustom_Block_Adminhtml_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('widget_id', array(
            'header'    => Mage::helper('widgetcustom')->__('ID'),
            'align'     => 'right',
            'width'     => '50px',
            'index'     => 'widget_id',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('widgetcustom')->__('Name'),
            'align'     => 'right',
            'index'     => 'name',
        ));

        $this->addColumn('content', array(
            'header'    => Mage::helper('widgetcustom')->__('Content'),
            'align'     => 'right',
            'index'     => 'content',
        ));

        $this->addColumn('action',
            array(
                'header'    =>    Mage::helper('widgetcustom')->__('Action'),
                'width'     => '50',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('widgetcustom')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
            )
        );

        $this->addExportType('*/*/exportCsv', Mage::helper('widgetcustom')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('widgetcustom')->__('XML'));
        return parent::_prepareColumns();
    }

    /**
     * prepare mass action for this grid
     *
     * @return Magestore_Widgetcustom_Block_Adminhtml_Widget_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('widget_id');
        $this->getMassactionBlock()->setFormFieldName('widget');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'     => Mage::helper('widgetcustom')->__('Delete'),
            'url'       => $this->getUrl('*/*/massDelete'),
            'confirm'   => Mage::helper('widgetcustom')->__('Are you sure?')
        ));
        return $this;
    }

    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    public function getGridUrl()
    {
        return $this->getData('grid_url')
            ? $this->getData('grid_url')
            : $this->getUrl('*/*/gird', array('_current'=>true,'id'=>$this->getRequest()->getParam('id')));
    }
}