<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Channel.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Channel extends Engine_Form {

    protected $_defaultProfileId;

    public function getDefaultProfileId() {
        return $this->_defaultProfileId;
    }

    public function setDefaultProfileId($default_profile_id) {
        $this->_defaultProfileId = $default_profile_id;
        return $this;
    }

    public function init() {
        $k=1001;
        $user = Engine_Api::_()->user()->getViewer();
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        // Init form
        $this
                ->setTitle('Create New Channel')
                ->setAttrib('id', 'form-channel-upload')
                ->setAttrib('name', 'channels_create')
                ->setAttrib('enctype', 'multipart/form-data')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

        $youtubeUploadPrivacy = Engine_Api::_()->authorization()->getPermission($user->level_id, 'sitevideo_channel', "youtube_channel");
        $apiKey = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.youtube.apikey');
        if ($youtubeUploadPrivacy && !empty($apiKey)) {
            
            $this->addElement('Text', 'youtube_channel_keyword', array(
                'label' => 'Channel Keyword',
                'description' => 'Enter the keywords to find the associated YouTube channels.',
                'maxlength' => '150',
                'filters' => array(
                    'StripTags',
                )
            ));
            $this->youtube_channel_keyword->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
            $this->addElement('Text', 'youtube_channel_url', array(
                'label' => 'Channel URL',
                'description' => 'Enter the YouTube channel’s URL to get the videos from that channel.',
                'maxlength' => '150',
                'filters' => array(
                    'StripTags',
                )
            ));
            $this->youtube_channel_url->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
            
            $this->addElement('Hidden', 'youtube_channel_id', array('order' => $k++,));
            $this->addElement('Hidden', 'pending_video', array('order' => $k++,));
        }
        // Init name
        $this->addElement('Text', 'title', array(
            'label' => 'Channel Title',
            'maxlength' => '40',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                //new Engine_Filter_HtmlSpecialChars(),
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '63')),
            )
        ));

        // Element: channel_url
        $channel_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('channel_id');

        $parent_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('parent_id', null);
        $show_url = $coreSettings->getSetting('sitevideo.channel.showurl.column', 1);
        $change_url = $coreSettings->getSetting('sitevideo.channel.change.url', 1);
        $edit_url = $coreSettings->getSetting('sitevideo.channel.edit.url', 0);

        $link = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('channel_url' => Zend_Registry::get('Zend_Translate')->_('CHANNEL-NAME')), 'sitevideo_entry_view');

        if (!empty($change_url)) {

            $front = Zend_Controller_Front::getInstance();
            $baseUrl = $front->getBaseUrl();
            $CHANNEL_NAME = Zend_Registry::get('Zend_Translate')->_("CHANNEL-NAME");
            $link2 = $_SERVER['HTTP_HOST'] . $baseUrl . '/' . $CHANNEL_NAME;
            $limit = $coreSettings->getSetting('sitevideo.channel.likelimit.forurlblock', 0);
            if (empty($limit)) {
                $description = sprintf(Zend_Registry::get('Zend_Translate')->_("This will be the end of your channel URL. It should be indicative of the title and can contain alphabets, numbers, underscores and  dashes only. Its length should be in the range of 3-255 characters. The complete URL of your channel will be: %s"), "<span id='short_channel_url_address'>http://$link2</span>");
            } else {
                $description = sprintf(Zend_Registry::get('Zend_Translate')->_("This will be the end of your channel URL. It should be indicative of the title and can contain alphabets, numbers, underscores and  dashes only. Its length should be in the range of 3-255 characters. The complete URL of your channel will be: %s"), "<span id='channel_url_address'>http://$link</span>");
                $description = $description . sprintf(Zend_Registry::get('Zend_Translate')->_('<br />and if your channel has %1$s or more likes URL will be: <br />%2$s'), "$limit", "<span id='short_channel_url_address'>http://$link2</span>");
            }
        } else {
            $description = sprintf(Zend_Registry::get('Zend_Translate')->_("This will be the end of your channel URL. It should be indicative of the title and can contain alphabets, numbers, underscores and  dashes only. Its length should be in the range of 3-255 characters. The complete URL of your channel will be: %s"), "<span id='channel_url_address'>http://$link</span>");
        }

        if (!empty($channel_id) && !empty($show_url) && !empty($edit_url)) {
            $this->addElement('Text', 'channel_url', array(
                'label' => 'URL',
                'description' => $description,
                'autocomplete' => 'off',
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                    array('NotEmpty', true),
                    // array('Alnum', true),
                    array('StringLength', true, array(3, 255)),
                    array('Regex', true, array('/^[a-zA-Z0-9-_]+$/')),
                ),
            ));
            $this->channel_url->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
            $this->channel_url->getValidator('NotEmpty')->setMessage('Please enter a valid channel url.', 'isEmpty');
            $this->channel_url->getValidator('Regex')->setMessage('Profile addresses must start with a letter.', 'regexNotMatch');
            $this->addElement('dummy', 'channel_url_msg', array('value' => 0));
        } elseif (empty($channel_id)) {
            $this->addElement('Text', 'channel_url', array(
                'label' => 'URL',
                'description' => $description,
                'autocomplete' => 'off',
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                    array('NotEmpty', true),
                    array('StringLength', true, array(3, 255)),
                    array('Regex', true, array('/^[a-zA-Z0-9-_]+$/')),
                    array('Db_NoRecordExists', true, array(Engine_Db_Table::getTablePrefix() . 'sitevideo_channels', 'channel_url'), array('field' => 'channel_id', 'value != ?' => 1))
                ),
            ));
            $this->channel_url->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
            $this->channel_url->getValidator('NotEmpty')->setMessage('Please enter a valid channel url.', 'isEmpty');
            $this->channel_url->getValidator('Db_NoRecordExists')->setMessage('Someone has already picked this channel url, please use another one.', 'recordFound');
            $this->channel_url->getValidator('Regex')->setMessage('Profile addresses must start with a letter.', 'regexNotMatch');
            $this->addElement('dummy', 'channel_url_msg', array('value' => 0));
        }

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.tags.enabled', 1)) {
            $this->addElement('Text', 'tags', array(
                'label' => 'Tags (Keywords)',
                'autocomplete' => 'off',
                'description' => Zend_Registry::get('Zend_Translate')->_('Separate tags with commas.'),
                'filters' => array(
                    new Engine_Filter_Censor(),
                ),
            ));
            $this->tags->getDecorator("Description")->setOption("placement", "append");
        }
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.category.enabled', 1)) {
            // prepare categories
            $categories = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getCategories(array('fetchColumns' => array('category_id', 'category_name'), 'sponsored' => 0, 'cat_depandancy' => 1, 'orderBy' => 'category_name'));
            if (count($categories) != 0) {
                $categories_prepared[""] = "";
                foreach ($categories as $category) {
                    $categories_prepared[$category->category_id] = $category->category_name;
                }
            }

            if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
                $onChangeEvent = "showFields(this.value, 1); subcategories(this.value, '', '');";
                $categoryFiles = 'application/modules/Sitevideo/views/scripts/_formSubcategory.tpl';
            } else {
                $onChangeEvent = "showSMFields(this.value, 1);sm4.core.category.set(this.value, 'subcategory');";
                $categoryFiles = 'application/modules/Sitevideo/views/sitemobile/scripts/_subCategory.tpl';
            }

            if (count($categories) > 0) {
                $this->addElement('Select', 'category_id', array(
                    'label' => 'Category',
                    'allowEmpty' => false,
                    'required' => true,
                    'multiOptions' => $categories_prepared,
                    'onchange' => $onChangeEvent,
                ));
            }

            $this->addElement('Select', 'subcategory_id', array(
                'RegisterInArrayValidator' => false,
                'allowEmpty' => true,
                'required' => false,
                'decorators' => array(array('ViewScript', array('viewScript' => $categoryFiles, 'class' => 'form element')))
            ));

            $this->addElement('Select', 'subsubcategory_id', array(
                'RegisterInArrayValidator' => false,
                'allowEmpty' => true,
                'required' => false,
            ));

            $this->addDisplayGroup(array(
                'subcategory_id',
                'subsubcategory_id',
                    ), 'Select', array(
                'decorators' => array(array('ViewScript', array(
                            'viewScript' => $categoryFiles,
                            'class' => 'form element')))
            ));
        }

        $defaultProfileId = "0_0_" . $this->getDefaultProfileId();

        $customFields = new Sitevideo_Form_Custom_Standard(array(
            'item' => 'sitevideo_channel',
            'decorators' => array(
                'FormElements'
        )));

        $customFields->removeElement('submit');
        if ($customFields->getElement($defaultProfileId)) {
            $customFields->getElement($defaultProfileId)
                    ->clearValidators()
                    ->setRequired(false)
                    ->setAllowEmpty(true);
        }

        $this->addSubForms(array(
            'fields' => $customFields
        ));

        // Init descriptions
        $this->addElement('Textarea', 'description', array(
            'label' => 'Channel Description',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_EnableLinks(),
            ),
        ));
        $availableLabels = array(
            'everyone' => 'Everyone',
            'registered' => 'All Registered Members',
            'owner_network' => 'Friends and Networks',
            'owner_member_member' => 'Friends of Friends',
            'owner_member' => 'Friends Only',
            'owner' => 'Just Me'
        );
        // Element: auth_view
        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitevideo_channel', $user, 'auth_view');
        $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
        if (!empty($viewOptions) && count($viewOptions) >= 1) {
            // Make a hidden field
            if (count($viewOptions) == 1) {
                $this->addElement('hidden', 'auth_view', array('order' => $k++,'value' => key($viewOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_view', array(
                    'label' => 'Privacy',
                    'description' => 'Who may see this channel?',
                    'multiOptions' => $viewOptions,
                    'value' => key($viewOptions),
                ));
                $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
            }
        }

        // Element: auth_comment
        $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitevideo_channel', $user, 'auth_comment');
        $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));

        if (!empty($commentOptions) && count($commentOptions) >= 1) {
            // Make a hidden field
            if (count($commentOptions) == 1) {
                $this->addElement('hidden', 'auth_comment', array('order' => $k++,'value' => key($commentOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_comment', array(
                    'label' => 'Comment Privacy',
                    'description' => 'Who may post comments on this channel?',
                    'multiOptions' => $commentOptions,
                    'value' => key($commentOptions),
                ));
                $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
            }
        }
        $orderPrivacyHiddenFields = 786590;
        $topic_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitevideo_channel', $user, "auth_topic");
        $topic_options = array_intersect_key($availableLabels, array_flip($topic_options));

        if (count($topic_options) > 1) {
            $this->addElement('Select', 'auth_topic', array(
                'label' => 'Topics Privacy',
                'description' => Zend_Registry::get('Zend_Translate')->_("Who may post discussion topics for this channel?"),
                'multiOptions' => $topic_options,
                'value' => 'member'
            ));
            $this->auth_topic->getDecorator('Description')->setOption('placement', 'append');
        } elseif (count($topic_options) == 1) {
            $this->addElement('Hidden', 'auth_topic', array('value' => key($topic_options),
                'order' => ++$orderPrivacyHiddenFields));
        } else {
            $this->addElement('Hidden', 'auth_topic', array(
                'value' => 'member',
                'order' => ++$orderPrivacyHiddenFields
            ));
        }


        $this->addElement('File', 'photo', array(
            'label' => 'Main Photo',
            'description' => '',
        ));
        $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');

        // Init search
        $this->addElement('Checkbox', 'search', array(
            'label' => Zend_Registry::get('Zend_Translate')->_("Show this channel in search results"),
            'value' => 1,
            'disableTranslator' => true
        ));
        // Init submit
        $this->addElement('Button', 'channelsubmit', array(
            'label' => 'Save',
            'type' => 'submit',
        ));
    }

    public function saveValues() {

        $values = $this->getValues();
        $params = Array();
        if ((empty($values['owner_type'])) || (empty($values['owner_id']))) {
            $params['owner_id'] = Engine_Api::_()->user()->getViewer()->user_id;
            $params['owner_type'] = 'user';
        } else {
            $params['owner_id'] = $values['owner_id'];
            $params['owner_type'] = $values['owner_type'];
            throw new Zend_Exception("Non-user channel owners not yet implemented");
        }
        $params['title'] = $values['title'];
        $params['category_id'] = (int) @$values['category_id'];
        $params['subcategory_id'] = (int) @$values['subcategory_id'];
        $params['subsubcategory_id'] = (int) @$values['subsubcategory_id'];
        $params['description'] = $values['description'];
        $params['search'] = $values['search'];
        $channel = Engine_Api::_()->getDbtable('channels', 'sitevideo')->createRow();
        $channel->setFromArray($params);
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
        if (empty($values['auth_topic'])) {
            $values['auth_topic'] = key($form->auth_topic->options);
            if (empty($values['auth_topic'])) {
                $values['auth_topic'] = 'owner_member';
            }
        }
        $viewMax = array_search($values['auth_view'], $roles);
        $commentMax = array_search($values['auth_comment'], $roles);
        $topicMax = array_search($values['auth_topic'], $roles);
        foreach ($roles as $i => $role) {
            $auth->setAllowed($channel, $role, 'view', ($i <= $viewMax));
            $auth->setAllowed($channel, $role, 'comment', ($i <= $commentMax));
            $auth->setAllowed($channel, $role, 'topic', ($i <= $topicMax));
        }
        return $channel;
    }

}