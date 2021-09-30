<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Widget_BrowseBreadcrumbController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->view->formValues = $values = $request->getParams();
        //GET CATEGORY TABLE
        $this->view->category_id = $this->view->subcategory_id = 0;
        $this->view->category_name = $this->view->subcategory_name = '';
        $category_id = $request->getParam('category_id', null);
        $sitecrowdfundingBrowseBreadcrumb = Zend_Registry::isRegistered('sitecrowdfundingBrowseBreadcrumb') ? Zend_Registry::get('sitecrowdfundingBrowseBreadcrumb') : null;
        $checkCategory = false;
        if (!empty($category_id)) {
            $this->view->category_id = $category_id;
            $this->view->category_name = Engine_Api::_()->getItem('sitecrowdfunding_category', $category_id)->category_name;
            $subcategory_id = $request->getParam('subcategory_id', null);
            if (!empty($subcategory_id)) {
                $this->view->subcategory_id = $subcategory_id;
                $this->view->subcategory_name = Engine_Api::_()->getItem('sitecrowdfunding_category', $subcategory_id)->category_name;
            }
            $checkCategory = true;
        }
        if (empty($sitecrowdfundingBrowseBreadcrumb))
            return $this->setNoRender();
        if (((isset($values['tag']) && !empty($values['tag']) && isset($values['tag_id']) && !empty($values['tag_id'])))) {
            $current_url = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
            $current_url = explode("?", $current_url);
            if (isset($current_url[1])) {
                $current_url1 = explode("&", $current_url[1]);
                foreach ($current_url1 as $key => $value) {
                    if (strstr($value, "tag=") || strstr($value, "tag_id=")) {
                        unset($current_url1[$key]);
                    }
                }
                $this->view->current_url2 = implode("&", $current_url1);
            }
            $checkCategory = true;
        }

        if (!$checkCategory)
            return $this->setNoRender();
    }

}
