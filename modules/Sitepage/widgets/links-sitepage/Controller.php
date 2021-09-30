<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_LinksSitepageController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET VIEWER CLAIMS
        $claim_id = Engine_Api::_()->getDbtable('claims', 'sitepage')->getViewerClaims($viewer_id);

        //CLAIM IS ENABLED OR NOT
        $canClaim = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitepage_page', 'claim');

        $this->view->showClaimLink = 0;
        if (!empty($claim_id) && !empty($canClaim)) {
            $this->view->showClaimLink = 1;
        }

        //CHECK THAT WE HAVE TO SHOW MANAGE ADMIN LINK OR NOTE
        $adminpages = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->getManageAdminPages($viewer_id);

        //GET STUFF
        $manageadmin_ids = array();
        foreach ($adminpages as $adminpage) {
            $manageadmin_ids[] = $adminpage->page_id;
        }
        $manageadmin_values = array();
        $manageadmin_values['adminpages'] = $manageadmin_ids;
        $manageadmin_values['orderby'] = 'creation_date';
        $manageadmin_data = Engine_Api::_()->sitepage()->getSitepagesPaginator($manageadmin_values, null);
        $this->view->manageadmin_count = $manageadmin_data->getTotalItemCount();

        $linksValues = array("pageAdmin", "pageClaimed", "pageLiked");
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
            $linksValues[] = "pagesJoined";
        }
        $this->view->showLinks = $this->_getParam('showLinks', array($linksValues));

        if (empty($this->view->showLinks))
            return $this->setNoRender();

        if (in_array("pageAdmin", $this->view->showLinks)) {
            $this->view->showPageAdmin = true;
        }

        if (in_array("pageClaimed", $this->view->showLinks)) {
            $this->view->showPageClaimed = true;
        }

        if (in_array("pageLiked", $this->view->showLinks)) {
            $this->view->showPageLiked = true;
        }

        if (in_array("pagesJoined", $this->view->showLinks)) {
            $this->view->showPageJoined = true;
        }
    }

}

?>