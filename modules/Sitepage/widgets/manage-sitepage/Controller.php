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
class Sitepage_Widget_ManageSitepageController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        $this->view->autoContentLoad = $isappajax = $this->_getParam('isappajax', 0);
        $this->view->showOwnerInfo = $this->_getParam("showOwnerInfo", 0);
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $this->view->pageAdminJoined = $this->_getParam("pageAdminJoined", 0);
        $this->view->can_edit = Engine_Api::_()->authorization()->isAllowed('sitepage_page', $viewer, "edit");
        $this->view->enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.price.field', 1);
        $this->view->enableLocation = $checkLocation = Engine_Api::_()->sitepage()->enableLocation();
        $this->view->can_delete = Engine_Api::_()->authorization()->isAllowed('sitepage_page', $viewer, "delete");
        $this->view->can_create = Engine_Api::_()->authorization()->isAllowed('sitepage_page', $viewer, "create");
        Engine_Api::_()->getDbtable('pagestatistics', 'sitepage')->setViews();

        //GET VIEWER CLAIMS
        $claim_id = Engine_Api::_()->getDbtable('claims', 'sitepage')->getViewerClaims($viewer_id);

        //CLAIM IS ENABLED OR NOT
        $canClaim = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitepage_page', 'claim');

        $this->view->showClaimLink = 0;
        if (!empty($claim_id) && !empty($canClaim)) {
            $this->view->showClaimLink = 1;
        }

        //FORM GENERATION
        $this->view->form = $form = new Sitepage_Form_Managesearch(array(
            'type' => 'sitepage_page'
        ));

        $form->removeElement('show');

        if (!empty($_POST)) {
            $form->populate($_POST);
            $this->view->search = $_POST['search'];
        }

        if (!empty($_POST))
            $form->populate($_POST);
        $values = $form->getValues();

        //CHECK TO SEE IF REQUEST IS FOR SPECIFIC USER'S PAGES
//        if ($this->view->pageAdminJoined == 2) {
//            $values['user_id'] = $viewer->getIdentity();
//        } else {
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

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember') && $this->view->pageAdminJoined == 1) {
                $onlymember = Engine_Api::_()->getDbtable('membership', 'sitepage')->getJoinPages($viewer->getIdentity(), 'onlymember');

                $onlymemberids = array();
                foreach ($onlymember as $onlymembers) {
                    $onlymemberids[] = $onlymembers->page_id;
                }
                $values['adminjoinedpages'] = array_merge($onlymemberids, $manageadmin_values['adminpages']);
            } else {
                $values['adminjoinedpages'] = $manageadmin_values['adminpages'];
            }
      //  }
        $values['type'] = 'manage';
        $values['type_location'] = 'manage';

        //GET PAGINATOR
        $this->view->paginator = $paginator = Engine_Api::_()->sitepage()->getSitepagesPaginator($values, null);
        $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.page', 10);

        $paginator->setItemCountPerPage($items_count);
        $this->view->paginator = $paginator->setCurrentPageNumber($values['page']);

        $this->view->page = $values['page'] ? $values['page'] : 1;
        $this->view->totalPages = ceil(($paginator->getTotalItemCount()) / $items_count);

        $this->view->quota = 0;
        if ($viewer->level_id != 1) {
            $this->view->quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitepage_page', 'max');
        }
        $this->view->current_count = $paginator->getTotalItemCount();
        $this->view->category_id = $values['category_id'];
        $this->view->subcategory_id = $values['subcategory_id'];
        $this->view->subsubcategory_id = $values['subsubcategory_id'];
    }

}

?>