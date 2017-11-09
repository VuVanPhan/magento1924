<?php
class Magestore_Widgetcustom_Block_Adminhtml_Widget_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('widgetCustom_tabs');
        $this->setDestElementId('edit_form');
        if ( $tab = $this->getRequest()->getParam( 'activeTab' ) )
        {
            $this->_activeTab = $tab;
        }
        else
        {
            $this->_activeTab = 'form_design';
        }
        $this->setTitle("<i class='fa fa-qrcode'></i>".Mage::helper('widgetcustom')->__('Widget Custom'));
    }

    protected function _beforeToHtml()
    {
        $_label = 'Page';
        $this->addTab('form_design', array(
            'label'     => "<i class='fa fa-object-group'></i>".Mage::helper('widgetcustom')->__('Design '.$_label),
            'title'     => Mage::helper('widgetcustom')->__('Design Page'),
            'content'   => $this->_getTabHtml('design'),
        ));
        parent::_beforeToHtml();
    }

    private function _getTabHtml($tab)
    {
        return $this->getLayout()->createBlock( 'widgetcustom/adminhtml_widget_edit_tab_' . $tab )->toHtml();
    }
}