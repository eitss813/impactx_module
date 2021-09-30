<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepagemember_Widget_FeaturedMembersSlideshowController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //SEARCH PARAMETER
    $params = array();
    $params['zero_count'] = 'featured';
    $params['widget_name'] = 'featured';
    $params['limit'] = $this->_getParam('itemCountPerPage', 10);
   
    $this->view->show_slideshow_object = $featuredVideos = Engine_Api::_()->getDbTable('membership', 'sitepage')->widgetMembersData($params);

    //COUNT FEATURED VIDEOS
    $this->view->num_of_slideshow = count($featuredVideos);
    
    //NUMBER OF THE RESULT.
    if (empty($this->view->num_of_slideshow)) {
      return $this->setNoRender();
    }
  }
}