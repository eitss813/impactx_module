<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Settings.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitepage_Api_Manageapi extends Core_Api_Abstract
{
    protected $_table;

    public function  __construct()
    {
        $this->_table = Engine_Api::_()->getDbtable('oauthConsumers', 'siteapi');
    }


}
