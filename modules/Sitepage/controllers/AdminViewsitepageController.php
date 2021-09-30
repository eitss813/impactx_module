<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminViewsitepageController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_AdminViewsitepageController extends Core_Controller_Action_Admin {

  //ACTION FOR MANAGE PAGES
  public function indexAction() {

    if ((Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.task.updateexpiredpages') + 900) <= time()) {
      Engine_Api::_()->sitepage()->updateExpiredPages();
    }

    //TAB CREATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_viewsitepage');

    //FORM GENERATION
    $this->view->formFilter = $formFilter = new Sitepage_Form_Admin_Manage_Filter();

    //GET PAGE ID
    $page = $this->_getParam('page', 1);

    //MAKE QUERY
    $tableUser = Engine_Api::_()->getItemTable('user')->info('name');

    $tablePage = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $tablePageName = $tablePage->info('name');

      $select = $tablePage->select()
              ->setIntegrityCheck(false)
              ->from($tablePageName)
              ->joinLeft($tableUser, "$tablePageName.owner_id = $tableUser.user_id", 'username');

    $values = array();

    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }
    foreach ($values as $key => $value) {

      if (null == $value) {
        unset($values[$key]);
      }
    }

    //SEARCHING
    $this->view->owner = '';
    $this->view->title = '';
    $this->view->sponsored = '';
    $this->view->approved = '';
    $this->view->featured = '';
    $this->view->status = '';
    $this->view->pagebrowse = '';
    $this->view->category_id = '';
    $this->view->subcategory_id = '';
    $this->view->subsubcategory_id = '';
    $this->view->package_id = '';

    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      $packageTable = Engine_Api::_()->getDbtable('packages', 'sitepage');

      $packageselect = $packageTable->select()->from($packageTable->info("name"), array("package_id", "title"))->order("package_id DESC");
      $this->view->packageList = $packageTable->fetchAll($packageselect);
    }


    $values = array_merge(array(
        'order' => 'page_id',
        'order_direction' => 'DESC',
            ), $values);


    if (!empty($_POST['owner'])) {
      $user_name = $_POST['owner'];
    } elseif (!empty($_GET['owner'])) {
      $user_name = $_GET['owner'];
    } else {
      $user_name = '';
    }


    if (!empty($_POST['title'])) {
      $page_name = $_POST['title'];
    } elseif (!empty($_GET['title'])) {
      $page_name = $_GET['title'];
    } elseif ($this->_getParam('title', '')) {
      $page_name = $this->_getParam('title', '');
    } else {
      $page_name = '';
    }

    //SEARCHING
    $this->view->owner = $values['owner'] = $user_name;
    $this->view->title = $values['title'] = $page_name;

    if (!empty($page_name)) {
      $select->where($tablePageName . '.title  LIKE ?', '%' . $page_name . '%');
    }

    if (!empty($user_name)) {
      $select->where($tableUser . '.displayname  LIKE ?', '%' . $user_name . '%');
    }

    if (isset($_POST['search'])) {

      if (!empty($_POST['sponsored'])) {
        $this->view->sponsored = $_POST['sponsored'];
        $_POST['sponsored']--;

        $select->where($tablePageName . '.sponsored = ? ', $_POST['sponsored']);
      }
      if (!empty($_POST['page_status'])) {

        $this->view->page_status = $_POST['page_status'];
        switch ($this->view->page_status) {
          case 1:
            $select->where($tablePageName . '.aprrove_date  IS NULL');
            break;
          case 2:
            $select->where($tablePageName . '.approved = ? ', 1);
            break;
          case 3:
            $select->where($tablePageName . '.aprrove_date  IS NOT NULL');
            $select->where($tablePageName . '.approved = ? ', 0);
            break;
          case 4:
            $select->where($tablePageName . '.declined  = ? ', 1);
            break;
        }
      }
      if (!empty($_POST['featured'])) {
        $this->view->featured = $_POST['featured'];
        $_POST['featured']--;
        $select->where($tablePageName . '.featured = ? ', $_POST['featured']);
      }
      if (!empty($_POST['status'])) {
        $this->view->status = $_POST['status'];
        $_POST['status']--;
        $select->where($tablePageName . '.closed = ? ', $_POST['status']);
      }

      if (!empty($_POST['package_id'])) {
        $this->view->package_id = $_POST['package_id'];
        $select->where($tablePageName . '.package_id = ? ', $_POST['package_id']);
      }
      if (!empty($_POST['pagebrowse'])) {
        $this->view->pagebrowse = $_POST['pagebrowse'];
        $_POST['pagebrowse']--;
        if ($_POST['pagebrowse'] == 0) {
          $select->order($tablePageName . '.view_count DESC');
        } elseif ($_POST['pagebrowse'] == 1) {
          $select->order($tablePageName . '.creation_date DESC');
        } elseif ($_POST['pagebrowse'] == 2) {
          $select->order($tablePageName . '.comment_count DESC');
        } elseif ($_POST['pagebrowse'] == 3) {
          $select->order($tablePageName . '.like_count DESC');
        }
      }

      if (!empty($_POST['category_id']) && empty($_POST['subcategory_id']) && empty($_POST['subsubcategory_id'])) {
        $this->view->category_id = $_POST['category_id'];
        $select->where($tablePageName . '.category_id = ? ', $_POST['category_id']);
      } elseif (!empty($_POST['category_id']) && !empty($_POST['subcategory_id']) && empty($_POST['subsubcategory_id'])) {
        $this->view->category_id = $_POST['category_id'];
        $subcategory_id = $this->view->subcategory_id = $_POST['subcategory_id'];
        $table = Engine_Api::_()->getDbtable('categories', 'sitepage');
        $categoriesName = $table->info('name');
        $selectcategory = $table->select()->from($categoriesName, 'category_name')
                ->where("(category_id = $subcategory_id)");
        $row = $table->fetchRow($selectcategory);
        if (!empty($row->category_name)) {
          $this->view->subcategory_name = $row->category_name;
        }

        $select->where($tablePageName . '.category_id = ? ', $_POST['category_id'])
                ->where($tablePageName . '.subcategory_id = ? ', $_POST['subcategory_id']);
      } elseif (!empty($_POST['category_id']) && !empty($_POST['subcategory_id']) && !empty($_POST['subsubcategory_id'])) {

        $this->view->category_id = $_POST['category_id'];
        $subcategory_id = $this->view->subcategory_id = $_POST['subcategory_id'];
        $subsubcategory_id = $this->view->subsubcategory_id = $_POST['subsubcategory_id'];

        $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($subcategory_id);
        if (!empty($row->category_name)) {
          $this->view->subcategory_name = $row->category_name;
        }
        $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($subsubcategory_id);
        if (!empty($row->category_name)) {
          $this->view->subsubcategory_name = $row->category_name;
        }
        $select->where($tablePageName . '.category_id = ? ', $_POST['category_id'])
                ->where($tablePageName . '.subcategory_id = ? ', $_POST['subcategory_id'])
                ->where($tablePageName . '.subsubcategory_id = ? ', $_POST['subsubcategory_id']);
        ;
      }
    }

    $this->view->formValues = array_filter($values);
    $this->view->assign($values);

    $select->order((!empty($values['order']) ? $values['order'] : 'page_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    //MAKE PAGINATOR
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator->setItemCountPerPage(50);
    $this->view->paginator = $paginator->setCurrentPageNumber($page);
  }

  //VIEW PAGE DETAIL
  public function detailAction() {

    $id = $this->_getParam('id');

    $tablePage = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $tablePageName = $tablePage->info('name');

    $select = $tablePage->select()
            ->setIntegrityCheck(false)
            ->from($tablePageName)
            ->where($tablePageName . '.page_id = ?', $id)
            ->limit(1);
    $this->view->sitepageDetail = $detail = $tablePage->fetchRow($select);

    $this->view->manageAdminEnabled = $manageAdminEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.manageadmin', 1);
    if (!empty($manageAdminEnabled)) {
      $manageadminTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
      $manageadminTableName = $manageadminTable->info('name');
      $select = $manageadminTable->select()
              ->from($manageadminTableName, array('COUNT(*) AS count'))
              ->where('page_id = ?', $id);
      $rows = $tablePage->fetchAll($select)->toArray();
      $this->view->admin_total = $rows[0]['count'];
    }

    $this->view->category_id = $category_id = $detail['category_id'];
    $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($category_id);
    if (!empty($row->category_name)) {
      $this->view->category_name = $row->category_name;
    }
    $this->view->subcategory_id = $subcategory_id = $detail['subcategory_id'];
    $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($subcategory_id);
    if (!empty($row->category_name)) {
      $this->view->subcategory_name = $row->category_name;
    }
    $this->view->subsubcategory_id = $subsubcategory_id = $detail['subsubcategory_id'];
    $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($subsubcategory_id);
    if (!empty($row->category_name)) {
      $this->view->subsubcategory_name = $row->category_name;
    }
    //SITEPAGE-REVIEW PLUGIN IS INSTALLED OR NOT
    $this->view->isEnabledSitepagereview = (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview');
  }

  //ACTION FOR MULTI-DELETE OF PAGES
  public function multiDeleteAction() {

    if ($this->getRequest()->isPost()) {

      $values = $this->getRequest()->getPost();

      foreach ($values as $key => $value) {

        if ($key == 'delete_' . $value) {

          //DELETE SITEPAGES FROM DATABASE
          $page_id = (int) $value;

          //START SUB PAGE WORK
          $getSubPageids = Engine_Api::_()->getDbTable('pages', 'sitepage')->getsubPageids($page_id);
          foreach ($getSubPageids as $getSubPageid) {
            Engine_Api::_()->sitepage()->onPageDelete($getSubPageid['page_id']);
          }
          //END SUB PAGE WORK

          Engine_Api::_()->sitepage()->onPageDelete($page_id);
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }

  //ACTION FOR PAGE EDIT
  public function editAction() {

    //TAB CREATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_viewsitepage');

    //GET PAGE ID AND PAGE OBJECT
    $page_id = $this->_getParam('id');
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //FORM GENERATION
    $this->view->form = $form = new Sitepage_Form_Admin_Manage_Edit();

    if (!empty($sitepage->declined)) {
      return $this->_forward('notfound', 'error', 'core');
    }

    $status_pageOption = array();
    $approved = $sitepage->approved;
    if (empty($sitepage->aprrove_date) && empty($approved)) {
      $status_pageOption["0"] = "Approval Pending";
      $status_pageOption["1"] = "Approved Page";
      $status_pageOption["2"] = "Declined Page";
    } else {
      $status_pageOption["1"] = "Approved";
      $status_pageOption["0"] = "Dis-Approved";
    }
    $form->getElement("status_page")->setMultiOptions($status_pageOption);

    if (!$this->getRequest()->isPost()) {

      $form->getElement("closed")->setValue($sitepage->closed);
      $form->getElement("status_page")->setValue($sitepage->approved);
      $form->getElement("featured")->setValue($sitepage->featured);
      $form->getElement("sponsored")->setValue($sitepage->sponsored);
      $title = "<a href='" . $this->view->url(array('page_url' => $sitepage->page_url), 'sitepage_entry_view') . "'  target='_blank'>" . $sitepage->title . "</a>";
      $form->title_dummy->setDescription($title);
      if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
        $form->package_title->setDescription("<a href='" . $this->view->url(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'package', 'action' => 'packge-detail', 'id' => $sitepage->package_id), 'admin_default') . "'  class ='smoothbox'>" . ucfirst($sitepage->getPackage()->title) . "</a>");

        $package = $sitepage->getPackage();
        if ($package->isFree()) {

          $form->getElement("status")->setMultiOptions(array("free" => "NA (Free)"));
          $form->getElement("status")->setValue("free");
          $form->getElement("status")->setAttribs(array('disable' => true));
        } else {
          $form->getElement("status")->setValue($sitepage->status);
        }
      }
    } elseif ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        //PROCESS
        $values = $form->getValues();
        if ($values['status_page'] == 2) {
          $values['declined'] = 1;
        } else {
          $approved = $values['status_page'];
        }
        $sitepage->setFromArray($values);
        if (!empty($sitepage->declined)) {
          Engine_Api::_()->sitepage()->sendMail("DECLINED", $sitepage->page_id);
        }
        $sitepage->save();
        $db->commit();
        if ($approved != $sitepage->approved) {

          return $this->_helper->redirector->gotoRoute(array('module' => 'sitepage', 'controller' => 'admin', 'action' => 'approved', "id" => $page_id), "default", true);
        }
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
  }
  
  //ACTION FOR EDIT CREATION DATE OF THE PAGES.
  public function editCreationDateAction() {
  
  		//SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');
    
    //GET PAGE ID AND PAGE OBJECT
    $page_id = $this->_getParam('page_id');
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
		$pageExpiryDate = strtotime($sitepage->expiration_date);

    //FORM GENERATION
    $form = $this->view->form = new Sitepage_Form_Admin_editCreationDate();

		$creation_date = $sitepage->creation_date;
		$form->populate($sitepage->toArray());
    $form->populate(array(
			'starttime' => $creation_date,
			'endtime' => $sitepage->expiration_date,
    ));

    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET FORM VALUES
      $values = $form->getValues();
      
      $modifiedCreationDate = strtotime($values['starttime']);
      if ($sitepage->expiration_date != '2250-01-01 00:00:00') {
				if ($pageExpiryDate < $modifiedCreationDate) {
					$itemError = Zend_Registry::get('Zend_Translate')->_("Creation Date * This should be less than expiration date.");
					$form->creation_date->setValue($sitepage->creation_date);
					$form->addError($itemError);
					return;
				}
			}

			//BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
      
        $table = Engine_Api::_()->getDbtable('pages', 'sitepage');
        $table->update(array('creation_date'=>  $values['starttime']), array('page_id =?' => $page_id));
        $db->commit();
        
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => 10,
              'parentRefresh' => 10,
              'messages' => array(Zend_Registry::get('Zend_Translate')->_('The Creation Date of the Page has been edited successfully.'))
      ));
    }
  }
}

?>