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

class Sitepagemember_Widget_HomeRecentMostvaluableSitepagememberController extends Engine_Content_Widget_Abstract {
  public function indexAction() {

    //SEARCH PARAMETER
    $params = array();
    $select_option = $this->_getParam('select_option', 1);
    if ($select_option == 1) {
			$params['widget_name'] = 'recent';
    } else {
			$params['widget_name'] = 'mostvaluable';
    }
    $params['limit'] = $this->_getParam('itemCount', 3);

    //MAKE PAGINATOR
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('membership', 'sitepage')->widgetMembersData($params);

    //NO RENDER
    if ( (Count($paginator) <= 0 ) ) {
      return $this->setNoRender();
    }
  }
}