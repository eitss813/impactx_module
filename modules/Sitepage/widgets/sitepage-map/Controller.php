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
class Sitepage_Widget_SitepageMapController extends Seaocore_Content_Widget_Abstract
{

    public function indexAction()
    {

        $this->view->followerList = $followerList = array();
        $this->view->projectsList = $projectsList = array();
        $this->view->membersList = $membersList = array();

        //GET SUBJECT
        $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

        $value['id'] = $sitepage->getIdentity();
        $this->view->location = $location = Engine_Api::_()->getDbtable('locations', 'sitepage')->getLocation($value);

        // get site-page project
        $page_id = $sitepage->page_id;
        $projectParams['page_id'] = $page_id;
        $this->view->projectsList = $projectsList = Engine_Api::_()->getDbTable('locations', 'sitecrowdfunding')->getProjectsLocation($projectParams);

        // get site-page followers
        $followParams['page_id'] = $page_id;
        $this->view->followerList = $followerList = Engine_Api::_()->getDbTable('locations', 'user')->getOrganisationUsersLocations($followParams);

        // get site-page members
        $this->view->membersList = $membersList = Engine_Api::_()->getDbtable('membership', 'sitepage')->getJoinMemberLocation($sitepage->page_id);

        // get site-page partner org
        $this->view->partnerPages = $partnerPages = Engine_Api::_()->getDbtable('locations', 'sitepage')->getPartnerLocationsByPageId($sitepage->page_id);

        // get manage admins
        $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
        $this->view->manageadmins = $manageadminsTable->getManageAdminUserLocation($page_id);

        if (empty($location) && count($projectsList) < 0 && count($followerList) < 0 && count($membersList) < 0 && count($partnerPages) < 0  ) {
            return $this->setNoRender();
        }


    }

}
