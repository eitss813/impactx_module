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
class Sitepage_Widget_PageProfileMetricsController extends Seaocore_Content_Widget_Abstract
{

    public function indexAction()
    {

        $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
        $this->view->page_id = $page_id = $sitepage->getIdentity();

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->view->params = $params = $request->getParams();

        // if no page_id, then dont render anything
        if (!$page_id) {
            return $this->setNoRender();
        }

        $this->view->ajaxUrlPath = 'widget/index/mod/sitepage/name/page-profile-metrics';

        $metric_page_no = $params['metric_page_no'];

        if(!$metric_page_no || $metric_page_no==null) {
            $metric_page_no = 1;
        }

        $this->view->metrics = $metrics = Engine_Api::_()->getDbtable('metrics', 'sitepage')->getMetricsDataByOrganisationIdPaginator($page_id,$metric_page_no);
        $this->view->metric_page_no = $metric_page_no;

        if(count($metrics) <= 0){
            return $this->setNoRender();
        }

    }
}
