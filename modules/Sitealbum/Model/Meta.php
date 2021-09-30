<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Meta.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Model_Meta extends Core_Model_Item_Abstract {

    protected $_parent_type = 'meta';
    protected $_searchColumns = array('field_id', 'label');
    protected $_parent_is_owner = true;

}
