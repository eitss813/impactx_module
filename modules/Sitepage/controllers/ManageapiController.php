<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: DashboardController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_ManageapiController extends Core_Controller_Action_Standard {

    //SET THE VALUE FOR ALL ACTION DEFAULT
    public function init() {

        if (!$this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'view')->isValid())
            return;

        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext
            ->addActionContext('rate', 'json')
            ->addActionContext('validation', 'html')
            ->initContext();

        $page_url = $this->_getParam('page_url', $this->_getParam('page_url', null));
        $page_id = $this->_getParam('page_id', $this->_getParam('page_id', null));

        if ($page_url) {
            $page_id = Engine_Api::_()->sitepage()->getPageId($page_url);
        }

        if ($page_id) {
            $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
            if ($sitepage) {
                if (!Engine_Api::_()->core()->hasSubject('sitepage_page')) {
                    Engine_Api::_()->core()->setSubject($sitepage);
                }
            }
        }

        //FOR UPDATE EXPIRATION
        if ((Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.task.updateexpiredpages') + 900) <= time()) {
            Engine_Api::_()->sitepage()->updateExpiredPages();
        }
    }


    public function manageapiAction()
    {

        //USER VALDIATION
        if( !$this->_helper->requireUser()->isValid() )
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        $this->view->user_id = $viewer_id;

        //FORM GENERATION
        $this->view->form = $form = new Sitepage_Form_Metrics();

        //$metricsList = Engine_Api::_()->getApi('metrics', 'sitepage');
        $tableMetricsTable = Engine_Api::_()->getDbtable('metrics', 'sitepage');
        $this->view->metricsList = $metricsList = $tableMetricsTable->getMetricsDataByOrganisationId($page_id);


    }

    public function manageAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteapi_admin_main', array(), 'siteapi_admin_api_clients');


        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);


        $params = array();
        $this->view->status = 2;
        if (isset($_GET['title']) && !empty($_GET['title']))
            $this->view->title = $params['title'] = $_GET['title'];

        if (isset($_GET['key']) && !empty($_GET['key']))
            $this->view->key = $params['key'] = $_GET['key'];

        if (isset($_GET['secret']) && !empty($_GET['secret']))
            $this->view->secret = $params['secret'] = $_GET['secret'];

        if (isset($_GET['status']))
            $this->view->status = $params['status'] = $_GET['status'];

        $params['page_id'] = $page_id;
        $page = $this->_getParam('page', 1);
       // include_once APPLICATION_PATH . '/application/modules/Siteapi/controllers/license/license2.php';
        $apiTable = Engine_Api::_()->getDbtable('consumers', 'siteapi');
        $this->view->paginator = $paginator = $apiTable->getCustomPaginator($params);



    }

    public function createAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteapi_admin_main', array(), 'siteapi_admin_api_clients');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);


        $this->view->form = $form = new Siteapi_Form_Admin_Consumer_Create();

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $this->getRequest()->getPost();

        $table = Engine_Api::_()->getDbtable('consumers', 'siteapi');

        if (isset($values['key']) && !empty($values['key'])) {
            $usernamePaginator = $table->getPaginator(array('key' => $values['key']));
            $itemCount = $usernamePaginator->getTotalItemCount();
            if (!empty($itemCount)) {
                $form->addError('Key already exist.');
                return;
            }

            $whiteSpaceCount = preg_match('/\s/', $values['key']);
            if (!empty($whiteSpaceCount)) {
                $form->addError('Space not allowed in key');
                return;
            }
        }

        if (isset($values['secret']) && !empty($values['secret'])) {
            $usernamePaginator = $table->getPaginator(array('secret' => $values['secret']));
            $itemCount = $usernamePaginator->getTotalItemCount();
            if (!empty($itemCount)) {
                $form->addError('API secret already exist.');
                return;
            }

            $whiteSpaceCount = preg_match('/\s/', $values['secret']);
            if (!empty($whiteSpaceCount)) {
                $form->addError('Space not allowed in secret');
                return;
            }
        }

        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $creation_date =   new Zend_Db_Expr('NOW()');
            $modified_date =   new Zend_Db_Expr('NOW()');
            // print_r($values);
            //create the consumer api
            $db->insert('engine4_siteapi_oauth_consumers', array(
                'title' => $values['title'],
                'key' => $values['key'],
                'secret' => $values['secret'],
                'page_id' => $page_id,
                'creation_date' => $creation_date,
                'modified_date' => $modified_date

            ));
            //include_once APPLICATION_PATH . '/application/modules/Siteapi/controllers/license/license2.php';

            $db->commit();

            //REDIRECT
            return $this->_helper->redirector->gotoRoute(array('action' => 'manage'));
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function editAction() {


        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);



       // $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteapi_admin_main', array(), 'siteapi_admin_api_clients');
        $id = $this->_getParam('id');
        $table = Engine_Api::_()->getItem('siteapi_consumers', $id);

        $this->view->form = $form = new Siteapi_Form_Admin_Consumer_Edit();

//        $this->view->form->type->setMultiOptions(array('All Clients'));
//        if(isset($table->type) && ($table->type == 1))
//            $this->view->form->type->setMultiOptions(array(1 => 'Android', 0 => 'All Clients'));
//
//        if(isset($table->type) && ($table->type == 2))
//            $this->view->form->type->setMultiOptions(array(2 => 'IOS', 0 => 'All Clients'));

        $form->populate($table->toArray());

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();

            if (isset($values['title']))
                $params['title'] = $values['title'];

//            if(isset($values['type']))
//                $params['type'] = $values['type'];

            $db = Engine_Api::_()->getDbtable('consumers', 'siteapi')->getAdapter();
            $db->beginTransaction();
            try {
                // Set the expiration of Access Token
//                $params['expire'] = 0;
//                if (isset($values['expire']) && !empty($values['expire']))
//                    $params['expire'] = $values['expire_limit'];

              //  include_once APPLICATION_PATH . '/application/modules/Sitepage/controllers/license/license2.php';

                //save the changes manually
                $db->update('engine4_siteapi_oauth_consumers', array(
                    'title' =>  $values['title']
                ),array(
                    'consumer_id = ?' => (int)$id
                ));

                $db->commit();

                //REDIRECT
                return $this->_helper->redirector->gotoRoute(array('action' => 'manage','page_id'=>$page_id));
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }

    //ACTION FOR MAKE PACKAGES ENABLE/DISABLE
    public function statusAction() {
        $this->_helper->layout->setLayout('admin-simple');
        $this->view->id = $id = $this->_getParam('id');
        $this->view->table = $table = Engine_Api::_()->getItem('siteapi_consumers', $id);

        if ($this->getRequest()->isPost()) {

            include_once APPLICATION_PATH . '/application/modules/Siteapi/controllers/license/license2.php';

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Successfully Changed!'))
            ));
        }
    }

    public function detailsAction() {
        $id = $this->_getParam('id');

        if (!empty($id))
            $getApiClient = Engine_Api::_()->getItem('siteapi_consumers', $id);

        if (!empty($getApiClient))
            $this->view->apiClientDetails = $getApiClient;
    }

    public function tokensAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteapi_admin_main', array(), 'siteapi_admin_api_clients');
        $consumer_id = $this->_getParam('consumer_id', null);

        if (!empty($consumer_id))
            $this->view->consumer = Engine_Api::_()->getItem('siteapi_consumers', $consumer_id);
        else
            return $this->_helper->redirector->gotoRoute(array('action' => 'manage'));

        $page = $this->_getParam('page', 1);

        $params = array();
        $this->view->revoke = 2;
        $params['consumer_id'] = $consumer_id;
        $params['type'] = 'access';
        if (isset($_GET['displayname']) && !empty($_GET['displayname']))
            $this->view->displayname = $params['displayname'] = $_GET['displayname'];

        if (isset($_GET['email']) && !empty($_GET['email']))
            $this->view->email = $params['email'] = $_GET['email'];

        if (isset($_GET['revoke']))
            $this->view->revoke = $params['revoke'] = $_GET['revoke'];

        include_once APPLICATION_PATH . '/application/modules/Siteapi/controllers/license/license2.php';
    }

    //ACTION FOR MAKE PACKAGES ENABLE/DISABLE
    public function revokedAction() {
        $this->_helper->layout->setLayout('admin-simple');
        $this->view->id = $id = $this->_getParam('id');
        $this->view->table = $table = Engine_Api::_()->getItem('siteapi_tokens', $id);

        if ($this->getRequest()->isPost()) {
            include_once APPLICATION_PATH . '/application/modules/Siteapi/controllers/license/license2.php';

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Successfully Changed!'))
            ));
        }
    }

    //ACTION FOR DELETE THE TOKEN
    public function deleteTokenAction() {
        $this->_helper->layout->setLayout('admin-simple');
        $this->view->id = $id = $this->_getParam('id');

        if ($this->getRequest()->isPost()) {
            include_once APPLICATION_PATH . '/application/modules/Siteapi/controllers/license/license2.php';

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Deleted Successfully!'))
            ));
        }
    }


}
