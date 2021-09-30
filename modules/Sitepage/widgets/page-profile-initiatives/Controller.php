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
class Sitepage_Widget_PageProfileInitiativesController extends Seaocore_Content_Widget_Abstract
{

    public function indexAction()
    {

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();
        $pages_ids = array();

        $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
        $this->view->page_id = $page_id = $sitepage->getIdentity();

        // if no page_id, then dont render anything
        if (!$page_id) {
            return $this->setNoRender();
        }

        $this->view->widgetPath = 'widget/index/mod/sitepage/name/page-profile-initiatives';
        $this->view->controllerName = $params['controller'];
        $this->view->actionName = $params['action'];

        // get partner-page-ids
        //$allPartnerPageIds = Engine_Api::_()->getDbtable('partners', 'sitepage')->getJoinedAndAddedPartnerPages($page_id);
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
            if (isset($params['initiatives_link']) && !empty($params['initiatives_link'])) {
                $currentLink = $params['initiatives_link'];
            } else {
                $currentLink = $params['initiatives_link'] = 'all';
            }

            // get initiatives
            if ($currentLink !== 'all') {
                $this->view->initiatives = $initiatives = Engine_Api::_()->getDbtable('initiatives', 'sitepage')->getAllInitiativesByPageId($currentLink);
                $pages_ids[] = $currentLink;
            } else {
                $this->view->initiatives = $initiatives = Engine_Api::_()->getDbtable('initiatives', 'sitepage')->getAllInitiativesByPageIds($allPartnerPageIds);
                $pages_ids = array_merge($pages_ids, $allPartnerPageIds);
                $pages_ids[] = $page_id;
            }

        }

        $this->view->params = $params;
        $this->view->pages_ids = $pages_ids;

        $this->view->allPartnerPages = $allPartnerPages = Engine_Api::_()->getDbtable('pages', 'sitepage')->getPageDetailsWithInitiativesCountForPageIds($allPartnerPageIds);

        // get all tabs count
        $allTabCount = 0;
        foreach ($allPartnerPages as $item) {
            $allTabCount = $allTabCount + $item->initiatives_count;
        }
        $this->view->allTabCount = $allTabCount;

        if(count($initiatives) <= 0){
            return $this->setNoRender();
        }

    }
}
