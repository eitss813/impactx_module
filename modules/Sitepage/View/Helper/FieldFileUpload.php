<?php

/**
 * Created by PhpStorm.
 * User: NguyenChiThanh_51203
 * Date: 8/27/2016
 * Time: 1:19 AM
 */
class Sitepage_View_Helper_FieldFileUpload extends Fields_View_Helper_FieldAbstract
{
    public function fieldFileUpload($subject, $field, $value)
    {
        if (!($value instanceof Fields_Model_Value)) return null;
        $filesObject = json_decode(html_entity_decode($value->getValue()));
        $file_ids = $filesObject -> file_ids;
        $file_names = $filesObject -> name;
        $file_sizes = $filesObject -> size;
        $html = '';
        $url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'download'));
        foreach ($file_ids as $key => $id)
        {
            $html .= '<div class="file-element"><a href="'. $url.'?file_id='. $id .'"><span class="ynicon yn-paperclip-o"></span>'. $file_names[$key] .'<span>('. (number_format($file_sizes[$key] / 1024)) .'Kb)</span></a></div>';
        }
        return $html;
    }
}