<?php

class Sm_Pagebuilder_Block_Adminhtml_Pagebuilder_Addwidget_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Form with widget to select
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'    => $this->helper('pagebuilder')->__('Widget')
        ));

        $select = $fieldset->addField('select_widget_type', 'select', array(
            'name'                  => 'widget_type',
            'style'                 => 'clear:both;display:block',
            'required'              => true,
            'options'               => $this->_getWidgetSelectOptions(),
            'after_element_html'    => $this->_getWidgetSelectAfterHtml(),
        ));

        $widgetLists = $fieldset->addField('widget_list_content', 'text', array(
            'label'                 => $this->helper('pagebuilder')->__('Widget List')
        ));

        // Setting custom renderer for content field to remove label column
        $renderer = $this->getLayout()->createBlock(
            'Sm_Pagebuilder_Block_Adminhtml_Pagebuilder_Form_Renderer_Fieldset_Addwidget'
        );
        $widgetLists->setRenderer($renderer);

        $form->setUseContainer(true);
        $form->setId('widget_options_form');
        $form->setMethod('post');
        $form->setAction($this->getUrl('*/*/buildWidget'));
        $this->setForm($form);
    }

    /**
     * Prepare options for widgets HTML select
     *
     * @return array
     */
    protected function _getWidgetSelectOptions()
    {
        foreach ($this->_getAvailableWidgets(true) as $data) {
            $options[$data['type']] = $data['name'];
        }
        return $options;
    }

    /**
     * Prepare widgets select after element HTML
     *
     * @return string
     */
    protected function _getWidgetSelectAfterHtml()
    {
        $html = '<p class="nm"><small></small></p>';
        $i = 0;
        foreach ($this->_getAvailableWidgets(true) as $data) {
            $html .= sprintf('<div id="widget-description-%s" class="no-display">%s</div>', $i, $data['description']);
            $i++;
        }
        return $html;
    }

    /**
     * Return array of available widgets based on configuration
     *
     * @return array
     */
    protected function _getAvailableWidgets($withEmptyElement = false)
    {
        if (!$this->hasData('available_widgets')) {
            $result = array();
            $allWidgets = Mage::getModel('widget/widget')->getWidgetsArray();
            $skipped = $this->_getSkippedWidgets();
            foreach ($allWidgets as $widget) {
                if (is_array($skipped) && in_array($widget['type'], $skipped)) {
                    continue;
                }
                $result[] = $widget;
            }
            if ($withEmptyElement) {
                array_unshift($result, array(
                    'type'        => '',
                    'name'        => $this->helper('adminhtml')->__('-- Please Select --'),
                    'description' => '',
                ));
            }
            $this->setData('available_widgets', $result);
        }

        return $this->_getData('available_widgets');
    }

    /**
     * Return array of widgets disabled for selection
     *
     * @return array
     */
    protected function _getSkippedWidgets()
    {
        return Mage::registry('skip_widgets');
    }
}
