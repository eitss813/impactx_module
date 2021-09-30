<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/26/2016
 * Time: 7:42 PM
 */
class Sitepage_View_Helper_FieldHTMLTextEditor extends Fields_View_Helper_FieldAbstract
{
    public function fieldHTMLTextEditor($subject, $field, $value)
    {
         return $field->config['content'];
    }
}