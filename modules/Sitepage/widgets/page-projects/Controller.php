<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_PageProjectsController extends Engine_Content_Widget_Abstract
{

    public function indexAction()
    {

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();
        $pages_ids = array();

        if (Engine_Api::_()->core()->hasSubject('sitepage_page')) {
            $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
            $this->view->page_id = $page_id = $sitepage->page_id;
        } else {
            $page_id = $this->_getParam('page_id', null);
            if ($page_id) {
                $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
                $this->view->page_id = $page_id = $sitepage->page_id;
            }
        }

        // if no page_id, then dont render anything
        if (!$page_id) {
            return $this->setNoRender();
        }

        $this->view->widgetPath = 'widget/index/mod/sitepage/name/page-projects';
        $this->view->controllerName = $params['controller'];
        $this->view->actionName = $params['action'];

        // get partner-page-ids
        // $allPartnerPageIds = Engine_Api::_()->getDbtable('partners', 'sitepage')->getJoinedAndAddedPartnerPages($page_id);
        // dont show sister organisation initaitves
        $allPartnerPageIds = [];


        // if partner-page-ids is empty, then get only their projects
        if (count($allPartnerPageIds) <= 0) {
            $this->view->isPartnersPresentYN = false;
            $allPartnerPageIds[] = $page_id;
            $this->view->initiatives = $initiatives = Engine_Api::_()->getDbtable('initiatives', 'sitepage')->getAllInitiativesByPageId($page_id);
            $pages_ids[] = $page_id;
        } else {

            $this->view->isPartnersPresentYN = true;
            $allPartnerPageIds[] = $page_id;
            if (isset($params['link']) && !empty($params['link'])) {
                $currentLink = $params['link'];
            } else {
                $currentLink = $params['link'] = 'all';
            }

            // get initiatives
            if ($currentLink !== 'all') {
                $this->view->initiatives = $initiatives = Engine_Api::_()->getDbtable('initiatives', 'sitepage')->getAllInitiativesByPageId($currentLink);
                $pages_ids[] = $currentLink;
            } else {
                $this->view->initiatives = $initiatives = Engine_Api::_()->getDbtable('initiatives', 'sitepage')->getAllInitiativesByPageIds($allPartnerPageIds);
                $pages_ids = array_merge($pages_ids,$allPartnerPageIds);
                $pages_ids[] = $page_id;
            }

        }

        $this->view->params = $params;
        $this->view->pages_ids = $pages_ids;

        if(isset($params['is_paginated'])){
            $this->view->is_paginated = $params['is_paginated'];
        }else{
            $this->view->is_paginated = false;
        }

        if(isset($params['initiative_id'])){
            $this->view->paginated_initiative_id = $params['initiative_id'];
        }else{
            $this->view->paginated_initiative_id = null;
        }

        if(isset($params['page_no'])){
            $this->view->paginated_page_no = $params['page_no'];
        }else{
            $this->view->paginated_page_no = 1;
        }

        $this->view->allPartnerPages = $allPartnerPages = Engine_Api::_()->getDbtable('pages', 'sitepage')->getPageDetailsWithProjectsCountForPageIds($allPartnerPageIds);
        $pages_ids_noproject=array(); $flag=0;
        foreach ($pages_ids as $p_id) {
            if(findId($allPartnerPages,$p_id)) {
                continue;
            }
            else {
                array_push($pages_ids_noproject,$p_id);
            }
        }
        $pages_ids_noproject= array_unique($pages_ids_noproject);

        $this->view->pages_ids_noproject = $pages_ids_noproject;
        // get all tabs count
        $allTabCount = 0;
        foreach ($allPartnerPages as $item){
            $allTabCount = $allTabCount + $item->projects_count;
        }
        $this->view->allTabCount = $allTabCount;

    }
}
function findId($allPartnerPages,$id) {
    foreach ($allPartnerPages as $allPartnerPages_id){
        if($allPartnerPages_id->page_id == $id) {
            return true;
        }
    }
}
?>