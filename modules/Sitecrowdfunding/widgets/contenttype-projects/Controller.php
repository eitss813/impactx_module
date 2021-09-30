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
class Sitecrowdfunding_Widget_ContenttypeProjectsController extends Seaocore_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        //GET VIEWER DETAILS
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer->getIdentity();
        $params = $this->_getAllParams();
        //GET PROJECT SUBJECT
        $subject = Engine_Api::_()->core()->getSubject();
        $this->view->moduleName = $moduleName = strtolower($subject->getModuleName());
        $this->view->getShortType = $getShortType = ucfirst($subject->getShortType());

        if ($moduleName == 'sitereview' && isset($subject->listingtype_id)) {
            if (!(Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitereview_listing_' . $subject->listingtype_id, 'item_module' => 'sitereview', 'checked' => 'enabled'))))
                return $this->setNoRender();
        } else {
            if (($moduleName != 'user') && !(Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getIntegratedModules(array('enabled' => 1, 'item_type' => $subject->getType(), 'item_module' => strtolower($subject->getModuleName()), 'checked' => 'enabled'))))
                return $this->setNoRender();
        }
        $sitecrowdfundingProjectContentType = Zend_Registry::isRegistered('sitecrowdfundingProjectContentType') ? Zend_Registry::get('sitecrowdfundingProjectContentType') : null;
        $this->view->parent_type = $params['parent_type'] = $subject->getType();
        $this->view->parent_id = $params['parent_id'] = $subject->getIdentity();
        $this->view->canEdit = Engine_Api::_()->sitecrowdfunding()->isEditPrivacy($subject->getType(), $subject->getIdentity(), $subject);
        $this->view->canDelete = Engine_Api::_()->sitecrowdfunding()->canDeletePrivacy($subject->getType(), $subject->getIdentity(), $subject);
        if (empty($sitecrowdfundingProjectContentType))
            return $this->setNoRender();
        if ($moduleName == 'sitepage' || $moduleName == 'sitebusiness' || $moduleName == 'sitegroup') {
            $this->view->user_layout = $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting($moduleName . '.layoutcreate', 0);
            $isModuleOwnerAllow = 'is' . $getShortType . 'OwnerAllow';
            $this->_childCount = $this->view->projectCount = Engine_Api::_()->$moduleName()->getTotalCount($subject->getIdentity(), 'sitecrowdfunding', 'projects');

            //START PACKAGE WORK
            if (Engine_Api::_()->$moduleName()->hasPackageEnable()) {

                if (!Engine_Api::_()->$moduleName()->allowPackageContent($subject->package_id, "modules", 'sitecrowdfunding')) {
                    return $this->setNoRender();
                }
            } else {
                //permission to create project in above modules(Not Available Yet )
                $isOwnerAllow = Engine_Api::_()->$moduleName()->$isModuleOwnerAllow($subject, 'sprcreate');

                if (empty($isOwnerAllow)) {
                    return $this->setNoRender();
                }
            }
            //END PACKAGE WORK

            $this->view->canCreate = $canCreate = Engine_Api::_()->$moduleName()->isManageAdmin($subject, 'sprcreate');

            //START MANAGE-ADMIN CHECK
            $isManageAdmin = Engine_Api::_()->$moduleName()->isManageAdmin($subject, 'view');
            if (empty($isManageAdmin)) {
                return $this->setNoRender();
            }

            $isManageAdmin = Engine_Api::_()->$moduleName()->isManageAdmin($subject, 'edit');

            if (empty($isManageAdmin)) {
                $this->view->canEdit = $canEdit = 0;
            } else {
                $this->view->canEdit = $canEdit = 1;
            }

            if (empty($canCreate) && empty($this->view->projectCount) && empty($canEdit) && !(Engine_Api::_()->$moduleName()->showTabsWithoutContent())) {
                return $this->setNoRender();
            }
        } else if ($moduleName == 'siteevent') {
            $this->view->user_layout = 0;
            $this->view->canEdit = $canEdit = $subject->authorization()->isAllowed($viewer, "edit");
            $this->_childCount = $this->view->projectCount = Engine_Api::_()->$moduleName()->getTotalCount($subject->getIdentity(), 'sitecrowdfunding', 'projects');
            //AUTHORIZATION CHECK
            $this->view->canCreate = $canCreate = Engine_Api::_()->siteevent()->allowProject($subject, $viewer, $this->view->projectCount);
            if (empty($canCreate) && empty($this->view->projectCount) && empty($canEdit)) {
                return $this->setNoRender();
            }
        } else if ($moduleName == 'sitereview') {
            $this->view->user_layout = 0;
            //AUTHORIZATION CHECK
            $table = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
            //DO NOT SHOW THE PROJECTS BEFORE START DATE
            $currentDate = date('Y-m-d H:i:s');

            $select = $table->select();
            $select->from($table->info('name'), array('count(*) as count'))
                    ->where("parent_type = ?", 'sitereview_listing')
                    ->where("parent_id =?", $subject->getIdentity())
                    ->where("approved =?", 1)
                    ->where("state <> ?", 'draft')
                    ->where("is_gateway_configured = ?", 1)
                    ->where("start_date <= '$currentDate'");

            $this->_childCount = $this->view->projectCount = $count = $select->query()->fetchColumn();
            $this->view->canCreate = $canCreate = Engine_Api::_()->sitereview()->allowProject($subject, $viewer, $this->view->projectCount);
            $this->view->canEdit = $canEdit = $subject->authorization()->isAllowed($viewer, "edit_listtype_$subject->listingtype_id");

            if (empty($canCreate) && empty($this->view->projectCount) && empty($canEdit)) {
                return $this->setNoRender();
            }
            $params['parent_type'] = 'sitereview_listing';
            $params['parent_id'] = $subject->getIdentity();
        } else if ($moduleName == 'user') {
            //IF THE WIDGET IS PLACED ON USER PROFILE PAGE THAN SHOW THE USER'S ALL PROJECTS
            $params['owner_id'] = $subject->user_id;
            $this->view->canCreate = $canCreate = Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, 'create');
            if (empty($canCreate) && empty($canEdit)) {
                return $this->setNoRender();
            }
            $this->view->parent_type = $params['parent_type'] = '';
            $this->view->parent_id = $params['parent_id'] = '';
        }

        if (isset($params['page']) && !empty($params['page']))
            $this->view->page = $page = $params['page'];
        else
            $this->view->page = $page = 1;

        $this->view->widgetPath = 'widget/index/mod/sitecrowdfunding/name/contenttype-projects';
        $params['id'] = $this->_getParam('id', null);
        $this->view->gridViewWidth = $params['gridViewWidth'] = $this->_getParam('projectWidth', 150);
        $this->view->gridViewHeight = $params['gridViewHeight'] = $this->_getParam('projectHeight', 150);
        $this->view->projectOption = $params['projectOption'] = $this->_getParam('projectOption');
        if (empty($this->view->projectOption) || !is_array($this->view->projectOption)) {
            $this->view->projectOption = $params['projectOption'] = array();
        }
        $this->view->showContent = $params['show_content'] = $this->_getParam('show_content', 2);
        $this->view->itemCountPerPage = $params['itemCountPerPage'] = $this->_getParam('itemCountPerPage', 10);
        $this->view->is_ajax = $params['is_ajax'] = $this->_getParam('is_ajax', false);
        if (empty($this->view->projectOption) || !is_array($this->view->projectOption)) {
            $this->view->projectOption = $params['projectOption'] = array();
        }

        $this->view->params = $params;

        $paginator = $this->view->paginator = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding')->getProjectPaginator($params);
        $this->view->totalCount = $paginator->getTotalItemCount();
        $paginator->setItemCountPerPage($params['itemCountPerPage']);
        $paginator->setCurrentPageNumber($page);

        $this->view->titleTruncation = $params['titleTruncation'] = $this->_getParam('titleTruncation', 17);
        $this->view->descriptionTruncation = $params['descriptionTruncation'] = $this->_getParam('descriptionTruncation', 50);
        //ADD COUNT TO TITLE
        if ($this->view->totalCount > 0) {
            $this->_childCount = $this->view->totalCount;
        }
    }

    public function getChildCount() {
        return $this->_childCount;
    }

}
