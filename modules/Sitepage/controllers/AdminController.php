<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_AdminController extends Core_Controller_Action_Admin {

  //ACTION FOR MAKE THE SITEPAGE FEATURED/UNFEATURED
  public function featuredAction() {
  	
    $page_id = $this->_getParam('id');
    if (!empty($page_id)) {
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
      $sitepage->featured = !$sitepage->featured;
      $sitepage->save();
    }
    $this->_redirect('admin/sitepage/viewsitepage');
  }

  //ACTION FOR MAKE THE SITEPAGE OPEN/CLOSED
  public function opencloseAction() {
  	
    $page_id = $this->_getParam('id');
    if (!empty($page_id)) {
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
      $sitepage->closed = !$sitepage->closed;
      $sitepage->save();
    }
    $this->_redirect('admin/sitepage/viewsitepage');
  }

  //ACTION FOR MAKE SPONSORED /UNSPONSORED
  public function sponsoredAction() {
  	
    $page_id = $this->_getParam('id');
    if (!empty($page_id)) {
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
      $sitepage->sponsored = !$sitepage->sponsored;
      $sitepage->save();
    }
    $this->_redirect('admin/sitepage/viewsitepage');
  }

  //ACTION FOR MAKE SITEPAGE APPROVE/DIS-APPROVE

  public function rejectAction(){

    $sitepage_id = $this->_getParam('id');
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepage_id);

    $this->view->form = $form = new Sitepage_Form_Admin_Notes();


    $this->view->adminnotes = $adminnotes = Engine_Api::_()->getDbTable('adminnotes','sitepage')->getAllAdminNotesByProjectId($sitepage_id);

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      $values = $form->getValues();
      if(!empty($values['description'])) {
        $table = Engine_Api::_()->getItemTable('sitepage_adminnote');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {

          $notes = $table->createRow();
          $inputs = array(
              'description' => $values['description'],
              'page_id' => $sitepage_id,
              'admin_id' => Engine_Api::_()->user()->getViewer()->getIdentity()
          );
          $notes->setFromArray($inputs);
          $notes->save();
          $db->commit();

        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }
      }
      $settings = Engine_Api::_()->getApi('settings', 'core');
      try {
        $sitepage->approved = 0;
        $sitepage->state = 'rejected';

        $sitepage->save();

        if($settings->getSetting('sitepage.email.page.disapproval', 0)) {
          // sending email confirmation
          Engine_Api::_()->sitepage()->sendMail("DISAPPROVED", $sitepage->page_id);
          // sending email confirmation
        }

         if($settings->getSetting('sitepage.notification.page.disapproval', 0)) {
           // sending the approved system notification
           $owner = $sitepage->getOwner();
           $sender = Engine_Api::_()->user()->getViewer();
           $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
           $type = 'sitepage_page_disapproved';
           $notifyApi->addNotification($owner, $sender, $sitepage, $type);
           // sending the approved system notification
         }

      }catch (Exception $e){
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Rejected Successfully.')
      ));

    }
  }

  public function approvedAction() {
  	
    $page_id = $this->_getParam('id');
    try {
      $settings = Engine_Api::_()->getApi('settings', 'core');
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
      $sitepage->approved = 1;
      $sitepage->state = 'published';
      $sitepage->pending = 0;
      if (empty($sitepage->aprrove_date)) {
        $sitepage->aprrove_date = date('Y-m-d H:i:s');
      }

      if (!empty($sitepage->approved)) {              

        $diff_days = 0;
        $package = $sitepage->getPackage();
        if (($sitepage->expiration_date !== '2250-01-01 00:00:00' && !empty($sitepage->expiration_date) && $sitepage->expiration_date !== '0000-00-00 00:00:00') && date('Y-m-d', strtotime($sitepage->expiration_date)) > date('Y-m-d')) {
          $diff_days = round((strtotime($sitepage->expiration_date) - strtotime(date('Y-m-d H:i:s'))) / 86400);
        }

        if (($diff_days <= 0 && $sitepage->expiration_date !== '2250-01-01 00:00:00') || empty($sitepage->expiration_date) || $sitepage->expiration_date == '0000-00-00 00:00:00') {
          if (!$package->isFree()) {
            if ($sitepage->status != "active") {
              $relDate = new Zend_Date(time());
              $relDate->add((int) 1, Zend_Date::DAY);
              $sitepage->expiration_date = date('Y-m-d H:i:s', $relDate->toValue());
            } else {

              $expirationDate = $package->getExpirationDate();
              if (!empty($expirationDate))
                $sitepage->expiration_date = date('Y-m-d H:i:s', $expirationDate);
              else
                $sitepage->expiration_date = '2250-01-01 00:00:00';
            }
          }else {

            $expirationDate = $package->getExpirationDate();
            if (!empty($expirationDate))
              $sitepage->expiration_date = date('Y-m-d H:i:s', $expirationDate);
            else
              $sitepage->expiration_date = '2250-01-01 00:00:00';
          }
        }

      }
      $sitepage->save();

       if($settings->getSetting('sitepage.email.page.approval', 0)){
         // sending email confirmation
         Engine_Api::_()->sitepage()->sendMail("APPROVED", $sitepage->page_id);
         // sending email confirmation
       }

      if($settings->getSetting('sitepage.activity.page.approval', 0)) {
        // sending the activity
        if (!empty($sitepage) && !empty($sitepage->draft) && empty($sitepage->pending)) {
          Engine_Api::_()->sitepage()->attachPageActivity($sitepage);
        }
        // sending the activity
      }

       if($settings->getSetting('sitepage.notification.page.approval', 0)) {
         // sending the approved system notification
         $owner = $sitepage->getOwner();
         $sender = Engine_Api::_()->user()->getViewer();
         $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
         $type = 'sitepage_page_approved';
         $notifyApi->addNotification($owner, $sender, $sitepage, $type);
         // sending the approved system notification
       }

    } catch (Exception $e) {
      throw $e;
    }
    $this->_redirect('admin/sitepage/viewsitepage');
  }

  //ACTION FOR MAKE SITEPAGE APPROVE/DIS-APPROVE
  public function renewAction() {

    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      $page_id = $this->_getParam('id');
      if ($this->getRequest()->isPost()) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
          $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
          if (!empty($sitepage->approved)) {
            $package = $sitepage->getPackage();
            if ($sitepage->expiration_date !== '2250-01-01 00:00:00') {

              $expirationDate = $package->getExpirationDate();
              $expiration = $package->getExpirationDate();

              $diff_days = 0;
              if (!empty($sitepage->expiration_date) && $sitepage->expiration_date !== '0000-00-00 00:00:00') {
                $diff_days = round((strtotime($sitepage->expiration_date) - strtotime(date('Y-m-d H:i:s'))) / 86400);
              }
              if ($expiration) {
                $date = date('Y-m-d H:i:s', $expiration);

                if ($diff_days >= 1) {

                  $diff_days_expiry = round((strtotime($date) - strtotime(date('Y-m-d H:i:s'))) / 86400);
                  $incrmnt_date = date('d', time()) + $diff_days_expiry + $diff_days;
                  $incrmnt_date = date('Y-m-d H:i:s', mktime(date("H"), date("i"), date("s"), date("m"), $incrmnt_date));
                } else {
                  $incrmnt_date = $date;
                }

                $sitepage->expiration_date = $incrmnt_date;
              } else {
                $sitepage->expiration_date = '2250-01-01 00:00:00';
              }
            }
            if ($package->isFree())
              $sitepage->status = "initial";
            else
              $sitepage->status = "active";
          }
          $sitepage->search = 1;
          $sitepage->save();
          $db->commit();
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }

        $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
        ));
      }
    }
    $this->renderScript('admin/renew.tpl');
  }

  //ACTION FOR DELETE THE SITEPAGE
  public function deleteAction() {

    $this->_helper->layout->setLayout('admin-simple');
    $this->view->page_id = $page_id = $this->_getParam('id');

    if ($this->getRequest()->isPost()) {
    
      //START SUB PAGE WORK
			$getSubPageids = Engine_Api::_()->getDbTable('pages', 'sitepage')->getsubPageids($page_id);
			foreach($getSubPageids as $getSubPageid) {
				Engine_Api::_()->sitepage()->onPageDelete($getSubPageid['page_id']);
			}
			//END SUB PAGE WORK
			
      Engine_Api::_()->sitepage()->onPageDelete($page_id);
      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => 10,
              'parentRefresh' => 10,
              'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
    }
    $this->renderScript('admin/delete.tpl');
  }

  //ACTION FOR CHANGE THE OWNER OF THE PAGE
  public function changeOwnerAction() {
  	
    //LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //GET PAGE ID
    $this->view->page_id = $page_id = $this->_getParam('id');

    //FORM
    $form = $this->view->form = new Sitepage_Form_Admin_Changeowner();

    //SET ACTION
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

    //GET SITEPAGE ITEM
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //OLD OWNER ID
    $oldownerid = $sitepage->owner_id;

    //CHECK FORM VALIDATION
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      //GET FORM VALUES
      $values = $form->getValues();

      //GET USER ID WHICH IS NOW NEW USER
      $changeuserid = $values['user_id'];

      //CHANGE USER TABLE
      $changed_user = Engine_Api::_()->getItem('user', $changeuserid);

      //OWNER USER TABLE
      $user = Engine_Api::_()->getItem('user', $sitepage->owner_id);

      //PAGE URL
      $page_url = Engine_Api::_()->sitepage()->getPageUrl($sitepage->page_id);

      //GET PAGE TITLE
      $pagetitle = $sitepage->title;

      //PAGE OBJECT LINK
      $pageobjectlink = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page_url' => $page_url), 'sitepage_entry_view');

      //GET DB
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        //UPDATE PAGE TABLE
        Engine_Api::_()->getDbtable('pages', 'sitepage')->update(array('owner_id' => $changeuserid), array('page_id = ?' => $page_id));

        //GET PAGE URL
        $page_baseurl = 'http://' . $_SERVER['HTTP_HOST'] .
            Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page_url' => $page_url), 'sitepage_entry_view', true);

        //MAKING PAGE TITLE LINK
        $page_title_link = '<a href="' . $page_baseurl . '"  >' . $pagetitle . ' </a>';

        //GET ADMIN EMAIL
        $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;

        //EMAIL THAT GOES TO OLD OWNER
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($user->email, 'SITEPAGE_CHANGEOWNER_EMAIL', array(
            'page_title' => $pagetitle,
            'page_title_with_link' => $page_title_link,
            'object_link' => $page_baseurl,
            'site_contact_us_link' => 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/help/contact',
            'email' => $email,
            'queue' => true

        ));

        //EMAIL THAT GOES TO NEW OWNER
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($changed_user->email, 'SITEPAGE_BECOMEOWNER_EMAIL', array(
            'page_title' => $pagetitle,
            'page_title_with_link' => $page_title_link,
            'object_link' => $page_baseurl,
            'site_contact_us_link' => 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/help/contact',
            'email' => $email,
            'queue' => true

        ));


		    //START FOR INRAGRATION WORK WITH OTHER PLUGIN. DELETE ACCORDING TO PAGE ID.
				$sitepageintegrationEnabled = Engine_Api::_()->getDbtable('modules',
				'core')->isModuleEnabled('sitepageintegration');
				if(!empty($sitepageintegrationEnabled)) {
					$contentsTable = Engine_Api::_()->getDbtable('contents', 'sitepageintegration');
					$contentsTable->delete(array('page_id = ?' => $page_id));
				}
        //END FOR INRAGRATION WORK WITH OTHER PLUGIN.
        
				$sitepagememberEnabled = Engine_Api::_()->getDbtable('modules',
				'core')->isModuleEnabled('sitepagemember');
				if(!empty($sitepagememberEnabled)) {
					$membershipTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
					$select = $membershipTable->select()->from($membershipTable->info('name'))->where('user_id = ?', $user->user_id)->where('resource_id = ?', $sitepage->page_id);
          $getRow = $membershipTable->fetchRow($select);
          
					$select = $membershipTable->select()->from($membershipTable->info('name'), 'member_id')->where('user_id = ?', $changed_user->user_id)->where('resource_id = ?', $sitepage->page_id);
          $getNewOwnerRow = (integer) $select->query()->fetchColumn();          
          
          if(!empty($getRow) && empty($getNewOwnerRow)) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
              $memberShip = $membershipTable->createRow();
              $getRowArray = $getRow->toArray();
              unset($getRowArray['member_id']);
              $memberShip->setFromArray($getRowArray);
              $memberShip->user_id = $changed_user->user_id;
              $memberShip->save();
              $db->commit();
            } catch (Exception $e) {
              $db->rollBack();
              throw $e;
            }              
          }
				}        

        //UPDATE IN CONTENT PAGE TABLE
        Engine_Api::_()->getDbtable('contentpages', 'sitepage')->update(array('user_id' => $changeuserid), array('page_id = ?' => $page_id));

        //UPDATE PHOTO TABLE
        Engine_Api::_()->getDbtable('photos', 'sitepage')->update(array('user_id' => $changeuserid), array('user_id = ?' => $oldownerid, 'page_id = ?' => $page_id));

        //UPDATE ALBUM TABLE
        Engine_Api::_()->getDbtable('albums', 'sitepage')->update(array('owner_id' => $changeuserid), array('owner_id = ?' => $oldownerid, 'page_id = ?' => $page_id));

        //UPDATE AND DELETE IN MANAGE ADMIN TABLE
        Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->delete(array('user_id = ?' => $changeuserid, 'page_id = ?' => $page_id));
        Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->update(array('user_id' => $changeuserid), array('user_id = ?' => $oldownerid, 'page_id = ?' => $page_id));
        
        //COMMIT
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      //SUCCESS
      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => 300,
              'parentRefresh' => 300,
              'messages' => array(Zend_Registry::get('Zend_Translate')->_('The page owner has been changed succesfully.'))
      ));
    }
  }

  //ACTION FOR GETTING THE LIST OF USERS
  public function getOwnerAction() {
  	
  	//GET SITEPAGE ITEM
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $this->_getParam('page_id'));
    
    //USER TABLE
    $tableUser = Engine_Api::_()->getDbtable('users', 'user');
    $userTableName = $tableUser->info('name');
    $noncreate_owner_level = array();
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    foreach (Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $level) {
      $can_create = 0;
      if ($level->type != "public") {
        $can_create = Engine_Api::_()->authorization()->getPermission($level->level_id, 'sitepage_page', 'edit');
        if (empty($can_create)) {
          $noncreate_owner_level[] = $level->level_id;
        }
      }
    }
    
    //SELECT
    $select = $tableUser->select()
            ->where('displayname  LIKE ? ', '%' . $this->_getParam('text') . '%')
            ->where('user_id !=?', $sitepage->owner_id)
            ->order('displayname ASC')
            ->limit($this->_getParam('limit', 40));
    
    if (!empty($noncreate_owner_level)) {
      $str = (string) ( is_array($noncreate_owner_level) ? "'" . join("', '", $noncreate_owner_level) . "'" : $noncreate_owner_level );
      $select->where($userTableName . '.level_id not in (?)', new Zend_Db_Expr($str));
    }
    
    //FETCH
    $userlists = $tableUser->fetchAll($select);    
   
    //MAKING DATA
    $data = array();
    $mode = $this->_getParam('struct');

    if ($mode == 'text') {
      foreach ($userlists as $userlist) {
        $content_photo = $this->view->itemPhoto($userlist, 'thumb.icon');
        $data[] = array(
                'id' => $userlist->user_id,
                'label' => $userlist->displayname,
                'photo' => $content_photo
        );
      }
    } else {
      foreach ($userlists as $userlist) {
        $content_photo = $this->view->itemPhoto($userlist, 'thumb.icon');
        $data[] = array(
                'id' => $userlist->user_id,
                'label' => $userlist->displayname,
                'photo' => $content_photo
        );
      }
    }

    return $this->_helper->json($data);
  }

  //ACTION FOR CHANGE THE CATEGORY OF THE PAGE  
  public function changeCategoryAction() {
  	
    //LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //GET PAGE ID
    $this->view->page_id = $page_id = $this->_getParam('id');

    //GET SITEPAGE ITEM
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //GET CATEGORY ID
    $this->view->category_id = $previous_category_id = $sitepage->category_id;

    //GET SUBCATEGORY
    $this->view->subcategory_id = $subcategory_id = $sitepage->subcategory_id;

    //GET SUBSUBCATEGORY
    $this->view->subsubcategory_id = $subsubcategory_id = $sitepage->subsubcategory_id;

    //GET ROW    
    $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($subcategory_id);
    if (!empty($row->category_name)) {
      $this->view->subcategory_name = $row->category_name;
    }

    $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($subsubcategory_id);
    if (!empty($row->category_name)) {
      $this->view->subsubcategory_name = $row->category_name;
    }
    
    //FORM
    $form = $this->view->form = new Sitepage_Form_Admin_Changecategory();

    //POPULATE
    $value['category_id'] = $sitepage->category_id;
    $form->populate($value);

    //CHECK FORM VALIDATION
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $is_error = 0;
      //GET FORM VALUES
      $values = $form->getValues();
      if (empty($values['category_id'])) {
        $is_error = 1;
        $this->view->category_id = 0;
      }

      //SET ERROR
      if ($is_error == 1) {
        $error = $this->view->translate('Page Category * Please complete this field - it is required.');
        $this->view->status = false;
        $error = Zend_Registry::get('Zend_Translate')->_($error);
        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
      }

      //GET DB
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        //SAVE VALUES
        $sitepage->category_id = $values['category_id'];
        $sitepage->subcategory_id = $values['subcategory_id'];
        $sitepage->subsubcategory_id = $values['subsubcategory_id'];
        $sitepage->save();
        $db->commit();

        //START SITEPAGEREVIEW CODE
        $sitepageReviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview');
        if ($sitepageReviewEnabled && $previous_category_id != $sitepage->category_id) {
          Engine_Api::_()->getDbtable('ratings', 'sitepagereview')->editPageCategory($sitepage->page_id, $previous_category_id, $sitepage->category_id);
        }
        //END SITEPAGEREVIEW CODE
        
        //START SITEPAGEMEMBER CODE
        $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
        if ($sitepagememberEnabled && $previous_category_id != $sitepage->category_id) {
          $db->query("UPDATE `engine4_sitepage_membership` SET `role_id` = '0' WHERE `engine4_sitepage_membership`.`page_id` = ". $sitepage->page_id. ";");
        }
        //END SITEPAGEMEMBER CODE

        //PROFILE MAPPING WORK IF CATEGORY IS EDIT
        if ($previous_category_id != $sitepage->category_id) {
          Engine_Api::_()->getDbtable('profilemaps', 'sitepage')->editCategoryMapping($sitepage);
        }
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      //SUCCESS
      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => 300,
              'parentRefresh' => 300,
              'messages' => array(Zend_Registry::get('Zend_Translate')->_('The category has been changed successfully.'))
      ));
    }
  }

  //ACTION FOR DISPLAY/HIDE FIELDS OF SEARCH FORM
  public function diplayFormAction() {
  	
    $field_id = $this->_getParam('id');
    $display = $this->_getParam('display');
    if (!empty($field_id)) {
      Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->update(array('display' => $display), array('module = ?' => 'sitepage', 'searchformsetting_id =?' => (int) $field_id));
    }
    $this->_redirect('admin/sitepage/settings/form-search');
  }

}
?>
