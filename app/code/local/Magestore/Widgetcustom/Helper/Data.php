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
 * Widget custom Helper
 *
 * @category    Magestore
 * @package     Magestore_Widgetcustom
 * @author      Magestore Developer
 */
class Magestore_Widgetcustom_Helper_Data extends Mage_Core_Helper_Abstract {
    /**
     * Truncate string by $length
     * @param string $string
     * @param int $length
     * @param string $etc
     * @return string
     */
    public function truncate($string, $length, $etc = '...')
    {
        return defined('MB_OVERLOAD_STRING')
            ? $this->_mb_truncate($string, $length, $etc)
            : $this->_truncate($string, $length, $etc);
    }

    /**
     * Truncate string if it's size over $length
     * @param string $string
     * @param int $length
     * @param string $etc
     * @return string
     */
    private function _truncate($string, $length, $etc = '...')
    {
        if ($length > 0 && $length < strlen($string)) {
            $buffer = '';
            $buffer_length = 0;
            $parts = preg_split('/(<[^>]*>)/', $string, -1, PREG_SPLIT_DELIM_CAPTURE);
            $self_closing_tag = split(',', 'area,base,basefont,br,col,frame,hr,img,input,isindex,link,meta,param,embed');
            $open = array();

            foreach ($parts as $i => $s) {
                if (false === strpos($s, '<')) {
                    $s_length = strlen($s);
                    if ($buffer_length + $s_length < $length) {
                        $buffer .= $s;
                        $buffer_length += $s_length;
                    } else if ($buffer_length + $s_length == $length) {
                        if (!empty($etc)) {
                            $buffer .= ($s[$s_length - 1] == ' ') ? $etc : " $etc";
                        }
                        break;
                    } else {
                        $words = preg_split('/([^\s]*)/', $s, -1, PREG_SPLIT_DELIM_CAPTURE);
                        $space_end = false;
                        foreach ($words as $w) {
                            if ($w_length = strlen($w)) {
                                if ($buffer_length + $w_length < $length) {
                                    $buffer .= $w;
                                    $buffer_length += $w_length;
                                    $space_end = (trim($w) == '');
                                } else {
                                    if (!empty($etc)) {
                                        $more = $space_end ? $etc : " $etc";
                                        $buffer .= $more;
                                        $buffer_length += strlen($more);
                                    }
                                    break;
                                }
                            }
                        }
                        break;
                    }
                } else {
                    preg_match('/^<([\/]?\s?)([a-zA-Z0-9]+)\s?[^>]*>$/', $s, $m);
                    //$tagclose = isset($m[1]) && trim($m[1])=='/';
                    if (empty($m[1]) && isset($m[2]) && !in_array($m[2], $self_closing_tag)) {
                        array_push($open, $m[2]);
                    } else if (trim($m[1]) == '/') {
                        $tag = array_pop($open);
                        if ($tag != $m[2]) {
                            // uncomment to to check invalid html string.
                        }
                    }
                    $buffer .= $s;
                }
            }
            // close tag openned.
            while (count($open) > 0) {
                $tag = array_pop($open);
                $buffer .= "</$tag>";
            }
            return $buffer;
        }
        return $string;
    }

    /**
     * Truncate mutibyte string if it's size over $length
     * @param string $string
     * @param int $length
     * @param string $etc
     * @return string
     */
    private function _mb_truncate($string, $length, $etc = '...')
    {
        $encoding = mb_detect_encoding($string);
        if ($length > 0 && $length < mb_strlen($string, $encoding)) {
            $buffer = '';
            $buffer_length = 0;
            $parts = preg_split('/(<[^>]*>)/', $string, -1, PREG_SPLIT_DELIM_CAPTURE);
            $self_closing_tag = explode(',', 'area,base,basefont,br,col,frame,hr,img,input,isindex,link,meta,param,embed');
            $open = array();

            foreach ($parts as $i => $s) {
                if (false === mb_strpos($s, '<')) {
                    $s_length = mb_strlen($s, $encoding);
                    if ($buffer_length + $s_length < $length) {
                        $buffer .= $s;
                        $buffer_length += $s_length;
                    } else if ($buffer_length + $s_length == $length) {
                        if (!empty($etc)) {
                            $buffer .= ($s[$s_length - 1] == ' ') ? $etc : " $etc";
                        }
                        break;
                    } else {
                        $words = preg_split('/([^\s]*)/', $s, -1, PREG_SPLIT_DELIM_CAPTURE);
                        $space_end = false;
                        foreach ($words as $w) {
                            if ($w_length = mb_strlen($w, $encoding)) {
                                if ($buffer_length + $w_length < $length) {
                                    $buffer .= $w;
                                    $buffer_length += $w_length;
                                    $space_end = (trim($w) == '');
                                } else {
                                    if (!empty($etc)) {
                                        $more = $space_end ? $etc : " $etc";
                                        $buffer .= $more;
                                        $buffer_length += mb_strlen($more);
                                    }
                                    break;
                                }
                            }
                        }
                        break;
                    }
                } else {
                    preg_match('/^<([\/]?\s?)([a-zA-Z0-9]+)\s?[^>]*>$/', $s, $m);
                    //$tagclose = isset($m[1]) && trim($m[1])=='/';
                    if (empty($m[1]) && isset($m[2]) && !in_array($m[2], $self_closing_tag)) {
                        array_push($open, $m[2]);
                    } else if (trim($m[1]) == '/') {
                        $tag = array_pop($open);
                        if ($tag != $m[2]) {
                            // uncomment to to check invalid html string.
                        }
                    }
                    $buffer .= $s;
                }
            }
            // close tag openned.
            while (count($open) > 0) {
                $tag = array_pop($open);
                $buffer .= "</$tag>";
            }
            return $buffer;
        }
        return $string;
    }
}