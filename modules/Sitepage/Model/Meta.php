<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Meta.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_Meta extends Core_Model_Item_Abstract {

  // Properties
  protected $_parent_type = 'meta';
  protected $_searchColumns = array('field_id', 'label');
  protected $_parent_is_owner = true;

}
