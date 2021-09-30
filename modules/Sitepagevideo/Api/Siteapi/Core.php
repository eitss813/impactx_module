<?php

class Sitepagevideo_Api_Siteapi_Core extends Core_Api_Abstract {
    /*
     * Returns create video form fields
     * 
     * @return array
     */

    public function getCreateVideoForm() {
        $fieldsArray = array();
        $fieldsArray[] = array(
            'type' => 'text',
            'name' => 'title',
            'label' => $this->translate('Title of the video'),
        );

        $fieldsArray[] = array(
            'type' => 'text',
            'name' => 'tags',
            'label' => $this->translate('Tags (Keywords)'),
            'description' => $this->translate('Separate tags with commas.'),
        );

        $fieldsArray[] = array(
            'type' => 'textarea',
            'name' => 'description',
            'label' => $this->translate('Description of the video'),
        );

        $fieldsArray[] = array(
            'type' => 'checkbox',
            'name' => 'search',
            'label' => $this->translate('Show this video in search results.'),
            'value' => 1,
        );

        // Video options work
        $video_options = Array();
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey'))
            $video_options[1] = $this->translate("Youtube");

        $video_options[2] = $this->translate("Vimeo");

        $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->sitepagevideo_ffmpeg_path;

        if (!empty($ffmpeg_path))
            $video_options[3] = $this->translate("My Device");

        $fieldsArray[] = array(
            'type' => 'select',
            'name' => 'type',
            'label' => $this->translate('Video source'),
            'multiOptions' => $video_options,
        );

        $fieldsArray[] = array(
            'type' => 'text',
            'name' => 'url',
            'label' => $this->translate('Video Link (URL)'),
            'description' => $this->translate('Paste the web address of the video here.'),
            'option' => $this->translate('this will occur if user selects option 2 or 3 from url field'),
            'maxlength' => '500',
        );

        $fieldsArray[] = array(
            'type' => 'hidden',
            'name' => 'code',
            'label' => $this->translate('the code from the url of video'),
        );

        $fieldsArray[] = array(
            'type' => 'file',
            'name' => 'Filedata',
            'title' => $this->translate('Video file'),
            'description' => $this->translate('Video file to be uploaded'),
        );
        
        $fieldsArray[] = array(
            'type' => 'submit',
            'name' => 'submit',
            'title' => $this->translate('submit'),
            'description' => $this->translate('Submits the form'),
        );

        return $fieldsArray;
        
    }

    /*
     * Edit Dirctory page video form
     * 
     * @return array
     */

    public function getEditForm($sitepagevideo) {
        $fieldsArray = array();

        $fieldsArray[] = array(
            'type' => 'text',
            'name' => 'title',
            'label' => $this->translate('Title of the video'),
        );

        // Get tag string from array
        //PREPARE TAGS
        $sitepageTags = $sitepagevideo->tags()->getTagMaps();
        $tagString = '';
        foreach ($sitepageTags as $tagmap) {
            if ($tagString !== '') {
                $tagString .= ', ';
            }
            $tagString .= $tagmap->getTag()->getTitle();
        }
        
        $fieldsArray = array(
            'type' => 'text',
            'name' => 'tags',
            'label' => $this->translate('Tags (Keywords)'),
            'description' => $this->translate('Separate tags with commas.'),
            'value' => $tagString,
        );

        $fieldsArray[] = array(
            'type' => 'textarea',
            'name' => 'description',
            'label' => $this->translate('Description of the video'),
        );

        $fieldsArray[] = array(
            'type' => 'checkbox',
            'name' => 'search',
            'label' => $this->translate('Show this video in search results.'),
            'value' => 1,
        );

        $fieldsArray[] = array(
            'type' => 'button',
            'name' => 'cancel',
            'label' => $this->translate("Canel"),
            'description' => $this->translate("Cancels the video edition"),
        );

        $fieldsArray[] = array(
            'type' => 'submit',
            'name' => 'submit',
            'label' => $this->translate("Submit"),
            'description' => $this->translate("Submits the form"),
        );

        return $fieldsArray;
    }

    /*
     * Search form Directory pages
     * 
     * @return array
     */

    public function getVideoBrowseSearchForm() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $searchForm = array();
        $searchForm[] = array(
            'type' => 'text',
            'name' => 'search',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search'),
        );
        $searchForm[] = array(
            'type' => 'checkbox',
            'name' => 'myvideos',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Show my videos'),
        );

        $browsebyData = array();
        $browsebyData[''] = "";
        $browsebyData['creation_date'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate('Creation Date');
        $browsebyData['like_count'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate("Like count");
        $browsebyData['comment_count'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate("Comment Count");
        $browsebyData['view_count'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate("View Count");
        $browsebyData['rating'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate("Rating");
        $browsebyData['featured'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate("Featured");
        $browsebyData['highlighted'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate("Highlighted");

        $searchForm[] = array(
            'type' => 'select',
            'name' => 'orderby',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Browse By:'),
            'multiOptions' => $browsebyData,
        );

        return $searchForm;
    }

    /*
     * Returns comments on video form 
     *
     * @return array
     */

    public function getcommentForm($type, $id) {
        $commentform = array();
        $commentform[] = array(
            'type' => "text",
            'name' => 'body',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Comment'),
        );
        return $commentform;
    }

    /*
     * Upload video sent in file
     */

    public function uploadVideo() {
        if (!$this->_helper->requireUser()->checkRequire()) {
            $this->respondWithValidationError('validation_fail', "Max file size limit exceeded (probably).");
        }

        $values = $this->_getAllParams();

        if (empty($_FILES['Filedata']))
            $this->respondWithValidationError('validation_fail', 'no file');

        if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
            $this->respondWithValidationError('validation_fail', 'Invalid upload');
        }

        $illegal_extensions = array('php', 'pl', 'cgi', 'html', 'htm', 'txt');
        if (in_array(pathinfo($_FILES['Filedata']['name'], PATHINFO_EXTENSION), $illegal_extensions))
            $this->respondWithValidationError('validation_fail', 'Invalid upload');

        $db = Engine_Api::_()->getDbtable('videos', 'sitepagevideo')->getAdapter();
        $db->beginTransaction();

        try {
            $viewer = Engine_Api::_()->user()->getViewer();
            $values['owner_id'] = $viewer->getIdentity();

            $params = array(
                'owner_id' => $viewer->getIdentity()
            );
            $video = Engine_Api::_()->sitepagevideo()->createSitepagevideo($params, $_FILES['Filedata'], $values);
            $video->title = $_FILES['Filedata']['name'];
            $video->owner_id = $viewer->getIdentity();
            $video->save();
            $db->commit();
            return $video->video_id;
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    private function translate($message = null) {
        return Engine_Api::_()->getApi('Core', 'siteapi')->translate($message);
    }

}
