<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/31/2016
 * Time: 10:20 AM
 */
class Sitepage_View_Helper_FieldSectionBreak extends Fields_View_Helper_FieldAbstract
{
    public function fieldSectionBreak($subject, $field, $value)
    {
        return $field -> description ? $field -> description : ' ';
    }
}