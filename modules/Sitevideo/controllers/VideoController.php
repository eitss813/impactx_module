<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: VideoController.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_VideoController extends Seaocore_Controller_Action_Standard
{

    protected $_set;

    public function init()
    {

        $this->_set = 0;
        if ($this->_getParam('set')) {
            $video_id = Engine_Api::_()->sitevideo()->getEncodeToDecode($this->_getParam('set'));
            if (Engine_Api::_()->getItem('sitevideo_video', $video_id)) {
                $this->_set = 1;
            }

        }

        if (0 !== ($video_id = (int) $this->_getParam('video_id')) &&
            null !== ($video = Engine_Api::_()->getItem('sitevideo_video', $video_id)) && !Engine_Api::_()->core()->hasSubject()) {
            Engine_Api::_()->core()->setSubject($video);
        }
    }

    public function indexAction()
    {
        if (!$this->_helper->requireAuth()->setAuthParams('video', null, 'view')->isValid()) {
            return;
        }
        $this->_helper->content->setNoRender()->setEnabled();
    }

    //ACTION FOR DELETING VIDEO

    public function deleteAction()
    {

        $viewer = Engine_Api::_()->user()->getViewer();
        $video  = Engine_Api::_()->getItem('sitevideo_video', $this->getRequest()->getParam('video_id'));

        $parent_type = $video->parent_type;
        $parent_id   = $video->parent_id;
        if ($video->parent_type && $video->parent_id) {

            $isParentDeletePrivacy = Engine_Api::_()->sitevideo()->canDeletePrivacy($video->parent_type, $video->parent_id, $video);

            if (empty($isParentDeletePrivacy)) {
                return $this->_forwardCustom('requireauth', 'error', 'core');
            }

        } else {
            if ($viewer->getIdentity() != $video->owner_id && !$this->_helper->requireAuth()->setAuthParams($video, null, 'delete')->isValid()) {
                return $this->_forward('requireauth', 'error', 'core');
            }
        }

        $this->view->tab_selected_id = $this->_getParam('tab');
        // In smoothbox
        $this->_helper->layout->setLayout('default-simple');
        $this->view->form = $form = new Sitevideo_Form_Video_Delete();
        if (!$video) {
            $this->view->status = false;
            $this->view->error  = Zend_Registry::get('Zend_Translate')->_("Video doesn't exists or not authorized to delete");
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error  = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }
        $db = $video->getTable()->getAdapter();
        $db->beginTransaction();

        try {

            Engine_Api::_()->getApi('core', 'sitevideo')->deleteVideo($video);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->view->status  = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Video has been deleted.');
        return $this->_forward('success', 'utility', 'core', array(
            'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'sitevideo_video_general', true),
            'messages'       => array($this->view->message),
        ));
    }

    //ACTION FOR EDITING VIDEO
    public function editAction()
    {
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        $video    = Engine_Api::_()->getItem('sitevideo_video', $this->_getParam('video_id'));
        $is_admin = $this->_getParam('admin', false);
        if (!$this->_helper->requireSubject()->isValid()) {
            return;
        }

        $this->_helper->content
            ->setEnabled();

        $parent_type = $video->parent_type;
        $parent_id   = $video->parent_id;
        if ($video->parent_type && $video->parent_id) {

            $canEdit = $isParentEditPrivacy = Engine_Api::_()->sitevideo()->isEditPrivacy($video->parent_type, $video->parent_id, $video);

            if (empty($isParentEditPrivacy)) {
                return $this->_forwardCustom('requireauth', 'error', 'core');
            }

        } else {
            if ($viewer->getIdentity() != $video->owner_id && !$this->_helper->requireAuth()->setAuthParams($video, null, 'edit')->isValid()) {
                return $this->_forward('requireauth', 'error', 'core');
            }
        }

        $this->view->tab_selected_id = $this->_getParam('tab');
        if (strpos($parent_type, "sitereview_listing") !== false) {
            $this->view->parentTypeItem = $parentTypeItem = Engine_Api::_()->getItem('sitereview_listing', $parent_id);
        } else {
            if ($parent_type && $parent_id) {
                $this->view->parentTypeItem = $parentTypeItem = Engine_Api::_()->getItem($parent_type, $parent_id);
            }

        }

        $this->view->video            = $video;
        $this->view->defaultProfileId = $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'sitevideo')->defaultProfileId();

        $this->view->form = $form = new Sitevideo_Form_Video_Edit(array('defaultProfileId' => $defaultProfileId, 'item' => $video));

        $form->getElement('search')->setValue($video->search);
        $form->getElement('title')->setValue($video->title);
        $form->getElement('description')->setValue($video->description);
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.category.enabled', 1)) {
            $this->view->category_id = $video->category_id;
            $form->getElement('category_id')->setValue($video->category_id);
            // if($video->subcategory_id)
            //$form->getElement('subcategory_id')->setValue($video->subcategory_id);
            //if($video->subsubcategory_id && $form->hasElement('subsubcategory_id'))
            //   $form->getElement('subsubcategory_id')->setValue($video->subsubcategory_id);
            $this->view->subcategory_id    = $video->subcategory_id;
            $this->view->subsubcategory_id = $video->subsubcategory_id;
        }

        if ($video->category_id) {
            //GET PROFILE MAPPING ID
            $categoryIds   = array();
            $categoryIds[] = $video->category_id;
            if ($video->subcategory_id) {
                $categoryIds[] = $video->subcategory_id;
            }

            if ($video->subsubcategory_id) {
                $categoryIds[] = $video->subsubcategory_id;
            }

            $this->view->profileType = $previous_profile_type = Engine_Api::_()->getDbtable('videoCategories', 'sitevideo')->getProfileType(array('categoryIds' => $categoryIds, 'category_id' => 0));
        }

        if (!$this->getRequest()->isPost()) {

            //NETWORK BASE CHANNEL
            if (Engine_Api::_()->sitevideo()->videoBaseNetworkEnable()) {
                if (!empty($video->networks_privacy)) {
                    $form->networks_privacy->setValue(explode(',', $video->networks_privacy));
                } else {
                    $form->networks_privacy->setValue(array(0));
                }
            }

            $form->populate($video->toArray());
        }

        // authorization
        $auth  = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        foreach ($roles as $role) {
            if (1 === $auth->isAllowed($video, $role, 'view')) {
                $form->auth_view->setValue($role);
            }
            if (1 === $auth->isAllowed($video, $role, 'comment')) {
                $form->auth_comment->setValue($role);
            }
        }
        $isTagEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.tags.enabled', 1);
        if ($isTagEnabled) {
            // prepare tags
            $videoTags = $video->tags()->getTagMaps();

            $tagString = '';
            foreach ($videoTags as $tagmap) {
                if ($tagString !== '') {
                    $tagString .= ', ';
                }

                $tagString .= $tagmap->getTag()->getTitle();
            }
            $this->view->tagNamePrepared = $tagString;
            $form->tags->setValue($tagString);
        }
        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error  = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->view->status = false;
            $this->view->error  = Zend_Registry::get('Zend_Translate')->_('Invalid data');
            return;
        }

        // Process
        $db = Engine_Api::_()->getDbtable('videos', 'sitevideo')->getAdapter();
        $db->beginTransaction();
        try {
            $values = $form->getValues();

            //NETWORK BASE CHANNEL
            if (Engine_Api::_()->sitevideo()->videoBaseNetworkEnable()) {
                if (isset($values['networks_privacy']) && !empty($values['networks_privacy'])) {
                    if (in_array(0, $values['networks_privacy'])) {
                        $values['networks_privacy'] = new Zend_Db_Expr('NULL');
                        $form->networks_privacy->setValue(array(0));
                    } else {
                        $values['networks_privacy'] = (string) (is_array($values['networks_privacy']) ? join(",", $values['networks_privacy']) : $netowrkIds);
                    }
                }
            }

            $video->setFromArray($values);
            $video->subcategory_id    = is_null($video->subcategory_id) ? 0 : ($video->subcategory_id);
            $video->category_id       = is_null($video->category_id) ? 0 : ($video->category_id);
            $video->subsubcategory_id = is_null($video->subsubcategory_id) ? 0 : ($video->subsubcategory_id);
            $video->save();
            if (!empty($values['art'])) {
              $video->saveVideoThumbnail($form->art);
            }

            // Set the information of the User Channel
            Engine_Api::_()->sitevideo()->setUserChannelInfo();

            //IF MAPPING HAS BEEN CHANGED OF CATEGORY THEN DELETE CORRESPONDENCE DATA FROM VALUES AND SEARCH TABLE
            if (isset($values['category_id']) && !empty($values['category_id'])) {

                //SAVE CUSTOM VALUES AND PROFILE TYPE VALUE
                $customfieldform = $form->getSubForm('fields');
                $customfieldform->setItem($video);
                $customfieldform->saveValues();
                if ($customfieldform->getElement('submit')) {
                    $customfieldform->removeElement('submit');
                }

                $categoryIds         = array();
                $categoryIds[]       = $video->category_id;
                $categoryIds[]       = $video->subcategory_id;
                $categoryIds[]       = $video->subsubcategory_id;
                $video->profile_type = Engine_Api::_()->getDbtable('videoCategories', 'sitevideo')->getProfileType(array('categoryIds' => $categoryIds, 'category_id' => 0));
                if ($video->profile_type != $previous_profile_type) {

                    $fieldvalueTable = Engine_Api::_()->fields()->getTable('video', 'values');
                    $fieldvalueTable->delete(array('item_id = ?' => $video->video_id));

                    Engine_Api::_()->fields()->getTable('video', 'search')->delete(array(
                        'item_id = ?' => $video->video_id,
                    ));

                    if (!empty($video->profile_type) && !empty($previous_profile_type)) {
                        //PUT NEW PROFILE TYPE
                        $fieldvalueTable->insert(array(
                            'item_id'  => $video->video_id,
                            'field_id' => $defaultProfileId,
                            'index'    => 0,
                            'value'    => $video->profile_type,
                        ));
                    }
                }
                $video->save();
            }

            // CREATE AUTH STUFF HERE
            $auth  = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            if ($values['auth_view']) {
                $auth_view = $values['auth_view'];
            } else {
                $auth_view = "everyone";
            }

            $viewMax = array_search($auth_view, $roles);
            foreach ($roles as $i => $role) {
                $auth->setAllowed($video, $role, 'view', ($i <= $viewMax));
            }

            $roles = array('owner', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            if ($values['auth_comment']) {
                $auth_comment = $values['auth_comment'];
            } else {
                $auth_comment = "everyone";
            }

            $commentMax = array_search($auth_comment, $roles);
            foreach ($roles as $i => $role) {
                $auth->setAllowed($video, $role, 'comment', ($i <= $commentMax));
            }

            if ($isTagEnabled) {
                // Add tags
                $tags = preg_split('/[,]+/', $values['tags']);
                $video->tags()->setTagMaps($viewer, $tags);
            }

            if (isset($values['password']) && !empty($values['password'])) {
                $video->search   = 0;
                $video->password = $values['password'];
            } else {
                $video->password = '';
            }
            $video->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $db->beginTransaction();
        try {
            // Rebuild privacy
            $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
            foreach ($actionTable->getActionsByObject($video) as $action) {
                $actionTable->resetActivityBindings($action);
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        if ($is_admin) {
            return $this->_helper->redirector->gotoRoute(array('module' => 'sitevideo', 'controller' => 'manage-video'), 'admin_default', true);
        }

        return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitevideo_video_general', true);
    }

// ACTION FOR EDIT THE DESCRIPTION OF THE VIDEOS
    public function editDescriptionAction()
    {
        //GET TEXT
        $text = $this->_getParam('text_string');

        //GET VIDEO ITEM
        $video = Engine_Api::_()->core()->getSubject();
        // GET DB
        $db = Engine_Db_Table::getDefaultAdapter();

        $db->beginTransaction();
        try {
            //SAVE VALUE
            $value['description'] = $text;
            $video->setFromArray($value);
            $video->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        exit();
    }

    //ACTION FOR MAKING FEATURED VIDEO
    public function featuredAction()
    {
        if (!$this->_helper->requireSubject('sitevideo_video')->isValid()) {
            return;
        }

        $video           = Engine_Api::_()->core()->getSubject();
        $video->featured = !$video->featured;
        $video->save();
        exit(0);
    }

    //ACTION FOR ADDING VIDEO OF THE DAY
    public function addVideoOfDayAction()
    {
        //FORM GENERATION
        $video = Engine_Api::_()->core()->getSubject();

        // CHECK FOR ONLY ADMIN CAN ADD VIDEO OF THE DAY
        $channel   = $video->getChannel();
        $viewer    = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $allowView = $addVideoOfTheDay = false;
        if (!empty($viewer_id) && $viewer->level_id == 1) {
            $addVideoOfTheDay = true;
            $auth             = Engine_Api::_()->authorization()->context;
            $allowView        = $auth->isAllowed($channel, 'everyone', 'view') === 1 ? true : false || $auth->isAllowed($channel, 'registered', 'view') === 1 ? true : false;
        }

        if (!$addVideoOfTheDay || !$allowView) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $form = $this->view->form = new Sitevideo_Form_ItemOfDayday();
        $form->setTitle('Video of the Day')
            ->setDescription('Select a start date and end date below.This video will be displayed as "Video of the Day" for this duration.If more than one videos of the day are found for a date then randomly one will be displayed.');

        //CHECK POST
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            //GET FORM VALUES
            $values                = $form->getValues();
            $values["resource_id"] = $video->getIdentity();
//BEGIN TRANSACTION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                $table = Engine_Api::_()->getDbtable('itemofthedays', 'sitevideo');
                $row   = $table->getItem('sitevideo_channel', $values["resource_id"]);

                if (empty($row)) {
                    $row = $table->createRow();
                }
                $values = array_merge($values, array('resource_type' => 'sitevideo_video'));

                if ($values['start_date'] > $values['end_date']) {
                    $values['end_date'] = $values['start_date'];
                }

                $row->setFromArray($values);
                $row->save();

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            return $this->_forward('success', 'utility', 'core', array(
                'messages'       => array(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.')),
                'layout'         => 'default-simple',
                'smoothboxClose' => true,
            ));
        }
    }

    //ACTION FOR EDIT THE TITLE OF THE VIDEOS
    public function editTitleAction()
    {
        //GET TEXT
        $text  = $this->_getParam('text_string');
        $video = Engine_Api::_()->core()->getSubject();
        //GET DB
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            //SAVE VALUE
            $value['title'] = $text;
            $video->setFromArray($value);
            $video->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        exit();
    }

    //ACTION FOR RATING THE VIDEO
    public function rateAction()
    {

        $viewer    = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $allowRating = Engine_Api::_()->authorization()->getPermission($level_id, 'sitevideo_channel', 'rate');
        if (empty($viewer_id) || empty($allowRating)) {
            return;
        }

        $rating   = $this->_getParam('rating');
        $video_id = $this->_getParam('video_id');

        $table = Engine_Api::_()->getDbtable('ratings', 'sitevideo');
        $db    = $table->getAdapter();
        $db->beginTransaction();

        try {
            $table->setRating($video_id, 'sitevideo_video', $rating);

            $video         = Engine_Api::_()->getItem('sitevideo_video', $video_id);
            $video->rating = $table->getRating($video->getIdentity(), 'sitevideo_video');
            $video->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $total = $table->ratingCount(array('resource_id' => $video->getIdentity(), 'resource_type' => 'sitevideo_video'));

        $data   = array();
        $data[] = array(
            'total'  => $total,
            'rating' => $rating,
        );
        return $this->_helper->json($data);
        $data = Zend_Json::encode($data);
        $this->getResponse()->setBody($data);
    }

    // ACTION FOR GET LINK WORK
    public function getLinkAction()
    {
        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid()) {
            return;
        }

        $this->view->subject = $subject = Engine_Api::_()->getItemByGuid($this->_getParam('subject'));

        $viewer = Engine_Api::_()->user()->getViewer();
        //GET AN ARRAY OF FRIEND IDS
        $friends = $viewer->membership()->getMembers();
        $ids     = array();
        foreach ($friends as $friend) {
            $ids[] = $friend->user_id;
        }

        // IF THERE ARE NO FRIENDS OF VIEWER THEN DON'T DISPLAY SENDINMESSEGE LINK
        $this->view->noSendMessege = 0;
        if (empty($ids)) {
            $this->view->noSendMessege = 1;
        }
        $encode_subjectId = Engine_Api::_()->sitevideo()->getDecodeToEncode('' . $subject->getIdentity() . '');

        $this->view->url         = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $subject->getHref() . '/set/' . $encode_subjectId;
        $this->view->subjectType = $subject->getType();
    }

    //ACTION FOR COMPOSING A MESSEGE TO SEND A VIDEO
    public function composeAction()
    {
        $this->_helper->layout->setLayout('default-simple');

        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid()) {
            return;
        }

        // Make form
        $this->view->form = $form = new Sitevideo_Form_Compose();

        //SET URL IN BODY OF MESSAGE
        $this->view->subject     = $subject     = Engine_Api::_()->getItemByGuid($this->_getParam('subject'));
        $this->view->subjectType = $subject->getType();

        $encode_subjectId = Engine_Api::_()->sitevideo()->getDecodeToEncode('' . $subject->getIdentity() . '');

        $url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $subject->getHref() . '/set/' . $encode_subjectId;
        // Build
        $isPopulated = false;
        $form->body->setValue($url);
        $this->view->isPopulated = $isPopulated;

        // Get config
        $this->view->maxRecipients = $maxRecipients = 10;

        // Check method/data
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // Process
        $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
        $db->beginTransaction();

        try {
            // Try attachment getting stuff
            $attachment     = null;
            $attachmentData = $this->getRequest()->getParam('attachment');
            if (!empty($attachmentData) && !empty($attachmentData['type'])) {
                $type   = $attachmentData['type'];
                $config = null;
                foreach (Zend_Registry::get('Engine_Manifest') as $data) {
                    if (!empty($data['composer'][$type])) {
                        $config = $data['composer'][$type];
                    }
                }
                if ($config) {
                    $plugin     = Engine_Api::_()->loadClass($config['plugin']);
                    $method     = 'onAttach' . ucfirst($type);
                    $attachment = $plugin->$method($attachmentData);
                    $parent     = $attachment->getParent();
                    if ($parent->getType() === 'user') {
                        $attachment->search = 0;
                        $attachment->save();
                    } else {
                        $parent->search = 0;
                        $parent->save();
                    }
                }
            }

            $viewer = Engine_Api::_()->user()->getViewer();
            $values = $form->getValues();

            $recipients = preg_split('/[,. ]+/', $values['toValues']);
            // clean the recipients for repeating ids
            // this can happen if recipient is selected and then a friend list is selected
            $recipients = array_unique($recipients);
            // Slice down to 10
            $recipients = array_slice($recipients, 0, $maxRecipients);
            // Get user objects
            $recipientsUsers = Engine_Api::_()->getItemMulti('user', $recipients);
            // Validate friends
            if ('friends' == Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth')) {
                foreach ($recipientsUsers as &$recipientUser) {
                    if (!$viewer->membership()->isMember($recipientUser)) {
                        return $form->addError('One of the members specified is not in your friends list.');
                    }
                }
            }

            // Create conversation
            $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
                $viewer, $recipients, $values['title'], $values['body'], $attachment
            );

            // Send notifications
            foreach ($recipientsUsers as $user) {
                if ($user->getIdentity() == $viewer->getIdentity()) {
                    continue;
                }
                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
                    $user, $viewer, $conversation, 'message_new'
                );
            }

            // Increment messages counter
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

            // Commit
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        if ($this->getRequest()->getParam('format') == 'smoothbox') {
            return $this->_forward('success', 'utility', 'core', array(
                'messages'       => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.')),
                'smoothboxClose' => true,
            ));
        } else {
            return $this->_forward('success', 'utility', 'core', array(
                'messages'       => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.')),
                'smoothboxClose' => true,
                'redirect'       => $conversation->getHref(), //$this->getFrontController()->getRouter()->assemble(array('action' => 'inbox'))
            ));
        }
    }

    //ACTION FOR SENDING A VIDEO BY EMAIL
    public function tellAFriendAction()
    {
        //DEFAULT LAYOUT
        $this->_helper->layout->setLayout('default-simple');

        //GET VIEWER DETAIL
        $viewer   = Engine_Api::_()->user()->getViewer();
        $viewr_id = $viewer->getIdentity();

        //GET VIDEO ID AND VIDEO OBJECT
        $video_id = $this->_getParam('video');
        $video    = Engine_Api::_()->getItem('sitevideo_video', $video_id);

        if (!$video->authorization()->isAllowed(null, 'edit')) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if (!$video->authorization()->isAllowed(null, 'view')) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $encode_videoId = Engine_Api::_()->sitevideo()->getDecodeToEncode('' . $video->getIdentity() . '');
        $url            = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $video->getHref() . '/set/' . $encode_videoId;
        if (empty($video)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        //FORM GENERATION
        $this->view->form = $form = new Sitevideo_Form_TellAFriend();

        if (!empty($viewr_id)) {
            $value['sender_email'] = $viewer->email;
            $form->populate($value);
        }
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = $form->getValues();

            //EDPLODES EMAIL IDS
            $reciver_ids  = explode(',', $values['sitevideo_reciver_emails']);
            $sender_email = $values['sitevideo_sender_email'];
            $sender_name  = $viewer->getTitle();

            //CHECK VALID EMAIL ID FORMITE
            $validator = new Zend_Validate_EmailAddress();
            $validator->getHostnameValidator()->setValidateTld(false);
            if (!$validator->isValid($sender_email)) {
                $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid sender email address value'));
                return;
            }
            foreach ($reciver_ids as $reciver_id) {
                $reciver_id = trim($reciver_id, ' ');
                if (!$validator->isValid($reciver_id)) {
                    $form->addError(Zend_Registry::get('Zend_Translate')->_('Please enter correct email address of the receiver(s).'));
                    return;
                }
            }

            $message = $values['sitevideo_message'];
            $heading = ucfirst($video->getTitle());
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciver_ids, 'SITECHANNEL_SEND_EMAIL', array(
                'host'         => $_SERVER['HTTP_HOST'],
                'video_title'  => $heading,
                'message'      => '<div>' . $message . '</div>',
                'object_link'  => $url,
                'sender_name'  => $sender_name,
                'sender_email' => $sender_email,
                'queue'        => true,
            ));

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'  => false,
                'format'         => 'smoothbox',
                'messages'       => array(Zend_Registry::get('Zend_Translate')->_('Your message to your friend has been sent successfully.')),
            ));
        }
    }

    // ACTION FOR MAKE CHANNEL MAIN VIDEO
    public function makeChannelCoverAction()
    {
        // Get video
        $video   = Engine_Api::_()->getItemByGuid($this->_getParam('video'));
        $channel = Engine_Api::_()->getItemByGuid($this->_getParam('channel'));

        if (!$video || !($video instanceof Core_Model_Item_Abstract) || empty($video->video_id)) {
            return $this->_forward('requiresubject', 'error', 'core');
        }

        if (!$video->authorization()->isAllowed(null, 'edit')) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        // Make form
        $this->view->form  = $form  = new Sitevideo_Form_MakeChannelCover();
        $this->view->video = $video;

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // Process
        $table = $channel->getTable();
        $db    = $table->getAdapter();
        $db->beginTransaction();

        try {

            if (!empty($video)) {
                $channel->video_id = $video->video_id;
                $channel->save();
            }

            $db->commit();
        }
        // Otherwise it's probably a problem with the database or the storage system (just throw it)
         catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_forward('success', 'utility', 'core', array(
            'messages'       => array(Zend_Registry::get('Zend_Translate')->_('Set as Channel Main Video')),
            'smoothboxClose' => true,
        ));
    }

    //ACTION FOR MOVING A VIDEO FROM ONE CHANNEL INTO ANOTHER CHANNEL
    public function moveToOtherChannelAction()
    {
        // Get video
        $video   = Engine_Api::_()->getItemByGuid($this->_getParam('video'));
        $channel = Engine_Api::_()->getItemByGuid($this->_getParam('channel'));

        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$video || !($video instanceof Core_Model_Item_Abstract) || empty($video->video_id)) {
            return $this->_forward('requiresubject', 'error', 'core');
        }

        if (!$video->authorization()->isAllowed(null, 'edit')) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        // Make form
        $this->view->form  = $form  = new Sitevideo_Form_MoveToOtherChannel(array('item' => $channel));
        $this->view->video = $video;

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $table = $channel->getTable();
        $db    = $table->getAdapter();
        $db->beginTransaction();

        try {
            $values = $form->getValues();

            if (!empty($values['move'])) {
                $nextVideo              = $video->getNextVideo();
                $video->main_channel_id = $values['move'];
                $video->save();

                if (($viewer->level_id == 1) && !$video->getOwner()->isSelf($viewer)) {
                    $video->owner_id = $viewer->getIdentity();
                    $video->save();
                }

                // Change channel cover if necessary
                if (($nextVideo instanceof Sitevideo_Model_Video) &&
                    (int) $channel->video_id == (int) $video->getIdentity()) {
                    $channel->video_id = $nextVideo->getIdentity();
                    $channel->save();
                }

                // Update videos_count of both channels
                $channel->videos_count = $channel->videos_count - 1;
                $channel->save();

                $movingIntoChannel               = Engine_Api::_()->getItem('sitevideo_channel', $values['move']);
                $movingIntoChannel->videos_count = $movingIntoChannel->videos_count + 1;
                $movingIntoChannel->save();

                // Remove activity attachments for this video
                Engine_Api::_()->getDbtable('actions', 'activity')->detachFromActivity($video);
            }
            $db->commit();
        }
        // Otherwise it's probably a problem with the database or the storage system (just throw it)
         catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_forward('success', 'utility', 'core', array(
            'messages'       => array(sprintf(Zend_Registry::get('Zend_Translate')->_('Videos has been successfully moved to %s.'), $movingIntoChannel->getTitle())),
            'smoothboxClose' => true,
        ));
    }

    public function manageAction()
    {
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
        $this->_helper->content->setNoRender()->setEnabled();
    }

    // ACTION FOR VIDEO VIEW
    public function viewAction()
    {
        if (!$this->_helper->requireSubject()->isValid()) {
            return;
        }

        $video                        = Engine_Api::_()->core()->getSubject();
        $viewer                       = Engine_Api::_()->user()->getViewer();
        $video_id                     = $video->getIdentity();
        $sitevideo_password_protected = isset($_COOKIE["sitevideo_password_protected_$video_id"]) ? $_COOKIE["sitevideo_password_protected_$video_id"] : 0;
        if (isset($video->password) && !empty($video->password) && $video->owner_id != $viewer->getIdentity() && !$sitevideo_password_protected) {
            return $this->_forward('requireauth', 'error', 'sitevideo');
        }
        if (!$this->_helper->requireAuth()->setAuthParams($video, null, 'view')->isValid()) {
            return;
        }

        if ($video->parent_type && $video->parent_id) {
            //WHO CAN VIEW THE VIDEOS
            if (strpos($video->parent_type, "sitereview_listing") !== false) {
                $sitereview = Engine_Api::_()->getItem('sitereview_listing', $video->parent_id);

                //WHO CAN VIEW THE VIDEOS
                if (!$this->_helper->requireAuth()->setAuthParams($sitereview, null, "view_listtype_$sitereview->listingtype_id")->isValid()) {
                    return $this->_forwardCustom('requireauth', 'error', 'core');
                }
            } else {
                $isParentViewPrivacy = Engine_Api::_()->sitevideo()->isParentViewPrivacy($video);

                if (empty($isParentViewPrivacy)) {
                    return $this->_forwardCustom('requireauth', 'error', 'core');
                }

            }
        } else {

            //WHO CAN VIEW THE VIDEOS
            $this->view->viewPrivacy = 1;
            if (!$video->canView($viewer)) {
                $this->view->viewPrivacy = 0;
            }
            if (!$this->view->viewPrivacy) {
                return $this->_forward('requireauth', 'error', 'core');
            }
        }
        // Render
        $this->_helper->content
            ->setNoRender()
            ->setEnabled()
        ;
    }

    public function browseAction()
    {
        if (!$this->_helper->requireAuth()->setAuthParams('video', null, 'view')->isValid()) {
            return;
        }
        $this->_helper->content->setNoRender()->setEnabled();
    }

    public function pinboardAction()
    {
        if (!$this->_helper->requireAuth()->setAuthParams('video', null, 'view')->isValid()) {
            return;
        }
        $this->_helper->content->setNoRender()->setEnabled();
    }

    //ACTON FOR CATEGORIES PAGE
    public function categoriesAction()
    {

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.category.enabled', 1)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        if (!$this->_helper->requireAuth()->setAuthParams('video', null, 'view')->isValid()) {
            return;
        }

        $siteinfo         = $this->view->layout()->siteinfo;
        $titles           = $siteinfo['title'];
        $keywords         = $siteinfo['keywords'];
        $video_type_title = 'Videos';
        if (!empty($keywords)) {
            $keywords .= ' - ';
        }

        $keywords .= $video_type_title;
        $siteinfo['keywords']           = $keywords;
        $this->view->layout()->siteinfo = $siteinfo;

        $this->_helper->content
            ->setNoRender()
            ->setEnabled();
    }

    public function categoryHomeAction()
    {
        $request     = Zend_Controller_Front::getInstance()->getRequest();
        $category_id = $request->getParam('category_id', null);
        // Zend_Registry::set('sitevideoCategoryId', $category_id);
        //GET STORE OBJECT
        $pageTable  = Engine_Api::_()->getDbtable('pages', 'core');
        $pageSelect = $pageTable->select()->where('name = ?', "sitevideo_video_categories-home_category_$category_id");
        $pageObject = $pageTable->fetchRow($pageSelect);

        $this->_helper->content
            ->setContentName($pageObject->page_id)
            ->setNoRender()
            ->setEnabled();
    }

    //ACTION FOR GETTING THE AUTOSUGGESTED CHANNELS BASED ON SEARCHING
    public function getSearchVideosAction()
    {

        //GET CHANNELS AND MAKE ARRAY
        $usersitevideos = Engine_Api::_()->getDbtable('videos', 'sitevideo')->getDayItems($this->_getParam('text'), $this->_getParam('limit', 10));
        $data           = array();
        $mode           = $this->_getParam('struct');
        $count          = count($usersitevideos);
        if ($mode == 'text') {
            $i = 0;
            foreach ($usersitevideos as $usersitevideo) {
                $sitevideo_url = $usersitevideo->getHref();
                $i++;
                $content_video = $this->view->itemPhoto($usersitevideo, 'thumb.normal');
                $data[]        = array(
                    'id'            => $usersitevideo->video_id,
                    'label'         => $usersitevideo->title,
                    'video'         => $content_video,
                    'sitevideo_url' => $sitevideo_url,
                    'total_count'   => $count,
                    'count'         => $i,
                );
            }
        } else {
            $i = 0;
            foreach ($usersitevideos as $usersitevideo) {
                $sitevideo_url = $usersitevideo->getHref();
                $content_video = $this->view->itemPhoto($usersitevideo, 'thumb.normal');
                $i++;
                $data[] = array(
                    'id'            => $usersitevideo->video_id,
                    'label'         => $usersitevideo->title,
                    'video'         => $content_video,
                    'sitevideo_url' => $sitevideo_url,
                    'total_count'   => $count,
                    'count'         => $i,
                );
            }
        }
        if (!empty($data) && $i >= 1) {
            if ($data[--$i]['count'] == $count) {
                $data[$count]['id']            = 'stopevent';
                $data[$count]['label']         = $this->_getParam('text');
                $data[$count]['sitevideo_url'] = 'seeMoreLink';
                $data[$count]['total_count']   = $count;
            }
        }
        return $this->_helper->json($data);
    }

    //ACTION TO GET VIDEO-SUB-CATEGORY
    public function subCategoryAction()
    {

        //GET CATEGORY ID
        $category_id_temp  = $this->_getParam('category_id_temp');
        $showAllCategories = $this->_getParam('showAllCategories', 1);

        //INTIALIZE ARRAY
        $this->view->subcats = $data = array();

        //RETURN IF CATEGORY ID IS EMPTY
        if (empty($category_id_temp)) {
            return;
        }

        //GET CATEGORY TABLE
        $tableCategory = Engine_Api::_()->getDbtable('videoCategories', 'sitevideo');

        //GET CATEGORY
        $category = $tableCategory->getCategory($category_id_temp);
        if (!empty($category->category_name)) {
            $categoryName = Engine_Api::_()->getItem('sitevideo_video_category', $category_id_temp)->getCategorySlug();
        }
        //GET SUB-CATEGORY
        $subCategories = $tableCategory->getSubCategories(array('category_id' => $category_id_temp, 'fetchColumns' => array('category_id', 'category_name', 'category_slug'), 'havingChannels' => $showAllCategories));

        foreach ($subCategories as $subCategory) {
            $content_array                      = array();
            $content_array['category_name']     = $this->view->translate($subCategory->category_name);
            $content_array['category_id']       = $subCategory->category_id;
            $content_array['categoryname_temp'] = $categoryName;
            $content_array['category_slug']     = $subCategory->getCategorySlug();
            $data[]                             = $content_array;
        }

        $this->view->subcats = $data;
    }

    //ACTION FOR FETCHING SUB-CATEGORY
    public function subsubCategoryAction()
    {

        //GET SUB-CATEGORY ID
        $subcategory_id_temp = $this->_getParam('subcategory_id_temp');
        $showAllCategories   = $this->_getParam('showAllCategories', 1);
        //INTIALIZE ARRAY
        $this->view->subsubcats = $data = array();

        //RETURN IF SUB-CATEGORY ID IS EMPTY
        if (empty($subcategory_id_temp)) {
            return;
        }

        //GET CATEGORY TABLE
        $tableCategory = Engine_Api::_()->getDbTable('videoCategories', 'sitevideo');

        //GET SUB-CATEGORY
        $subCategory = $tableCategory->getCategory($subcategory_id_temp);
        if (!empty($subCategory->category_name)) {
            $subCategoryName = Engine_Api::_()->getItem('sitevideo_video_category', $subcategory_id_temp)->getCategorySlug();
        }

        //GET 3RD LEVEL CATEGORIES
        $subCategories = $tableCategory->getSubCategories(array('category_id' => $subcategory_id_temp, 'fetchColumns' => array('category_id', 'category_name', 'category_slug'), 'havingChannels' => $showAllCategories));
        foreach ($subCategories as $subCategory) {
            $content_array                      = array();
            $content_array['category_name']     = $this->view->translate($subCategory->category_name);
            $content_array['category_id']       = $subCategory->category_id;
            $content_array['categoryname_temp'] = $subCategoryName;
            $data[]                             = $content_array;
        }
        $this->view->subsubcats = $data;
    }

    //GET CATEGORIES ACTION
    public function getVideoCategoriesAction()
    {

        $element_value     = $this->_getParam('element_value', 1);
        $element_type      = $this->_getParam('element_type', 'category_id');
        $showAllCategories = $this->_getParam('showAllCategories', 1);

        $categoriesTable   = Engine_Api::_()->getDbTable('videoCategories', 'sitevideo');
        $categoryTableName = $categoriesTable->info('name');
        $select            = $categoriesTable->select()
            ->from($categoryTableName, array('category_id', 'category_name'))
            ->where($categoryTableName . ".$element_type = ?", $element_value);

        if ($element_type == 'category_id') {
            $select->where('cat_dependency = ?', 0)->where('subcat_dependency = ?', 0);
        } elseif ($element_type == 'cat_dependency') {
            $select->where('subcat_dependency = ?', 0);
        } elseif ($element_type == 'subcat_dependency') {
            $select->where('cat_dependency = ?', $element_value);
        }

        if (!$showAllCategories) {
            $tableVideos     = Engine_Api::_()->getDbTable('videos', 'sitevideo');
            $tableVideosName = $tableVideos->info('name');
            $select->setIntegrityCheck();
            if ($element_type == 'subcat_dependency') {
                $select->join($tableVideosName, "$tableVideosName.subcategory_id=$categoryTableName.$element_type", null);
            } else {
                $select->join($tableVideosName, "$tableVideosName.category_id=$categoryTableName.$element_type", null);
            }
            $select->where($tableVideosName . '.approved = ?', 1)->where($tableVideosName . '.draft = ?', 0)->where($tableVideosName . '.search = ?', 1)->where($tableVideosName . '.closed = ?', 0);
            $select = $tableVideos->getNetworkBaseSql($select, array('not_groupBy' => 1));
        }

        $select->group($categoryTableName . '.category_id');

        $categoriesData = $categoriesTable->fetchAll($select);

        $categories = array();
        if (Count($categoriesData) > 0) {
            foreach ($categoriesData as $category) {
                $data                  = array();
                $data['category_name'] = $this->view->translate($category->category_name);
                $data['category_id']   = $category->category_id;
                $data['category_slug'] = $category->getCategorySlug();
                $categories[]          = $data;
            }
        }

        $this->view->categories = $categories;
    }

    //ACTION FOR CONSTRUCT TAG CLOUD
    public function tagscloudAction()
    {

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.tags.enabled', 1)) {
            return $this->_forward('notfound', 'error', 'core');
        }
        if (!$this->_helper->requireAuth()->setAuthParams('video', null, 'view')->isValid()) {
            return;
        }
        $this->_helper->content
            ->setContentName('sitevideo_video_tagscloud')
            ->setNoRender()
            ->setEnabled();
    }

    //ACTION FOR BROWSE LOCATION VIDEOS.
    public function mapAction()
    {

        //GET PAGE OBJECT

        $pageTable  = Engine_Api::_()->getDbtable('pages', 'core');
        $pageSelect = $pageTable->select()->where('name = ?', "sitevideo_video_map");
        $pageObject = $pageTable->fetchRow($pageSelect);

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.location', 0)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        } else {
            $this->_helper->content->setContentName($pageObject->page_id)->setNoRender()->setEnabled();
        }
    }

    // ACTION FOR EDIT VIDEO LOCATION
    public function editLocationAction()
    {

        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }

        if (!$this->_helper->requireAuth()->setAuthParams('video', null, 'edit')->isValid()) {
            return;
        }

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.location', 0)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //GET PAGE ID, PAGE OBJECT AND THEN CHECK PAGE VALIDATION
        $resource      = Engine_Api::_()->getItemByGuid($this->_getParam('subject'));
        $resource_type = $resource->getType();

        $id               = 'video_id';
        $itemTable        = Engine_Api::_()->getDbtable('videos', 'sitevideo');
        $resource_id      = $resource->getIdentity();
        $this->view->form = $form = new Sitevideo_Form_Address(array('item' => $resource));

        //POPULATE FORM
        if (!$this->getRequest()->isPost()) {
            $form->populate($resource->toArray());
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {

            $values             = $form->getValues();
            $resource->location = $values['location'];
            if (empty($values['location'])) {
                //DELETE THE RESULT FORM THE TABLE.
                Engine_Api::_()->getDbtable('locationitems', 'seaocore')->delete(array('resource_id =?' => $resource_id, 'resource_type = ?' => $resource_type));
                $resource->seao_locationid = '0';
            }
            $resource->save();
            unset($values['submit']);

            if (!empty($values['location'])) {

                //DELETE THE RESULT FORM THE TABLE.
                Engine_Api::_()->getDbtable('locationitems', 'seaocore')->delete(array('resource_id =?' => $resource_id, 'resource_type = ?' => $resource_type));

                $seaoLocation = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocationItemId($values['location'], '', $resource_type, $resource_id);

                //group table entry of location id.
                $itemTable->update(array('seao_locationid' => $seaoLocation), array("$id =?" => $resource_id));
            }

            $db->commit();

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 500,
                'parentRefresh'  => 300,
                'messages'       => array(Zend_Registry::get('Zend_Translate')->_('Video location has been modified successfully.')),
            ));
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function embedAction()
    {
        // Get subject
        $this->view->video = $video = Engine_Api::_()->core()->getSubject('video');

        // Check if embedding is allowed
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.embeds', 1)) {
            $this->view->error = 1;
            return;
        } else if (isset($video->allow_embed) && !$video->allow_embed) {
            $this->view->error = 2;
            return;
        }

        // Get embed code
        $this->view->embedCode = $video->getEmbedCode();
    }

    public function externalAction()
    {
        // Get subject
        $this->view->video = $video = Engine_Api::_()->core()->getSubject();

        // Check if embedding is allowed
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.embeds', 1)) {
            $this->view->error = 1;
            return;
        } else if (isset($video->allow_embed) && !$video->allow_embed) {
            $this->view->error = 2;
            return;
        }

        // Get embed code
        $embedded = "";
        if ($video->status == 1) {
            $video->view_count++;
            $video->save();
            $embedded = $video->getRichContent(true);
        }

        // Track views from external sources
        Engine_Api::_()->getDbtable('statistics', 'core')
            ->increment('sitevideo.embedviews');

        // Get file location
        if ($video->checkType('upload') && $video->status == 1) {
            if (!empty($video->file_id)) {
                $storage_file = Engine_Api::_()->getItem('storage_file', $video->file_id);
                if ($storage_file) {
                    $this->view->video_location  = $storage_file->map();
                    $this->view->video_extension = $storage_file->extension;
                }
            }
        }
        $ratingTable              = Engine_Api::_()->getDbtable('ratings', 'sitevideo');
        $this->view->rating_count = $ratingTable->ratingCount(array('resource_id' => $video->getIdentity(), 'resource_type' => 'sitevideo_video'));

        $this->view->video         = $video;
        $this->view->videoEmbedded = $embedded;
        if ($video->category_id) {
            $this->view->category = Engine_Api::_()->getDbtable('videoCategories', 'sitevideo')->getCategory($video->category_id);
        }
    }

    public function checkPasswordProtectionAction()
    {
        $video_id                = (int) $this->_getParam('video_id');
        $video                   = Engine_Api::_()->getItem('sitevideo_video', $video_id);
        $password                = $this->_getParam('password');
        $checkPasswordProtection = $video->checkPasswordProtection($password);
        $view                    = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        if (!$checkPasswordProtection) {
            setcookie("sitevideo_password_protected_$video_id", $checkPasswordProtection, time() + 60 * 60 * 24 * 30, $view->url(array(), 'default', true));
            echo Zend_Json::encode(array('status' => 0));
        } else {
            setcookie("sitevideo_password_protected_$video_id", $checkPasswordProtection, time() + 60 * 60 * 24 * 30, $view->url(array(), 'default', true));
            echo Zend_Json::encode(array('status' => 1));
        }
        exit();
    }

    //ACTION FOR ADD VIDEOS
    public function createAction()
    {

        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
        if (isset($_GET['ul'])) {
            return $this->_forward('upload-video', null, null, array('format' => 'json'));
        }
        if (isset($_GET['ulStream'])) {
            return $this->_forward('upload-stream', null, null, array('format' => 'json'));
        }
        $viewer     = Engine_Api::_()->user()->getViewer();
        $viewerId   = Engine_Api::_()->user()->getViewer()->user_id;
        $channel_id = $this->_getParam('channel_id');

        if ($channel_id) {
            $this->view->channel = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);
        }

        if (isset($_FILES['Filedata'])) {
            $_POST['id'] = $this->uploadVideoAction();
        }

        $this->view->parent_type = $parent_type = $this->_getParam('parent_type');
        $this->view->parent_id   = $parent_id   = $this->_getParam('parent_id');
        $videoTable              = Engine_Api::_()->getDbtable('videos', 'sitevideo');
        $userVideoCount          = $videoTable
            ->select()
            ->from($videoTable->info('name'), new Zend_Db_Expr('COUNT(video_id)'))
            ->where('owner_id = ?', $viewerId)
            ->limit(1)
            ->query()
            ->fetchColumn();
        if (strpos($parent_type, "sitereview_listing") !== false) {
            //AUTHORIZATION CHECK
            $subject        = Engine_Api::_()->getItem('sitereview_listing', $parent_id);
            $userVideoCount = $videoTable
                ->select()
                ->from($videoTable->info('name'), array('count(*) as count'))
                ->where("parent_type = ?", $parent_type)
                ->where("parent_id =?", $parent_id)
                ->query()
                ->fetchColumn();

            $this->view->canCreate = $canCreate = Engine_Api::_()->sitereview()->allowVideo($subject, $viewer, $userVideoCount);
        } else {
            if ($parent_type && $parent_id) {
                $this->view->parentTypeItem = $parentTypeItem = Engine_Api::_()->getItem($parent_type, $parent_id);
            }

            $this->view->canCreate = $canCreate = Engine_Api::_()->sitevideo()->isCreatePrivacy($parent_type, $parent_id);
        }

        if (empty($canCreate)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        // Render
        $this->_helper->content->setEnabled();
        $db = Engine_Db_Table::getDefaultAdapter();
        //GET DEFAULT PROFILE TYPE ID
        $this->view->defaultProfileId = $defaultProfileId = Engine_Api::_()->getDbTable('metasVideos', 'sitevideo')->defaultProfileId();

        // Get form
        $this->view->form = $form = new Sitevideo_Form_Video(array('defaultProfileId' => $defaultProfileId));

        if (!empty($viewerId)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        $sitevideoCreateVideo = Zend_Registry::isRegistered('sitevideoCreateVideo') ? Zend_Registry::get('sitevideoCreateVideo') : null;
        $maxAllowedVideo      = Engine_Api::_()->authorization()->getPermission($level_id, 'sitevideo_video', 'sitevideo_max_allowed_video');
        if (!empty($maxAllowedVideo)) {

            if ($userVideoCount >= $maxAllowedVideo) {
                $error = sprintf(Zend_Registry::get('Zend_Translate')->_('You have exceeded maximum videos upload limit. You are allowed to upload maximum %s videos only.'), $maxAllowedVideo);

                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }
        }
        if ($channel_id) {
            $form->getElement('channel')->setValue($channel_id);
        }

        if (!$this->getRequest()->isPost()) {
            return;
        }
        $title = "";
        $data  = $this->getRequest()->getPost();
        if ($data['type'] == 'upload' && empty($data['title'])) {
            $video = Engine_Api::_()->getItem('sitevideo_video', $this->_getParam('id'));
            if (!empty($video->title)) {
                $data['title'] = $video->title;
            }

        }
        if (!$form->isValid($data)) {
            return;
        }
        $isVideoTypeValid = Engine_Api::_()->sitevideo()->isVideoTypeValid();
        if (empty($isVideoTypeValid) || empty($sitevideoCreateVideo)) {
            return;
        }

        $values = $form->getValues();
        $db->beginTransaction();

        try {
            $insert_action = false;
            $values        = $form->getValues();
            $table         = Engine_Api::_()->getDbtable('videos', 'sitevideo');
            if ($values['type'] == 'upload' || $values['type'] == 'stream') {
                $video = Engine_Api::_()->getItem('sitevideo_video', $this->_getParam('id'));
            } else {
                $video = $table->createRow();
            }
            $video = $form->saveValues($video);

            if ($parent_type && $parent_id) {
                $video->parent_type = $parent_type;
                $video->parent_id   = $parent_id;
                $video->save();
            }

            //ADDING TAGS
            $keywords = '';
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.tags.enabled', 1)) {
                $tags = $values['tags'];
                if (!empty($tags)) {
                    $tags = preg_split('/[,]+/', $tags);
                    $tags = array_filter(array_map("trim", $tags));
                    $video->tags()->addTagMaps($viewer, $tags);

                    foreach ($tags as $tag) {
                        $keywords .= " $tag";
                    }
                }
            }
            $categoryIds[]       = $video->category_id;
            $categoryIds[]       = $video->subcategory_id;
            $categoryIds[]       = $video->subsubcategory_id;
            $video->profile_type = Engine_Api::_()->getDbtable('videoCategories', 'sitevideo')->getProfileType(array('categoryIds' => $categoryIds, 'category_id' => 0));

            // Set the information of the User Channel
            Engine_Api::_()->sitevideo()->setUserChannelInfo();

            //SAVE LOCATION
            $location = $form->getValue('sitevideo_location');
            if (!empty($location)) {
                $seaoLocationId         = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocationItemId($location, '', $video->getType(), $video->getIdentity());
                $video->seao_locationid = $seaoLocationId;
                $video->location        = $location;
            }
            $video->save();

            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.location', 0)) {
                $video->setLocation();
            }
            // Now try to create thumbnail
            if ($video->type == 'iframely') {

                $information = $this->handleIframelyInformation($values['url']);
                if (empty($information)) {
                    $form->addError('We could not find a video there - please check the URL and try again.');
                } else {
                    if (!$video->photo_id) {
                      $video->saveVideoThumbnail($information['thumbnail']);
                    }
                    $video->duration    = $information['duration'];
                    $video->description = $information['description'];
                    $video->code        = $information['code'];
                    $video->save();
                    $insert_action = true;
                }
            } else {
                  if (!$video->photo_id) {
                    if( !$video->checkType('embedcode') && !$video->checkType('twitter') && !$video->checkType('pinterest') ) {
                      $thumbnail = $this->handleThumbnail($video->type, $video->code);
                    } else {
                      $thumbnail = $form->thumbnail;
                    }
                    $temVideo = $video->saveVideoThumbnail($thumbnail);
                  }else{
                    $temVideo = $video;
                  }

                if ($temVideo) {
                    $information = $this->handleInformation($video->type, $video->code);
                    if (!empty($information)) {
                        if (isset($information['duration'])) {
                            $video->duration = $information['duration'];
                        }
                        if (!$video->description) {
                            $video->description = $information['description'];
                        }
                        if (isset($information['url']) && !empty($information['url'])) {
                            $video->code = $information['url'];
                        }
                        $video->save();
                    }
                    // Insert new action item
                    $insert_action = true;
                }
            }

            if ($values['type'] != 'upload' && $values['type'] != 'stream') {
                $video->status = 1;
                $video->save();
                $insert_action = true;
            }
            //SAVE CUSTOM VALUES AND PROFILE TYPE VALUE

            $customfieldform = $form->getSubForm('fields');
            $customfieldform->setItem($video);
            $customfieldform->saveValues();

            $generateFeed = true;
            if (isset($values['password']) && !empty($values['password'])) {
                $video->search   = 0;
                $video->password = $values['password'];
                $generateFeed    = false;
            } else {
                $video->password = '';
            }
            $video->synchronized = 1;
            $video->save();

            if (!empty($video->main_channel_id)) {
                $video->addVideomap();
                $videoChannel = Engine_Api::_()->getItem('sitevideo_channel', $video->main_channel_id);
                //Send Site Notification
                Engine_Api::_()->getApi('core', 'sitevideo')->sendSiteNotification($video, $videoChannel, 'sitevideo_video_new');
                //Send Email Notification
                Engine_Api::_()->getApi('core', 'sitevideo')->sendEmailNotification($video, $videoChannel, 'sitevideo_video_new', 'SITEVIDEO_CREATENOTIFICATION_EMAIL');
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $db->beginTransaction();
        try {
            if ($video->status && $insert_action && $generateFeed) {
                $owner = $video->getOwner();

                if (isset($values['channel']) && !empty($values['channel'])) {
                    $channel = Engine_Api::_()->getItem('sitevideo_channel', $values['channel']);
                    $action  = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $channel, 'sitevideo_channel_video_new');
                } else if ($parent_type == 'sitepage_page') {
                    $sitepage = Engine_Api::_()->getItem('sitepage_page', $parent_id);
                    $action   = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $sitepage, 'sitevideo_sitepage_video_new');
                } else if ($parent_type == 'sitebusiness_business') {
                    $sitebusiness = Engine_Api::_()->getItem('sitebusiness_business', $parent_id);
                    $action       = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $sitebusiness, 'sitevideo_sitebusiness_video_new');
                } else if ($parent_type == 'sitegroup_group') {
                    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $parent_id);
                    $action    = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $sitegroup, 'sitevideo_sitegroup_video_new');
                } else if ($parent_type == 'sitestore_store') {
                    $sitestore = Engine_Api::_()->getItem('sitestore_store', $parent_id);
                    $action    = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $sitestore, 'sitevideo_sitestore_video_new');
                } else if ($parent_type == 'sitestoreproduct_product') {
                    $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $parent_id);
                    $action    = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $sitestoreproduct, 'sitevideo_sitestoreproduct_video_new');
                } else if ($parent_type == 'siteevent_event') {
                    $siteevent = Engine_Api::_()->getItem('siteevent_event', $parent_id);
                    $action    = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $siteevent, 'sitevideo_siteevent_video_new');
                } else if (strpos($parent_type, 'sitereview_listing') !== false) {
                    $sitereview = Engine_Api::_()->getItem('sitereview_listing', $parent_id);
                    $action     = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $sitereview, 'sitereview_video_new_listtype_' . $sitereview->listingtype_id);
                } else {
                    $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $video, 'sitevideo_video_new');
                }

                if ($action != null) {
                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $video);
                }
                $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                foreach ($actionTable->getActionsByObject($video) as $action) {
                    $actionTable->resetActivityBindings($action);
                }
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        //UPDATE KEYWORDS IN SEARCH TABLE
        if (!empty($keywords)) {
            Engine_Api::_()->getDbTable('search', 'core')->update(array('keywords' => $keywords), array('type = ?' => 'video', 'id = ?' => $video->video_id));
        }

        //SENDING ACTIVITY FEED TO FACEBOOK.
        $enable_Facebooksefeed = $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
        if (!empty($enable_Facebooksefeed)) {

            $sitevideo_array           = array();
            $sitevideo_array['type']   = 'sitevideo_video_new';
            $sitevideo_array['object'] = $video;

            Engine_Api::_()->facebooksefeed()->sendFacebookFeed($sitevideo_array);
        }
        if (empty($parent_type)) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitevideo_video_general', true);
        }

        return $this->_redirect($this->getRequest()->getServer('HTTP_REFERER'));
    }

    //ACTION FOR UPLOADING VIDEOS BY FANCY UPLOADER
    public function uploadVideoAction()
    {
        if (!$this->_helper->requireUser()->checkRequire()) {
            $this->view->status = false;
            $this->view->error  = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error  = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        $values = $this->getRequest()->getPost();
        $response = Seaocore_Service_FancyUpload::upload();


        if(!empty($response['error'])) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload') . $response['error'];
            return;
        }


        if(empty($response['path']) || empty(file_exists($response['path']))) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
            return;
        }

        $illegal_extensions = array('php', 'pl', 'cgi', 'html', 'htm', 'txt');

        if( in_array(pathinfo($response['path'], PATHINFO_EXTENSION), $illegal_extensions) ) {
            $this->view->status = false;
            $this->view->error  = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
            return;
        }

        $db = Engine_Api::_()->getDbtable('videos', 'sitevideo')->getAdapter();
        $db->beginTransaction();

        try {
            $viewer             = Engine_Api::_()->user()->getViewer();
            $values['owner_id'] = $viewer->getIdentity();

            $params = array(
                'owner_type' => 'user',
                'owner_id'   => $viewer->getIdentity(),
            );
            $video = Engine_Api::_()->sitevideo()->createVideo($params, $response['path'], $values);
            $this->view->status   = true;
            $this->view->name     = $response['name'];
            $this->view->code     = $video->code;
            $this->view->video_id = $video->video_id;

            // sets up title and owner_id now just incase members switch page as soon as upload is completed
            $video->title = $response['name'];
            $video->description = $response['name'];
            $video->owner_id    = $viewer->getIdentity();
            $video->type        = $video->type? : 'upload';
            $video->status      = 0;
            $video->save();
            $db->commit();
            return $video->video_id;
        } catch (Exception $e) {
            $db->rollBack();
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_($e->getMessage());
            // throw $e;
            return;
        }
        @unlink($response['path']);
    }

    public function uploadStreamAction()
    {
      if( !$this->_helper->requireUser()->checkRequire() ) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
        return;
      }

      if( !$this->getRequest()->isPost() ) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
        return;
      }

      $values = $this->getRequest()->getPost();
      $response = Seaocore_Service_FancyUpload::upload();


      if( !empty($response['error']) ) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload') . $response['error'];
        return;
      }


      if( empty($response['path']) || empty(file_exists($response['path'])) ) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
        return;
      }

      $illegal_extensions = array('php', 'pl', 'cgi', 'html', 'htm', 'txt');

      if( in_array(pathinfo($response['path'], PATHINFO_EXTENSION), $illegal_extensions) ) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
        return;
      }

      $db = Engine_Api::_()->getDbtable('videos', 'sitevideo')->getAdapter();
      $db->beginTransaction();

      try {
        $viewer = Engine_Api::_()->user()->getViewer();
        $values['owner_id'] = $viewer->getIdentity();

        $params = array(
          'type' => 'stream',
          'owner_type' => 'user',
          'owner_id' => $viewer->getIdentity(),
        );
        $video = Engine_Api::_()->sitevideo()->createStreamVideo($params, $response['path']);
        $this->view->status = true;
        $this->view->name = $response['name'];
        $this->view->code = $video->code;
        $this->view->video_id = $video->video_id;
        // sets up title and owner_id now just incase members switch page as soon as upload is completed
        $video->title = $response['name'];
        $video->description = $response['name'];
        $video->save();
        $db->commit();
        return $video->video_id;
      } catch( Exception $e ) {
        $db->rollBack();
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_($e->getMessage());
        // throw $e;
        return;
      }
      // @unlink($response['path']);
    }

  // HELPER FUNCTIONS

    public function extractCode($url, $type)
    {
        switch ($type) {
            //youtube
            case "youtube":
                // change new youtube URL to old one
                $new_code = @pathinfo($url);
                $url      = preg_replace("/#!/", "?", $url);

                // get v variable from the url
                $arr = array();
                $arr = @parse_url($url);
                if ($arr['host'] === 'youtu.be') {
                    $data = explode("?", $new_code['basename']);
                    $code = $data[0];
                } else {
                    $parameters = $arr["query"];
                    parse_str($parameters, $data);
                    $code = $data['v'];
                    if ($code == "") {
                        $code = $new_code['basename'];
                    }
                }
                return $code;
            //vimeo
            case "vimeo":
                // get the first variable after slash
                $code = @pathinfo($url);
                return isset($code['basename']) ? $code['basename'] : "";
            case "dailymotion":
                // get the first variable after slash
                $code = @pathinfo($url);
                return isset($code['basename']) ? $code['basename'] : "";
            case "instagram":
            case "twitter":
                return $url;
        }
    }

    // YouTube Functions
    public function checkYouTube($code)
    {
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $key          = $coreSettings->getSetting('sitevideo.youtube.apikey', $coreSettings->getSetting('video.youtube.apikey'));
        if (!$data = @file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=id&id=' . $code . '&key=' . $key)) {
            return false;
        }

        $data = Zend_Json::decode($data);
        if (empty($data['items'])) {
            return false;
        }

        return true;
    }

    // Vimeo Functions
    public function checkVimeo($code)
    {
        //http://www.vimeo.com/api/docs/simple-api
        //http://vimeo.com/api/v2/video
        $data = @simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
        $id   = count($data->video->id);
        if ($id == 0) {
            return false;
        }

        return true;
    }

    public function checkDailymotion($code)
    {
        $path = "http://www.dailymotion.com/services/oembed?url=http://www.dailymotion.com/video/" . $code;
        $data = @file_get_contents($path);
        return ((is_string($data) &&
            (is_object(json_decode($data)) ||
                is_array(json_decode($data))))) ? true : false;
    }

    public function checkInstagram($code)
    {
        $path = "https://api.instagram.com/oembed?url=" . $code;
        $data = @file_get_contents($path);
        return ((is_string($data) &&
            (is_object(json_decode($data)) ||
                is_array(json_decode($data))))) ? true : false;
    }

    public function checkTwitter($code)
    {
        $path = "https://api.twitter.com/1/statuses/oembed.json?id=" . $code;
        $data = @file_get_contents($path);
        return ((is_string($data) &&
            (is_object(json_decode($data)) ||
                is_array(json_decode($data))))) ? true : false;
    }

    // handles thumbnails
    public function handleThumbnail($type, $code = null)
    {
        switch ($type) {

            //youtube
            case "youtube":
                $thumbnail     = "";
                $thumbnailSize = array('maxresdefault', 'sddefault', 'hqdefault', 'mqdefault', 'default');
                foreach ($thumbnailSize as $size) {
                    $thumbnailUrl = "https://i.ytimg.com/vi/$code/$size.jpg";
                    $file_headers = @get_headers($thumbnailUrl);
                    if (isset($file_headers[0]) && strpos($file_headers[0], '404 Not Found') == false) {
                        $thumbnail = $thumbnailUrl;
                        break;
                    }
                }
                return $thumbnail;
            //vimeo
            case "vimeo":
                $thumbnail = "";
                $data      = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
                if (isset($data->video->thumbnail_large)) {
                    $thumbnail = $data->video->thumbnail_large;
                } else if (isset($data->video->thumbnail_medium)) {
                    $thumbnail = $data->video->thumbnail_medium;
                } else if (isset($data->video->thumbnail_small)) {
                    $thumbnail = $data->video->thumbnail_small;
                }

                return $thumbnail;
            //dailymotion
            case "dailymotion":
                $thumbnail      = "";
                $thumbnailUrl   = 'https://api.dailymotion.com/video/' . $code . '?fields=thumbnail_small_url,thumbnail_large_url,thumbnail_medium_url';
                $json_thumbnail = file_get_contents($thumbnailUrl);
                if ($json_thumbnail) {
                    $thumbnails = json_decode($json_thumbnail);
                    if (isset($thumbnails->thumbnail_large_url)) {
                        $thumbnail = $thumbnails->thumbnail_large_url;
                    } else if (isset($thumbnails->thumbnail_medium_url)) {
                        $thumbnail = $thumbnails->thumbnail_medium_url;
                    } else if (isset($thumbnails->thumbnail_small_url)) {
                        $thumbnail = $thumbnails->thumbnail_small_url;
                    }
                }
                return $thumbnail;
            case "instagram":
                $thumbnail = "";
                $path      = "https://api.instagram.com/oembed/?url=" . $code;
                $data      = @file_get_contents($path);
                if (((is_string($data) && (is_object(json_decode($data)) || is_array(json_decode($data)))))) {
                    $instagramData = Zend_Json::decode($data);
                    $thumbnail     = $instagramData['thumbnail_url'];
                }
                return $thumbnail;
        }
    }

    // retrieves infromation and returns title + desc
    public function handleInformation($type, $code)
    {
        switch ($type) {
            //youtube
            case "youtube":
                $key  = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.youtube.apikey');
                $data = file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails&id=' . $code . '&key=' . $key);
                if (empty($data)) {
                    return;
                }
                $data                       = Zend_Json::decode($data);
                $information                = array();
                $youtube_video              = $data['items'][0];
                $information['title']       = $youtube_video['snippet']['title'];
                $information['description'] = $youtube_video['snippet']['description'];
                $information['duration']    = Engine_Date::convertISO8601IntoSeconds($youtube_video['contentDetails']['duration']);
                return $information;
            //vimeo
            case "vimeo":
                //thumbnail_medium
                $data                       = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
                $information                = array();
                $information['title']       = $data->video->title;
                $information['description'] = $data->video->description;
                $information['duration']    = $data->video->duration;
                //http://img.youtube.com/vi/Y75eFjjgAEc/default.jpg
                return $information;
            //dailymotion
            case "dailymotion":
                $path = "http://www.dailymotion.com/services/oembed?url=http://www.dailymotion.com/video/" . $code;

                $data        = @file_get_contents($path);
                $information = array();
                if (((is_string($data) && (is_object(json_decode($data)) || is_array(json_decode($data)))))) {
                    $dailymotionData = Zend_Json::decode($data);

                    $information['title']       = $dailymotionData['title'];
                    $information['description'] = $dailymotionData['description'];
                    $durationUrl                = 'https://api.dailymotion.com/video/' . $code . '?fields=duration';

                    $json_duration = file_get_contents($durationUrl);
                    if ($json_duration) {
                        $durationDecode          = json_decode($json_duration);
                        $information['duration'] = $durationDecode->duration;
                    }
                }
                return $information;
            case "instagram":
                $path        = "https://api.instagram.com/oembed/?url=" . $code;
                $data        = @file_get_contents($path);
                $information = array();
                if (((is_string($data) && (is_object(json_decode($data)) || is_array(json_decode($data)))))) {
                    $instagramData              = Zend_Json::decode($data);
                    $information['title']       = $instagramData['title'];
                    $information['description'] = "";
                }
                return $information;
            case "twitter":
                $path        = "https://api.twitter.com/1/statuses/oembed.json?id=" . $code;
                $data        = @file_get_contents($path);
                $information = array();
                if (((is_string($data) && (is_object(json_decode($data)) || is_array(json_decode($data)))))) {
                    $twitterData        = Zend_Json::decode($data);
                    $information['url'] = $twitterData['url'];
                }
                return $information;
        }
    }

    private function getFFMPEGPath()
    {
        // Check we can execute
        if (!function_exists('shell_exec')) {
            throw new Sitevideo_Model_Exception('Unable to execute shell commands using shell_exec(); the function is disabled.');
        }

        if (!function_exists('exec')) {
            throw new Sitevideo_Model_Exception('Unable to execute shell commands using exec(); the function is disabled.');
        }

        // Make sure FFMPEG path is set
        $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->sitevideo_ffmpeg_path;
        if (!$ffmpeg_path) {
            throw new Sitevideo_Model_Exception('Ffmpeg not configured');
        }

        // Make sure FFMPEG can be run
        if (!@file_exists($ffmpeg_path) || !@is_executable($ffmpeg_path)) {
            $output = null;
            $return = null;
            exec($ffmpeg_path . ' -version', $output, $return);

            if ($return > 0) {
                throw new Sitevideo_Model_Exception('Ffmpeg found, but is not executable');
            }
        }

        return $ffmpeg_path;
    }

    private function getTmpDir()
    {
        // Check the video temporary directory
        $tmpDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' .
            DIRECTORY_SEPARATOR . 'sitevideo';

        if (!is_dir($tmpDir) && !mkdir($tmpDir, 0777, true)) {
            throw new Sitevideo_Model_Exception('Video temporary directory did not exist and could not be created.');
        }

        if (!is_writable($tmpDir)) {
            throw new Sitevideo_Model_Exception('Video temporary directory is not writable.');
        }

        return $tmpDir;
    }

    private function getVideo($video)
    {
        // Get the video object
        if (is_numeric($video)) {
            $video = Engine_Api::_()->getItem('sitevideo_video', $video);
        }

        if (!($video instanceof Sitevideo_Model_Video)) {
            throw new Sitevideo_Model_Exception('Argument was not a valid video');
        }

        return $video;
    }

    private function getStorageObject($video)
    {
        // Pull video from storage system for encoding
        $storageObject = Engine_Api::_()->getItem('storage_file', $video->file_id);

        if (!$storageObject) {
            throw new Sitevideo_Model_Exception('Video storage file was missing');
        }

        return $storageObject;
    }

    private function getOriginalPath($storageObject)
    {
        $originalPath = $storageObject->temporary();

        if (!file_exists($originalPath)) {
            throw new Sitevideo_Model_Exception('Could not pull to temporary file');
        }

        return $originalPath;
    }

    private function getVideoFilters($video, $width, $height)
    {
        $filters = "scale=$width:$height";

        if ($video->rotation > 0) {
            $filters = "pad='max(iw,ih*($width/$height))':ow/($width/$height):(ow-iw)/2:(oh-ih)/2,$filters";

            if ($video->rotation == 180) {
                $filters = "hflip,vflip,$filters";
            } else {
                $transpose = array(90 => 1, 270 => 2);

                if (empty($transpose[$video->rotation])) {
                    throw new Sitevideo_Model_Exception('Invalid rotation value');
                }

                $filters = "transpose=${transpose[$video->rotation]},$filters";
            }
        }

        return $filters;
    }

    private function conversionSucceeded($video, $videoOutput, $outputPath)
    {
        $success = true;

        // Unsupported format
        if (preg_match('/Unknown format/i', $videoOutput) ||
            preg_match('/Unsupported codec/i', $videoOutput) ||
            preg_match('/patch welcome/i', $videoOutput) ||
            preg_match('/Audio encoding failed/i', $videoOutput) ||
            !is_file($outputPath) ||
            filesize($outputPath) <= 0) {
            $success       = false;
            $video->status = 3;
        }

        // This is for audio files
        else if (preg_match('/video:0kB/i', $videoOutput)) {
            $success       = false;
            $video->status = 5;
        }

        return $success;
    }

    private function notifyOwner($video, $owner)
    {
        $translate = Zend_Registry::get('Zend_Translate');
        $language  = !empty($owner->language) && $owner->language != 'auto' ? $owner->language : null;

        $notificationMessage = '';
        $exceptionMessage    = 'Unknown encoding error.';

        if ($video->status == 3) {
            $exceptionMessage    = 'Video format is not supported by FFMPEG.';
            $notificationMessage = 'Video conversion failed. Video format is not supported by FFMPEG. Please try %1$sagain%2$s.';
        } else if ($video->status == 5) {
            $exceptionMessage    = 'Audio-only files are not supported.';
            $notificationMessage = 'Video conversion failed. Audio files are not supported. Please try %1$sagain%2$s.';
        } else if ($video->status == 7) {
            $notificationMessage = 'Video conversion failed. You may be over the site upload limit.  Try %1$suploading%2$s a smaller file, or delete some files to free up space.';
        }

        $notificationMessage = $translate->translate(sprintf($notificationMessage, '', ''), $language);

        Engine_Api::_()->getDbtable('notifications', 'activity')
            ->addNotification($owner, $owner, $video, 'video_processed_failed', array(
                'message'      => $notificationMessage,
                'message_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'sitevideo_general', true),
            ));

        return $exceptionMessage;
    }

    private function getDuration($videoOutput)
    {
        $duration = 0;

        if (preg_match('/Duration:\s+(.*?)[.]/i', $videoOutput, $matches)) {
            list($hours, $minutes, $seconds) = preg_split('[:]', $matches[1]);
            $duration                        = ceil($seconds + ($minutes * 60) + ($hours * 3600));
        }

        return $duration;
    }

    private function generateThumbnail($outputPath, $output, $thumb_splice, $thumbPath, $log)
    {
        $ffmpeg_path = $this->getFFMPEGPath();

        // Thumbnail process command
        $thumbCommand = $ffmpeg_path . ' '
        . '-i ' . escapeshellarg($outputPath) . ' '
        . '-f image2' . ' '
        . '-ss ' . $thumb_splice . ' '
        . '-vframes 1' . ' '
        . '-v 2' . ' '
        . '-y ' . escapeshellarg($thumbPath) . ' '
            . '2>&1';

        // Process thumbnail
        $thumbOutput = $output .
        $thumbCommand . PHP_EOL .
        shell_exec($thumbCommand);

        // Log thumb output
        if ($log) {
            $log->log($thumbOutput, Zend_Log::INFO);
        }

        // Check output message for success
        $thumbSuccess = true;
        if (preg_match('/video:0kB/i', $thumbOutput)) {
            $thumbSuccess = false;
        }

        // Resize thumbnail
        if ($thumbSuccess) {
            try {
                $image = Engine_Image::factory();
                $image->open($thumbPath)
                    ->resize(120, 240)
                    ->write($thumbPath)
                    ->destroy();
            } catch (Exception $e) {
                $this->_addMessage((string) $e->__toString());
                $thumbSuccess = false;
            }
        }

        return $thumbSuccess;
    }

    private function buildVideoCmd($video, $width, $height, $type, $originalPath, $outputPath, $compatibilityMode = false)
    {
        $ffmpeg_path = $this->getFFMPEGPath();

        $videoCommand = $ffmpeg_path . ' '
        . '-i ' . escapeshellarg($originalPath) . ' '
            . '-ab 64k' . ' '
            . '-ar 44100' . ' '
            . '-qscale 5' . ' '
            . '-r 25' . ' ';

        if ($type == 'mp4') {
            $videoCommand .= '-vcodec libx264' . ' '
                . '-acodec aac' . ' '
                . '-strict experimental' . ' '
                . '-preset veryfast' . ' '
                . '-f mp4' . ' '
            ;
        } else {
            $videoCommand .= '-vcodec flv -f flv ';
        }

        if ($compatibilityMode) {
            $videoCommand .= "-s ${width}x${height}" . ' ';
        } else {
            $filters = $this->getVideoFilters($video, $width, $height);
            $videoCommand .= '-vf "' . $filters . '" ';
        }

        $videoCommand .=
        '-y ' . escapeshellarg($outputPath) . ' '
            . '2>&1';

        return $videoCommand;
    }

//    protected function _process($video, $type, $compatibilityMode = false)
//    {
//        $tmpDir = $this->getTmpDir();
//        $video  = $this->getVideo($video);
//
//        // Update to encoding status
//        $video->status = 2;
//        $video->type   = 'upload';
//        $video->save();
//
//        // Prepare information
//        $owner = $video->getOwner();
//
//        // Pull video from storage system for encoding
//        $storageObject = $this->getStorageObject($video);
//        $originalPath  = $this->getOriginalPath($storageObject);
//
//        $outputPath = $tmpDir . DIRECTORY_SEPARATOR . $video->getIdentity() . '_vconverted.' . $type;
//        $thumbPath  = $tmpDir . DIRECTORY_SEPARATOR . $video->getIdentity() . '_vthumb.jpg';
//
//        $width  = 480;
//        $height = 386;
//
//        $videoCommand = $this->buildVideoCmd($video, $width, $height, $type, $originalPath, $outputPath, $compatibilityMode);
//
//        // Prepare output header
//        $output = PHP_EOL;
//        $output .= $originalPath . PHP_EOL;
//        $output .= $outputPath . PHP_EOL;
//        $output .= $thumbPath . PHP_EOL;
//
//        // Prepare logger
//        $log = new Zend_Log();
//        $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/sitevideo.log'));
//
//        // Execute video encode command
//        $videoOutput = $output .
//        $videoCommand . PHP_EOL .
//        shell_exec($videoCommand);
//
//        // Log
//        if ($log) {
//            $log->log($videoOutput, Zend_Log::INFO);
//        }
//
//        // Check for failure
//        $success = $this->conversionSucceeded($video, $videoOutput, $outputPath);
//
//        // Failure
//        if (!$success) {
//            if (!$compatibilityMode) {
//                $this->_process($video, true);
//                return;
//            }
//
//            $exceptionMessage = '';
//
//            $db = $video->getTable()->getAdapter();
//            $db->beginTransaction();
//
//            try {
//                $video->save();
//                $exceptionMessage = $this->notifyOwner($video, $owner);
//                $db->commit();
//            } catch (Exception $e) {
//                $videoOutput .= PHP_EOL . $e->__toString() . PHP_EOL;
//
//                if ($log) {
//                    $log->write($e->__toString(), Zend_Log::ERR);
//                }
//
//                $db->rollBack();
//            }
//
//            // Write to additional log in dev
//            if (APPLICATION_ENV == 'development') {
//                file_put_contents($tmpDir . '/' . $video->video_id . '.txt', $videoOutput);
//            }
//
//            throw new Sitevideo_Model_Exception($exceptionMessage);
//        }
//
//        // Success
//        else {
//            // Get duration of the video to caculate where to get the thumbnail
//            $duration = $this->getDuration($videoOutput);
//
//            // Log duration
//            if ($log) {
//                $log->log('Duration: ' . $duration, Zend_Log::INFO);
//            }
//
//            // Fetch where to take the thumbnail
//            $thumb_splice = $duration / 2;
//
//            $thumbSuccess = $this->generateThumbnail($outputPath, $output, $thumb_splice, $thumbPath, $log);
//
//            // Save video and thumbnail to storage system
//            $params = array(
//                'parent_id'   => $video->getIdentity(),
//                'parent_type' => $video->getType(),
//                'user_id'     => $video->owner_id,
//            );
//
//            $db = $video->getTable()->getAdapter();
//            $db->beginTransaction();
//
//            try {
//                $storageObject->setFromArray($params);
//                $storageObject->store($outputPath);
//
//                if ($thumbSuccess) {
//                    $thumbFileRow = Engine_Api::_()->storage()->create($thumbPath, $params);
//                }
//
//                $db->commit();
//            } catch (Exception $e) {
//                $db->rollBack();
//
//                // delete the files from temp dir
//                unlink($originalPath);
//                unlink($outputPath);
//
//                if ($thumbSuccess) {
//                    unlink($thumbPath);
//                }
//
//                $video->status = 7;
//                $video->save();
//
//                $this->notifyOwner($video, $owner);
//
//                throw $e; // throw
//            }
//
//            // Video processing was a success!
//            // Save the information
//            if ($thumbSuccess) {
//                $video->photo_id = $thumbFileRow->file_id;
//            }
//
//            $video->duration = $duration;
//            $video->status   = 1;
//            $video->save();
//
//            // delete the files from temp dir
//            unlink($originalPath);
//            unlink($outputPath);
//            unlink($thumbPath);
//
//            // insert action in a separate transaction if video status is a success
//            $actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');
//            $db           = $actionsTable->getAdapter();
//            $db->beginTransaction();
//
//            try {
//                // new action
//                $action = $actionsTable->addActivity($owner, $video, 'sitevideo_video_new');
//
//                if ($action) {
//                    $actionsTable->attachActivity($action, $video);
//                }
//
//                // notify the owner
//                Engine_Api::_()->getDbtable('notifications', 'activity')
//                    ->addNotification($owner, $owner, $video, 'sitevideo_processed');
//
//                $db->commit();
//            } catch (Exception $e) {
//                $db->rollBack();
//                throw $e; // throw
//            }
//        }
//    }

    public function validationAction()
    {
        $video_type = $this->_getParam('type');
        $code       = $this->_getParam('code');
        $ajax       = $this->_getParam('ajax', false);
        $valid      = false;
        // check which API should be used
        if ($video_type == "iframely") {
            $url                     = trim(strip_tags($code));
            $information             = $this->handleIframelyInformation($url);
            $this->view->code        = $code;
            $this->view->ajax        = $ajax;
            $this->view->valid       = !empty($information['code']);
            $this->view->information = $information;
        } else {

            if ($video_type == "youtube") {
                $valid = $this->checkYouTube($code);
                $type  = "youtube";
            } elseif ($video_type == "vimeo") {
                $valid = $this->checkVimeo($code);
                $type  = "vimeo";
            } elseif ($video_type == "dailymotion") {

                $valid = $this->checkDailymotion($code);
                $type  = "dailymotion";
            } elseif ($video_type == "instagram") {
                $scheme = $this->_getParam('scheme');
                $host   = $this->_getParam('host');
                $code   = $scheme . "://" . $host . $code;
                $valid  = $this->checkInstagram($code);
                $type   = "instagram";
            } elseif ($video_type == "twitter") {
                $valid = $this->checkTwitter($code);
                $type  = "twitter";

            }
            $this->view->code        = $code;
            $this->view->ajax        = $ajax;
            $this->view->valid       = $valid;
            $code                    = $this->extractCode($code, $type);
            $this->view->information = $this->handleInformation($type, $code);
        }
        $this->_helper->layout->setLayout('default-simple');
    }

    public function composeUploadAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$viewer->getIdentity()) {
            $this->_redirect('login');
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error  = Zend_Registry::get('Zend_Translate')->_('Invalid method');
            return;
        }

        $video_url     = $this->_getParam('uri');
        $video_type    = $this->_getParam('type');
        $composer_type = $this->_getParam('c_type', 'wall');

        $channel_id = $this->_getParam('channel_id');

        $code = $this->extractCode($video_url, $video_type);

        if ($video_type == 'youtube') {
            $valid = $this->checkYouTube($code);
        } elseif ($video_type == 'vimeo') {
            $valid = $this->checkVimeo($code);
        } elseif ($video_type == 'dailymotion') {
            $valid = $this->checkDailymotion($code);
        }

        // check to make sure the user has not met their quota of # of allowed video uploads
        // set up data needed to check quota
        $values['user_id'] = $viewer->getIdentity();
        $paginator         = Engine_Api::_()->getDbtable('videos', 'sitevideo')->getVideoPaginator($values);
        $quota             = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitevideo_video', 'sitevideo_max_allowed_video');
        $current_count     = $paginator->getTotalItemCount();

        if (($current_count >= $quota) && !empty($quota)) {
            // return error message
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('You have already uploaded the maximum number of videos allowed. If you would like to upload a new video, please delete an old one first.');
        } else if ($valid) {
            $db = Engine_Api::_()->getDbtable('videos', 'sitevideo')->getAdapter();
            $db->beginTransaction();

            try {

                // create video
                $table           = Engine_Api::_()->getDbtable('videos', 'sitevideo');
                $video           = $table->createRow();
                $video->owner_id = $viewer->getIdentity();
                $video->code     = $code;
                $video->type     = $video_type;

                $video_id = $video->save();

                if ($channel_id) {
                    $videoMaptable        = Engine_Api::_()->getDbtable('videomaps', 'sitevideo');
                    $videomap             = $videoMaptable->createRow();
                    $videomap->channel_id = $channel_id;
                    $videomap->video_id   = $video_id;
                    $videomap->owner_type = 'user';
                    $videomap->owner_id   = $viewer->getIdentity();
                    $videomap->save();
                    $channel               = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);
                    $channel->videos_count = $channel->videos_count + 1;
                    $channel->save();
                }

                // Now try to create thumbnail
                if (!$video->photo_id) {
                  $thumbnail = $this->handleThumbnail($video->type, $video->code);
                  $itemVideo = $video->saveVideoThumbnail($thumbnail);
                } else {
                  $itemVideo = $video; 
                }

                if ($itemVideo) {
                    $information     = $this->handleInformation($video->type, $video->code);
                    $video->duration = $information['duration'];
                    if (!$video->description) {
                        $video->description = $information['description'];
                    }
                    if (isset($information['html']) && !empty($information['html'])) {
                        $video->code = $information['html'];
                    }
                    $video->title = $information['title'];
                    $video->save();
                    // Insert new action item
                    $insert_action = true;
                }
                // If video is from the composer, keep it hidden until the post is complete
                if ($composer_type) {
                    $video->search = 0;
                }

                $video->synchronized = 1;
                $video->status       = 1;
                $video->save();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            // make the video public
            if ($composer_type === 'wall') {
                // CREATE AUTH STUFF HERE
                $auth  = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                foreach ($roles as $i => $role) {
                    $auth->setAllowed($video, $role, 'view', ($i <= $roles));
                    $auth->setAllowed($video, $role, 'comment', ($i <= $roles));
                }
            }

            $this->view->status      = true;
            $this->view->video_id    = $video->video_id;
            $this->view->photo_id    = $video->photo_id;
            $this->view->title       = $video->title;
            $this->view->description = $video->description;
            $this->view->src         = $video->getPhotoUrl();
            $this->view->message     = Zend_Registry::get('Zend_Translate')->_('Video posted successfully');
        } else {
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('We could not find a video there - please check the URL and try again.');
        }
    }

    public function handleIframelyInformation($uri)
    {
        $iframelyDisallowHost = Engine_Api::_()->getApi('settings', 'core')->getSetting('video_iframely_disallow');
        if (parse_url($uri, PHP_URL_SCHEME) === null) {
            $uri = "http://" . $uri;
        }
        $uriHost = Zend_Uri::factory($uri)->getHost();
        if ($iframelyDisallowHost && in_array($uriHost, $iframelyDisallowHost)) {
            return;
        }
        $config   = Engine_Api::_()->getApi('settings', 'core')->core_iframely;
        $iframely = Engine_Iframely::factory($config)->get($uri);
        if (!in_array('player', array_keys($iframely['links']))) {
            return;
        }
        $information = array('thumbnail' => '', 'title' => '', 'description' => '', 'duration' => '');
        if (!empty($iframely['links']['thumbnail'])) {
            $information['thumbnail'] = $iframely['links']['thumbnail'][0]['href'];
            if (parse_url($information['thumbnail'], PHP_URL_SCHEME) === null) {
                $information['thumbnail'] = str_replace(array('://', '//'), '', $information['thumbnail']);
                $information['thumbnail'] = "http://" . $information['thumbnail'];
            }
        }
        if (!empty($iframely['meta']['title'])) {
            $information['title'] = $iframely['meta']['title'];
        }
        if (!empty($iframely['meta']['description'])) {
            $information['description'] = $iframely['meta']['description'];
        }
        if (!empty($iframely['meta']['duration'])) {
            $information['duration'] = $iframely['meta']['duration'];
        }
        $information['code'] = $iframely['html'];
        return $information;
    }

}
