<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagealbum_AdminSettingsController extends Core_Controller_Action_Admin {

    public function __call($method, $params) {
        /*
         * YOU MAY DISPLAY ANY ERROR MESSAGE USING FORM OBJECT.
         * YOU MAY EXECUTE ANY SCRIPT, WHICH YOU WANT TO EXECUTE ON FORM SUBMIT.
         * REMEMBER:
         *    RETURN TRUE: IF YOU DO NOT WANT TO STOP EXECUTION.
         *    RETURN FALSE: IF YOU WANT TO STOP EXECUTION.
         */
        if (!empty($method) && $method == 'Sitepagealbum_Form_Admin_Global') {

        }
        return true;
    }
    
  //ACTION FOR GLOBAL SETTINGS
  public function indexAction() {
  	
		if( $this->getRequest()->isPost() ) {
			$sitepageKeyVeri = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.lsettings', null);
			if( !empty($sitepageKeyVeri) ) {
				Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepage.lsettings', trim($sitepageKeyVeri));
			}
			if( $_POST['sitepagealbum_lsettings'] ) {
				$_POST['sitepagealbum_lsettings'] = trim($_POST['sitepagealbum_lsettings']);
			}
		}
    include_once APPLICATION_PATH . '/application/modules/Sitepagealbum/controllers/license/license1.php';
  }

  //ACTION FOR FAQ
  public function faqAction() {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepagealbum_admin_main', array(), 'sitepagealbum_admin_main_faq');
  }


  public function addFeaturedAction() {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepagealbum_admin_main', array(), 'sitepagealbum_admin_widget_settings');

    //SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //FORM GENERATION
    $form = $this->view->form = new Sitepagealbum_Form_Admin_FeaturedAlbum();
    $form->setTitle('Add an Photo as Featured')
            ->setDescription('Using the auto-suggest field below, choose the photo to be made featured.');
    $form->getElement('title')->setLabel('Photo Title');
    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      //GET FORM VALUES
      $values = $form->getValues();
      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $photo = Engine_Api::_()->getItem('sitepage_photo', $values['resource_id']);
        $photo->featured = !$photo->featured;
        $photo->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('The make featured photo has been added successfully.'))
              ));
    }
  }

  //ACTION FOR PHOTO SUGGESTION DROP-DOWN
  public function getPhotoAction() {
    $title = $this->_getParam('text', null);
    $limit = $this->_getParam('limit', 40);
    $featured = $this->_getParam('featured', 0);
    $pageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $pageTableName = $pageTable->info('name');
    $albumTable = Engine_Api::_()->getDbtable('albums', 'sitepage');
    $albumName = $albumTable->info('name');
    $photoTable = Engine_Api::_()->getDbtable('photos', 'sitepage');
    $photoName = $photoTable->info('name');
    $allowTable = Engine_Api::_()->getDbtable('allow', 'authorization');
    $allowName = $allowTable->info('name');
    $data = array();
    $select = $photoTable->select()
													->setIntegrityCheck(false)
													->from($photoName)
                          ->join($albumName, $albumName . '.album_id = ' . $photoName . '.album_id', array())
													->join($pageTableName, $pageTableName . '.page_id = '. $albumName . '.page_id',array('title AS page_title', 'photo_id as page_photo_id'))
													->join($allowName, $allowName . '.resource_id = '. $pageTableName . '.page_id', array('resource_type','role'))
													->where($allowName.'.resource_type = ?', 'sitepage_page')
													->where($allowName.'.role = ?', 'registered')
													->where($allowName.'.action = ?', 'view')
													->where($albumName.'.search = ?', 1)
                          ->where($photoName . '.title  LIKE ? ', '%' . $title . '%')
													->limit($limit)
													->order($photoName . '.title')
													->limit($limit);
		$select = $select
						->where($pageTableName . '.closed = ?', '0')
						->where($pageTableName . '.approved = ?', '1')
						->where($pageTableName . '.declined = ?', '0')
						->where($pageTableName . '.draft = ?', '1');
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      $select->where($pageTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }
    
    if (!empty($featured))
      $select->where($photoName . ".featured = ?", 0);

    $photos = $photoTable->fetchAll($select);

    foreach ($photos as $photo) {
      $content_photo = $this->view->itemPhoto($photo, 'thumb.normal');
      $data[] = array(
          'id' => $photo->photo_id,
          'label' => $photo->title,
          'photo' => $content_photo
      );
    }
    return $this->_helper->json($data);
  }

  public function featuredAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepagealbum_admin_main', array(), 'sitepagealbum_admin_main_photo_featured');

    $albumTable = Engine_Api::_()->getDbtable('albums', 'sitepage');
    $albumName = $albumTable->info('name');
    $photoTable = Engine_Api::_()->getDbtable('photos', 'sitepage');
    $photoName = $photoTable->info('name');
    $data = array();
    $select = $photoTable->select()
            ->setIntegrityCheck(false)
            ->from($photoName);
    //if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
      $select->join($albumName, $albumName . '.album_id = ' . $photoName . '.album_id', array());
   // } 
    $select->where($photoName . ".featured = ?", 1)
            ->order($photoName . '.creation_date DESC');
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    // Set item count per page and current page number
    $paginator->setItemCountPerPage(50);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->view->isAlbum = true;
  }

  public function removeFeaturedAction() {

    $this->view->id = $this->_getParam('id');
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $photo = Engine_Api::_()->getItem('sitepage_photo', $this->_getParam('id'));
        $photo->featured = !$photo->featured;
        $photo->save();
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
    $this->renderScript('admin-settings/un-featured.tpl');
  }

  public function readmeAction() {
    
  }

}

?>