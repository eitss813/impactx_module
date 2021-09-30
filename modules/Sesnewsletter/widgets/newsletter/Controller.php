<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Controller.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesnewsletter_Widget_NewsletterController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $allowSubs = 1;
        if(!empty($viewer_id)) {
            $allowSubs = Engine_Api::_()->authorization()->getPermission($viewer, 'sesnewsletter', 'allowsubs');

            if(!empty($viewer_id) && !empty($allowSubs)) {
                $isExist = Engine_Api::_()->getDbTable('subscribers', 'sesnewsletter')->isExist($viewer->email);
                if($isExist)
                    return $this->setNoRender();
            }

            if(empty($allowSubs))
                return $this->setNoRender();
        }
    }
}
