<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: AdminManageController.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_AdminManageController extends Core_Controller_Action_Admin {

  public function indexAction() {
  
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesblog_admin_main', array(), 'sesblog_admin_main_manage');

    $this->view->formFilter = $formFilter = new Sesblog_Form_Admin_Manage_Filter();
    
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $item = Engine_Api::_()->getItem('sesblog_blog', $value);
					Engine_Api::_()->sesblog()->deleteBlog($item);
        }
      }
    }
    
    $values = array();
    
    if ($formFilter->isValid($this->_getAllParams()))
      $values = $formFilter->getValues();
      
    $values = array_merge(array('order' => isset($_GET['order']) ? $_GET['order'] :'', 'order_direction' => isset($_GET['order_direction']) ? $_GET['order_direction'] : ''), $values);
    
    $this->view->assign($values);
    
    $uTableName = Engine_Api::_()->getItemTable('user')->info('name');
    
    $table = Engine_Api::_()->getDbTable('blogs', 'sesblog');
    $tableName = $table->info('name');
    
    $select = $table->select()
                    ->setIntegrityCheck(false)
                    ->from($tableName)
                    ->joinLeft($uTableName, "$tableName.owner_id = $uTableName.user_id", 'username')
                    ->order((!empty($_GET['order']) ? $_GET['order'] : 'blog_id' ) . ' ' . (!empty($_GET['order_direction']) ? $_GET['order_direction'] : 'DESC' ));
                      
    if (!empty($_GET['title']))
      $select->where($tableName . '.title LIKE ?', '%' . $_GET['title'] . '%');

    if (!empty($_GET['owner_name']))
      $select->where($uTableName . '.displayname LIKE ?', '%' . $_GET['owner_name'] . '%');
    
    if (!empty($_GET['category_id']))
      $select->where($tableName . '.category_id =?', $_GET['category_id']);

    if (!empty($_GET['subcat_id']))
      $select->where($tableName . '.subcat_id =?', $_GET['subcat_id']);

    if (!empty($_GET['subsubcat_id']))
      $select->where($tableName . '.subsubcat_id =?', $_GET['subsubcat_id']);

    if (isset($_GET['featured']) && $_GET['featured'] != '')
    $select->where($tableName . '.featured = ?', $_GET['featured']);

    if (isset($_GET['sponsored']) && $_GET['sponsored'] != '')
    $select->where($tableName . '.sponsored = ?', $_GET['sponsored']);
		
		if (isset($_GET['package_id']) && $_GET['package_id'] != '')
    $select->where($tableName . '.package_id = ?', $_GET['package_id']);
    
    if (isset($_GET['status']) && $_GET['status'] != '')
    $select->where($tableName . '.draft = ?', $_GET['status']);
		
		if (isset($_GET['is_approved']) && $_GET['is_approved'] != '')
    	$select->where($tableName . '.is_approved = ?', $_GET['is_approved']);
		
    if (isset($_GET['verified']) && $_GET['verified'] != '')
    $select->where($tableName . '.verified = ?', $_GET['verified']);
    
    if (isset($_GET['offtheday']) && $_GET['offtheday'] != '')
    $select->where($tableName . '.offtheday = ?', $_GET['offtheday']);
    
    if (isset($_GET['rating']) && $_GET['rating'] != '') {
      if ($_GET['rating'] == 1):
        $select->where($tableName . '.rating <> ?', 0);
      elseif ($_GET['rating'] == 0 && $_GET['rating'] != ''):
        $select->where($tableName . '.rating = ?', $_GET['rating']);
      endif;
    }

    if (!empty($_GET['creation_date']))
      $select->where($tableName . '.creation_date LIKE ?', $_GET['creation_date'] . '%');
    
		if (isset($_GET['subcat_id'])) {
			$formFilter->subcat_id->setValue($_GET['subcat_id']);
			$this->view->category_id = $_GET['category_id'];
		}

    if (isset($_GET['subsubcat_id'])) {
			$formFilter->subsubcat_id->setValue($_GET['subsubcat_id']);
			$this->view->subcat_id = $_GET['subcat_id'];
    }
    
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
  }
  
  public function claimAction() {
  
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesblog_admin_main', array(), 'sesblog_admin_main_claim');

    $this->view->formFilter = $formFilter = new Sesblog_Form_Admin_Manage_ClaimFilter();
      
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          Engine_Api::_()->getItem('sesblog_claim', $value)->delete();
        }
      }
    }

    $values = array();
    if ($formFilter->isValid($this->_getAllParams()))
      $values = $formFilter->getValues();
      
    $values = array_merge(array('order' => isset($_GET['order']) ? $_GET['order'] :'', 'order_direction' => isset($_GET['order_direction']) ? $_GET['order_direction'] : ''), $values);
    
    $this->view->assign($values);
    
    $uTableName = Engine_Api::_()->getItemTable('user')->info('name');
    
    $table = Engine_Api::_()->getDbTable('claims', 'sesblog');
    $tableName = $table->info('name');
    
    $btable = Engine_Api::_()->getDbTable('blogs', 'sesblog');
    $btableName = $table->info('name');
    
    $select = $table->select()
                  ->setIntegrityCheck(false)
                  ->from($tableName)
                  ->joinLeft($uTableName, "$tableName.user_id = $uTableName.user_id", 'username')
                  ->order((!empty($_GET['order']) ? $_GET['order'] : 'claim_id' ) . ' ' . (!empty($_GET['order_direction']) ? $_GET['order_direction'] : 'DESC' ));
                  
    if (!empty($_GET['title']))
      $select->where($tableName . '.title LIKE ?', '%' . $_GET['title'] . '%');

    if (!empty($_GET['owner_name']))
      $select->where($uTableName . '.displayname LIKE ?', '%' . $_GET['owner_name'] . '%');

    if (isset($_GET['is_approved']) && $_GET['is_approved'] != '')
      $select->where($tableName . '.status = ?', $_GET['is_approved']);

    if (!empty($_GET['creation_date']))
      $select->where($tableName . '.creation_date LIKE ?', $_GET['creation_date'] . '%');

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
  }

  public function deleteAction() {

    $this->_helper->layout->setLayout('admin-simple');

    if( $this->getRequest()->isPost()) {
      try {
        Engine_Api::_()->sesblog()->deleteBlog(Engine_Api::_()->getItem('sesblog_blog', $this->_getParam('id')));
      } catch( Exception $e) {
        //slience
      }
      
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh'=> 10,
        'messages' => array('You have successfully delete entry.')
      ));
    }
    $this->renderScript('admin-manage/delete.tpl');
  }
  
  public function approvedAction() {
  
    $id = $this->_getParam('id');
    if (!empty($id)) {
    
      $item = Engine_Api::_()->getItem('sesblog_blog', $id);
      $item->is_approved = !$item->is_approved;
      $item->save();
      
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionsByObject($item);
      
      if(count($action->toArray()) <= 0 && (!$item->publish_date || strtotime($item->publish_date) <= time())) {
      
        $viewer = Engine_Api::_()->getItem('user', $item->owner_id);
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $item, 'sesblog_new');
        if($action)
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $item);
      }
    }
    $this->_redirect('admin/sesblog/manage');
  }
  
  public function featuredAction() {
  
    $id = $this->_getParam('id');
    if (!empty($id)) {
      $item = Engine_Api::_()->getItem('sesblog_blog', $id);
      $item->featured = !$item->featured;
      $item->save();
    }
    $this->_redirect('admin/sesblog/manage');
  }
  
  public function sponsoredAction() {
  
    $id = $this->_getParam('id');
    if (!empty($id)) {
      $item = Engine_Api::_()->getItem('sesblog_blog', $id);
      $item->sponsored = !$item->sponsored;
      $item->save();
    }
    $this->_redirect('admin/sesblog/manage');
  }

  public function verifyAction() {
  
    $id = $this->_getParam('id');
    if (!empty($id)) {
      $item = Engine_Api::_()->getItem('sesblog_blog', $id);
      $item->verified = !$item->verified;
      $item->save();
    }
    $this->_redirect('admin/sesblog/manage');
  }
  
  public function ofthedayAction() {
  
    $db = Engine_Db_Table::getDefaultAdapter();
    
    $this->_helper->layout->setLayout('admin-simple');
    
    $id = $this->_getParam('id');
    
    $param = $this->_getParam('param');
    
    $this->view->form = $form = new Sesblog_Form_Admin_Manage_Oftheday();
    
    $item = Engine_Api::_()->getItem('sesblog_blog', $id);
    
    $form->setTitle("Blog of the Day");
    $form->setDescription('Here, choose the start date and end date for this blog to be displayed as "Blog of the Day".');
    
    if (!$param)
      $form->remove->setLabel("Remove as Blog of the Day");
    
    if (!empty($id))
      $form->populate($item->toArray());
      
    if ($this->getRequest()->isPost()) {

      if (!$form->isValid($this->getRequest()->getPost())) 
        return;
        
      $values = $form->getValues();
      $values['starttime'] = date('Y-m-d',  strtotime($values['starttime']));
      $values['endtime'] = date('Y-m-d', strtotime($values['endtime']));
      
      $db->update('engine4_sesblog_blogs', array('starttime' => $values['starttime'], 'endtime' => $values['endtime']), array("blog_id = ?" => $id));
      
      if (isset($values['remove']) && $values['remove']) {
        $db->update('engine4_sesblog_blogs', array('offtheday' => 0), array("blog_id = ?" => $id));
      } else {
        $db->update('engine4_sesblog_blogs', array('offtheday' => 1), array("blog_id = ?" => $id));
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Successfully updated the item.')
      ));
    }
  }
  
  public function showDetailAction() {
    $this->view->claimItem = Engine_Api::_()->getItem('sesblog_claim', $this->_getParam('id'));
  }
  
  public function approveClaimAction() {
  
    $db = Engine_Db_Table::getDefaultAdapter();
    
    $this->_helper->layout->setLayout('admin-simple');
    
    $claimItem = Engine_Api::_()->getItem('sesblog_claim', $this->_getParam('id', null));
    $currentOwnerId = $claimItem->user_id;
    
    $item = Engine_Api::_()->getItem('sesblog_blog', $claimItem->blog_id);

    $this->view->form = $form = new Sesblog_Form_Admin_Manage_Approveform();

		if (!$this->getRequest()->isPost())
      return;
      
		if (!$form->isValid($this->getRequest()->getPost()))
      return;
    
    $viewer = Engine_Api::_()->user()->getViewer();
    
		if(!empty($_POST['approve_decline']) && ($_POST['approve_decline'] == 'accept')) {
			
			//upgrade album
			$db->update('engine4_sesblog_albums', array('owner_id' => $currentOwnerId), array("blog_id = ?" => $claimItem->blog_id));
			
			//upgrade photos
			$db->update('engine4_sesblog_photos', array('user_id' => $currentOwnerId), array("blog_id = ?" => $claimItem->blog_id));
			
			//upgrade favourites
			$db->update('engine4_sesblog_favourites', array('user_id' => $currentOwnerId), array("resource_id = ?" => $claimItem->blog_id,'user_id = ?'=>$item->owner_id));
			
			//upgrade reviews
			$db->update('engine4_sesblog_reviews', array('owner_id' => $currentOwnerId), array("blog_id = ?" => $claimItem->blog_id,'owner_id = ?'=>$item->owner_id));
			
			//upgrade roles
			$db->update('engine4_sesblog_roles', array('user_id' => $currentOwnerId), array("blog_id = ?" => $claimItem->blog_id,'user_id = ?'=>$item->owner_id));
			
			//upgrade extention if exists
			//upgrade video
			if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesvideo')){ 
				$db->update('engine4_video_videos', array('owner_id' => $currentOwnerId), array("parent_id = ?" => $claimItem->blog_id,'parent_type = ?'=>'sesblog_blog','owner_id = ?'=>$item->owner_id));
			}
			
			//upgrade music plugin
			if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesmusic')){
				//upgrade video
				$db->update('engine4_sesmusic_albums', array('owner_id' => $currentOwnerId), array("resource_id = ?" => $claimItem->blog_id,'resource_type = ?'=>'sesblog_blog','owner_id = ? '=>$item->owner_id));
			}
			//done upgrade extention work
			
			$db->update('engine4_sesblog_claims', array('status' => 1), array("claim_id = ?" => $claimItem->claim_id));
			$db->update('engine4_sesblog_blogs', array('owner_id' => $currentOwnerId), array("blog_id = ?" => $claimItem->blog_id));
			
			$fromName = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.name', 'Site Admin');
			$fromAddress = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.from', 'admin@' . $_SERVER['HTTP_HOST']);
			$mailDataClaimOwner = array('sender_title' => $fromName);
			$bodyForClaimOwner = '';
			$bodyForClaimOwner .= $this->view->translate("Email: %s", $fromAddress) . '<br />';
			$bodyForClaimOwner .= $this->view->translate("Site Owner Comment For Claim: %s", $_POST['admin_comment']) . '<br /><br />';
			$mailDataClaimOwner['message'] = $bodyForClaimOwner;
		  Engine_Api::_()->getApi('mail', 'core')->sendSystem($claimItem->user_email, 'sesblog_claim_owner_approve', $mailDataClaimOwner);
			$mailDataBlogOwner = array('sender_title' => $fromName);
			$bodyForBlogOwner = '';
			$bodyForBlogOwner .= $this->view->translate("Email: %s", $fromAddress) . '<br />';
			if(isset($claimItem->contact_number) && !empty($claimItem->contact_number))
			$bodyForBlogOwner .= $this->view->translate("Claim Owner Contact Number: %s", $claimItem->contact_number) . '<br />';
			$bodyForBlogOwner .= $this->view->translate("Site Owner Comment For Claim: %s", $_POST['admin_comment']) . '<br /><br />';
			$mailDataBlogOwner['message'] = $bodyForBlogOwner;
			
			$blogOwnerId = Engine_Api::_()->getItem('sesblog_blog', $claimItem->blog_id)->owner_id;
			$blogOwnerEmail = Engine_Api::_()->getItem('user', $blogOwnerId)->email;
			$claimOwner = Engine_Api::_()->getItem('user', $currentOwnerId);
			$blogOwner = Engine_Api::_()->getItem('user', $item->owner_id);
			Engine_Api::_()->getApi('mail', 'core')->sendSystem($blogOwnerEmail, 'sesblog_blog_owner_approve', $mailDataBlogOwner);
			Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($claimOwner, $viewer, $item, 'sesblog_claim_approve');
      Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($blogOwner, $viewer, $item, 'sesblog_owner_informed');
		} elseif(!empty($_POST['approve_decline']) && ($_POST['approve_decline'] == 'decline')) {
		  $claimOwner = Engine_Api::_()->getItem('user', $currentOwnerId);
		  $db->delete('engine4_sesblog_claims', array("claim_id = ?" => $claimItem->claim_id));
		  Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($claimOwner, $viewer, $item, 'sesblog_claim_declined');
		} else {
		  $form->addError($this->view->translate("Choose atleast one option for this claim request."));
      return;
		}
		
		$this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh' => 10,
      'messages' => array('Claim has been updated Successfully')
		));
  }

  public function viewAction() {
    $this->view->item = Engine_Api::_()->getItem('sesblog_blog', $this->_getParam('id', null));
  }  
}
