<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 9/5/2016
 * Time: 2:11 PM
 */
class Yndynamicform_View_Helper_FieldAgreement extends Fields_View_Helper_FieldAbstract
{
    public function fieldAgreement($subject, $field, $value)
    {
        return nl2br($field -> config['content']);
    }
}