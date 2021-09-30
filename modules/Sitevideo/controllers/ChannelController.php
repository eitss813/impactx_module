<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ChannelController.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_ChannelController extends Seaocore_Controller_Action_Standard {

    protected $_set;

    public function init() {

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.allow', 1)) {
            return $this->_forward('notfound', 'error', 'core');
        }
        $this->_set = 0;
        if ($this->_getParam('set')) {
            $channel_id = Engine_Api::_()->sitevideo()->getEncodeToDecode($this->_getParam('set'));
            if (Engine_Api::_()->getItem('sitevideo_channel', $channel_id))
                $this->_set = 1;
        }

        if (!$this->_set && !$this->_helper->requireAuth()->setAuthParams('sitevideo_channel', null, 'view')->isValid())
            return;

        if (empty($_POST)) {
            //GET CHANNEL ID AND CHANNEL URL
            $channel_url = $this->_getParam('channel_url', $this->_getParam('channel_url', null));
            $channel_id = $this->_getParam('channel_id', $this->_getParam('channel_id', null));
            if ($channel_url) {
                $channel_id = Engine_Api::_()->sitevideo()->getChannelId($channel_url);
            }
            if ($channel_id) {
                $sitevideo = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);
                if ($sitevideo) {
                    Engine_Api::_()->core()->setSubject($sitevideo);
                }
            }
        } else {
            $channel_id = $this->_getParam('channel_id', $this->_getParam('channel_id', null));
            if ($channel_id) {
                $sitevideo = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);
                if ($sitevideo) {
                    Engine_Api::_()->core()->setSubject($sitevideo);
                }
            }
        }
    }

    public function pinboardAction() {

        if (!$this->_helper->requireAuth()->setAuthParams('sitevideo_channel', null, 'view')->isValid()) {
            return;
        }
        $this->_helper->content->setNoRender()->setEnabled();
    }

    //ACTION FOR CHANNEL VIEW
    public function viewAction() {

        if (!$this->_helper->requireSubject('sitevideo_channel')->isValid())
            return;

        $this->view->channel = $channel = Engine_Api::_()->core()->getSubject();

        //Meta Keyword work
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        $is_suggestion_enabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');

        if (!empty($is_suggestion_enabled) && !empty($channel)) {

            Engine_Api::_()->sitevideo()->deleteSuggestion('sitevideo_channel', $channel->getIdentity(), 'sitevideo_channel_suggestion');
        }

        $siteinfo = $view->layout()->siteinfo;
        $keywords = "";
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        //WHO CAN VIEW THE EVENTS
        $this->view->viewPrivacy = 1;
        if (!$channel->canView($viewer)) {
            $this->view->viewPrivacy = 0;
        }
        if (!$this->view->viewPrivacy) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $categoryname = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getCategoryName($channel->category_id);
        if (isset($categoryname) && !empty($categoryname)) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $categoryname;
        }

        $subcategoryname = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getCategoryName($channel->subcategory_id);
        if (isset($subcategoryname) && !empty($subcategoryname)) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $subcategoryname;
        }

        if (isset($channel->tag) && !empty($channel->tag)) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $channel->tag;
        }

        if (isset($channel->location) && !empty($channel->location)) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $channel->location;
        }


        //GET KEYWORDS
        $metakeywords = Engine_Api::_()->getDbTable('otherinfo', 'sitevideo')->getColumnValue($channel->getIdentity(), 'keywords');

        if ($metakeywords) {
            $keywords .= ', ';
        }
        $keywords .= $metakeywords;
        $siteinfo['keywords'] = $keywords;
        $view->layout()->siteinfo = $siteinfo;


        if (!$this->_set && !$this->_helper->requireAuth()->setAuthParams($channel, null, 'view')->isValid())
            return;

        $getLightBox = Zend_Registry::isRegistered('sitevideo_getlightbox') ? Zend_Registry::get('sitevideo_getlightbox') : null;
        if (empty($getLightBox)) {
            return;
        }

        $this->_helper->content->setEnabled();

        //START: "SUGGEST TO FRIENDS" LINK WORK
        //HERE WE ARE DELETE THIS CHANNEL SUGGESTION IF VIEWER HAVE
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion')) {
            Engine_Api::_()->sitevideo()->deleteSuggestion('sitevideo_channel', $channel->getIdentity(), 'channel_suggestion');
        }
        if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
            Zend_Registry::set('setFixedCreationFormBack', 'Back');
        }
    }

    //ACTION FOR MANAGE VIDEO
    public function manageAction() {
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
        $this->_helper->content->setNoRender()->setEnabled();
    }

    public function editvideosAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;

        if (!$this->_helper->requireSubject('sitevideo_channel')->isValid())
            return;

        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
            return;

        // Prepare data
        $this->view->channel = $channel = Engine_Api::_()->core()->getSubject();
        return $this->_helper->redirector->gotoRoute(array('action' => 'video-edit', 'channel_id' => $channel->channel_id), 'sitevideo_dashboard', true);
    }

    //ACTION FOR ORDERING VIDEOS BY DRAG AND DROP
    public function orderAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$this->_helper->requireSubject('sitevideo_channel')->isValid())
            return;
        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
            return;

        $channel = Engine_Api::_()->core()->getSubject();

        $order = $this->_getParam('order');
        if (!$order || !is_array($order)) {
            $this->view->status = false;
            return;
        }

        // Get a list of all videos in this channel, by order
        $videoTable = Engine_Api::_()->getItemTable('sitevideo_video');
        $currentOrder = $videoTable->select()
                ->from($videoTable, 'video_id')
                ->where('main_channel_id = ?', $channel->getIdentity())
                ->order('order ASC')
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN)
        ;

        // Find the starting point?
        $start = null;
        $end = null;
        for ($i = 0, $l = count($currentOrder); $i < $l; $i++) {
            if (in_array($currentOrder[$i], $order)) {
                $start = $i;
                $end = $i + count($order);
                break;
            }
        }

        if (null === $start || null === $end) {
            $this->view->status = false;
            return;
        }
        $video_id = 0;
        for ($i = 0, $l = count($currentOrder); $i < $l; $i++) {
            if ($i >= $start && $i <= $end) {
                if (isset($order[$i - $start]))
                    $video_id = $order[$i - $start];
            } else {
                if (isset($currentOrder[$i]))
                    $video_id = $currentOrder[$i];
            }
            $videoTable->update(array(
                'order' => $i,
                    ), array(
                'video_id = ?' => $video_id,
            ));
        }

        $this->view->status = true;
    }

    //ACTION FOR EDITING CHANNEL
    public function editAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$this->_helper->requireSubject('sitevideo_channel')->isValid())
            return;
        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
            return;

        // Render
        $this->_helper->content->setEnabled();

        // Get navigation
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitevideo_main');

        // Hack navigation
        foreach ($navigation->getPages() as $page) {
            if ($page->route != 'sitevideo_general' || $page->action != 'manage')
                continue;
            $page->active = true;
        }
        $channel = Engine_Api::_()->getItem('sitevideo_channel', $this->_getParam('channel_id'));
        $this->view->channel = $channel;
        $this->view->defaultProfileId = $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'sitevideo')->defaultProfileId();
        $this->view->TabActive = "edit";
        // Make form
        $this->view->form = $form = new Sitevideo_Form_Channel_Edit(array('defaultProfileId' => $defaultProfileId, 'item' => $channel));

        $form->getElement('search')->setValue($channel->search);
        $form->getElement('title')->setValue($channel->title);
        $form->getElement('description')->setValue($channel->description);
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.category.enabled', 1)) {
            $this->view->category_id = $channel->category_id;
            $form->getElement('category_id')->setValue($channel->category_id);
            // if($channel->subcategory_id  && $form->hasElement('subbsubcategory_id'))
            // $form->getElement('subcategory_id')->setValue($channel->subcategory_id);
            // if($channel->subsubcategory_id && $form->hasElement('subsubcategory_id'))
            // $form->getElement('subsubcategory_id')->setValue($channel->subsubcategory_id);
            $this->view->subcategory_id = $channel->subcategory_id;
            $this->view->subsubcategory_id = $channel->subsubcategory_id;
        }

        if ($channel->category_id) {
            //GET PROFILE MAPPING ID
            $categoryIds = array();
            $categoryIds[] = $channel->category_id;
            if ($channel->subcategory_id)
                $categoryIds[] = $channel->subcategory_id;
            if ($channel->subsubcategory_id)
                $categoryIds[] = $channel->subsubcategory_id;
            $this->view->profileType = $previous_profile_type = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getProfileType(array('categoryIds' => $categoryIds, 'category_id' => 0));
        }

        $this->view->show_url = $show_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.showurl.column', 1);
        if (empty($show_url)) {
            $form->removeElement('channel_url');
            $form->removeElement('channel_url_msg');
        }
        if (!$this->getRequest()->isPost()) {
            $form->populate($channel->toArray());

            //prepare tags
            $sitevideoTags = $channel->tags()->getTagMaps();
            $tagString = '';

            foreach ($sitevideoTags as $tagmap) {
                if ($tagString != '')
                    $tagString .= ', ';
                $tagString .= $tagmap->getTag()->getTitle();
            }

            if (isset($form->tags))
                $form->tags->setValue($tagString);

            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            foreach ($roles as $role) {
                if (1 === $auth->isAllowed($channel, $role, 'view') && isset($form->auth_view)) {
                    $form->auth_view->setValue($role);
                }
                if (1 === $auth->isAllowed($channel, $role, 'comment') && isset($form->auth_comment)) {
                    $form->auth_comment->setValue($role);
                }
                if (1 === $auth->isAllowed($channel, $role, 'tag') && isset($form->auth_tag)) {
                    $form->auth_tag->setValue($role);
                }
            }

            //NETWORK BASE CHANNEL
            if (Engine_Api::_()->sitevideo()->channelBaseNetworkEnable()) {
                if (!empty($channel->networks_privacy)) {
                    $form->networks_privacy->setValue(explode(',', $channel->networks_privacy));
                } else {
                    $form->networks_privacy->setValue(array(0));
                }
            }

            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
            return;
        }

        $isVideoTypeValid = Engine_Api::_()->sitevideo()->isVideoTypeValid();
        if (empty($isVideoTypeValid))
            return;

        // Process
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $values = $form->getValues();
//            if(isset( $_POST['subsubcategory_id']))
//            $values['subsubcategory_id'] = $_POST['subsubcategory_id'];
//            if(isset( $_POST['subcategory_id']))
//            $values['subcategory_id'] = $_POST['subcategory_id'];
            //NETWORK BASE CHANNEL
            if (Engine_Api::_()->sitevideo()->channelBaseNetworkEnable()) {
                if (isset($values['networks_privacy']) && !empty($values['networks_privacy'])) {
                    if (in_array(0, $values['networks_privacy'])) {
                        $values['networks_privacy'] = new Zend_Db_Expr('NULL');
                        $form->networks_privacy->setValue(array(0));
                    } else {
                        $values['networks_privacy'] = (string) ( is_array($values['networks_privacy']) ? join(",", $values['networks_privacy']) : $netowrkIds );
                    }
                }
            }

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
                if (isset($values['channel_url']) && in_array(strtolower($values['channel_url']), $urlArray)) {
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
            //SAVE TAGS
            $tags = '';
            if (isset($values['tags'])) {
                $tags = preg_split('/[,]+/', $values['tags']);
                $tags = array_filter(array_map("trim", $tags));
            }

            //GET VIEWER
            $viewer = Engine_Api::_()->user()->getViewer();

            if ($tags)
                $channel->tags()->setTagMaps($viewer, $tags);

            $channel->setFromArray($values);
            if ($_POST['subcategory_id']) {
                $channel->subcategory_id = $_POST['subcategory_id'];
            }

            if ($_POST['subsubcategory_id']) {
                $channel->subsubcategory_id = $_POST['subsubcategory_id'];
            }

            $channel->save();

            // CREATE AUTH STUFF HERE
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

            if (empty($values['auth_view'])) {
                $values['auth_view'] = key($form->auth_view->options);
                if (empty($values['auth_view'])) {
                    $values['auth_view'] = 'everyone';
                }
            }
            if (empty($values['auth_comment'])) {
                $values['auth_comment'] = key($form->auth_comment->options);
                if (empty($values['auth_comment'])) {
                    $values['auth_comment'] = 'owner_member';
                }
            }
            if (empty($values['auth_tag'])) {
                $values['auth_tag'] = key($form->auth_tag->options);
                if (empty($values['auth_tag'])) {
                    $values['auth_tag'] = 'owner_member';
                }
            }

            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);
            $tagMax = array_search($values['auth_tag'], $roles);

            foreach ($roles as $i => $role) {
                $auth->setAllowed($channel, $role, 'view', ($i <= $viewMax));
                $auth->setAllowed($channel, $role, 'comment', ($i <= $commentMax));
                $auth->setAllowed($channel, $role, 'tag', ($i <= $tagMax));
            }

            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.category.enabled', 1)) {
                //SAVE CUSTOM VALUES AND PROFILE TYPE VALUE
                $customfieldform = $form->getSubForm('fields');
                $customfieldform->setItem($channel);
                $customfieldform->saveValues();
                if ($customfieldform->getElement('submit')) {
                    $customfieldform->removeElement('submit');
                }

                //IF MAPPING HAS BEEN CHANGED OF CATEGORY THEN DELETE CORRESPONDENCE DATA FROM VALUES AND SEARCH TABLE
                if (isset($values['category_id']) && !empty($values['category_id'])) {
                    $categoryIds = array();
                    $categoryIds[] = $channel->category_id;
                    $categoryIds[] = $channel->subcategory_id;
                    $categoryIds[] = $channel->subsubcategory_id;
                    $channel->profile_type = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getProfileType(array('categoryIds' => $categoryIds, 'category_id' => 0));
                    if ($channel->profile_type != $previous_profile_type) {

                        $fieldvalueTable = Engine_Api::_()->fields()->getTable('sitevideo_channel', 'values');
                        $fieldvalueTable->delete(array('item_id = ?' => $channel->channel_id));

                        Engine_Api::_()->fields()->getTable('sitevideo_channel', 'search')->delete(array(
                            'item_id = ?' => $channel->channel_id,
                        ));

                        if (!empty($channel->profile_type) && !empty($previous_profile_type)) {
                            //PUT NEW PROFILE TYPE
                            $fieldvalueTable->insert(array(
                                'item_id' => $channel->channel_id,
                                'field_id' => $defaultProfileId,
                                'index' => 0,
                                'value' => $channel->profile_type,
                            ));
                        }
                    }
                    $channel->save();
                }
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $db->beginTransaction();
        try {
            // Rebuild privacy
            $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
            foreach ($actionTable->getActionsByObject($channel) as $action) {
                $actionTable->resetActivityBindings($action);
            }

            $this->view->subcategory_id = $channel->subcategory_id;
            $this->view->subsubcategory_id = $channel->subsubcategory_id;
            if ($channel->category_id) {
                //GET PROFILE MAPPING ID
                $categoryIds = array();
                $categoryIds[] = $channel->category_id;
                if ($channel->subcategory_id)
                    $categoryIds[] = $channel->subcategory_id;
                if ($channel->subsubcategory_id)
                    $categoryIds[] = $channel->subsubcategory_id;
                $this->view->profileType = $previous_profile_type = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getProfileType(array('categoryIds' => $categoryIds, 'category_id' => 0));
            }
            //SHOW SUCCESS MESSAGE
            $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        // return $this->_helper->redirector->gotoRoute(array('action' => 'view', 'slug' => $channel->getSlug(), 'channel_url' => $channel->channel_url), 'sitevideo_entry_view', true);
    }

    //ACTION FOR DELETING CHANNEL
    public function deleteAction() {

        $channel = Engine_Api::_()->getItem('sitevideo_channel', $this->getRequest()->getParam('channel_id'));

        if (!$this->_helper->requireAuth()->setAuthParams($channel, null, 'delete')->isValid())
            return;

        // In smoothbox
        $this->_helper->layout->setLayout('default-simple');

        $this->view->form = $form = new Sitevideo_Form_Channel_Delete();

        if (!$channel) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Channel doesn't exists or not authorized to delete");
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        $db = $channel->getTable()->getAdapter();
        $db->beginTransaction();

        try {

            Engine_Api::_()->getApi('core', 'sitevideo')->deleteChannel($channel);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('The selected channel has been successfully deleted.');
        return $this->_forward('success', 'utility', 'core', array(
                    'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'sitevideo_channel_general', true),
                    'messages' => Array($this->view->message)
        ));
    }

    //ACTION TO SET OVERVIEW
    public function overviewAction() {

        //ONLY LOGGED IN USER CAN ADD OVERVIEW
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET EVENT ID AND OBJECT
        $channel_id = $this->_getParam('channel_id');
        $this->view->channel = $channel = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.overview', 1)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        if (!$channel->authorization()->isAllowed($viewer, 'edit')) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        if (!Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitevideo_channel', "overview")) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        //SELECTED TAB
        $this->view->TabActive = "overview";

        //MAKE FORM
        $this->view->form = $form = new Sitevideo_Form_Overview();

        //IF NOT POSTED
        if (!$this->getRequest()->isPost()) {
            $saved = $this->_getParam('saved');
            if (!empty($saved))
                $this->view->success = Zend_Registry::get('Zend_Translate')->_('Your channel has been successfully created. You can enhance your channel from this Dashboard by creating other components.');
        }
        $channel_id = $channel->getIdentity();
        $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitevideo');
        //SAVE THE VALUE
        if ($this->getRequest()->isPost()) {
            $tableOtherinfo->update(array('overview' => $_POST['overview']), array('channel_id = ?' => $channel_id));
            $this->view->form = $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
        }
        //POPULATE FORM
        $values['overview'] = $tableOtherinfo->getColumnValue($channel_id, 'overview');
        $form->populate($values);
    }

    //ACTION FOR CONSTRUCT TAG CLOUD
    public function tagscloudAction() {
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.tags.enabled', 1)) {
            return $this->_forward('notfound', 'error', 'core');
        }
        if (!$this->_helper->requireAuth()->setAuthParams('sitevideo_channel', null, 'view')->isValid()) {
            return;
        }
        $this->_helper->content
                ->setContentName('sitevideo_channel_tagscloud')
                ->setNoRender()
                ->setEnabled();
    }

}
