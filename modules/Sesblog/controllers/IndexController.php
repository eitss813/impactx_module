<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: IndexController.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_IndexController extends Core_Controller_Action_Standard {

  public function init() {
  
    $viewer = Engine_Api::_()->user()->getViewer();
    if(!$viewer || !$viewer->getIdentity()) {
      $action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
      if(Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', $action) && $action!="view") {
        $_SESSION['redirect_url'] = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
        $this->_redirect('login');
      }
    }

    if (!$this->_helper->requireAuth()->setAuthParams('sesblog_blog', null, 'view')->isValid()) 
      return;

    $item_id = Engine_Api::_()->getDbtable('blogs', 'sesblog')->getBlogId($this->_getParam('blog_id', $this->_getParam('id', null)));
    if ($item_id) {
      $item = Engine_Api::_()->getItem('sesblog_blog', $item_id);
      if ($item) {
        Engine_Api::_()->core()->setSubject($item);
      }
    }
  }
  
  
  public function packageAction() {
    if (!$this->_helper->requireUser->isValid())
      return;
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->existingleftpackages = $existingleftpackages = Engine_Api::_()->getDbTable('orderspackages', 'sesblogpackage')->getLeftPackages(array('owner_id' => $viewer->getIdentity()));
    $this->_helper->content->setEnabled();
  }

  public function transactionsAction() {
    if (!$this->_helper->requireUser->isValid())
      return;
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    $tableTransaction = Engine_Api::_()->getItemTable('sesblogpackage_transaction');
    $tableTransactionName = $tableTransaction->info('name');
    $blogTable = Engine_Api::_()->getDbTable('blogs', 'sesblog');
    $blogTableName = $blogTable->info('name');
    $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');

    $select = $tableTransaction->select()
            ->setIntegrityCheck(false)
            ->from($tableTransactionName)
            ->joinLeft($tableUserName, "$tableTransactionName.owner_id = $tableUserName.user_id", 'username')
            ->where($tableUserName . '.user_id IS NOT NULL')
            ->joinLeft($blogTableName, "$tableTransactionName.transaction_id = $blogTableName.transaction_id", 'blog_id')
            ->where($blogTableName . '.blog_id IS NOT NULL')
            ->where($tableTransactionName . '.owner_id =?', $viewer->getIdentity());
    $paginator = Zend_Paginator::factory($select);
    $this->view->paginator = $paginator;
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->_helper->content->setEnabled();
  }

  public function viewpagescrollAction()
  {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && isset($_POST['settings'])  && !empty($_POST['settings']) && !empty($_POST['id']))
    {
      if(isset($_POST['id']) && !empty($_POST['id']))
      {
        $sesblog=Engine_Api::_()->getItem('sesblog_blog', $_POST['id']);
        $params = array();
        $params=$_POST['settings'];
        $viewer = Engine_Api::_()->user()->getViewer();
        $params['viewer_id'] = $viewer->getIdentity();
        if (is_array($_POST['settings']['show_criteria']) && !empty($_POST['settings']['show_criteria'])) {
          foreach ($_POST['settings']['show_criteria'] as $show_criteria)
            $params[$show_criteria . 'Active'] = $show_criteria;
        }
        if( !empty($sesblog->category_id) )
        { $category = Engine_Api::_()->getDbtable('categories', 'sesblog')->find($sesblog->category_id)->current();}

        echo include APPLICATION_PATH . '/application/modules/Sesblog/views/scripts/viewfileloadbyajax.tpl';
        exit();

      }
    }
    exit();
  }

  public function nonloginredirectAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if(!$viewer || !$viewer->getIdentity() && isset($_POST['sessionurl'])) {
      $_SESSION['redirect_url']=$_POST['sessionurl'];
      echo 1;
      exit();
    }
  }

  public function acceptAction()
  {

    // Check auth
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject('sesblog_blog')->isValid()) return;

    $viewer = Engine_Api::_()->user()->getViewer();

    // Make form
    $this->view->form = $form = new Sesblog_Form_Accept();

    // Process form
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Method');
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Data');
      return;
    }


    // Process
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    $checkBlogUserAdmin = Engine_Api::_()->sesblog()->checkBlogUserAdmin($subject->getIdentity());
    try {

      $checkBlogUserAdmin->resource_approved = '1';
      $checkBlogUserAdmin->save();

      $getAllBlogAdmins = Engine_Api::_()->getDbTable('roles', 'sesblog')->getAllBlogAdmins(array('blog_id' => $subject->getIdentity()));
      foreach ($getAllBlogAdmins as $getAllBlogAdmin) {
        //Notification Work for admin
        $owner = Engine_Api::_()->getItem('user', $getAllBlogAdmin->user_id);
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $subject, 'sesblog_acceptadminre');
      }
    } catch (Exception $e) {
    }

    $this->view->status = true;
    $this->view->error = false;

    $message = Zend_Registry::get('Zend_Translate')->_('You have accepted the request to the blog %s');
    $message = sprintf($message, $subject->__toString());
    $this->view->message = $message;

    if (null === $this->_helper->contextSwitch->getCurrentContext()) {
      return $this->_forward('success', 'utility', 'core', array(
        'messages' => array($message),
        'layout' => 'default-simple',
        'parentRefresh' => true,
      ));
    }
  }

  public function rejectAction()
  {
    // Check auth
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject('sesblog_blog')->isValid()) return;

    // Make form
    $this->view->form = $form = new Sesblog_Form_Reject();

    // Process form
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Method');
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Data');
      return;
    }

    // Process
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $checkBlogUserAdmin = Engine_Api::_()->sesblog()->checkBlogUserAdmin($subject->getIdentity());
    try {
      $checkBlogUserAdmin->delete();
      $getAllBlogAdmins = Engine_Api::_()->getDbTable('roles', 'sesblog')->getAllBlogAdmins(array('blog_id' => $subject->getIdentity()));
      foreach ($getAllBlogAdmins as $getAllBlogAdmin) {
        //Notification Work for admin
        $owner = Engine_Api::_()->getItem('user', $getAllBlogAdmin->user_id);
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $subject, 'sesblog_rejestadminre');
      }
    } catch (Exception $e) {

    }

    $this->view->status = true;
    $this->view->error = false;
    $message = Zend_Registry::get('Zend_Translate')->_('You have decline the request to the blog %s');
    $message = sprintf($message, $subject->__toString());
    $this->view->message = $message;

    if (null === $this->_helper->contextSwitch->getCurrentContext()) {
      return $this->_forward('success', 'utility', 'core', array(
        'messages' => array($message),
        'layout' => 'default-simple',
        'parentRefresh' => true,
      ));
    }
  }

  public function removeasadminAction()
  {
    // Check auth
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject('sesblog_blog')->isValid()) return;

    // Make form
    $this->view->form = $form = new Sesblog_Form_Remove();

    // Process form
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Method');
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Data');
      return;
    }

    // Process
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $checkBlogUserAdmin = Engine_Api::_()->sesblog()->checkBlogUserAdmin($subject->getIdentity());
    try {
      $checkBlogUserAdmin->delete();
      $getAllBlogAdmins = Engine_Api::_()->getDbTable('roles', 'sesblog')->getAllBlogAdmins(array('blog_id' => $subject->getIdentity()));
      foreach ($getAllBlogAdmins as $getAllBlogAdmin) {
        //Notification Work for admin
        $owner = Engine_Api::_()->getItem('user', $getAllBlogAdmin->user_id);
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $subject, 'sesblog_removeadminre');
      }
    } catch (Exception $e) {

    }

    $this->view->status = true;
    $this->view->error = false;
    $message = Zend_Registry::get('Zend_Translate')->_('You have successfully remove as admin to the blog %s');
    $message = sprintf($message, $subject->__toString());
    $this->view->message = $message;

    if (null === $this->_helper->contextSwitch->getCurrentContext()) {
      return $this->_forward('success', 'utility', 'core', array(
        'messages' => array($message),
        'layout' => 'default-simple',
        'parentRefresh' => true,
      ));
    }
  }


  //fetch user like item as per given item id .
  public function likeItemAction()
  {
    $item_id = $this->_getParam('item_id', '0');
    $item_type = $this->_getParam('item_type', '0');
    if (!$item_id || !$item_type)
      return;
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $title = $this->_getParam('title', 0);
    $this->view->title = $title == '' ? $view->translate("People Who Like This") : $title;
    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $this->view->viewmore = isset($_POST['viewmore']) ? $_POST['viewmore'] : '';
    $item = Engine_Api::_()->getItem($item_type, $item_id);
    $param['type'] = $this->view->item_type = $item_type;
    $param['id'] = $this->view->item_id = $item->getIdentity();
    $paginator = Engine_Api::_()->sesvideo()->likeItemCore($param);
    $this->view->item_id = $item_id;
    $this->view->paginator = $paginator;
    // Set item count per page and current page number
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($page);
  }

  public function browseBlogsAction()
  {

    $integrateothermodule_id = $this->_getParam('integrateothermodule_id', null);
    $page = 'sesblog_index_' . $integrateothermodule_id;
    //Render
    $this->_helper->content->setContentName($page)->setEnabled();
  }

  public function indexAction()
  {
    // Render
    $this->_helper->content->setEnabled();
  }

  //Browse Blog Contributors
  public function contributorsAction()
  {
    // Render
    $this->_helper->content->setEnabled();
  }

  public function welcomeAction()
  {
    //Render
    $this->_helper->content->setEnabled();
  }

  public function browseAction()
  {
    // Render
    $this->_helper->content->setEnabled();
  }

  public function locationsAction()
  {
    //Render
    $this->_helper->content->setEnabled();
  }

  public function claimAction()
  {

    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer || !$viewer->getIdentity())
      if (!$this->_helper->requireUser()->isValid()) return;

    if (!Engine_Api::_()->authorization()->getPermission($viewer, 'sesblog_claim', 'create') || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.claim', 1))
      return $this->_forward('requireauth', 'error', 'core');

    // Render
    $this->_helper->content->setEnabled();
  }

  public function claimRequestsAction()
  {

    $checkClaimRequest = Engine_Api::_()->getDbTable('claims', 'sesblog')->claimCount();
    if (!$checkClaimRequest)
      return $this->_forward('notfound', 'error', 'core');
    // Render
    $this->_helper->content->setEnabled();
  }

  public function tagsAction()
  {

    //if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid())
    // return;
    //Render
    $this->_helper->content->setEnabled();
  }

  public function homeAction()
  {
    //Render
   $this->_helper->content->setEnabled();
  }

  public function viewAction()
  {

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer->getIdentity();
    $id = $this->_getParam('blog_id', null);
    $this->view->blog_id = $blog_id = Engine_Api::_()->getDbtable('blogs', 'sesblog')->getBlogId($id);
    if (!Engine_Api::_()->core()->hasSubject())
      $sesblog = Engine_Api::_()->getItem('sesblog_blog', $blog_id);
    else
      $sesblog = Engine_Api::_()->core()->getSubject();

    if (!$this->_helper->requireSubject()->isValid())
      return;


    if (!$this->_helper->requireAuth()->setAuthParams($sesblog, $viewer, 'view')->isValid())
      return;



    if (!$sesblog || !$sesblog->getIdentity() || ((!$sesblog->is_approved || $sesblog->draft) && !$sesblog->isOwner($viewer)))
     {
       $viewer = Engine_Api::_()->user()->getViewer();
       if(Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sesblog_blog', 'view')!=2)
       {
         return $this->_helper->requireSubject->forward();
       }
     }

    //Privacy: networks and member level based
    if (Engine_Api::_()->authorization()->isAllowed('sesblog_blog', $sesblog->getOwner(), 'allow_levels') || Engine_Api::_()->authorization()->isAllowed('sesblog_blog', $sesblog->getOwner(), 'allow_networks')) {
      $returnValue = Engine_Api::_()->sesblog()->checkPrivacySetting($sesblog->getIdentity());
      if ($returnValue == false) {
        return $this->_forward('requireauth', 'error', 'core');
      }
    }


    // Get styles
    $table = Engine_Api::_()->getDbtable('styles', 'core');
    $select = $table->select()
      ->where('type = ?', $sesblog->getType())
      ->where('id = ?', $sesblog->getIdentity())
      ->limit();
    $row = $table->fetchRow($select);
    if (null !== $row && !empty($row->style)) {
      $this->view->headStyle()->appendStyle($row->style);
    }
    $sesblog_profilsesblogs = Zend_Registry::isRegistered('sesblog_profilsesblogs') ? Zend_Registry::get('sesblog_profilsesblogs') : null;
    if (empty($sesblog_profilsesblogs))
      return $this->_forward('notfound', 'error', 'core');
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $getmodule = Engine_Api::_()->getDbTable('modules', 'core')->getModule('core');
    if (!empty($getmodule->version) && version_compare($getmodule->version, '4.8.8') >= 0) {

      $view->doctype('XHTML1_RDFA');
      if ($sesblog->seo_title)
        $view->headTitle($sesblog->seo_title, 'SET');
      if ($sesblog->seo_keywords)
        $view->headMeta()->appendName('keywords', $sesblog->seo_keywords);
      if ($sesblog->seo_description)
        $view->headMeta()->appendName('description', $sesblog->seo_description);
    }

    if ($sesblog->style == 1)
      $page = 'sesblog_index_view_1';
    elseif ($sesblog->style == 2)
      $page = 'sesblog_index_view_2';
    elseif ($sesblog->style == 3)
      $page = 'sesblog_index_view_3';
    elseif ($sesblog->style == 4)
      $page = 'sesblog_index_view_4';

    $this->_helper->content->setContentName($page)->setEnabled();
  }

  // USER SPECIFIC METHODS
  public function manageAction()
  {

    if (!$this->_helper->requireUser()->isValid()) return;

    // Render
    $this->_helper->content
      //->setNoRender()
      ->setEnabled();

    // Prepare data
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->form = $form = new Sesblog_Form_Search();
    $this->view->canCreate = $this->_helper->requireAuth()->setAuthParams('sesblog', null, 'create')->checkRequire();

    $form->removeElement('show');

    // Populate form
    $categories = Engine_Api::_()->getDbtable('categories', 'sesblog')->getCategoriesAssoc();
    if (!empty($categories) && is_array($categories) && $form->getElement('category')) {
      $form->getElement('category')->addMultiOptions($categories);
    }
  }

  public function listAction()
  {

    // Preload info
    $this->view->viewer = Engine_Api::_()->user()->getViewer();
    $this->view->owner = $owner = Engine_Api::_()->getItem('user', $this->_getParam('user_id'));
    Engine_Api::_()->core()->setSubject($owner);

    if (!$this->_helper->requireSubject()->isValid())
      return;

    // Make form
    $form = new Sesblog_Form_Search();
    $form->populate($this->getRequest()->getParams());
    $values = $form->getValues();
    $this->view->formValues = array_filter($form->getValues());
    $values['user_id'] = $owner->getIdentity();
    $sesblog_profilsesblogs = Zend_Registry::isRegistered('sesblog_profilsesblogs') ? Zend_Registry::get('sesblog_profilsesblogs') : null;
    if (empty($sesblog_profilsesblogs))
      return $this->_forward('notfound', 'error', 'core');
    // Prepare data
    $sesblogTable = Engine_Api::_()->getDbtable('blogs', 'sesblog');

    // Get paginator
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('blogs', 'sesblog')->getSesblogsPaginator($values);
    $items_per_page = Engine_Api::_()->getApi('settings', 'core')->sesblog_page;
    $paginator->setItemCountPerPage($items_per_page);
    $this->view->paginator = $paginator->setCurrentPageNumber($values['page']);

    // Render
    $this->_helper->content
      //->setNoRender()
      ->setEnabled();
  }

  public function createAction() {
  
    if (!$this->_helper->requireUser()->isValid()) 
      return;
    
    if (!$this->_helper->requireAuth()->setAuthParams('sesblog_blog', null, 'create')->isValid()) 
      return;

    $sessmoothbox = $this->view->typesmoothbox = false;
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    
    //Start Package Work
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesblogpackage') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblogpackage.enable.package', 0)) {
      $package = Engine_Api::_()->getItem('sesblogpackage_package', $this->_getParam('package_id', 0));
      $existingpackage = Engine_Api::_()->getItem('sesblogpackage_orderspackage', $this->_getParam('existing_package_id', 0));
      if ($existingpackage) {
        $package = Engine_Api::_()->getItem('sesblogpackage_package', $existingpackage->package_id);
      }
      if (!$package && !$existingpackage) {
        //check package exists for this member level
        $packageMemberLevel = Engine_Api::_()->getDbTable('packages', 'sesblogpackage')->getPackage(array('member_level' => $viewer->level_id));
        if (count($packageMemberLevel)) {
          //redirect to package page
          return $this->_helper->redirector->gotoRoute(array('action' => 'blog'), 'sesblogpackage_general', true);
        }
      }
      if ($existingpackage) {
        $canCreate = Engine_Api::_()->getDbTable('orderspackages', 'sesblogpackage')->checkUserPackage($this->_getParam('existing_package_id', 0), $this->view->viewer()->getIdentity());
        if (!$canCreate)
          return $this->_helper->redirector->gotoRoute(array('action' => 'blog'), 'sesblogpackage_general', true);
      }
    }
    //End Package Work

    if ($this->_getParam('typesmoothbox', false)) {

      // Render
      $sessmoothbox = true;
      $this->view->typesmoothbox = true;
      $this->_helper->layout->setLayout('default-simple');
      $layoutOri = $this->view->layout()->orientation;
      if ($layoutOri == 'right-to-left') {
        $this->view->direction = 'rtl';
      } else {
        $this->view->direction = 'ltr';
      }
      $language = explode('_', $this->view->locale()->getLocale()->__toString());
      $this->view->language = $language[0];
    } else {
      $this->_helper->content->setEnabled();
    }

		if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesblogpackage') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblogpackage.enable.package', 1)){
			$package = Engine_Api::_()->getItem('sesblogpackage_package',$this->_getParam('package_id',0));
			$existingpackage = Engine_Api::_()->getItem('sesblogpackage_orderspackage',$this->_getParam('existing_package_id',0));
			if($existingpackage){
				$package = Engine_Api::_()->getItem('sesblogpackage_package',$existingpackage->package_id);
			}
			if (!$package && !$existingpackage){
				//check package exists for this member level
				$packageMemberLevel = Engine_Api::_()->getDbTable('packages','sesblogpackage')->getPackage(array('member_level'=>$viewer->level_id));
				if(count($packageMemberLevel)){
					//redirect to package page
					return $this->_helper->redirector->gotoRoute(array('action'=>'blog'), 'sesblogpackage_general', true);
				}
			}
		}
    
    $session = new Zend_Session_Namespace();
    if (empty($_POST))
      unset($session->album_id);

    $this->view->defaultProfileId = $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'sesblog')->profileFieldId();
    
    if (isset($sesblog->category_id) && $sesblog->category_id != 0) {
      $this->view->category_id = $sesblog->category_id;
    } else if (isset($_POST['category_id']) && $_POST['category_id'] != 0)
      $this->view->category_id = $_POST['category_id'];
    else
      $this->view->category_id = 0;
      
    if (isset($sesblog->subsubcat_id) && $sesblog->subsubcat_id != 0) {
      $this->view->subsubcat_id = $sesblog->subsubcat_id;
    } else if (isset($_POST['subsubcat_id']) && $_POST['subsubcat_id'] != 0)
      $this->view->subsubcat_id = $_POST['subsubcat_id'];
    else
      $this->view->subsubcat_id = 0;
      
    if (isset($sesblog->subcat_id) && $sesblog->subcat_id != 0) {
      $this->view->subcat_id = $sesblog->subcat_id;
    } else if (isset($_POST['subcat_id']) && $_POST['subcat_id'] != 0)
      $this->view->subcat_id = $_POST['subcat_id'];
    else
      $this->view->subcat_id = 0;

    $resource_id = $this->_getParam('resource_id', null);
    $resource_type = $this->_getParam('resource_type', null);

    //set up data needed to check quota
    $parentType = $this->_getParam('parent_type', null);
    if ($parentType)
      $event_id = $this->_getParam('event_id', null);

    $parentId = $this->_getParam('parent_id', 0);
    if ($parentId && !Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.subblog', 1))
      return $this->_forward('notfound', 'error', 'core');
    
    $sesblog_create = Zend_Registry::isRegistered('sesblog_create') ? Zend_Registry::get('sesblog_create') : null;
    if (empty($sesblog_create))
      return $this->_forward('notfound', 'error', 'core');
      
    $values['user_id'] = $viewer_id;
    
    $paginator = Engine_Api::_()->getDbtable('blogs', 'sesblog')->getSesblogsPaginator($values);
    $this->view->current_count = $paginator->getTotalItemCount();
    
    $this->view->quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sesblog_blog', 'max');
    
    $this->view->categories = Engine_Api::_()->getDbtable('categories', 'sesblog')->getCategoriesAssoc();

    // Prepare form
    $this->view->form = $form = new Sesblog_Form_Index_Create(array('defaultProfileId' => $defaultProfileId, 'smoothboxType' => $sessmoothbox,));

    // If not post or form not valid, return
    if (!$this->getRequest()->isPost())
      return;

    if (!$form->isValid($this->getRequest()->getPost()))
      return;

    //check custom url
    if (isset($_POST['custom_url']) && !empty($_POST['custom_url'])) {
      $custom_url = Engine_Api::_()->getDbTable('blogs', 'sesblog')->checkCustomUrl($_POST['custom_url']);
      if ($custom_url) {
        $form->addError($this->view->translate("Custom Url is not available. Please select another URL."));
        return;
      }
    }
    
    $authApi = Engine_Api::_()->authorization()->getAdapter('levels');

    // Process
    $table = Engine_Api::_()->getDbtable('blogs', 'sesblog');
    $db = $table->getAdapter();
    $db->beginTransaction();
    try {
    
      $values = array_merge($form->getValues(), array(
        'owner_type' => $viewer->getType(),
        'owner_id' => $viewer_id,
      ));
      if (isset($values['levels']))
        $values['levels'] = implode(',', $values['levels']);
      if (isset($values['networks']))
        $values['networks'] = implode(',', $values['networks']);
      $values['ip_address'] = $_SERVER['REMOTE_ADDR'];
      
      $sesblog = $table->createRow();
      
      if (is_null($values['subsubcat_id']))
        $values['subsubcat_id'] = 0;
        
      if (is_null($values['subcat_id']))
        $values['subcat_id'] = 0;
        
      if(isset($_POST['body']))
        $values['readtime'] = Engine_Api::_()->sesblog()->estimatedReadingTime(addslashes($_POST['body']));

			if(isset($package)) {
        $values['package_id'] = $package->getIdentity();
        
        $packageParams = json_decode($package->params, true);
        if ($package->isFree()) {
          $values['is_approved'] = $packageParams['blog_approve'];
          $values['featured'] = $packageParams['blog_featured'];
          $values['sponsored'] = $packageParams['blog_sponsored'];
          $values['verified'] = $packageParams['blog_verified'];
        } else {
          $values['is_approved'] = 0;
        }
        
        //Existing Package Work
        if ($existingpackage) {
          $values['existing_package_order'] = $existingpackage->getIdentity();
          $values['orderspackage_id'] = $existingpackage->getIdentity();
          $existingpackage->item_count = $existingpackage->item_count - 1;
          $existingpackage->save();
          $params = json_decode($package->params, true);
          if (isset($params['blog_approve']) && $params['blog_approve'])
            $values['is_approved'] = 1;
          if (isset($params['blog_featured']) && $params['blog_featured'])
            $values['featured'] = 1;
          if (isset($params['blog_sponsored']) && $params['blog_sponsored'])
            $values['sponsored'] = 1;
          if (isset($params['blog_verified']) && $params['blog_verified'])
            $values['verified'] = 1;
        }
			}else{
				$values['is_approved'] = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sesblog_blog', $viewer, 'blog_approve');
				if(isset($sesblog->package_id) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesblogpackage') ){
					$values['package_id'] = Engine_Api::_()->getDbTable('packages','sesblogpackage')->getDefaultPackage();
				}
			}
			

      if ($_POST['blogstyle'])
        $values['style'] = $_POST['blogstyle'];

      //SEO By Default Work
      if ($values['tags'])
        $values['seo_keywords'] = $values['tags'];

      $sesblog->setFromArray($values);

      //Upload Main Image
      if (isset($_FILES['photo_file']) && $_FILES['photo_file']['name'] != '') {
        $sesblog->photo_id = Engine_Api::_()->sesbasic()->setPhoto($form->photo_file, false, false, 'sesblog', 'sesblog_blog', '', $sesblog, true);
      }

      if (isset($_POST['start_date']) && $_POST['start_date'] != '') {
        $starttime = isset($_POST['start_date']) ? date('Y-m-d H:i:s', strtotime($_POST['start_date'] . ' ' . $_POST['start_time'])) : '';
        $sesblog->publish_date = $starttime;
      }

      if (isset($_POST['start_date']) && $viewer->timezone && $_POST['start_date'] != '') {
        //Convert Time Zone
        $oldTz = date_default_timezone_get();
        date_default_timezone_set($viewer->timezone);
        $start = strtotime($_POST['start_date'] . ' ' . $_POST['start_time']);
        date_default_timezone_set($oldTz);
        $sesblog->publish_date = date('Y-m-d H:i:s', $start);
      }

      $sesblog->parent_id = $parentId;
      
      // Save oraganization id in database by Questwalk[SR813]
      $sesblog->org_id = $this->_getParam('org_id', 0);
      $sesblog->project_id = $this->_getParam('project_id', 0);

      $sesblog->save();
      
      //Start Default Package Order Work
      if (isset($package)) {
        if (!$existingpackage) {
          $transactionsOrdersTable = Engine_Api::_()->getDbtable('orderspackages', 'sesblogpackage');
          $transactionsOrdersTable->insert(array(
              'owner_id' => $viewer->user_id,
              'item_count' => ($package->item_count - 1 ),
              'package_id' => $package->getIdentity(),
              'state' => 'active',
              'expiration_date' => $package->getExpirationDate() ? date('Y-m-d H:i:s', $package->getExpirationDate()) : '3000-00-00 00:00:00',
              'ip_address' => $_SERVER['REMOTE_ADDR'],
              'creation_date' => new Zend_Db_Expr('NOW()'),
              'modified_date' => new Zend_Db_Expr('NOW()'),
          ));
          $sesblog->orderspackage_id = $transactionsOrdersTable->getAdapter()->lastInsertId();
          $sesblog->existing_package_order = 0;
        } else {
          $existingpackage->item_count = $existingpackage->item_count--;
          $existingpackage->save();
        }
      }
      //End Default package Order Work

      $blog_id = $sesblog->blog_id;

      if (!empty($_POST['custom_url']) && $_POST['custom_url'] != '')
        $sesblog->custom_url = $_POST['custom_url'];
      else
        $sesblog->custom_url = $sesblog->blog_id;
      $sesblog->save();

      $roleTable = Engine_Api::_()->getDbtable('roles', 'sesblog');
      $row = $roleTable->createRow();
      $row->blog_id = $blog_id;
      $row->user_id = $viewer->getIdentity();
      $row->resource_approved = '1';
      $row->save();

      // Other module work
      if (!empty($resource_type) && !empty($resource_id)) {
        $sesblog->resource_id = $resource_id;
        $sesblog->resource_type = $resource_type;
        $sesblog->save();
      }
      
      if (!isset($package)) {
        if (Engine_Api::_()->authorization()->getPermission($viewer, 'sesblog_blog', 'autofeatured'))
          $sesblog->featured = 1;
        if (Engine_Api::_()->authorization()->getPermission($viewer, 'sesblog_blog', 'autosponsored'))
          $sesblog->sponsored = 1;
        if (Engine_Api::_()->authorization()->getPermission($viewer, 'sesblog_blog', 'autoverified'))
          $sesblog->verified = 1;
        $sesblog->save();
      }

      //Location
      if (isset($_POST['lat']) && isset($_POST['lng']) && $_POST['lat'] != '' && $_POST['lng'] != '' && !empty($_POST['location'])) {
        $dbGetInsert = Engine_Db_Table::getDefaultAdapter();
        $dbGetInsert->query('INSERT INTO engine4_sesbasic_locations (resource_id,venue, lat, lng ,city,state,zip,country,address,address2, resource_type) VALUES ("' . $sesblog->getIdentity() . '","' . $_POST['location'] . '", "' . $_POST['lat'] . '","' . $_POST['lng'] . '","' . $_POST['city'] . '","' . $_POST['state'] . '","' . $_POST['zip'] . '","' . $_POST['country'] . '","' . $_POST['address'] . '","' . $_POST['address2'] . '",  "sesblog_blog")	ON DUPLICATE KEY UPDATE	lat = "' . $_POST['lat'] . '" , lng = "' . $_POST['lng'] . '",city = "' . $_POST['city'] . '", state = "' . $_POST['state'] . '", country = "' . $_POST['country'] . '", zip = "' . $_POST['zip'] . '", address = "' . $_POST['address'] . '", address2 = "' . $_POST['address2'] . '", venue = "' . $_POST['venue'] . '"');
        $sesblog->location = $_POST['location'];
        $sesblog->save();
      } else {
        $dbGetInsert = Engine_Db_Table::getDefaultAdapter();
        $dbGetInsert->query('INSERT INTO engine4_sesbasic_locations (resource_id,venue, lat, lng ,city,state,zip,country,address,address2, resource_type) VALUES ("' . $sesblog->getIdentity() . '","' . $_POST['location'] . '", "' . $_POST['lat'] . '","' . $_POST['lng'] . '","' . $_POST['city'] . '","' . $_POST['state'] . '","' . $_POST['zip'] . '","' . $_POST['country'] . '","' . $_POST['address'] . '","' . $_POST['address2'] . '",  "sesblog_blog")	ON DUPLICATE KEY UPDATE	lat = "' . $_POST['lat'] . '" , lng = "' . $_POST['lng'] . '",city = "' . $_POST['city'] . '", state = "' . $_POST['state'] . '", country = "' . $_POST['country'] . '", zip = "' . $_POST['zip'] . '", address = "' . $_POST['address'] . '", address2 = "' . $_POST['address2'] . '", venue = "' . $_POST['venue'] . '"');
        $sesblog->location = $_POST['location'];
        $sesblog->save();
      }

      if ($parentType == 'sesevent_blog') {
        $sesblog->parent_type = $parentType;
        $sesblog->event_id = $event_id;
        $sesblog->save();
        $seseventblog = Engine_Api::_()->getDbtable('mapevents', 'sesblog')->createRow();
        $seseventblog->event_id = $event_id;
        $seseventblog->blog_id = $blog_id;
        $seseventblog->save();
      }

      if (isset ($_POST['cover']) && !empty($_POST['cover'])) {
        $sesblog->photo_id = $_POST['cover'];
        $sesblog->save();
      }

      $customfieldform = $form->getSubForm('fields');
      if (!is_null($customfieldform)) {
        $customfieldform->setItem($sesblog);
        $customfieldform->saveValues();
      }

      // Auth
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

      if (empty($values['auth_view']))
        $values['auth_view'] = 'everyone';
      if (empty($values['auth_comment']))
        $values['auth_comment'] = 'everyone';
      if (empty($values['auth_video']))
        $values['auth_video'] = 'everyone';
      if (empty($values['auth_music']))
        $values['auth_music'] = 'everyone';

      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);
      $videoMax = array_search(isset($values['auth_video']) ? $values['auth_video'] : '', $roles);
      $musicMax = array_search(isset($values['auth_music']) ? $values['auth_music'] : '', $roles);
      foreach ($roles as $i => $role) {
        $auth->setAllowed($sesblog, $role, 'view', ($i <= $viewMax));
        $auth->setAllowed($sesblog, $role, 'comment', ($i <= $commentMax));
        $auth->setAllowed($sesblog, $role, 'video', ($i <= $videoMax));
        $auth->setAllowed($sesblog, $role, 'music', ($i <= $musicMax));
      }

      // Add tags
      $tags = preg_split('/[,]+/', $values['tags']);  
      $sesblog->tags()->addTagMaps($viewer, $tags);
      
      $sesblog->body = isset($_POST['body']) ? $_POST['body'] : null;
      $sesblog->save();

      $session = new Zend_Session_Namespace();
      if (!empty($session->album_id)) {
        $album_id = $session->album_id;
        if (isset($blog_id) && isset($sesblog->title)) {
          Engine_Api::_()->getDbTable('albums', 'sesblog')->update(array('blog_id' => $blog_id, 'owner_id' => $viewer->getIdentity(), 'title' => $sesblog->title), array('album_id = ?' => $album_id));
          if (isset ($_POST['cover']) && !empty($_POST['cover'])) {
            Engine_Api::_()->getDbTable('albums', 'sesblog')->update(array('photo_id' => $_POST['cover']), array('album_id = ?' => $album_id));
          }
          Engine_Api::_()->getDbTable('photos', 'sesblog')->update(array('blog_id' => $blog_id), array('album_id = ?' => $album_id));
          unset($session->album_id);
        }
      }

      // Add activity only if sesblog is published
      if ($values['draft'] == 0 && $values['is_approved'] == 1 && (!$sesblog->publish_date || strtotime($sesblog->publish_date) <= time())) {
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $sesblog, 'sesblog_new');
        // make sure action exists before attaching the sesblog to the activity
        if ($action)
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $sesblog);

        //Tag Work
        if ($action && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesadvancedactivity') && $tags) {
          $dbGetInsert = Engine_Db_Table::getDefaultAdapter();
          foreach ($tags as $tag) {
            $dbGetInsert->query('INSERT INTO `engine4_sesadvancedactivity_hashtags` (`action_id`, `title`) VALUES ("' . $action->getIdentity() . '", "' . $tag . '")');
          }
        }

        //Send notifications for subscribers
        Engine_Api::_()->getDbtable('subscriptions', 'sesblog')->sendNotifications($sesblog);
        $sesblog->is_publish = 1;
        $sesblog->save();
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $autoOpenSharePopup = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.autoopenpopup', 1);
    if ($autoOpenSharePopup && $sesblog->draft && $sesblog->is_approved) {
      $_SESSION['newPage'] = true;
    }

    $redirect = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.redirect.creation', 0);
    if ($parentType) {
      $eventCustom_url = Engine_Api::_()->getItem('sesevent_event', $event_id)->custom_url;
      return $this->_helper->redirector->gotoRoute(array('id' => $eventCustom_url), 'sesevent_profile', true);
    } else if (!empty($resource_id) && !empty($resource_type)) {
      // Other module work
      $resource = Engine_Api::_()->getItem($resource_type, $resource_id);
      header('location:' . $resource->getHref());
      die;
    } else if ($redirect) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'dashboard', 'action' => 'edit', 'blog_id' => $sesblog->custom_url), 'sesblog_dashboard', true);
    } else {
      return $this->_helper->redirector->gotoRoute(array('action' => 'view', 'blog_id' => $sesblog->custom_url), 'sesblog_entry_view', true);
    }
  }

  function likeAction()
  {

    if (Engine_Api::_()->user()->getViewer()->getIdentity() == 0) {
      echo json_encode(array('status' => 'false', 'error' => 'Login'));
      die;
    }

    $type = 'sesblog_blog';
    $dbTable = 'blogs';
    $resorces_id = 'blog_id';
    $notificationType = 'liked';
    $actionType = 'sesblog_blog_like';

    if ($this->_getParam('type', false) && $this->_getParam('type') == 'sesblog_album') {
      $type = 'sesblog_album';
      $dbTable = 'albums';
      $resorces_id = 'album_id';
      $actionType = 'sesblog_album_like';
    } else if ($this->_getParam('type', false) && $this->_getParam('type') == 'sesblog_photo') {
      $type = 'sesblog_photo';
      $dbTable = 'photos';
      $resorces_id = 'photo_id';
      $actionType = 'sesblog_photo_like';
    }

    $item_id = $this->_getParam('id');
    if (intval($item_id) == 0) {
      echo json_encode(array('status' => 'false', 'error' => 'Invalid argument supplied.'));
      die;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    $itemTable = Engine_Api::_()->getDbtable($dbTable, 'sesblog');
    $tableLike = Engine_Api::_()->getDbtable('likes', 'core');
    $tableMainLike = $tableLike->info('name');

    $select = $tableLike->select()
      ->from($tableMainLike)
      ->where('resource_type = ?', $type)
      ->where('poster_id = ?', $viewer_id)
      ->where('poster_type = ?', 'user')
      ->where('resource_id = ?', $item_id);
    $result = $tableLike->fetchRow($select);

    if (count($result) > 0) {
      //delete
      $db = $result->getTable()->getAdapter();
      $db->beginTransaction();
      try {
        $result->delete();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $item = Engine_Api::_()->getItem($type, $item_id);
      echo json_encode(array('status' => 'true', 'error' => '', 'condition' => 'reduced', 'count' => $item->like_count));
      die;
    } else {

      //update
      $db = Engine_Api::_()->getDbTable('likes', 'core')->getAdapter();
      $db->beginTransaction();
      try {

        $like = $tableLike->createRow();
        $like->poster_id = $viewer_id;
        $like->resource_type = $type;
        $like->resource_id = $item_id;
        $like->poster_type = 'user';
        $like->save();

        $itemTable->update(array('like_count' => new Zend_Db_Expr('like_count + 1')), array($resorces_id . '= ?' => $item_id));

        //Commit
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      //Send notification and activity feed work.
      $item = Engine_Api::_()->getItem($type, $item_id);
      $subject = $item;
      $owner = $subject->getOwner();
      if ($owner->getType() == 'user' && $owner->getIdentity() != $viewer->getIdentity()) {
        $activityTable = Engine_Api::_()->getDbtable('actions', 'activity');
        Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type =?' => $notificationType, "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $subject->getType(), "object_id = ?" => $subject->getIdentity()));
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $subject, $notificationType);
        $result = $activityTable->fetchRow(array('type =?' => $actionType, "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $subject->getType(), "object_id = ?" => $subject->getIdentity()));

        if (!$result) {
          if ($subject && empty($subject->title) && $this->_getParam('type') == 'sesblog_photo') {
            $album_id = $subject->album_id;
            $subject = Engine_Api::_()->getItem('sesblog_album', $album_id);
          }
          $action = $activityTable->addActivity($viewer, $subject, $actionType);
          if ($action)
            $activityTable->attachActivity($action, $subject);
        }
      }
      echo json_encode(array('status' => 'true', 'error' => '', 'condition' => 'increment', 'count' => $item->like_count));
      die;
    }
  }


  //item favourite as per item tye given
  function favouriteAction()
  {
    if (Engine_Api::_()->user()->getViewer()->getIdentity() == 0) {
      echo json_encode(array('status' => 'false', 'error' => 'Login'));
      die;
    }
    if ($this->_getParam('type') == 'sesblog_blog') {
      $type = 'sesblog_blog';
      $dbTable = 'blogs';
      $resorces_id = 'blog_id';
      $notificationType = 'sesblog_blog_favourite';
    } else if ($this->_getParam('type') == 'sesblog_photo') {
      $type = 'sesblog_photo';
      $dbTable = 'photos';
      $resorces_id = 'photo_id';
      // $notificationType = 'sesevent_favourite_playlist';
    } else if ($this->_getParam('type') == 'sesblog_album') {
      $type = 'sesblog_album';
      $dbTable = 'albums';
      $resorces_id = 'album_id';
      // $notificationType = 'sesevent_favourite_playlist';
    }
    $item_id = $this->_getParam('id');
    if (intval($item_id) == 0) {
      echo json_encode(array('status' => 'false', 'error' => 'Invalid argument supplied.'));
      die;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $Fav = Engine_Api::_()->getDbTable('favourites', 'sesblog')->getItemfav($type, $item_id);

    $favItem = Engine_Api::_()->getDbtable($dbTable, 'sesblog');
    if (count($Fav) > 0) {
      //delete
      $db = $Fav->getTable()->getAdapter();
      $db->beginTransaction();
      try {
        $Fav->delete();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $favItem->update(array('favourite_count' => new Zend_Db_Expr('favourite_count - 1')), array($resorces_id . ' = ?' => $item_id));
      $item = Engine_Api::_()->getItem($type, $item_id);
      if (@$notificationType) {
        Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type =?' => $notificationType, "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $item->getType(), "object_id = ?" => $item->getIdentity()));
        Engine_Api::_()->getDbtable('actions', 'activity')->delete(array('type =?' => $notificationType, "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $item->getType(), "object_id = ?" => $item->getIdentity()));
        Engine_Api::_()->getDbtable('actions', 'activity')->detachFromActivity($item);
      }
      echo json_encode(array('status' => 'true', 'error' => '', 'condition' => 'reduced', 'count' => $item->favourite_count));
      $this->view->favourite_id = 0;
      die;
    } else {
      //update
      $db = Engine_Api::_()->getDbTable('favourites', 'sesblog')->getAdapter();
      $db->beginTransaction();
      try {
        $fav = Engine_Api::_()->getDbTable('favourites', 'sesblog')->createRow();
        $fav->user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $fav->resource_type = $type;
        $fav->resource_id = $item_id;
        $fav->save();
        $favItem->update(array('favourite_count' => new Zend_Db_Expr('favourite_count + 1'),
        ), array(
          $resorces_id . '= ?' => $item_id,
        ));
        // Commit
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      //send notification and activity feed work.
      $item = Engine_Api::_()->getItem(@$type, @$item_id);
      if (@$notificationType) {
        $subject = $item;
        $owner = $subject->getOwner();
        if ($owner->getType() == 'user' && $owner->getIdentity() != $viewer->getIdentity() && @$notificationType) {
          $activityTable = Engine_Api::_()->getDbtable('actions', 'activity');
          Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type =?' => $notificationType, "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $subject->getType(), "object_id = ?" => $subject->getIdentity()));
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $subject, $notificationType);
          $result = $activityTable->fetchRow(array('type =?' => $notificationType, "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $subject->getType(), "object_id = ?" => $subject->getIdentity()));
          if (!$result) {
            $action = $activityTable->addActivity($viewer, $subject, $notificationType);
            if ($action)
              $activityTable->attachActivity($action, $subject);
          }
        }
      }
      $this->view->favourite_id = 1;
      echo json_encode(array('status' => 'true', 'error' => '', 'condition' => 'increment', 'count' => $item->favourite_count, 'favourite_id' => 1));
      die;
    }
  }
  
  public function cancelClaimRequestAction() {
  
    $claim = Engine_Api::_()->getItem('sesblog_claim', $this->getRequest()->getParam('claim_id'));

    // In smoothbox
    $this->_helper->layout->setLayout('default-simple');

    $this->view->form = $form = new Sesblog_Form_Claim_Delete();

    if (!$claim) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Sesblog entry doesn't exist or not authorized to delete");
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $db = $claim->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $claim->delete();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('You have been successfully cancel claim request.');
    return $this->_forward('success', 'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'sesblog_general', true),
      'messages' => Array($this->view->message)
    ));
  }

  public function deleteAction()
  {

    $sesblog = Engine_Api::_()->getItem('sesblog_blog', $this->getRequest()->getParam('blog_id'));
    if (!$this->_helper->requireAuth()->setAuthParams($sesblog, null, 'delete')->isValid()) return;

    // In smoothbox
    $this->_helper->layout->setLayout('default-simple');

    $this->view->form = $form = new Sesblog_Form_Delete();

    if (!$sesblog) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Sesblog entry doesn't exist or not authorized to delete");
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $db = $sesblog->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      Engine_Api::_()->sesblog()->deleteBlog($sesblog);;

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your sesblog entry has been deleted.');
    return $this->_forward('success', 'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'sesblog_general', true),
      'messages' => Array($this->view->message)
    ));
  }

  public function styleAction()
  {

    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireAuth()->setAuthParams('sesblog_blog', null, 'style')->isValid()) return;

    // In smoothbox
    $this->_helper->layout->setLayout('default-simple');

    // Require user
    if (!$this->_helper->requireUser()->isValid()) return;
    $user = Engine_Api::_()->user()->getViewer();

    // Make form
    $this->view->form = $form = new Sesblog_Form_Style();

    // Get current row
    $table = Engine_Api::_()->getDbtable('styles', 'core');
    $select = $table->select()
      ->where('type = ?', 'user_sesblog') // @todo this is not a real type
      ->where('id = ?', $user->getIdentity())
      ->limit(1);

    $row = $table->fetchRow($select);

    // Check post
    if (!$this->getRequest()->isPost()) {
      $form->populate(array(
        'style' => (null === $row ? '' : $row->style)
      ));
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) return;


    // Cool! Process
    $style = $form->getValue('style');

    // Save
    if (null == $row) {
      $row = $table->createRow();
      $row->type = 'user_sesblog'; // @todo this is not a real type
      $row->id = $user->getIdentity();
    }

    $row->style = $style;
    $row->save();

    $this->view->draft = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_("Your changes have been saved.");
    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => true,
      'parentRefresh' => false,
      'messages' => array($this->view->message)
    ));
  }

  public function linkBlogAction()
  {

    // In smoothbox
    $this->_helper->layout->setLayout('default-simple');
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->blog_id = $blog_id = $this->_getParam('blog_id', '0');
    if ($blog_id == 0)
      return;
    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $this->view->viewmore = isset($_POST['viewmore']) ? $_POST['viewmore'] : '';

    $eventTable = Engine_Api::_()->getItemTable('sesevent_event');
    $eventTableName = $eventTable->info('name');
    $blogMapTable = Engine_Api::_()->getDbTable('mapevents', 'sesblog');
    $blogMapTableName = $blogMapTable->info('name');
    $select = $eventTable->select()
      ->setIntegrityCheck(false)
      ->from($eventTableName)
      ->Joinleft($blogMapTableName, "$blogMapTableName.event_id = $eventTableName.event_id", null)
      ->where($eventTableName . '.event_id !=?', '')
      ->where($blogMapTableName . '.blog_id !=? or blog_id is null', $blog_id);

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    // Set item count per page and current page number
    $paginator->setItemCountPerPage(20);
    $paginator->setCurrentPageNumber($page);

    if (!$this->getRequest()->isPost())
      return;

    $eventIds = $_POST['event'];
    $blogObject = Engine_Api::_()->getItem('sesblog_blog', $blog_id);
    foreach ($eventIds as $eventId) {
      $item = Engine_Api::_()->getItem('sesevent_event', $eventId);
      $owner = $item->getOwner();
      $table = Engine_Api::_()->getDbtable('mapevents', 'sesblog');
      $db = $table->getAdapter();
      $db->beginTransaction();
      try {
        $seseventblog = $table->createRow();
        $seseventblog->event_id = $eventId;
        $seseventblog->blog_id = $blog_id;
        $seseventblog->request_owner_blog = 1;
        $seseventblog->approved = 0;
        $seseventblog->save();
        $blogPageLink = '<a href="' . $blogObject->getHref() . '">' . ucfirst($blogObject->getTitle()) . '</a>';
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $item, 'sesblog_link_event', array("blogPageLink" => $blogPageLink));


        // Commit
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
    $this->view->message = Zend_Registry::get('Zend_Translate')->_("Your changes have been saved.");
    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => true,
      'parentRefresh' => false,
      'messages' => array($this->view->message)
    ));
  }

  public function blogRequestAction()
  {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('sesblog_main');

    $BlogTable = Engine_Api::_()->getDbtable('blogs', 'sesblog');
    $BlogTableName = $BlogTable->info('name');
    $mapBlogTable = Engine_Api::_()->getDbtable('mapevents', 'sesblog');
    $mapBlogTableName = $mapBlogTable->info('name');
    $viewerId = Engine_Api::_()->user()->getViewer()->getIdentity();
    $select = $BlogTable->select()
      ->setIntegrityCheck(false)
      ->from($BlogTableName, array('owner_id', 'blog_id'))
      ->Joinleft($mapBlogTableName, "$mapBlogTableName.blog_id = $BlogTableName.blog_id", array('event_id', 'approved'))
      ->where($BlogTableName . '.owner_id =?', $viewerId)
      ->where($mapBlogTableName . '.approved =?', 0)
      ->where($mapBlogTableName . '.request_owner_event =? and request_owner_event IS NOT null', 1);
    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($page);
    $paginator->setItemCountPerPage(10);
  }

  public function approvedAction()
  {

    $blog_id = $this->_getParam('blog_id');
    $event_id = $this->_getParam('event_id');
    $mapBlogTable = Engine_Api::_()->getDbtable('mapevents', 'sesblog');
    $selectMapTable = $mapBlogTable->select()->where('event_id =?', $event_id)->where('blog_id =?', $blog_id)->where('request_owner_event =?', 1);
    $mapResult = $mapBlogTable->fetchRow($selectMapTable);
    if (!empty($blog_id)) {
      $blog = Engine_Api::_()->getItem('sesblog_blog', $event_id);
      if (!$mapResult->approved)
        $approved = 1;
      else
        $approved = 0;

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->update('engine4_sesblog_mapevents', array(
        'approved' => $approved,
      ), array(
        'event_id = ?' => $event_id,
        'blog_id = ?' => $blog_id,
      ));
    }
    $this->_redirect('sesblogs/blog-request');
  }

  public function rejectRequestAction()
  {

    $viewer = Engine_Api::_()->user()->getViewer();
    $blog_id = $this->_getParam('blog_id');
    $blogObject = Engine_Api::_()->getItem('sesblog_blog', $blog_id);
    $event_id = $this->_getParam('event_id');
    $eventObject = Engine_Api::_()->getItem('sesevent_event', $event_id);
    $owner = $eventObject->getOwner();
    $mapBlogTable = Engine_Api::_()->getDbtable('mapevents', 'sesblog');
    $selectMapTable = $mapBlogTable->select()->where('event_id =?', $event_id)->where('blog_id =?', $blog_id)->where('request_owner_event =?', 1);
    $mapResult = $mapBlogTable->fetchRow($selectMapTable);
    $db = $mapResult->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      $mapResult->delete();
      $blogPageLink = '<a href="' . $blogObject->getHref() . '">' . ucfirst($blogObject->getTitle()) . '</a>';
      Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $eventObject, 'sesblog_reject_event_request', array("blogPageLink" => $blogPageLink));
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('sesblogs/blog-request');
  }

  public function subcategoryAction()
  {

    $category_id = $this->_getParam('category_id', null);
    $CategoryType = $this->_getParam('type', null);
    if ($category_id) {
      $categoryTable = Engine_Api::_()->getDbtable('categories', 'sesblog');
      $category_select = $categoryTable->select()
        ->from($categoryTable->info('name'))
        ->where('subcat_id = ?', $category_id);
      $subcategory = $categoryTable->fetchAll($category_select);
      $count_subcat = count($subcategory->toarray());
      if (isset($_POST['selected']))
        $selected = $_POST['selected'];
      else
        $selected = '';
      $data = '';
      if ($subcategory && $count_subcat) {
        if ($CategoryType == 'search') {
          $data .= '<option value="0">' . Zend_Registry::get('Zend_Translate')->_("Choose 2nd Level Category") . '</option>';
          foreach ($subcategory as $category) {
            $data .= '<option ' . ($selected == $category['category_id'] ? 'selected = "selected"' : '') . ' value="' . $category["category_id"] . '" >' . Zend_Registry::get('Zend_Translate')->_($category["category_name"]) . '</option>';
          }
        } else {
          //$data .= '<option value="0">' . Zend_Registry::get('Zend_Translate')->_("Choose 2nd Level Category") . '</option>';
          $data .= '<option value=""></option>';
          foreach ($subcategory as $category) {
            $data .= '<option ' . ($selected == $category['category_id'] ? 'selected = "selected"' : '') . ' value="' . $category["category_id"] . '" >' . Zend_Registry::get('Zend_Translate')->_($category["category_name"]) . '</option>';
          }

        }
      }
    } else
      $data = '';
    echo $data;
    die;
  }

  public function subsubcategoryAction()
  {

    $category_id = $this->_getParam('subcategory_id', null);
    $CategoryType = $this->_getParam('type', null);
    if ($category_id) {
      $categoryTable = Engine_Api::_()->getDbtable('categories', 'sesblog');
      $category_select = $categoryTable->select()
        ->from($categoryTable->info('name'))
        ->where('subsubcat_id = ?', $category_id);
      $subcategory = $categoryTable->fetchAll($category_select);
      $count_subcat = count($subcategory->toarray());
      if (isset($_POST['selected']))
        $selected = $_POST['selected'];
      else
        $selected = '';
      $data = '';
      if ($subcategory && $count_subcat) {
        $data .= '<option value=""></option>';
        foreach ($subcategory as $category) {
          $data .= '<option ' . ($selected == $category['category_id'] ? 'selected = "selected"' : '') . ' value="' . $category["category_id"] . '">' . Zend_Registry::get('Zend_Translate')->_($category["category_name"]) . '</option>';
        }

      }
    } else
      $data = '';
    echo $data;
    die;
  }

  public function editPhotoAction()
  {
    $this->view->photo_id = $photo_id = $this->_getParam('photo_id');
    $this->view->photo = Engine_Api::_()->getItem('sesblog_photo', $photo_id);
  }

  public function saveInformationAction()
  {

    $photo_id = $this->_getParam('photo_id');
    $title = $this->_getParam('title', null);
    $description = $this->_getParam('description', null);
    Engine_Api::_()->getDbTable('photos', 'sesblog')->update(array('title' => $title, 'description' => $description), array('photo_id = ?' => $photo_id));
  }

  public function removeAction()
  {

    if (empty($_POST['photo_id'])) die('error');
    $photo_id = (int)$this->_getParam('photo_id');
    $photo = Engine_Api::_()->getItem('sesblog_photo', $photo_id);
    $db = Engine_Api::_()->getDbTable('photos', 'sesblog')->getAdapter();
    $db->beginTransaction();
    try {
      Engine_Api::_()->getDbtable('photos', 'sesblog')->delete(array('photo_id =?' => $photo_id));
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  public function getBlogAction()
  {
    $sesdata = array();
    $value['textSearch'] = $this->_getParam('text', null);
    $value['search'] = 1;
    $value['fetchAll'] = true;
    $value['getblog'] = true;
    $blogs = Engine_Api::_()->getDbtable('blogs', 'sesblog')->getSesblogsSelect($value);
    foreach ($blogs as $blog) {
      $video_icon = $this->view->itemPhoto($blog, 'thumb.icon');
      $sesdata[] = array(
        'id' => $blog->blog_id,
        'blog_id' => $blog->blog_id,
        'label' => $blog->title,
        'photo' => $video_icon
      );
    }
    return $this->_helper->json($sesdata);
  }

  public function shareAction()
  {

    if (!$this->_helper->requireUser()->isValid())
      return;
    $type = $this->_getParam('type');
    $id = $this->_getParam('id');
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->attachment = $attachment = Engine_Api::_()->getItem($type, $id);
    if (empty($_POST['is_ajax']))
      $this->view->form = $form = new Activity_Form_Share();
    if (!$attachment) {
      // tell smoothbox to close
      $this->view->status = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('You cannot share this item because it has been removed.');
      $this->view->smoothboxClose = true;
      return $this->render('deletedItem');
    }
    // hide facebook and twitter option if not logged in
    $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
    if (!$facebookTable->isConnected() && empty($_POST['is_ajax'])) {
      $form->removeElement('post_to_facebook');
    }
    $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
    if (!$twitterTable->isConnected() && empty($_POST['is_ajax'])) {
      $form->removeElement('post_to_twitter');
    }
    if (empty($_POST['is_ajax']) && !$this->getRequest()->isPost()) {
      return;
    }
    if (empty($_POST['is_ajax']) && !$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    // Process
    $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
    $db->beginTransaction();
    try {
      // Get body
      if (empty($_POST['is_ajax']))
        $body = $form->getValue('body');
      else
        $body = '';
      // Set Params for Attachment
      $params = array(
        'type' => '<a href="' . $attachment->getHref() . '">' . $attachment->getMediaType() . '</a>',
      );
      // Add activity
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      //$action = $api->addActivity($viewer, $viewer, 'post_self', $body);
      $action = $api->addActivity($viewer, $attachment->getOwner(), 'share', $body, $params);
      if ($action) {
        $api->attachActivity($action, $attachment);
      }
      $db->commit();
      // Notifications
      $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
      // Add notification for owner of activity (if user and not viewer)
      if ($action->subject_type == 'user' && $attachment->getOwner()->getIdentity() != $viewer->getIdentity()) {
        $notifyApi->addNotification($attachment->getOwner(), $viewer, $action, 'shared', array(
          'label' => $attachment->getMediaType(),
        ));
      }
      // Preprocess attachment parameters
      if (empty($_POST['is_ajax']))
        $publishMessage = html_entity_decode($form->getValue('body'));
      else
        $publishMessage = '';
      $publishUrl = null;
      $publishName = null;
      $publishDesc = null;
      $publishPicUrl = null;
      // Add attachment
      if ($attachment) {
        $publishUrl = $attachment->getHref();
        $publishName = $attachment->getTitle();
        $publishDesc = $attachment->getDescription();
        if (empty($publishName)) {
          $publishName = ucwords($attachment->getShortType());
        }
        if (($tmpPicUrl = $attachment->getPhotoUrl())) {
          $publishPicUrl = $tmpPicUrl;
        }
        // prevents OAuthException: (#100) FBCDN image is not allowed in stream
        if ($publishPicUrl &&
          preg_match('/fbcdn.net$/i', parse_url($publishPicUrl, PHP_URL_HOST))) {
          $publishPicUrl = null;
        }
      } else {
        $publishUrl = $action->getHref();
      }
      // Check to ensure proto/host
      if ($publishUrl &&
        false === stripos($publishUrl, 'http://') &&
        false === stripos($publishUrl, 'https://')) {
        $publishUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishUrl;
      }
      if ($publishPicUrl &&
        false === stripos($publishPicUrl, 'http://') &&
        false === stripos($publishPicUrl, 'https://')) {
        $publishPicUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishPicUrl;
      }
      // Add site title
      if ($publishName) {
        $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title
          . ": " . $publishName;
      } else {
        $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title;
      }
      // Publish to facebook, if checked & enabled
      if ($this->_getParam('post_to_facebook', false) &&
        'publish' == Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable) {
        try {
          $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
          $facebookApi = $facebook = $facebookTable->getApi();
          $fb_uid = $facebookTable->find($viewer->getIdentity())->current();
          if ($fb_uid &&
            $fb_uid->facebook_uid &&
            $facebookApi &&
            $facebookApi->getUser() &&
            $facebookApi->getUser() == $fb_uid->facebook_uid) {
            $fb_data = array(
              'message' => $publishMessage,
            );
            if ($publishUrl) {
              $fb_data['link'] = $publishUrl;
            }
            if ($publishName) {
              $fb_data['name'] = $publishName;
            }
            if ($publishDesc) {
              $fb_data['description'] = $publishDesc;
            }
            if ($publishPicUrl) {
              $fb_data['picture'] = $publishPicUrl;
            }
            $res = $facebookApi->api('/me/feed', 'POST', $fb_data);
          }
        } catch (Exception $e) {
          // Silence
        }
      } // end Facebook
      // Publish to twitter, if checked & enabled
      if ($this->_getParam('post_to_twitter', false) &&
        'publish' == Engine_Api::_()->getApi('settings', 'core')->core_twitter_enable) {
        try {
          $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
          if ($twitterTable->isConnected()) {
            // Get attachment info
            $title = $attachment->getTitle();
            $url = $attachment->getHref();
            $picUrl = $attachment->getPhotoUrl();
            // Check stuff
            if ($url && false === stripos($url, 'http://')) {
              $url = 'http://' . $_SERVER['HTTP_HOST'] . $url;
            }
            if ($picUrl && false === stripos($picUrl, 'http://')) {
              $picUrl = 'http://' . $_SERVER['HTTP_HOST'] . $picUrl;
            }
            // Try to keep full message
            // @todo url shortener?
            $message = html_entity_decode($form->getValue('body'));
            if (strlen($message) + strlen($title) + strlen($url) + strlen($picUrl) + 9 <= 140) {
              if ($title) {
                $message .= ' - ' . $title;
              }
              if ($url) {
                $message .= ' - ' . $url;
              }
              if ($picUrl) {
                $message .= ' - ' . $picUrl;
              }
            } else if (strlen($message) + strlen($title) + strlen($url) + 6 <= 140) {
              if ($title) {
                $message .= ' - ' . $title;
              }
              if ($url) {
                $message .= ' - ' . $url;
              }
            } else {
              if (strlen($title) > 24) {
                $title = Engine_String::substr($title, 0, 21) . '...';
              }
              // Sigh truncate I guess
              if (strlen($message) + strlen($title) + strlen($url) + 9 > 140) {
                $message = Engine_String::substr($message, 0, 140 - (strlen($title) + strlen($url) + 9)) - 3 . '...';
              }
              if ($title) {
                $message .= ' - ' . $title;
              }
              if ($url) {
                $message .= ' - ' . $url;
              }
            }
            $twitter = $twitterTable->getApi();
            $twitter->statuses->update($message);
          }
        } catch (Exception $e) {
          // Silence
        }
      }
      // Publish to janrain
      if (//$this->_getParam('post_to_janrain', false) &&
        'publish' == Engine_Api::_()->getApi('settings', 'core')->core_janrain_enable) {
        try {
          $session = new Zend_Session_Namespace('JanrainActivity');
          $session->unsetAll();
          $session->message = $publishMessage;
          $session->url = $publishUrl ? $publishUrl : 'http://' . $_SERVER['HTTP_HOST'] . _ENGINE_R_BASE;
          $session->name = $publishName;
          $session->desc = $publishDesc;
          $session->picture = $publishPicUrl;
        } catch (Exception $e) {
          // Silence
        }
      }
    } catch (Exception $e) {
      $db->rollBack();
      throw $e; // This should be caught by error handler
    }
    // If we're here, we're done
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Success!');
    $typeItem = ucwords(str_replace(array('sesblog_'), '', $attachment->getType()));
    // Redirect if in normal context
    if (null === $this->_helper->contextSwitch->getCurrentContext()) {
      $return_url = $form->getValue('return_url', false);
      if (!$return_url) {
        $return_url = $this->view->url(array(), 'default', true);
      }
      return $this->_helper->redirector->gotoUrl($return_url, array('prependBase' => false));
    } else if ('smoothbox' === $this->_helper->contextSwitch->getCurrentContext()) {
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => false,
        'messages' => array($typeItem . ' share successfully.')
      ));
    } else if (isset($_POST['is_ajax'])) {
      echo "true";
      die();
    }
  }

  public function locationAction()
  {

    $this->view->type = $this->_getParam('type', 'blog');
    $this->view->blog_id = $blog_id = $this->_getParam('blog_id');
    $this->view->blog = $blog = Engine_Api::_()->getItem('sesblog_blog', $blog_id);
    if (!$blog)
      return;
    $this->view->form = $form = new Sesblog_Form_Location();
    $form->populate($blog->toArray());
  }

  public function customUrlCheckAction()
  {
    $value = $this->sanitize($this->_getParam('value', null));
    if (!$value) {
      echo json_encode(array('error' => true));
      die;
    }
    $blog_id = $this->_getParam('blog_id', null);
    $custom_url = Engine_Api::_()->getDbtable('blogs', 'sesblog')->checkCustomUrl($value, $blog_id);
    if ($custom_url) {
      echo json_encode(array('error' => true, 'value' => $value));
      die;
    } else {
      echo json_encode(array('error' => false, 'value' => $value));
      die;
    }
  }

  function sanitize($string, $force_lowercase = true, $anal = false)
  {
    $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
      "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
      "â€”", "â€“", ",", "<", ".", ">", "/", "?");
    $clean = trim(str_replace($strip, "", strip_tags($string)));
    $clean = preg_replace('/\s+/', "-", $clean);
    $clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean;
    return ($force_lowercase) ?
      (function_exists('mb_strtolower')) ?
        mb_strtolower($clean, 'UTF-8') :
        strtolower($clean) :
      $clean;
  }

  public function getBlogsAction()
  {
    $sesdata = array();
    $viewerId = Engine_Api::_()->user()->getViewer()->getIdentity();
    $blogTable = Engine_Api::_()->getDbtable('blogs', 'sesblog');
    $blogTableName = $blogTable->info('name');
    $blogClaimTable = Engine_Api::_()->getDbtable('claims', 'sesblog');
    $blogClaimTableName = $blogClaimTable->info('name');
    $text = $this->_getParam('text', null);
    $selectClaimTable = $blogClaimTable->select()
      ->from($blogClaimTableName, 'blog_id')
      ->where('user_id =?', $viewerId);
    $claimedBlogs = $blogClaimTable->fetchAll($selectClaimTable);

    $currentTime = date('Y-m-d H:i:s');
    $select = $blogTable->select()
      ->where('draft =?', 0)
      ->where("publish_date <= '$currentTime' OR publish_date = ''")
      ->where('owner_id !=?', $viewerId)
      ->where($blogTableName . '.title  LIKE ? ', '%' . $text . '%');
    if (count($claimedBlogs) > 0)
      $select->where('blog_id NOT IN(?)', $selectClaimTable);
    $select->order('blog_id ASC')->limit('40');
    $blogs = $blogTable->fetchAll($select);
    foreach ($blogs as $blog) {
      $blog_icon_photo = $this->view->itemPhoto($blog, 'thumb.icon');
      $sesdata[] = array(
        'id' => $blog->blog_id,
        'label' => $blog->title,
        'photo' => $blog_icon_photo
      );
    }
    return $this->_helper->json($sesdata);
  }

  public function rssFeedAction()
  {

    $this->_helper->layout->disableLayout();
    $value['fetchAll'] = true;
    $value['rss'] = 1;
    $value['orderby'] = 'blog_id';
    $this->view->blogs = Engine_Api::_()->getDbTable('blogs', 'sesblog')->getSesblogsSelect($value);
    $this->getResponse()->setHeader('Content-type', 'text/xml');
  }

  protected function setPhoto($photo, $id)
  {

    if ($photo instanceof Zend_Form_Element_File) {
      $file = $photo->getFileName();
      $fileName = $file;
    } else if ($photo instanceof Storage_Model_File) {
      $file = $photo->temporary();
      $fileName = $photo->name;
    } else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id)) {
      $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
      $file = $tmpRow->temporary();
      $fileName = $tmpRow->name;
    } else if (is_array($photo) && !empty($photo['tmp_name'])) {
      $file = $photo['tmp_name'];
      $fileName = $photo['name'];
    } else if (is_string($photo) && file_exists($photo)) {
      $file = $photo;
      $fileName = $photo;
    } else {
      throw new User_Model_Exception('invalid argument passed to setPhoto');
    }
    if (!$fileName) {
      $fileName = $file;
    }
    $name = basename($file);
    $extension = ltrim(strrchr($fileName, '.'), '.');
    $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => 'sesblog_blog',
      'parent_id' => $id,
      'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
      'name' => $fileName,
    );
    // Save
    $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
    $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_main.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(500, 500)
      ->write($mainPath)
      ->destroy();
    // Store
    try {
      $iMain = $filesTable->createFile($mainPath, $params);
    } catch (Exception $e) {
      // Remove temp files
      @unlink($mainPath);
      // Throw
      if ($e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE) {
        throw new Sesblog_Model_Exception($e->getMessage(), $e->getCode());
      } else {
        throw $e;
      }
    }
    // Remove temp files
    @unlink($mainPath);
    // Update row
    // Delete the old file?
    if (!empty($tmpRow)) {
      $tmpRow->delete();
    }
    return $iMain->file_id;
  }

}
