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
class Sitevideo_Widget_VideoViewController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $video = Engine_Api::_()->core()->getSubject();
        $is_suggestion_enabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
        
        if(!empty($is_suggestion_enabled) && !empty($video)){
        
        Engine_Api::_()->sitevideo()->deleteSuggestion('sitevideo_video', $video->getIdentity(), 'sitevideo_video_suggestion');
        
        }
        
        $this->view->videoTags = $video->tags()->getTagMaps();
        // Check if edit/delete is allowed
        $this->view->canEdit = $canEdit = $video->authorization()->isAllowed($viewer, 'edit');
        $this->view->canDelete = $canDelete = $video->authorization()->isAllowed($viewer, 'delete');
        $this->view->canDownload = Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'download');
        
        // check if embedding is allowed
        $can_embed = true;
        $this->view->allow_location =false;
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.location', 1)) {
            $this->view->allow_location = true;
        }
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.embeds', 1)) {
            $can_embed = false;
        } else if (isset($video->allow_embed) && !$video->allow_embed) {
            $can_embed = false;
        }

        $this->view->can_embed = $can_embed;
        $this->view->viewOptions = $this->_getParam('viewOptions');
        $this->view->width = $this->_getParam('width',0);
        $this->view->height = $this->_getParam('height',540);
        $this->view->canComment = $canComment = $video->authorization()->isAllowed($viewer, 'comment');

        // increment count
        $embedded = "";

        if ($video->status == 1) {
            if (!$video->isOwner($viewer)) {
                $video->view_count++;
                $video->save();
            }
            $embedded = $video->getRichContent(true);
        }

        if ($video->checkType('upload') && $video->status == 1) {
            if (!empty($video->file_id)) {
                $storage_file = Engine_Api::_()->getItem('storage_file', $video->file_id);
                if ($storage_file) {
                    $this->view->video_location = $storage_file->map();
                    $this->view->video_extension = $storage_file->extension;
                }
            }
        }
        if ($video->checkType('stream') && $video->status == 1) {
            if (!empty($video->file_id)) {
                $storage_file = Engine_Api::_()->getItem('storage_file', $video->file_id);
                if ($storage_file) {
                    $this->view->video_location = $storage_file->map();
                    $this->view->video_extension = $storage_file->extension;
                }
            }
        }

        $this->view->viewer_id = $viewer->getIdentity();
        $ratingTable = Engine_Api::_()->getDbtable('ratings', 'sitevideo');
        $sitevideo_getview = Zend_Registry::isRegistered('sitevideo_getview') ? Zend_Registry::get('sitevideo_getview') : null;
        $this->view->rating_count = $ratingTable->ratingCount(array('resource_id' => $video->getIdentity(), 'resource_type' => 'sitevideo_video'));
        $this->view->rated = $ratingTable->checkRated(array('resource_id' => $video->getIdentity(), 'resource_type' => 'sitevideo_video'));
        $this->view->video = $video;
        $this->view->videoEmbedded = $embedded;
        //GET QUICK INFO DETAILS
        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitevideo/View/Helper', 'Sitevideo_View_Helper');
        $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($video);
        $sitevideoSpecificationVideos = Zend_Registry::isRegistered('sitevideoSpecificationVideos') ? Zend_Registry::get('sitevideoSpecificationVideos') : null;
        $itemCount = 100;
        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->view->show_fields = $this->view->FieldValueLoopQuickInfoSitevideo($video, $this->view->fieldStructure, $itemCount);
        }
        if (empty($sitevideo_getview))
            return $this->setNoRender();

        if ($video->category_id) {
            $this->view->category = Engine_Api::_()->getDbtable('videoCategories', 'sitevideo')->getCategory($video->category_id);
        }

        if ($viewer->getIdentity())
            $video->saveWatchStatus();
    }

}
