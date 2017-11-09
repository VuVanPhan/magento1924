<?php
/**
 * Created by PhpStorm.
 * User: Vu Van Phan
 * Date: 12-10-2015
 * Time: 14:00
 */
class Sm_Pagebuilder_Block_Adminhtml_Pagebuilder_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct(){
		parent::__construct();
		$this->setId('pagebuilder_list_grid');
		$this->setDefaultSort('page_id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
	}

	public function _getCollectionBlock(){
		return "pagebuilder/page";
	}

	public function _prepareCollection(){
		$collection = Mage::getModel($this->_getCollectionBlock())->getCollection();
		$collection->setFirstStoreFlag(true);
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$builder_type_label = 'Page';
		$this->addColumn('page_id', array(
			'header'    => Mage::helper('pagebuilder')->__('ID'),
			'align'     => 'right',
			'width'     => '80px',
			'index'     => 'page_id',
		));

		$this->addColumn('title', array(
			'header'    => Mage::helper('pagebuilder')->__('Title'),
			'align'     => 'left',
			'index'     => 'title',
		));

		$this->addColumn('page_code', array(
			'header'    => Mage::helper('pagebuilder')->__($builder_type_label.' Code'),
			'align'     => 'left',
			'index'     => 'page_code',
		));

		$this->addColumn('page_shortcode', array(
			'header'    => Mage::helper('pagebuilder')->__($builder_type_label.' ShortCode'),
			'align'     => 'left',
			'index'     => 'page_shortcode',
		));

		if (!Mage::app()->isSingleStoreMode()) {
			$this->addColumn('store_id', array(
				'header'        => Mage::helper('pagebuilder')->__('Store View'),
				'width'         => '170px',
				'index'         => 'store_id',
				'type'          => 'store',
				'store_all'     => true,
				'store_view'    => true,
				'sortable'      => false,
				'filter_condition_callback' => array($this, '_filterStoreCondition'),
			));
		}

		$this->addColumn('status', array(
			'header'    => Mage::helper('pagebuilder')->__('Status'),
			'align'     => 'left',
			'width'     => '100px',
			'index'     => 'status',
			'type'      => 'options',
			'options'   => Mage::getSingleton('pagebuilder/system_config_source_status')->getOptionArray(),
		));

		$this->addColumn('action', array(
			'header'    => Mage::helper('pagebuilder')->__('Action'),
			'width'     => '100px',
			'align'     => 'center',
			'type'      => 'action',
			'getter'    => 'getId',
			'actions'   => array(
				array(
					'caption'   => Mage::helper('pagebuilder')->__('Preview'),
					'target'    => 'blank',
					'url'       => array('base' => 'pagebuilder/index/preview'),
					'field'     => 'id'
				)
			),
			'filter'    => false,
			'sortable'  => false,
			'index'     => 'stores',
			'is_system' => true,
		));

		$this->addExportType('*/*/exportCsv', Mage::helper('pagebuilder')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('pagebuilder')->__('XML'));

		parent::_prepareColumns();
	}

	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('page_id');
		$this->getMassactionBlock()->setFormFieldName('page_id');
		$this->getMassactionBlock()->setUseSelectAll(true);

		$this->getMassactionBlock()->addItem('delete', array(
			'label'     => Mage::helper('pagebuilder')->__('Delete'),
			'url'       => $this->getUrl('*/*/massDelete'),
			'confirm'   => Mage::helper('pagebuilder')->__('Are you sure you want to do this?')
		));

//		$this->getMassactionBlock()->addItem('duplicate', array(
//			'label'     => Mage::helper('pagebuilder')->__('Duplicate'),
//			'url'       => $this->getUrl('*/*/massDuplicate'),
//			'confirm'   => Mage::helper('pagebuilder')->__('Are you sure you want to do this?')
//		));

		$this->getMassactionBlock()->addItem('status', array(
			'label'         => Mage::helper('pagebuilder')->__('Change status'),
			'url'           => $this->getUrl('*/*/massStatus', array('_current' => true)),
			'additional'    => array(
				'visibility'    => array(
					'name'      => 'status',
					'type'      => 'select',
					'class'     => 'required-entry',
					'label'     => Mage::helper('pagebuilder')->__('Status'),
					'values'    => array(
						1   => Mage::helper('pagebuilder')->__('Enabled'),
						0   => Mage::helper('pagebuilder')->__('Disabled')
					)
				)
			)
		));

		return $this;
	}

	protected function _afterLoadCollection()
	{
		$this->getCollection()->walk('afterLoad');
		parent::_afterLoadCollection();
	}

	protected function _filterStoreCondition($collection, $column)
	{
		if (!$value = $column->getFilter()->getValue()) {
			return;
		}

		$this->getCollection()->addStoreFilter($value);
	}

	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
}