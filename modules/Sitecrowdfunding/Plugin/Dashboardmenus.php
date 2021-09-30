<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Dashboardmenus.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Plugin_Dashboardmenus {

    private function _getProjectId() {

        //PROJECT OBJECT SET OR NOT
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if ($project->getType() !== 'sitecrowdfunding_project') {
            return false;
        }

     /*   //PROJECT LEVEL CHECK
        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy)) {
                return false;
            }
        }
*/
        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }


        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }
        return $project_id;
    }

    public function onMenuInitialize_SitecrowdfundingDashboardEditGoals($row){
        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if ($project->getType() !== 'sitecrowdfunding_project') {
            return false;
        }

     /*   // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy)) {
                return false;
            }
        }
     */
        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }


        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }

        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_extended',
            'controller' => 'goals',
            'action' => 'manage-goals',
            'class' => 'ajax_dashboard_enabled',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );

    }


    public function onMenuInitialize_SitecrowdfundingDashboardForms($row){
        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if ($project->getType() !== 'sitecrowdfunding_project') {
            return false;
        }

        /*   // todo: Allow edit for project admins:
           $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
           if ($isProjectAdmin != 1) {
               $viewer = Engine_Api::_()->user()->getViewer();
               $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
               if (empty($editPrivacy)) {
                   return false;
               }
           }
        */
        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }


        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }

        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_extended',
            'controller' => 'form',
            'action' => 'index',
            'class' => 'ajax_dashboard_enabled',
            'params' => array(
                'project_id' => $project->getIdentity(),
                'type' => 'forms_assigned',
            ),
        );

    }

    public function onMenuInitialize_SitecrowdfundingDashboardContactinfo($row) {

        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if ($project->getType() !== 'sitecrowdfunding_project') {
            return false;
        }

    /*    // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy)) {
                return false;
            }
        }
   */
        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }


        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }
        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_extended',
            'controller' => 'contact',
            'action' => 'contact-info',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitecrowdfundingDashboardEditinfo($row) {

        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if ($project->getType() !== 'sitecrowdfunding_project') {
            return false;
        }

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }

        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }


        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_specific',
            'action' => 'edit',
            'params' => array(
                'project_id' => $project->getIdentity(),
                'type'=> 'backstory'
            ),
        );
    }

    public function onMenuInitialize_SitecrowdfundingDashboardEdittags($row) {

        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if ($project->getType() !== 'sitecrowdfunding_project') {
            return false;
        }

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }

        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }

        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_specific',
            'action' => 'edit',
            'params' => array(
                'project_id' => $project->getIdentity(),
                'type'=> 'tag'
            ),
        );
    }


    public function onMenuInitialize_SitecrowdfundingDashboardEditcategory($row) {

        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if ($project->getType() !== 'sitecrowdfunding_project') {
            return false;
        }

       /* // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }
       */
        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }


        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }
        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_specific',
            'action' => 'edit',
            'params' => array(
                'project_id' => $project->getIdentity(),
                'type'=> 'category'
            ),
        );
    }

    public function onMenuInitialize_SitecrowdfundingDashboardProjectSettings($row){

        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = $this->_getProjectId();
        if (empty($project_id)) {
            return false;
        }

        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }


        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }


        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_dashboard',
            'action' => 'project-settings',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitecrowdfundingDashboardInitiativedetails($row) {

        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if ($project->getType() !== 'sitecrowdfunding_project') {
            return false;
        }

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }

        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }


        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_initiative',
            'action' => 'edit-initiative-answers',
            'params' => array(
                'project_id' => $project->getIdentity(),
            ),
        );
    }

    public function onMenuInitialize_SitecrowdfundingDashboardMetricdetails($row) {

        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if ($project->getType() !== 'sitecrowdfunding_project') {
            return false;
        }

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }

        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }

        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_metric',
            'action' => 'list-metric',
            'params' => array(
                'project_id' => $project->getIdentity(),
            ),
        );
    }

    public function onMenuInitialize_SitecrowdfundingDashboardSettings($row) {


        echo "sedttting";
        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if ($project->getType() !== 'sitecrowdfunding_project') {
            return false;
        }
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);

       /* // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy)) {
                return false;
            }
        }
       */
        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }


        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }

        $sitevideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo');
        $isIntegrated = Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => "sitecrowdfunding_project", 'item_module' => 'sitecrowdfunding'));
        if (empty($sitevideoEnabled) || empty($isIntegrated))
            return false;

        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_dashboard',
            'action' => 'set-settings',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitecrowdfundingDashboardOverview($row) {

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.overview', 1))
            return false;
        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($project->getType() !== 'sitecrowdfunding_project') {
            return false;
        }

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }


        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if ($isProjectAdmin != 1 &&  empty($editPrivacyOrganization)) {
            if (Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()) {
                $allowOverview = Engine_Api::_()->sitecrowdfunding()->allowPackageContent($project->package_id, "overview") ? 1 : 0;
            } else {
                $allowOverview = Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, "overview");
            }
            if (empty($allowOverview)) {
                return false;
            }

            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy)) {
                return false;
            }

            $overviewPrivacy = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitecrowdfunding_project', "overview");
            if (empty($overviewPrivacy)) {
                return false;
            }
        }


        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_dashboard',
            'action' => 'overview',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitecrowdfundingDashboardAdditional($row) {

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.additional', 1))
            return false;
        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($project->getType() !== 'sitecrowdfunding_project') {
            return false;
        }

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }


        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if ($isProjectAdmin != 1 &&  empty($editPrivacyOrganization)) {
            if (Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()) {
                $allowOverview = Engine_Api::_()->sitecrowdfunding()->allowPackageContent($project->package_id, "overview") ? 1 : 0;
            } else {
                $allowOverview = Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, "overview");
            }
            if (empty($allowOverview)) {
                return false;
            }

            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy)) {
                return false;
            }

            $overviewPrivacy = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitecrowdfunding_project', "overview");
            if (empty($overviewPrivacy)) {
                return false;
            }
        }


        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_dashboard',
            'action' => 'additional',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );
    }



    public function onMenuInitialize_SitecrowdfundingDashboardProfilepicture($row) {

        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if ($project->getType() !== 'sitecrowdfunding_project') {
            return false;
        }

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy)) {
                return false;
            }
        }

        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_dashboard',
            'action' => 'change-photo',
            'class' => 'ajax_dashboard_enabled',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitecrowdfundingDashboardEditphoto($row) {

        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if ($project->getType() !== 'sitecrowdfunding_project') {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }

        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }



        if (Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()) {
            $allowPhotoUpload = Engine_Api::_()->sitecrowdfunding()->allowPackageContent($project->package_id, "photo") ? 1 : 0;
        } else {
            $allowPhotoUpload = Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, "photo");
        }
        if (empty($allowPhotoUpload)) {
            return false;
        }

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_albumspecific',
            'action' => 'editphotos',
            'class' => 'ajax_dashboard_enabled',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitecrowdfundingDashboardEditOrganizations($row) {

        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if ($project->getType() !== 'sitecrowdfunding_project') {
            return false;
        }

       /* // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy)) {
                return false;
            }
        }
       */
        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }


        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }

//        if (Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()) {
//            $allowPhotoUpload = Engine_Api::_()->sitecrowdfunding()->allowPackageContent($project->package_id, "photo") ? 1 : 0;
//        } else {
//            $allowPhotoUpload = Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, "photo");
//        }
//        if (empty($allowPhotoUpload)) {
//            return false;
//        }

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_organizationspecific',
            'action' => 'editorganizations',
            'class' => 'ajax_dashboard_enabled',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitecrowdfundingDashboardEditvideo($row) {
        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if ($project->getType() !== 'sitecrowdfunding_project') {
            return false;
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }

        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {

            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }

        $viewer_id = $viewer->getIdentity();
        if (!empty($viewer_id)) {
            $level_id = Engine_Api::_()->user()->getViewer()->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        $allowed_upload_video_video = Engine_Api::_()->authorization()->getPermission($level_id, 'video', 'create');
        if (empty($allowed_upload_video_video))
            return false;

        if ($project->owner_id != $viewer_id)
            return false;

        $request = Zend_Controller_Front::getInstance()->getRequest();
        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_dashboard',
            'action' => 'video-edit',
            'class' => 'ajax_dashboard_enabled',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitecrowdfundingDashboardEditmetakeyword($row) {

        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if ($project->getType() !== 'sitecrowdfunding_project') {
            return false;
        }

       /* // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }
       */
        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }


        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }
        $allowMetaKeywords = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitecrowdfunding_project', "metakeyword");

        if (empty($allowMetaKeywords) || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.metakeyword', 1)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_dashboard',
            'action' => 'meta-detail',
            'class' => 'ajax_dashboard_enabled',
            'href' => '',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitecrowdfundingDashboardEditlocation($row) {

        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if ($project->getType() !== 'sitecrowdfunding_project') {
            return false;
        }

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }

        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }


        if (Engine_Api::_()->sitecrowdfunding()->enableLocation()) {
            return array(
                'label' => $row->label,
                'route' => 'sitecrowdfunding_specific',
                'action' => 'editlocation',
                'params' => array(
                    'project_id' => $project->getIdentity()
                ),
            );
        }

        return false;
    }

    public function onMenuInitialize_SitecrowdfundingDashboardAnnouncements($row) {

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.announcement', 1)) {
            return false;
        }

        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if ($project->getType() !== 'sitecrowdfunding_project') {
            return false;
        }

     /*   // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy)) {
                return false;
            }
        }
     */
        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }


        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $url = $view->url(array('controller' => 'announcement', 'action' => 'manage', 'project_id' => $project->project_id), "sitecrowdfunding_extended", true);

        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_extended',
            'controller' => 'announcement',
            'action' => 'manage',
            'class' => 'ajax_dashboard_enabled',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitecrowdfundingDashboardRewards($row) {

        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if ($project->getType() !== 'sitecrowdfunding_project') {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $rewardCreatePrivacy = Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, "reward_create");
        if (empty($rewardCreatePrivacy)) {
            return false;
        }

       /* // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy)) {
                return false;
            }
        }
      */
        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }


        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }

        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_extended',
            'controller' => 'reward',
            'action' => 'manage',
            'class' => 'ajax_dashboard_enabled',
            'href' => '',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitecrowdfundingDashboardPaymentaccount($row) {

        $project_id = $this->_getProjectId();
        if (empty($project_id)) {
            return false;
        }

        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        //PAYMENT FLOW CHECK
        $paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.method', 'normal');

        if ($paymentMethod == 'escrow' || $paymentMethod == 'split') {
            return false;
        } else {
            $paymentToSiteadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.to.siteadmin', 0);
            if (empty($paymentToSiteadmin)) {
                return false;
            }
        }
        return array(
            'label' => 'Payment Account',
            'route' => 'sitecrowdfunding_specific',
            'action' => 'payment-info',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'sitecrowdfunding_dashboard_paymentmethod',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitecrowdfundingDashboardPaymentmethod($row) {

        $project_id = $this->_getProjectId();
        if (empty($project_id)) {
            return false;
        }

        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }


        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }


        //PAYMENT FLOW CHECK
        $paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.method', 'normal');
        if ($paymentMethod == 'normal') {
            $paymentToSiteadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.to.siteadmin', 0);
            if ($paymentToSiteadmin) {
                return false;
            }
        }
        return array(
            'label' => 'Payment Methods',
            'route' => 'sitecrowdfunding_specific',
            'action' => 'payment-info',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'sitecrowdfunding_dashboard_paymentmethod',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitecrowdfundingDashboardKycupload($row) {

        $project_id = $this->_getProjectId();
        if (empty($project_id)) {
            return false;
        }
        $gateways = array();
        $paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.method', 'normal');
        if ($paymentMethod == 'split') {
            $gateways = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.allowed.payment.split.gateway', array());
        } else if ($paymentMethod == 'escrow') {
            $gateways = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.allowed.payment.escrow.gateway', array());
        }
        if (!in_array('mangopay', $gateways)) {
            return false;
        }
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        return array(
            'label' => 'Upload KYC',
            'route' => 'sitecrowdfunding_specific',
            'action' => 'upload-kyc',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'sitecrowdfunding_dashboard_kycupload',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitecrowdfundingDashboardAboutyou($row) {

        $project_id = $this->_getProjectId();
        if (empty($project_id)) {
            return false;
        }

        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }


        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }

        return array(
            'label' => 'About You',
            'route' => 'sitecrowdfunding_dashboard',
            'action' => 'about-you',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'sitecrowdfunding_dashboard_aboutyou',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitecrowdfundingDashboardUploadvideo($row) {

        $project_id = $this->_getProjectId();
        if (empty($project_id)) {
            return false;
        }

        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

    /*    // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy)) {
                return false;
            }
        }

    */
        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }


        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }
        $sitevideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo');
        $isIntegrated = Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => "sitecrowdfunding_project", 'item_module' => 'sitecrowdfunding'));
        if (empty($sitevideoEnabled) || empty($isIntegrated))
            return false;

        if (Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()) {
            $allowVideoUpload = Engine_Api::_()->sitecrowdfunding()->allowPackageContent($project->package_id, "video") ? 1 : 0;
        } else {
            $allowVideoUpload = Engine_Api::_()->authorization()->isAllowed($project, $viewer, "video");
        }
//        if (empty($allowVideoUpload)) {
//            return false;
//        }

        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_dashboard',
            'action' => 'upload-video',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'sitecrowdfunding_dashboard_uploadvideo',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitecrowdfundingDashboardEditstatus($row){
        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = $this->_getProjectId();
        if (empty($project_id)) {
            return false;
        }
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy)) {
                return false;
            }
        }

        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_extended',
            'controller' => 'status',
            'action' => 'edit-status',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitecrowdfundingDashboardEditfunding($row) {
        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = $this->_getProjectId();
        if (empty($project_id)) {
            return false;
        }
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $viewer = Engine_Api::_()->user()->getViewer();

      /*  // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy)) {
                return false;
            }
        }
      */
        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }


        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }
        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_extended',
            'controller' => 'funding',
            'action' => 'edit-funding',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );

    }

    public function onMenuInitialize_SitecrowdfundingDashboardEditMilestone($row) {
        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = $this->_getProjectId();
        if (empty($project_id)) {
            return false;
        }
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $viewer = Engine_Api::_()->user()->getViewer();

      /*  // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy)) {
                return false;
            }
        }
      */
        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }


        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }
        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_extended',
            'controller' => 'milestone',
            'action' => 'manage-milestone',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );

    }


    public function onMenuInitialize_SitecrowdfundingDashboardEditprivacy($row) {
        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = $this->_getProjectId();
        if (empty($project_id)) {
            return false;
        }
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $viewer = Engine_Api::_()->user()->getViewer();

     /*   // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy)) {
                return false;
            }
        }
     */
        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }


        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }

        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_extended',
            'controller' => 'privacy',
            'action' => 'edit-privacy',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );

    }

    public function onMenuInitialize_SitecrowdfundingDashboardManagememberroles($row) {

        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = $this->_getProjectId();
        if (empty($project_id)) {
            return false;
        }

        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.member.category.settings', 1) == 1) {
            return false;
        }

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy)) {
                return false;
            }
        }

        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_extended',
            'controller' => 'dashboard',
            'action' => 'manage-member-role',
            'class' => 'ajax_dashboard_enabled',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitecrowdfundingDashboardManagemembers($row) {

        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = $this->_getProjectId();
        if (empty($project_id)) {
            return false;
        }

        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy)) {
                return false;
            }
        }

        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_extended',
            'controller' => 'member',
            'action' => 'list-members',
            'class' => 'ajax_dashboard_enabled',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitecrowdfundingDashboardManageleaders($row) {

        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = $this->_getProjectId();
        if (empty($project_id)) {
            return false;
        }

        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

//        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.leader', 1)) {
//            return false;
//        }

        $viewer = Engine_Api::_()->user()->getViewer();

//        // Dont show the leader for other project admins except project-owner
//        if ($project->owner_id != $viewer->getIdentity()){
//            return false;
//        }
//
//        $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
//        if (empty($editPrivacy)) {
//            return false;
//        }

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy)) {
                return false;
            }
        }

        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_extended',
            'controller' => 'dashboard',
            'action' => 'manage-leaders',
            'class' => 'ajax_dashboard_enabled',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitecrowdfundingDashboardBackersreport($row) {

        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = $this->_getProjectId();
        if (empty($project_id)) {
            return false;
        }

        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.leader', 1)) {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy)) {
                return false;
            }
        }

        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_backer',
            'controller' => 'backer',
            'action' => 'backers-report',
            'class' => 'ajax_dashboard_enabled',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitecrowdfundingDashboardTransactiondetails($row) {

        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = $this->_getProjectId();
        if (empty($project_id)) {
            return false;
        }
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);


        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }


        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }



        //PAYMENT FLOW CHECK
        $paymentToSiteadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.to.siteadmin', 0);
        if ($paymentToSiteadmin) {
            return array(
                'label' => $row->label,
                'route' => 'sitecrowdfunding_backer',
                'action' => 'transaction',
                // 'class' => 'ajax_dashboard_enabled',
                // 'name' => 'siteevent_dashboard_transactions',
                //'tab' => 54,
                'params' => array(
                    'project_id' => $project->getIdentity()
                ),
            );
        } else {
            return array(
                'label' => $row->label,
                'route' => 'sitecrowdfunding_dashboard',
                'controller' => 'dashboard',
                'action' => 'project-transactions',
                'params' => array(
                    'project_id' => $project->getIdentity()
                ),
            );
        }
    }

    public function onMenuInitialize_SitecrowdfundingDashboardPackages($row) {

        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = $this->_getProjectId();
        if (empty($project_id)) {
            return false;
        }

        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.package.setting', 0)) {
            return false;
        }

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy)) {
                return false;
            }
        }

        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_package',
            'controller' => 'package',
            'action' => 'update-package',
            'class' => 'ajax_dashboard_enabled',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitecrowdfundingDashboardPaymentrequests($row) {

        $project_id = $this->_getProjectId();
        if (empty($project_id)) {
            return false;
        }

        //PAYMENT FLOW CHECK
        $paymentToSiteadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.to.siteadmin', 0);
        if (empty($paymentToSiteadmin)) {
            return false;
        }
        $paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.method', 'normal');
        if ($paymentMethod != 'normal') {
            return false;
        }
        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_backer',
            'action' => 'payment-to-me',
            'class' => 'ajax_dashboard_enabled',
            'params' => array(
                'project_id' => $project_id
            ),
        );
    }

    public function onMenuInitialize_SitecrowdfundingDashboardYourbill($row) {

        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = $this->_getProjectId();
        if (empty($project_id)) {
            return false;
        }
        //PAYMENT FLOW CHECK
        $paymentToSiteadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.to.siteadmin', 0);
        if ($paymentToSiteadmin) {
            return false;
        }
        $paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.method', 'normal');
        if ($paymentMethod != 'normal') {
            return false;
        }
        $commission = Engine_Api::_()->sitecrowdfunding()->getOrderCommission($project_id);
        if (!empty($commission[1])) {
            return array(
                'label' => $row->label,
                'route' => 'sitecrowdfunding_backer',
                'action' => 'your-bill',
                'class' => 'ajax_dashboard_enabled',
                'params' => array(
                    'project_id' => $project_id
                ),
            );
        } else
            return false;;
    }

    public function onMenuInitialize_SitecrowdfundingDashboardEditoutcome($row) {

        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = $this->_getProjectId();
        if (empty($project_id)) {
            return false;
        }

        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }


        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }


        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_extended',
            'controller' => 'outcome',
            'action' => 'settings',
            'class' => 'ajax_dashboard_enabled',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitecrowdfundingDashboardEditoutput($row) {

        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = $this->_getProjectId();
        if (empty($project_id)) {
            return false;
        }

        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy)) {
                return false;
            }
        }

        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_extended',
            'controller' => 'output',
            'action' => 'manage-output',
            'class' => 'ajax_dashboard_enabled',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitecrowdfundingDashboardExternalfunding($row) {
        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = $this->_getProjectId();
        if (empty($project_id)) {
            return false;
        }
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

       /** // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy)) {
                return false;
            }
        }
        */
        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }


        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }

        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_extended',
            'controller' => 'funding',
            'action' => 'external-funding',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );

    }

    public function onMenuInitialize_SitecrowdfundingDashboardManageadminsettings($row) {

        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = $this->_getProjectId();
        if (empty($project_id)) {
            return false;
        }
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }

        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }

        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_extended',
            'controller' => 'project-admin-settings',
            'action' => 'settings',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );

    }

    public function onMenuInitialize_SitecrowdfundingDashboardProjectfunding($row) {

        //GET PROJECT ID AND PROJECT OBJECT
        $project_id = $this->_getProjectId();
        if (empty($project_id)) {
            return false;
        }
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }


        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }

        return array(
            'label' => $row->label,
            'route' => 'sitecrowdfunding_extended',
            'controller' => 'project-funding',
            'action' => 'details',
            'params' => array(
                'project_id' => $project->getIdentity()
            ),
        );

    }

}
