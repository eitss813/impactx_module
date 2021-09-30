<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_IndexController extends Seaocore_Controller_Action_Standard {

    //ACTION FOR HOME PAGE
    public function indexAction() {

        if (!$this->_helper->requireAuth()->setAuthParams('sitevideo_channel', null, 'view')->isValid()) {
            return;
        }

        //OPEN TAB IN NEW PAGE
        if ($this->renderWidgetCustom())
            return;

        $this->_helper->content->setNoRender()->setEnabled();
    }

    public function channelExistsAction() {
        $youtubeChannelId = $this->_getParam('youtube_channel_id');
        $channelId = $this->_getParam('id');
        $viewer = Engine_Api::_()->user()->getViewer();
        $condition = array('youtube_channel_id = ?' => $youtubeChannelId,
            'owner_id = ?' => $viewer->getIdentity()
        );
        if (!empty($channelId)) {
            $condition['channel_id<>?'] = $channelId;
        }
        $channels = Engine_Api::_()->getDbtable('channels', 'sitevideo')->fetchAll($condition);
        $isChannelExist = false;
        if (count($channels) > 0)
            $isChannelExist = true;

        return $this->_helper->json($isChannelExist);
    }

    //ACTION FOR BROWSE PAGE 
    public function browseAction() {
        //die("reach");
        if (!$this->_helper->requireAuth()->setAuthParams('sitevideo_channel', null, 'view')->isValid()) {
            return;
        }

        //GET PAGE OBJECT
        $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
        $pageSelect = $pageTable->select()->where('name = ?', "sitevideo_index_browse");
        $pageObject = $pageTable->fetchRow($pageSelect);

        $params = array();
        $channel_type_title = '';
        if (!empty($pageObject->title)) {
            //$params['default_title'] = $title = $pageObject->title;
        } else {
            $params['default_title'] = $title = Zend_Registry::get('Zend_Translate')->_('Browse Channels');
        }

        //GET CHANNEL CATEGORY TABLE
        $tableCategory = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo');
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $category_id = $request->getParam('category_id', null);

        if (!empty($category_id)) {
            if ($channel_type_title)
                $params['channel_type_title'] = $title = $channel_type_title;
            $meta_title = $tableCategory->getCategory($category_id)->meta_title;
            if (empty($meta_title)) {
                $params['categoryname'] = Engine_Api::_()->getItem('sitevideo_channel_category', $category_id)->getCategorySlug();
            } else {
                $params['categoryname'] = $meta_title;
            }
            $meta_description = $tableCategory->getCategory($category_id)->meta_description;
            if (!empty($meta_description))
                $params['description'] = $meta_description;

            $meta_keywords = $tableCategory->getCategory($category_id)->meta_keywords;
            if (empty($meta_keywords)) {
                $params['categoryname_keywords'] = Engine_Api::_()->getItem('sitevideo_channel_category', $category_id)->getCategorySlug();
            } else {
                $params['categoryname_keywords'] = $meta_keywords;
            }

            $subcategory_id = $request->getParam('subcategory_id', null);
            if (!empty($subcategory_id)) {
                $meta_title = $tableCategory->getCategory($subcategory_id)->meta_title;
                if (empty($meta_title)) {
                    $params['subcategoryname'] = Engine_Api::_()->getItem('sitevideo_channel_category', $subcategory_id)->getCategorySlug();
                } else {
                    $params['subcategoryname'] = $meta_title;
                }

                $meta_description = $tableCategory->getCategory($subcategory_id)->meta_description;
                if (!empty($meta_description))
                    $params['description'] = $meta_description;

                $meta_keywords = $tableCategory->getCategory($subcategory_id)->meta_keywords;
                if (empty($meta_keywords)) {
                    $params['subcategoryname_keywords'] = Engine_Api::_()->getItem('sitevideo_channel_category', $subcategory_id)->getCategorySlug();
                } else {
                    $params['subcategoryname_keywords'] = $meta_keywords;
                }
            }
        }

        //SET META TITLE
        //Engine_Api::_()->sitevideo()->setMetaTitles($params);
        //SET META TITLE
        Engine_Api::_()->sitevideo()->setMetaDescriptionsBrowse($params);

        //GET LOCATION
        if (isset($_GET['location']) && !empty($_GET['location'])) {
            $params['location'] = $_GET['location'];
        }

        //GET TAG
        if (isset($_GET['tag']) && !empty($_GET['tag'])) {
            $params['tag'] = $_GET['tag'];
        }

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $params['search'] = $_GET['search'];
        }

        //GET CHANNELS TITLE
        $params['channel_type_title'] = $this->view->translate('Channels');

        //SET META KEYWORDS
        Engine_Api::_()->sitevideo()->setMetaKeywords($params);

        $this->_helper->content->setNoRender()->setEnabled();
    }

    //ACTION FOR MY CHANNEL PAGE
    public function manageAction() {

        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }

        if (!$this->_helper->requireAuth()->setAuthParams('sitevideo_channel', null, 'create')->isValid()) {
            return;
        }
        $this->_helper->content->setNoRender()->setEnabled();
    }

    //ACTION FOR UPLOADING IMAGES THROUGH WYSIWYG EDITOR
    public function uploadPhotoAction() {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        $this->_helper->layout->disableLayout();

        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album')) {
            return false;
        }

        if (!Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create')) {
            return false;
        }

        if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid())
            return;

        if (!$this->_helper->requireUser()->checkRequire()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }
        $fileName = Engine_Api::_()->seaocore()->tinymceEditorPhotoUploadedFileName();
        if (!isset($_FILES[$fileName]) || !is_uploaded_file($_FILES[$fileName]['tmp_name'])) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
            return;
        }

        $db = Engine_Api::_()->getDbtable('photos', 'album')->getAdapter();
        $db->beginTransaction();

        try {
            $viewer = Engine_Api::_()->user()->getViewer();

            $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
            $photo = $photoTable->createRow();
            $photo->setFromArray(array(
                'owner_type' => 'user',
                'owner_id' => $viewer->getIdentity()
            ));
            $photo->save();

            Engine_Api::_()->sitevideo()->setUserCreateChannel();
            $photo->setPhoto($_FILES[$fileName]);

            $this->view->status = true;
            $this->view->name = $_FILES[$fileName]['name'];
            $this->view->photo_id = $photo->photo_id;
            $this->view->photo_url = $photo->getPhotoUrl();

            $table = Engine_Api::_()->getDbtable('albums', 'album');
            $album = $table->getSpecialAlbum($viewer, 'message');

            $photo->album_id = $album->album_id;
            $photo->save();

            if (!$album->photo_id) {
                $album->photo_id = $photo->getIdentity();
                $album->save();
            }

            $auth = Engine_Api::_()->authorization()->context;
            $auth->setAllowed($photo, 'everyone', 'view', true);
            $auth->setAllowed($photo, 'everyone', 'comment', true);
            $auth->setAllowed($album, 'everyone', 'view', true);
            $auth->setAllowed($album, 'everyone', 'comment', true);

            $db->commit();
        } catch (Album_Model_Exception $e) {
            $db->rollBack();
            $this->view->status = false;
            $this->view->error = $this->view->translate($e->getMessage());
            throw $e;
            return;
        } catch (Exception $e) {
            $db->rollBack();
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
            throw $e;
            return;
        }
    }

    //ACTION TO GET SUB-CATEGORY
    public function subCategoryAction() {

        //GET CATEGORY ID
        $category_id_temp = $this->_getParam('category_id_temp');
        $showAllCategories = $this->_getParam('showAllCategories', 1);

        //INTIALIZE ARRAY
        $this->view->subcats = $data = array();

        //RETURN IF CATEGORY ID IS EMPTY
        if (empty($category_id_temp))
            return;

        //GET CATEGORY TABLE
        $tableCategory = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo');

        //GET CATEGORY
        $category = $tableCategory->getCategory($category_id_temp);
        if (!empty($category->category_name)) {
            $categoryName = Engine_Api::_()->getItem('sitevideo_channel_category', $category_id_temp)->getCategorySlug();
        }
        //GET SUB-CATEGORY
        $subCategories = $tableCategory->getSubCategories(array('category_id' => $category_id_temp, 'fetchColumns' => array('category_id', 'category_name', 'category_slug'), 'havingChannels' => $showAllCategories));

        foreach ($subCategories as $subCategory) {
            $content_array = array();
            $content_array['category_name'] = $this->view->translate($subCategory->category_name);
            $content_array['category_id'] = $subCategory->category_id;
            $content_array['categoryname_temp'] = $categoryName;
            $content_array['category_slug'] = $subCategory->getCategorySlug();
            $data[] = $content_array;
        }

        $this->view->subcats = $data;
    }

    //ACTION FOR FETCHING SUB-CATEGORY
    public function subsubCategoryAction() {

        //GET SUB-CATEGORY ID
        $subcategory_id_temp = $this->_getParam('subcategory_id_temp');
        $showAllCategories = $this->_getParam('showAllCategories', 1);
        //INTIALIZE ARRAY
        $this->view->subsubcats = $data = array();

        //RETURN IF SUB-CATEGORY ID IS EMPTY
        if (empty($subcategory_id_temp))
            return;

        //GET CATEGORY TABLE
        $tableCategory = Engine_Api::_()->getDbTable('channelCategories', 'sitevideo');

        //GET SUB-CATEGORY
        $subCategory = $tableCategory->getCategory($subcategory_id_temp);
        if (!empty($subCategory->category_name)) {
            $subCategoryName = Engine_Api::_()->getItem('sitevideo_channel_category', $subcategory_id_temp)->getCategorySlug();
        }

        //GET 3RD LEVEL CATEGORIES
        $subCategories = $tableCategory->getSubCategories(array('category_id' => $subcategory_id_temp, 'fetchColumns' => array('category_id', 'category_name', 'category_slug'), 'havingChannels' => $showAllCategories));
        foreach ($subCategories as $subCategory) {
            $content_array = array();
            $content_array['category_name'] = $this->view->translate($subCategory->category_name);
            $content_array['category_id'] = $subCategory->category_id;
            $content_array['categoryname_temp'] = $subCategoryName;
            $data[] = $content_array;
        }
        $this->view->subsubcats = $data;
    }

    //ACTION TO SAVE RATING AND SEND DATA ARRAY
    public function rateAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        $rating = $this->_getParam('rating');
        $subject_id = $this->_getParam('subject_id');
        $subject_type = $this->_getParam('subject_type');
        $ratingTable = Engine_Api::_()->getDbtable('ratings', 'sitevideo');
        $db = $ratingTable->getAdapter();
        $db->beginTransaction();
        try {
            $ratingTable->setRating($subject_id, $subject_type, $rating);
            Engine_Api::_()->sitevideo()->setUserCreateChannel();
            $subject = Engine_Api::_()->getItem($subject_type, $subject_id);
            $subject->rating = $ratingTable->getRating($subject_id, $subject_type);
            $subject->save();
            if ($viewer->getIdentity() != $subject->getOwner()->getIdentity()) {
                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                $notifyApi->addNotification($subject->getOwner(), $viewer, $subject, 'sitevideo_rate', array(
                    'label' => $subject->getShortType()
                ));
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $total = $ratingTable->ratingCount(array('resource_id' => $subject_id, 'resource_type' => $subject_type));
        $data = array();
        $data[] = array(
            'total' => $total,
            'rating' => $rating,
        );
        return $this->_helper->json($data);
        $data = Zend_Json::encode($data);
        $this->getResponse()->setBody($data);
    }

    //ACTON FOR CATEGORIES PAGE
    public function categoriesAction() {

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.category.enabled', 1)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        if (!$this->_helper->requireAuth()->setAuthParams('sitevideo_channel', null, 'view')->isValid()) {
            return;
        }

        $siteinfo = $this->view->layout()->siteinfo;
        $titles = $siteinfo['title'];
        $keywords = $siteinfo['keywords'];
        $channel_type_title = 'Channels';

        if (!empty($keywords))
            $keywords .= ' - ';

        $keywords .= $channel_type_title;
        $siteinfo['keywords'] = $keywords;
        $this->view->layout()->siteinfo = $siteinfo;

        $this->_helper->content
                ->setNoRender()
                ->setEnabled();
    }

    public function categoryHomeAction() {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $category_id = $request->getParam('category_id', null);
        Zend_Registry::set('sitevideoCategoryId', $category_id);

        //GET STORE OBJECT
        $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
        $pageSelect = $pageTable->select()->where('name = ?', "sitevideo_index_categories-home_category_$category_id");
        $pageObject = $pageTable->fetchRow($pageSelect);

        $this->_helper->content
                ->setContentName($pageObject->page_id)
                ->setNoRender()
                ->setEnabled();
    }

    //ACTION FOR GETTING THE AUTOSUGGESTED CHANNELS BASED ON SEARCHING
    public function getSearchPlaylistAction() {

        //GET CHANNELS AND MAKE ARRAY
        $usersitevideos = Engine_Api::_()->getDbtable('playlists', 'sitevideo')->getDayItems($this->_getParam('text'), $this->_getParam('limit', 10));
        $data = array();
        $mode = $this->_getParam('struct');
        $count = count($usersitevideos);
        if ($mode == 'text') {
            $i = 0;
            foreach ($usersitevideos as $usersitevideo) {
                $sitevideo_url = $this->view->url(array('slug' => $usersitevideo->getSlug(), 'channel_url' => $usersitevideo->channel_url), "sitevideo_entry_view", true);
                $i++;
                $content_video = $this->view->itemPhoto($usersitevideo, 'thumb.normal');
                $data[] = array(
                    'id' => $usersitevideo->channel_id,
                    'label' => $usersitevideo->title,
                    'video' => $content_video,
                    'sitevideo_url' => $sitevideo_url,
                    'total_count' => $count,
                    'count' => $i
                );
            }
        } else {
            $i = 0;
            foreach ($usersitevideos as $usersitevideo) {
                $sitevideo_url = $this->view->url(array('slug' => $usersitevideo->getSlug(), 'channel_url' => $usersitevideo->channel_url), "sitevideo_entry_view", true);
                $content_video = $this->view->itemPhoto($usersitevideo, 'thumb.normal');
                $i++;
                $data[] = array(
                    'id' => $usersitevideo->channel_id,
                    'label' => $usersitevideo->title,
                    'video' => $content_video,
                    'sitevideo_url' => $sitevideo_url,
                    'total_count' => $count,
                    'count' => $i
                );
            }
        }
        if (!empty($data) && $i >= 1) {
            if ($data[--$i]['count'] == $count) {
                $data[$count]['id'] = 'stopevent';
                $data[$count]['label'] = $this->_getParam('text');
                $data[$count]['sitevideo_url'] = 'seeMoreLink';
                $data[$count]['total_count'] = $count;
            }
        }
        return $this->_helper->json($data);
    }

    //ACTION FOR GETTING THE AUTOSUGGESTED CHANNELS BASED ON SEARCHING
    public function getSearchChannelsAction() {

        //GET CHANNELS AND MAKE ARRAY
        $usersitevideos = Engine_Api::_()->getDbtable('channels', 'sitevideo')->getDayItems($this->_getParam('text'), $this->_getParam('limit', 10));
        $data = array();
        $mode = $this->_getParam('struct');
        $count = count($usersitevideos);
        if ($mode == 'text') {
            $i = 0;
            foreach ($usersitevideos as $usersitevideo) {
                $sitevideo_url = $this->view->url(array('slug' => $usersitevideo->getSlug(), 'channel_url' => $usersitevideo->channel_url), "sitevideo_entry_view", true);
                $i++;
                $content_video = $this->view->itemPhoto($usersitevideo, 'thumb.normal');
                $data[] = array(
                    'id' => $usersitevideo->channel_id,
                    'label' => $usersitevideo->title,
                    'video' => $content_video,
                    'sitevideo_url' => $sitevideo_url,
                    'total_count' => $count,
                    'count' => $i
                );
            }
        } else {
            $i = 0;
            foreach ($usersitevideos as $usersitevideo) {
                $sitevideo_url = $this->view->url(array('slug' => $usersitevideo->getSlug(), 'channel_url' => $usersitevideo->channel_url), "sitevideo_entry_view", true);
                $content_video = $this->view->itemPhoto($usersitevideo, 'thumb.normal');
                $i++;
                $data[] = array(
                    'id' => $usersitevideo->channel_id,
                    'label' => $usersitevideo->title,
                    'video' => $content_video,
                    'sitevideo_url' => $sitevideo_url,
                    'total_count' => $count,
                    'count' => $i
                );
            }
        }
        if (!empty($data) && $i >= 1) {
            if ($data[--$i]['count'] == $count) {
                $data[$count]['id'] = 'stopevent';
                $data[$count]['label'] = $this->_getParam('text');
                $data[$count]['sitevideo_url'] = 'seeMoreLink';
                $data[$count]['total_count'] = $count;
            }
        }
        return $this->_helper->json($data);
    }

    /*
     * ACTION FOR ADD CHANNEL 
     */

    public function createAction() {

        //Checking for "Channel" is enabled for this site
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.allow', 1))
            return $this->_forwardCustom('requireauth', 'error', 'core');

        if (!$this->_helper->requireAuth()->setAuthParams('sitevideo_channel', null, 'create')->isValid()) {
            return;
        }
        // Render
        $this->_helper->content->setEnabled();

        //GET DEFAULT PROFILE TYPE ID
        $this->view->defaultProfileId = $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'sitevideo')->defaultProfileId();

        // Get form
        $this->view->form = $form = new Sitevideo_Form_Channel(array('defaultProfileId' => $defaultProfileId));

        $this->view->show_url = $show_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.showurl.column', 1);
        if (empty($show_url)) {
            $form->removeElement('channel_url');
            $form->removeElement('channel_url_msg');
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewerId = Engine_Api::_()->user()->getViewer()->user_id;
        if (!empty($viewerId)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        $sitevideoCreateChannel = Zend_Registry::isRegistered('sitevideoCreateChannel') ? Zend_Registry::get('sitevideoCreateChannel') : null;
        $maxAllowedChannel = Engine_Api::_()->authorization()->getPermission($level_id, 'sitevideo_channel', 'sitevideo_max_allowed_channel');
        if (!empty($maxAllowedChannel)) {
            $channelTable = Engine_Api::_()->getDbtable('channels', 'sitevideo');
            $userChannelCount = $channelTable
                    ->select()
                    ->from($channelTable->info('name'), new Zend_Db_Expr('COUNT(channel_id)'))
                    ->where('owner_id = ?', $viewerId)
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
            if ($userChannelCount >= $maxAllowedChannel) {
                $error = sprintf(Zend_Registry::get('Zend_Translate')->_('You have exceeded maximum channels creation limit. You are allowed to create maximum %s channels only.'), $maxAllowedChannel);
                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }
        }
        if (!$this->getRequest()->isPost()) {
            if (null !== ($channel_id = $this->_getParam('channel_id'))) {
                $form->populate(array(
                    'channel' => $channel_id
                ));
            }
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        if (empty($sitevideoCreateChannel))
            return;

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $values = $form->getValues();

            $channel = $form->saveValues();

            $table = Engine_Api::_()->getItemTable('sitevideo_channel');
            if (empty($show_url)) {
                $resultChannelTable = $table->select()->where('title =?', $values['title'])->from($table, 'title')
                                ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
                $count_index = count($resultChannelTable);
                $resultChannelUrl = $table->select()->where('channel_url =?', $values['title'])->from($table, 'channel_url')
                                ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
                $count_index_url = count($resultChannelUrl);
            }
            $urlArray = Engine_Api::_()->sitevideo()->getBannedUrls();

            if (!empty($show_url)) {
                if (in_array(strtolower($values['channel_url']), $urlArray)) {
                    $form->addError(Zend_Registry::get('Zend_Translate')->_('Sorry, this URL has been restricted by our automated system. Please choose a different URL.'));
                    return;
                }
            } else {
                $lastchannel_id = $table->select()
                        ->from($table->info('name'), array('channel_id'))->order('channel_id DESC')
                        ->query()
                        ->fetchColumn();
                $values['channel_url'] = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($values['title']))), '-');
                if (!empty($count_index) || !empty($count_index_url)) {
                    $lastchannel_id = $lastchannel_id + 1;
                    $values['channel_url'] = $values['channel_url'] . '-' . $lastchannel_id;
                } else {
                    $values['channel_url'] = $values['channel_url'];
                }
                if (in_array(strtolower($values['channel_url']), $urlArray)) {

                    $form->addError(Zend_Registry::get('Zend_Translate')->_('Sorry, this Channel Title has been restricted by our automated system. Please choose a different Title.', array('escape' => false)));
                    return;
                }
            }

            if (isset($values['channel_url'])) {
                $channel->channel_url = $values['channel_url'];
                $channel->save();
            }

            $channel_id = $channel->channel_id;
            if (empty($show_url)) {
                $values['channel_url'] = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($values['title']))), '-');
                if (!empty($count_index) || !empty($count_index_url)) {
                    $values['channel_url'] = $values['channel_url'] . '-' . $channel_id;
                    $table->update(array('channel_url' => $values['channel_url']), array('channel_id = ?' => $channel_id));
                } else {
                    $values['channel_url'] = $values['channel_url'];
                    $table->update(array('channel_url' => $values['channel_url']), array('channel_id = ?' => $channel_id));
                }
            }

            //ADDING TAGS
            $keywords = '';
            $tags = $form->getValue('tags');
            if (!empty($tags)) {
                $tags = preg_split('/[,]+/', $tags);
                $tags = array_filter(array_map("trim", $tags));
                $channel->tags()->addTagMaps($viewer, $tags);

                foreach ($tags as $tag) {
                    $keywords .= " $tag";
                }
            }
            $categoryIds[] = $channel->category_id;
            $categoryIds[] = $channel->subcategory_id;
            $categoryIds[] = $channel->subsubcategory_id;
            $channel->profile_type = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getProfileType(array('categoryIds' => $categoryIds, 'category_id' => 0));
            $photo = $form->getValue('photo');
            // Set photo
            if (!empty($photo)) {
                $channel->setPhoto($form->photo);
            }
            if (isset($values['youtube_channel_id']) && !empty($values['youtube_channel_id'])) {
                $channel->youtube_channel_id = $values['youtube_channel_id'];
                if (!empty($values['pending_video'])) {
                    $channel->pending_video = $values['pending_video'];
                    $channel->save();
                    Engine_Api::_()->getDbtable('jobs', 'core')->addJob('Sitevideo_Upload', array(
                        'channel_id' => $channel->channel_id,
                    ));
                }
            }
            $channel->save();
            $tableOtherinfo = Engine_Api::_()->getDbtable('otherinfo', 'sitevideo');
            $row = $tableOtherinfo->getOtherinfo($channel->channel_id);
            if (empty($row)) {
                $tableOtherinfo->insert(array(
                    'channel_id' => $channel->channel_id,
                    'overview' => ''
                ));
            }

            //SAVE CUSTOM VALUES AND PROFILE TYPE VALUE
            $customfieldform = $form->getSubForm('fields');
            $customfieldform->setItem($channel);
            $customfieldform->saveValues();
            //Create Channel feeds
            $owner = $channel->getOwner();
            $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $channel, 'sitevideo_channel_new');
            if ($action != null) {
                Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $channel);
            }
            //COMMIT
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }



        //UPDATE KEYWORDS IN SEARCH TABLE
        if (!empty($keywords)) {
            Engine_Api::_()->getDbTable('search', 'core')->update(array('keywords' => $keywords), array('type = ?' => 'sitevideo_channel', 'id = ?' => $channel->channel_id));
        }
        //SENDING ACTIVITY FEED TO FACEBOOK.
        $enable_Facebooksefeed = $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
        if (!empty($enable_Facebooksefeed)) {

            $sitevideo_array = array();
            $sitevideo_array['type'] = 'sitevideo_channel_new';
            $sitevideo_array['object'] = $channel;

            Engine_Api::_()->facebooksefeed()->sendFacebookFeed($sitevideo_array);
        }

        if (!empty($channel->pending_video)) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitevideo_video_general', true);
        }
        return $this->_helper->redirector->gotoRoute(array('action' => 'create', 'channel_id' => $channel->channel_id), 'sitevideo_video_general', true);
//      $this->view->success = 1;
//        $this->view->channel = $channel;
//        $this->view->channel_id = $channel->channel_id;
    }

    //ACTION FOR CHANNEL URL VALIDATION AT CHANNEL CREATION TIME
    public function channelurlvalidationAction() {
        $view = Zend_Registry::get('Zend_View');
        $channel_url = $this->_getParam('channel_url');

        $urlArray = Engine_Api::_()->sitevideo()->getBannedUrls();

        if (empty($channel_url)) {
            echo Zend_Json::encode(array('success' => 0, 'error_msg' => '<span class="seaocore_txt_red"><img src="' . $view->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/images/cross.png"/>URL not valid.</span>'));
            exit();
        }

        $url_lenght = strlen($channel_url);
        if ($url_lenght < 3) {
            $error_msg1 = Zend_Registry::get('Zend_Translate')->_("URL component should be atleast 3 characters long.");
            echo Zend_Json::encode(array('success' => 0, 'error_msg' => "<span class='seaocore_txt_red'><img src='" . $view->layout()->staticBaseUrl . "application/modules/Sitevideo/externals/images/cross.png'/>$error_msg1</span>"));
            exit();
        } elseif ($url_lenght > 255) {
            $error_msg2 = Zend_Registry::get('Zend_Translate')->_("URL component should be maximum 255 characters long.");
            echo Zend_Json::encode(array('success' => 0, 'error_msg' => "<span class='seaocore_txt_red'><img src='" . $view->layout()->staticBaseUrl . "application/modules/Sitevideo/externals/images/cross.png'/>$error_msg2</span>"));
            exit();
        }

        $change_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.change.url', 1);
        $check_url = $this->_getParam('check_url');
        if (!empty($check_url)) {
            $channelId = $this->_getParam('channel_id');
            $channelId = Engine_Api::_()->sitevideo()->getChannelId($channel_url, $channelId);
        } else {
            $channelId = Engine_Api::_()->sitevideo()->getChannelId($channel_url);
        }

        if (!empty($channelId) || (in_array(strtolower($channel_url), $urlArray))) {
            $error_msg3 = Zend_Registry::get('Zend_Translate')->_("URL not available.");
            echo Zend_Json::encode(array('success' => 0, 'error_msg' => "<span class='seaocore_txt_red'><img src='" . $view->layout()->staticBaseUrl . "application/modules/Sitevideo/externals/images/cross.png'/>$error_msg3</span>"));
            exit();
        }

        if (!preg_match("/^[a-zA-Z0-9-_]+$/", $channel_url)) {
            $error_msg4 = Zend_Registry::get('Zend_Translate')->_("URL component can contain alphabets, numbers, underscores & dashes only.");
            echo Zend_Json::encode(array('success' => 0, 'error_msg' => "<span class='seaocore_txt_red'><img src='" . $view->layout()->staticBaseUrl . "application/modules/Sitevideo/externals/images/cross.png'/>$error_msg4</span>"));
            exit();
        } else {
            $error_msg5 = Zend_Registry::get('Zend_Translate')->_("URL Available!");
            echo Zend_Json::encode(array('success' => 1, 'success_msg' => "<span style='color:green;'><img src='" . $view->layout()->staticBaseUrl . "application/modules/Sitevideo/externals/images/tick.png'/>$error_msg5</span>"));
            exit();
        }
    }

    //GET CATEGORIES ACTION
    public function getChannelCategoriesAction() {

        $element_value = $this->_getParam('element_value', 1);
        $element_type = $this->_getParam('element_type', 'category_id');
        $showAllCategories = $this->_getParam('showAllCategories', 1);

        $categoriesTable = Engine_Api::_()->getDbTable('channelCategories', 'sitevideo');
        $categoryTableName = $categoriesTable->info('name');
        $select = $categoriesTable->select()
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
            $tableChannels = Engine_Api::_()->getDbTable('channels', 'sitevideo');
            $tableChannelsName = $tableChannels->info('name');
            $select->setIntegrityCheck();
            if ($element_type == 'subcat_dependency') {
                $select->join($tableChannelsName, "$tableChannelsName.subcategory_id=$categoryTableName.$element_type", null);
            } else {
                $select->join($tableChannelsName, "$tableChannelsName.category_id=$categoryTableName.$element_type", null);
            }
            $select->where($tableChannelsName . '.approved = ?', 1)->where($tableChannelsName . '.draft = ?', 0)->where($tableChannelsName . '.search = ?', 1)->where($tableChannelsName . '.closed = ?', 0);
            $select = $tableChannels->getNetworkBaseSql($select, array('not_groupBy' => 1));
        }

        $select->group($categoryTableName . '.category_id');

        $categoriesData = $categoriesTable->fetchAll($select);

        $categories = array();
        if (Count($categoriesData) > 0) {
            foreach ($categoriesData as $category) {
                $data = array();
                $data['category_name'] = $this->view->translate($category->category_name);
                $data['category_id'] = $category->category_id;
                $data['category_slug'] = $category->getCategorySlug();
                $categories[] = $data;
            }
        }

        $this->view->categories = $categories;
    }

    //ACTION FOR SHOWING SPONSORED VIDEOS IN WIDGET
    public function homesponsoredAction() {

        //CORE SETTINGS API
        $settings = Engine_Api::_()->getApi('settings', 'core');

        //SEAOCORE API
        $this->view->seacore_api = Engine_Api::_()->seaocore();
        //RETURN THE OBJECT OF LIMIT PER PAGE FROM CORE SETTING TABLE
        $this->view->sponserdSitevideosCount = $limit_sitevideo = $_GET['curnt_limit'];
        $limit_sitevideo_horizontal = $limit_sitevideo * 2;

        $values = array();
        $values = $this->_getAllParams();

        //GET COUNT
        $totalCount = $_GET['total'];

        //RETRIVE THE VALUE OF START INDEX
        $startindex = $_GET['startindex'];

        if ($startindex > $totalCount) {
            $startindex = $totalCount - $limit_sitevideo;
        }

        if ($startindex < 0) {
            $startindex = 0;
        }

        $this->view->direction = $_GET['direction'];
        $values['start_index'] = $startindex;
        $this->view->totalItemsInSlide = $values['selectLimit'] = $limit_sitevideo_horizontal;

        //GET VIDEOS
        $this->view->sitevideos = $videos = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getVideos($values);
        $this->view->count = count($this->view->sitevideos);
        $this->view->vertical = $_GET['vertical'];
        $this->view->blockHeight = $this->_getParam('blockHeight', 245);
        $this->view->blockWidth = $this->_getParam('blockWidth', 150);
        $this->view->videoOption = $this->_getParam('videoOption');
    }

    public function authErrorAction() {
        $this->_forwardCustom('requireauth', 'error', 'core');
    }

    public function testAction() {

        die;
    }

}
