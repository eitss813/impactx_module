<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Composer.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagevideo_Plugin_Composer extends Core_Plugin_Abstract {

  public function onAttachSitepagevideo($data) {
    if (!is_array($data) || empty($data['video_id'])) {
      return;
    }

    $video = Engine_Api::_()->getItem('sitepagevideo_video', $data['video_id']);
    $filter = new Zend_Filter();
    $filter->addFilter(new Engine_Filter_Censor());
    $filter->addFilter(new Zend_Filter_StripTags());
    // update $video with new title and description
    $video->title = $filter->filter($data['title']);
    $video->description = $filter->filter($data['description']);

    // Set parents of the video
    if (Engine_Api::_()->core()->hasSubject()) {
      $subject = Engine_Api::_()->core()->getSubject();
      if (in_array($subject->getType(), array('sitepageevent_event'))):
        $subject = Engine_Api::_()->getItem('sitepage_page', $subject->page_id);
      endif;

      $subject_type = $subject->getType();
      $subject_id = $subject->getIdentity();

      //  $video->parent_type = $subject_type;
      $video->page_id = $subject_id;
    }
    $video->search = 1;
    $video->save();

    if (!($video instanceof Core_Model_Item_Abstract) || !$video->getIdentity()) {
      return;
    }

    return $video;
  }

}