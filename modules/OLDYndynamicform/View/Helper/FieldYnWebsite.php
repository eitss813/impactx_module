<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 9/9/2016
 * Time: 5:59 PM
 */
class Yndynamicform_View_Helper_FieldYnWebsite extends Fields_View_Helper_FieldAbstract
{
    public function fieldYnWebsite($subject, $field, $value, $params = array())
    {
        $str = $value->value;

        if( strpos($str, 'http://') === false && strpos($str, 'https://')) {
            $str = 'http://' . $str;
        }

        if (!isset($params['target'])) {
            $params['target'] = '_blank';
        }

        return $this->view->htmlLink($str, $str, $params);
    }
}