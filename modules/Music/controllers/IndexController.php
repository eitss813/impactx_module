<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: IndexController.php 9878 2013-02-13 03:18:43Z shaun $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Music_IndexController extends Core_Controller_Action_Standard
{
  public function init()
  {
    // Check auth
    if( !$this->_helper->requireAuth()->setAuthParams('music_playlist', null, 'view')->isValid()) {
      return;
    }

    // Get viewer info
    $this->view->viewer     = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id  = Engine_Api::_()->user()->getViewer()->getIdentity();
  }
  
  public function browseAction()
  {
    // Can create?
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('music_playlist', null, 'create');

    // Get browse params
    $this->view->formFilter = $formFilter = new Music_Form_Search();
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    } else {
      $values = array();
    }
    $this->view->formValues = array_filter($values);

    // Show
    $viewer = Engine_Api::_()->user()->getViewer();
    if( @$values['show'] == 2 && $viewer->getIdentity() ) {
      // Get an array of friend ids
      $values['users'] = $viewer->membership()->getMembershipsOfIds();
      $values['searchBit'] = 1;
    }
    unset($values['show']);

    // Get paginator
    $this->view->paginator = $paginator = Engine_Api::_()->music()->getPlaylistPaginator($values);
    $paginator->setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('music.playlistsperpage', 10));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Render
    $this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;
  }
  
  public function manageAction()
  {

    // only members can manage music
    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }

    // Render
    $this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;

    // Can create?
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('music_playlist', null, 'create');
    
    // Get browse params
    $this->view->formFilter = $formFilter = new Music_Form_Search();
    $formFilter->removeElement('show');
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    } else {
      $values = array();
    }
    $this->view->formValues = array_filter($values);

    // Get paginator
    $values['user'] = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->paginator = $paginator = Engine_Api::_()->music()->getPlaylistPaginator($values);
    $paginator->setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('music.playlistsperpage', 10));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
  }

  public function createAction()
  {
    // only members can upload music
    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireAuth()->setAuthParams('music_playlist', null, 'create')->isValid() ) {
      return;
    }

    $this->_helper->content
        // ->setNoRender()
        ->setEnabled()
        ;

    // catch uploads from FLASH fancy-uploader and redirect to uploadSongAction()
    if( $this->getRequest()->getQuery('ul', false) ) {
      return $this->_forward('upload', 'song', null, array('format' => 'json'));
    }

    // Get form
    $this->view->form = $form = new Music_Form_Create();
    $this->view->playlist_id = $this->_getParam('playlist_id', '0');

    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

      $itemFlood = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('music_playlist', $this->view->viewer()->level_id, 'flood');
      if(!empty($itemFlood[0])){
          //get last activity
          $tableFlood = Engine_Api::_()->getDbTable("playlists",'music');
          $select = $tableFlood->select()->where("owner_id = ?",$this->view->viewer()->getIdentity())->order("creation_date DESC");
          if($itemFlood[1] == "minute"){
              $select->where("creation_date >= DATE_SUB(NOW(),INTERVAL 1 MINUTE)");
          }else if($itemFlood[1] == "day"){
              $select->where("creation_date >= DATE_SUB(NOW(),INTERVAL 1 DAY)");
          }else{
              $select->where("creation_date >= DATE_SUB(NOW(),INTERVAL 1 HOUR)");
          }
          $floodItem = $tableFlood->fetchAll($select);
          if(count($floodItem) && $itemFlood[0] <= count($floodItem)){
              $message = Engine_Api::_()->core()->floodCheckMessage($itemFlood,$this->view);
              $form->addError($message);
              return;
          }
      }

    // Process
    $db = Engine_Api::_()->getDbTable('playlists', 'music')->getAdapter();
    $db->beginTransaction();
    try {
      $playlist = $this->view->form->saveValues();
      $db->commit();
    } catch( Exception $e ) {
      return $this->exceptionWrapper($e, $form, $db);
    }
    
    return $this->_helper->redirector->gotoUrl($playlist->getHref(), array('prependBase' => false));
  }
}
