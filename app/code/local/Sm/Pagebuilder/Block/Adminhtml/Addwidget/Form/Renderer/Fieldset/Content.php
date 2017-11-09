<?php
/**
 * Created by PhpStorm.
 * User: Vu Van Phan
 * Date: 06-11-2015
 * Time: 10:57
 */
class Sm_Pagebuilder_Block_Adminhtml_Addwidget_Form_Renderer_Fieldset_Content extends Mage_Adminhtml_Block_Template
    implements Varien_Data_Form_Element_Renderer_Interface
{
    protected $_element;

    protected function _construct()
    {
        $this->setTemplate('sm/pagebuilder/widget/form/renderer/fieldset/content.phtml');
    }

    public function getElement()
    {
        return $this->_element;
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this->toHtml();
    }

    public function getAddWidgetUrl() {
        return $this->getUrl('adminhtml/pagebuilder_pagebuilder/indexWidget');
    }

    /**
     * Return custom button HTML
     *
     * @param array $data Button params
     * @return string
     */
    protected function _getButtonHtml($data)
    {
        $html = '<button type="button"';
        $html.= ' class="scalable '.(isset($data['class']) ? $data['class'] : '').'"';
        $html.= isset($data['onclick']) ? ' onclick="'.$data['onclick'].'"' : '';
        $html.= isset($data['style']) ? ' style="'.$data['style'].'"' : '';
        $html.= isset($data['id']) ? ' id="'.$data['id'].'"' : '';
        $html.= '>';
        $html.= isset($data['title']) ? '<span><span><span>'.$data['title'].'</span></span></span>' : '';
        $html.= '</button>';

        return $html;
    }

    /**
     * Translate string using defined helper
     *
     * @param string $string String to be translated
     * @return string
     */
    public function translate($string)
    {
        $translator = $this->getConfig('translator');
        if (method_exists($translator, '__')) {
            $result = $translator->__($string);
            if (is_string($result)) {
                return $result;
            }
        }

        return $string;
    }

    /**
     * Editor config retriever
     *
     * @param string $key Config var key
     * @return mixed
     */
    public function getConfig($key = null)
    {
        if ( !($this->_getData('config') instanceof Varien_Object) ) {
            $config = new Varien_Object();
            $this->setConfig($config);
        }
        if ($key !== null) {
            return $this->_getData('config')->getData($key);
        }
        return $this->_getData('config');
    }

    public function getHtmlId()
    {
        return $this->getElement()->getId();
    }

    public function getWidgetButton($visible = true) {
        $buttonsHtml = '';

        $buttonsHtml .= $this->_getButtonHtml(array(
            'title'     => $this->translate('Insert Widget...'),
            'onclick'   => "_PdmWidgetTools.openDialog('" . $this->getWidgetUrl() . "widget_target_id/"
                . $this->getHtmlId() . "')",
            'class'     => 'add-widget plugin',
            'style'     => $visible ? '' : 'display:none',
        ));

        return $buttonsHtml;
    }

    public function getWidgetUrl() {
        return Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/pagebuilder_pagebuilder/indexWidget');
    }
}