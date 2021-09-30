<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Template.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesnewsletter_Model_Template extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = array('search');

  public function isSearchable()
  {
    return $this->custom === 1 && parent::isSearchable();
  }
}
