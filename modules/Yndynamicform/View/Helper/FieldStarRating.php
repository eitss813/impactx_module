<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/26/2016
 * Time: 8:10 PM
 */
class Yndynamicform_View_Helper_FieldStarRating extends Fields_View_Helper_FieldAbstract
{
    public function fieldStarRating($subject, $field, $value)
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        if ($request->getActionName() != 'view' && $request->getModuleName() == 'yndynamicform') {
            return $value->value;
        }
        $config = $field->config;
        $html = "<div style='". $field -> style ."' class='yndform_rating'>";
        for ($i = 1 ; $i <= 5; $i++)
        {
            if ($i <= $value -> value) {
                $html .= "<span style='color: " . $config['selected_star_color'] . "' id='rate_$i' class='ynicon yn-star rating'></span>";
            } else {
                $html .= "<span style='color: " . $config['unselected_star_color'] . "' id='rate_$i' class='ynicon yn-star'></span>";
            }
        }
        $html .= '</div>';
        return $html;
    }
}