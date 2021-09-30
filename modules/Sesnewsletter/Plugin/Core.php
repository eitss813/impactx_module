<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Core.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesnewsletter_Plugin_Core extends Zend_Controller_Plugin_Abstract {

    public function onUserCreateAfter($event) {

        $user = $event->getPayload();
        $user_id = $user->getIdentity();
        $types = Engine_Api::_()->getDbTable('types', 'sesnewsletter')->getSignupuserTypes();
        if(count($types) > 0) {
            foreach($types as $type) {
                Engine_Api::_()->sesnewsletter()->addSubscriber($user_id, $type->getIdentity());
            }
        }
    }
}
