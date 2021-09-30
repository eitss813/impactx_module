<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Widget_OwnedBySitevideoController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('video')) {
            return $this->setNoRender();
        }

        $this->view->sitevideo = $sitevideo = Engine_Api::_()->core()->getSubject('video');
        if (!$sitevideo->parent_type && !$sitevideo->parent_id) {
            return $this->setNoRender();
        }

        if (strpos($sitevideo->parent_type, "sitereview_listing") !== false) {
            $this->view->item = $item = Engine_Api::_()->getItem('sitereview_listing', $sitevideo->parent_id);
            $shortType = strtolower($item->getShortType());
            $listingType = Engine_Api::_()->getItem('sitereview_listingtype', $item->listingtype_id);
            $this->view->title = $title = "(" . ucfirst($listingType->title_singular) . ") -";
        } else {
            $this->view->item = $item = Engine_Api::_()->getItem($sitevideo->parent_type, $sitevideo->parent_id);
            $shortType = strtolower($item->getShortType());
            $this->view->title = $title = "(" . ucfirst(Engine_Api::_()->getApi('settings', 'core')->getSetting("language.phrases.$shortType", $shortType)) . ") -";
        }
    }

}
