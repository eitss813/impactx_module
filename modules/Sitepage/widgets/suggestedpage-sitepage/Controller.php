<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_SuggestedpageSitepageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    // Get subject and check auth
    if ( !Engine_Api::_()->core()->hasSubject('sitepage_page') ) {
      return $this->setNoRender();
    }
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    $featured = $this->_getParam('featured', 0);
    $sponsored = $this->_getParam('sponsored', 0);

    //GETTING THE TAG ID OF THIS SITEPAGE ID.
    $items_count = $this->_getParam('itemCount', 5);
    $table = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $rName = $table->info('name');

    $select = $table->select();
    $this->view->sitereviewEnabled = $sitepagereviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview');
    if($sitepagereviewEnabled) {
        $select->from($rName, array('page_id', 'owner_id', 'title', 'photo_id', 'rating', 'featured', 'sponsored', 'creation_date', 'view_count', 'like_count', 'comment_count', 'review_count'));        
    }
    else {
        $select->from($rName, array('page_id', 'owner_id', 'title', 'photo_id', 'featured', 'sponsored', 'creation_date', 'view_count', 'like_count', 'comment_count'));        
    }
    
    $select->order('RAND() DESC ')
            ->where($rName . '.owner_id <> ?', $viewer_id)
            ->where($rName . '.page_id <> ?', $sitepage->page_id)
            ->where($rName . '.closed = ?', '0')
            ->where($rName . '.draft = ?', '1')
            ->where($rName . '.approved = ?', '1')
            ->where($rName . ".search = ?", 1)
            ->group($rName . '.page_id')
            ->limit($items_count);

    if ( $featured == '1' ) {
      $select = $select->where($rName . '.	featured =?', '0');
    }
    elseif ( $featured == '2' ) {
      $select = $select->where($rName . '.	featured =?', '1');
    }

    if ( $sponsored == '1' ) {
      $select = $select->where($rName . '.	sponsored =?', '0');
    }
    elseif ( $sponsored == '2' ) {
      $select = $select->where($rName . '.	sponsored =?', '1');
    }
    $sqlStr = '';

    if ( !empty($sitepage->category_id) ) {
      if ( empty($sqlStr) ) {
        $sqlStr = $rName . '.category_id = ' . "'" . $sitepage->category_id . "'";
      }
      else {
        $sqlStr.= ' OR ' . $rName . '.category_id = ' . "'" . $sitepage->category_id . "'";
      }
    } 
    
    if ( !empty($sitepage->price) ) {
      $price = $sitepage->price;
      $price_min = $price - (int) abs(($price * 10) / 100);
      $price_max = $price + (int) abs(($price * 10) / 100);
      if ( !empty($sqlStr) ) {
        $sqlStr.= ' OR ' . $rName . ".price  BETWEEN " . $price_min . " AND " . $price_max . "";
      }
      else {
        $sqlStr.= $rName . ".price  BETWEEN " . $price_min . " AND " . $price_max . "";
      }
    }

    if ( !empty($sqlStr) ) {
      $select->where($sqlStr);
    }
    
    $this->view->suggestedsitepage = $results = $table->fetchAll($select);

    // NOT RENDER IF SITEPAGE COUNT ZERO
    if (count($this->view->suggestedsitepage) <= 0 ) {
      return $this->setNoRender();
    }
  }

}
?>