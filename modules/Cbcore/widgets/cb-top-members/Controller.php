<?php
/**
 * SocialEngine
 *
 * @category   Module
 * @package    Consecutive Bytes Core
 * @copyright  Copyright 2015 - 2017 Consecutive Bytes
 * @license    http://www.consecutivebytes.com/license/
 * @version    4.9.0
 * @author     Consecutive Bytes
 */
class Cbcore_Widget_CbTopMembersController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
	$this->view->title = $title = $this->_getParam('title');
	// Get numcount
    $itemcount = $this->_getParam('itemcount');
    if(!empty($itemcount)){
        $itemcount = $this->_getParam('itemcount');
    }else{
        $itemcount = 5;
    }

    $photo =  $this->_getParam('photo' , 0);
    $table = Engine_Api::_()->getDbtable('users', 'user');
    $select = $table->select();
    if($photo == 1){
        $select->where('photo_id != ?', 0);
    }
    $select->where('search = ?', 1)->where('enabled = ?', 1)->order('creation_date DESC');

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', $itemcount));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }
  }
}