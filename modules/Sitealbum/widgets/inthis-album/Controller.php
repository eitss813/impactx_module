<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Widget_InthisAlbumController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {
  
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
    
    $this->view->limit = $this->_getParam('itemCountPerPage', 3);
    $subject = Engine_Api::_()->core()->getSubject();

    $album_id = $subject->getIdentity();
    $this->view->insideAlbum = $inSideAlbum = Engine_Api::_()->sitealbum()->getTaggedUser($album_id);
    $count = count($inSideAlbum); 
    if ($count <= 0) {
      return $this->setNoRender();
    }
    $this->view->count = $this->_childCount = $count;
    $this->view->viewer = $this->view->viewer();  
 
  }

  public function getChildCount() {
    return $this->_childCount;
  }
}