<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Widget_ReviewBrowseSearchController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        $searchForm = $this->view->searchForm = new Sitemember_Form_Review_Search();
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->view->requestParams = $requestParams = $request->getParams();

        if (isset($requestParams['page'])) {
            unset($requestParams['page']);
        }

        $searchForm
                ->setMethod('get')
                ->populate($requestParams)
        ;

        $this->view->searchField = 'search';
        $this->view->widgetParams = $widgetParams = $this->_getAllParams();
    }

}