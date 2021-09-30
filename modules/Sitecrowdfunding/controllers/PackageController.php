<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PackageController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_PackageController extends Core_Controller_Action_Standard {

    //COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
    public function init() {

        //USER VALIDATON
        if (!$this->_helper->requireUser()->isValid())
            return;

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('sitecrowdfunding_project', null, "create")->isValid())
            return;
    }

    //ACTION FOR SHOW PACKAGES
    public function indexAction() {
        //PROJECT CREATION PRIVACY
        if (!$this->_helper->requireAuth()->setAuthParams('sitecrowdfunding_project', null, "create")->isValid())
            return;
        if (!Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()) {
            return;
        }
        $package_show = $this->_getParam('package', 0);
        $this->view->parent_type = $parent_type = $this->_getParam('parent_type');
        $this->view->parent_id = $parent_id = $this->_getParam('parent_id');

        if ($package_show == 1) {
            $packageCount = Engine_Api::_()->getDbTable('packages', 'sitecrowdfunding')->getPackageCount();

            if (!Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()) {
                //REDIRECT
                if ($parent_type && $parent_id) {
                    return $this->_helper->redirector->gotoRoute(array('action' => 'create', 'parent_id' => $parent_id, 'parent_type' => $parent_type), "sitecrowdfunding_general", true);
                } else {
                    return $this->_helper->redirector->gotoRoute(array('action' => 'create'), "sitecrowdfunding_general", true);
                }
            }
            if ($packageCount == 1) {
                $package = Engine_Api::_()->getDbTable('packages', 'sitecrowdfunding')->getEnabledPackage();
                if (($package->price == '0.00')) {
                    if ($parent_type && $parent_id) {
                        return $this->_helper->redirector->gotoRoute(array('action' => 'create', 'id' => $package->package_id, 'parent_id' => $parent_id, 'parent_type' => $parent_type), "sitecrowdfunding_general", true);
                    } else {
                        return $this->_helper->redirector->gotoRoute(array('action' => 'create', 'id' => $package->package_id), "sitecrowdfunding_general", true);
                    }
                }
            }
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $values['owner_id'] = $viewer->getIdentity();
        $values['allProjects'] = 'all';
        $paginator = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding')->getProjectPaginator($values);
        $this->view->current_count = $paginator->getTotalItemCount();
        $this->view->quota = $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitecrowdfunding_project', "max");
        $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
        //WIDGET SETTINGS ARRAY - INFO ARRAY WHICH IS TO BE SHOWN IN PACKAGE DETAILS.
        $this->view->packageInfoArray = $coreSettingsApi->getSetting('sitecrowdfunding.package.information');
        if (!is_array($this->view->packageInfoArray))
            $this->view->packageInfoArray = array();
        $this->view->package_view = $coreSettingsApi->getSetting('sitecrowdfunding.package.view', 1);
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $paginator = Engine_Api::_()->getDbtable('packages', 'sitecrowdfunding')->getPackagesSql($viewer->getIdentity());
        $this->view->paginator = $paginator->setCurrentPageNumber($this->_getParam('page'));

        $this->_helper->content
                ->setContentName("sitecrowdfunding_package_index")
                //->setNoRender()
                ->setEnabled();
    }

    //ACTION FOR PACKAGE DETAIL
    public function detailAction() {

        //PROJECT CREATION PRIVACY
        if (!$this->_helper->requireAuth()->setAuthParams('sitecrowdfunding_project', null, "create")->isValid())
            return;

        //PACKAGE ENABLE VALIDATION
        if (!Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $id = $this->_getParam('id');
        if (empty($id)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $this->view->viewer = Engine_Api::_()->user()->getViewer();

        $this->view->package = Engine_Api::_()->getItem('sitecrowdfunding_package', $id);

        //WIDGET SETTINGS ARRAY - INFO ARRAY WHICH IS TO BE SHOWN IN PACKAGE DETAILS.
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $this->view->packageInfoArray = $settings->getSetting('sitecrowdfunding.package.information', array("price", "billing_cycle", "duration", "featured", "sponsored", "rich_overview", "videos", "photos", "description", "commission"));
    }

    //ACTION FOR PACKAGE UPDATION
    public function updatePackageAction() {

        //PACKAGE ENABLE VALIDATION
        if (!Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //GET PROJECT ID PROJECT OBJECT AND THEN CHECK VALIDATIONS
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        if (empty($project_id)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if (empty($project)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
            return;
        }

        $this->view->package_view = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.package.view', 1);

        //WIDGET SETTINGS ARRAY - INFO ARRAY WHICH IS TO BE SHOWN IN PACKAGE DETAILS.
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $this->view->packageInfoArray = $settings->getSetting('sitecrowdfunding.package.information', array("price", "billing_cycle", "duration", "featured", "sponsored", "rich_overview", "videos", "photos", "description", "commission"));

        $this->view->viewer = Engine_Api::_()->user()->getViewer();
        $this->view->TabActive = "package";

        $this->view->show_editor = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.tinymceditor', 0);

        $this->view->package = Engine_Api::_()->getItem('sitecrowdfunding_package', $project->package_id);
        $paginator = Engine_Api::_()->getDbTable('packages', 'sitecrowdfunding')->getPackageResult($project);
        $this->view->paginator = $paginator->setCurrentPageNumber($this->_getParam('page'));

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("sitecrowdfunding_main");
        $this->view->is_ajax = $this->_getParam('is_ajax', '');
    }

    //ACTION FOR PACKAGE UPGRADE CONFIRMATION
    public function updateConfirmationAction() {

        //PACKAGE ENABLE VALIDATION
        if (!Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //GET PROJECT ID, PROJECT OBJECT AND THEN CHECK VALIDATIONS
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        if (empty($project_id)) {
            return $this->_forward('notfound', 'error', 'core');
        }
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if (empty($project)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $this->view->package_id = $this->_getParam('package_id');
        $package_chnage = Engine_Api::_()->getItem('sitecrowdfunding_package', $this->view->package_id);

        if (empty($package_chnage) || !$package_chnage->enabled || (!empty($package_chnage->level_id) && !in_array($project->getOwner()->level_id, explode(",", $package_chnage->level_id)))) {
            return $this->_forward('notfound', 'error', 'core');
        }

        if ($this->getRequest()->getPost()) {
            if (!empty($_POST['package_id'])) {
                $table = $project->getTable();
                $db = $table->getAdapter();
                $db->beginTransaction();
                try {
                    $is_upgrade_package = true;
                    //APPLIED CHECKS BECAUSE CANCEL SHOULD NOT BE CALLED IF ALREADY CANCELLED 
                    if ($project->status == 'active')
                        $project->cancel($is_upgrade_package);

                    $project->package_id = $_POST['package_id'];
                    $package = Engine_Api::_()->getItem('sitecrowdfunding_package', $project->package_id);

                    $project->featured = $package->featured;
                    $project->sponsored = $package->sponsored;
                    $project->pending = 1;
                    $project->funding_end_date = new Zend_Db_Expr('NULL');
                    $project->funding_status = 'initial';
                    if (($package->isFree())) {
                        $project->funding_approved = $package->funding_approved;
                    } else {
                        $project->funding_approved = 0;
                    }
                    if (!empty($project->funding_approved)) {
                        $project->pending = 0;
                        $expirationDate = $package->getExpirationDate();
                        $currentDate = date('Y-m-d H:i:s');
                        if (!empty($expirationDate))
                            $project->funding_end_date = date('Y-m-d H:i:s', $expirationDate);
                        else
                            $project->funding_end_date = '2250-01-01 00:00:00';

                        if (empty($project->approved_date)) {
                            $project->approved_date = date('Y-m-d H:i:s');
                            if ($project->funding_state != 'draft' && $project->search && $project->is_gateway_configured && $project->funding_start_date <= $currentDate) {
                                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($project->getOwner(), $project, 'sitecrowdfunding_project_new');
                                if ($action != null) {
                                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $project);
                                }
                            }
                        }
                    }
                    $project->save();
                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
            }
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'format' => 'smoothbox',
                'parentRedirect' => $this->view->url(array('action' => 'update-package', 'project_id' => $project->project_id), "sitecrowdfunding_package", true),
                'parentRedirectTime' => 15,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('The package for your Project has been successfully changed.'))
            ));
        }
    }

    //ACTION FOR PACKAGE PAYMENT
    public function paymentAction() {

        //PACKAGE ENABLE VALIDATION
        if (!Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $this->view->show_editor = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.tinymceditor', 0);

        //GET PROJECT ID, PROJECT OBJECT AND THEN CHECK VALIDATIONS
        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode'))
            $project_id = $_POST['project_id_session'];
        else
            $project_id = $this->_getParam('project_id');

        if (empty($project_id)) {
            return $this->_forward('notfound', 'error', 'core');
        }
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if (empty($project)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $package = Engine_Api::_()->getItem('sitecrowdfunding_package', $project->package_id);

        if ((!$package->isFree())) {
            $session = new Zend_Session_Namespace('Payment_Sitecrowdfunding');
            $session->project_id = $project_id;
            return $this->_helper->redirector->gotoRoute(array(), "sitecrowdfunding_payment", true);
        } else {
            return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), "sitecrowdfunding_general", true);
        }
    }

    //ACTION FOR PACKAGE CANCEL
    public function cancelAction() {

        //PACKAGE ENABLE VALIDATION
        if (!Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()) {
            return $this->_forward('notfound', 'error', 'core');
        }

        if (!($package_id = $this->_getParam('package_id')) ||
                !($package = Engine_Api::_()->getItem('sitecrowdfunding_package', $package_id))) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'index', 'package_id' => null));
        }

        $this->view->package_id = $package_id;
        $project_id = $this->_getParam('project_id');

        $this->view->form = $form = new Sitecrowdfunding_Form_Package_Cancel();

        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // Try to cancel
        $this->view->form = null;
        try {
            Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id)->cancel();
            $this->view->status = true;
        } catch (Exception $e) {
            $this->view->status = false;
            $this->view->error = $e->getMessage();
        }
    }

}
