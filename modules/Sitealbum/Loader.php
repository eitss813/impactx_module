<?php

/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_Loader
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Loader.php 9747 2012-07-26 02:08:08Z john $
 * @todo       documentation
 */

/**
 * @category   Engine
 * @package    Engine_Loader
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitealbum_Loader extends Engine_Loader {

  /**
   * Get current singleton instance
   * 
   * @return Engine_Loader
   */
  public static function getInstance() {
    return new self();
  }

  /**
   * Loads and instantiates a resource class
   * 
   * @param string $class
   * @return mixed
   */
  public function setComponentsObject($class, $orignalClassName = null) {
    if (empty($orignalClassName))
      $orignalClassName = $class;
    $loader = Engine_Loader::getInstance();
    return $loader->_components[$orignalClassName] = $loader->load($class);
  }

}

