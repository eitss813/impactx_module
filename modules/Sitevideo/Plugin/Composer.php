<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Composer.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Plugin_Composer extends Core_Plugin_Abstract {

    public function onAttachVideo($data) {
        if (!is_array($data) || empty($data['video_id'])) {
            return;
        }

        $video = Engine_Api::_()->getItem('sitevideo_video', $data['video_id']);
        $filter = new Zend_Filter();
        $filter->addFilter(new Engine_Filter_Censor());
        $filter->addFilter(new Zend_Filter_StripTags());
        // update $video with new title and description
        $video->title = $filter->filter($data['title']);
        $video->description = $filter->filter($data['description']);

        // Set parents of the video
        if (Engine_Api::_()->core()->hasSubject()) {
            $subject = Engine_Api::_()->core()->getSubject();
            $subject_type = $subject->getType();
            $subject_id = $subject->getIdentity();

            $video->parent_type = $subject_type;
            $video->parent_id = $subject_id;
        }
        $video->search = 1;
        $video->save();

        if (!($video instanceof Core_Model_Item_Abstract) || !$video->getIdentity()) {
            return;
        }

        return $video;
    }

}
