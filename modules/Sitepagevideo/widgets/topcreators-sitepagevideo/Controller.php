<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagevideo_Widget_TopcreatorsSitepagevideoController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //SEARCH PARAMETER
    $limit = $this->_getParam('itemCount', 5);
    $category_id = $this->_getParam('category_id',0);
    //MAKE PAGINATOR
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('videos', 'sitepagevideo')->topcreatorData($limit,$category_id);

    //NO RENDER
    if ( (Count($paginator) <= 0 ) ) {
      return $this->setNoRender();
    }
  }
}
?>