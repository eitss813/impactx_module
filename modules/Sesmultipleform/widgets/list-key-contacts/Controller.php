<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Controller.php 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmultipleform_Widget_ListKeyContactsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {

    // Should we consider creation or modified recent?
    $listtype = $this->_getParam('listtype', 'creation');
    if( !in_array($listtype, array('creation', 'order','random')) ) {
      $listtype = 'creation';
    }
    $this->view->recentType = $listtype;
		$this->view->blockposition = $this->_getParam('blockposition', 1);
    $this->view->height = $this->_getParam('height', 200);
    $this->view->width = $this->_getParam('width', 200);
    $this->view->emailshow = $this->_getParam('emailshow', 1);

    $nonloggined = $this->_getParam('nonloggined', 1);
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if (empty($nonloggined) && empty($viewer_id))
      return $this->setNoRender();
    // Get paginator
  $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('sesmultipleform_keycontact')->getContactsList(array('listtype'=>$listtype));
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 4));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    // Hide if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) { 
      return $this->setNoRender();
    }
  }
}