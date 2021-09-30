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
class Sitepage_Widget_InsightsSitepageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET PAGE OBJECT
    $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'insight');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK

    $values = array();
    $values['page_id'] = $sitepage->page_id;
    $values['month_activeusers'] = 1;

    //GET DATA
    $statObject = Engine_Api::_()->getDbtable('pagestatistics', 'sitepage')->getInsights($values);
    $rawData = Engine_Api::_()->getDbtable('pagestatistics', 'sitepage')->fetchAll($statObject);

    $new_responder_array = array();
    $merged_array = array();
    $this->view->total_users = $total_users = 0;

    foreach ($rawData as $rawDatum) {
      $new = 0;
      $array = array();
      if (!empty($rawDatum->viewer_id)) {
        $array[] = $rawDatum->viewer_id;
        $new_responder_array[] = $rawDatum->viewer_id;
      }
      $merged_array = array_unique(array_merge($array, $merged_array));
      if (!empty($merged_array)) {
        $new = count($merged_array);
      }
      if (!empty($rawDatum->summation_view)) {
        $this->view->total_users = $total_users = $new;
      }
    }
  }
}

?>