<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Fieldset element renderer
 *
 * @category   Mage
 * @package    Magestore_Widgetcustom
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magestore_Widgetcustom_Block_Adminhtml_Custom_Form_Renderer_Fieldset_Element extends Mage_Adminhtml_Block_Template
    implements Varien_Data_Form_Element_Renderer_Interface
{
    protected $_element;
    protected $_form;

    protected function _construct()
    {
        $this->setTemplate('widgetcustom/widget/page/edit/form/renderer/content.phtml');
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
            'onclick'   => "widgetCustomTools.openDialog('" . $this->getWidgetUrl() . "widget_target_id/"
                . $this->getHtmlId() . "')",
            'class'     => 'add-widget plugin',
            'style'     => $visible ? '' : 'display:none',
        ));

        return $buttonsHtml;
    }

    public function getWidgetUrl() {
        return Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/widgetcustom_widget/loadIndex');
    }
}