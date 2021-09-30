<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Contentpage.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_Contentpage extends Core_Model_Item_Abstract {

  protected $_parent_type = 'user';
  protected $_searchTriggers = false;

  /**
   * Gets an absolute URL to the page to view this item
   *
   * @param array $params 
   * @return string
   */
  public function getHref($params = array()) {

    if (!empty($this->url)) {
      $id = str_replace(array('_', ' '), '-', $this->url);
    } else if (!empty($this->name)) {
      $id = str_replace(array('_', ' '), '-', $this->name);
    } else {
      $id = $this->page_id;
    }

    $params = array_merge(array(
        'route' => 'default',
        'reset' => true,
        'module' => 'sitepage',
        'controller' => 'page',
        'action' => $id
            ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
            ->assemble($params, $route, $reset);
  }

}

?>