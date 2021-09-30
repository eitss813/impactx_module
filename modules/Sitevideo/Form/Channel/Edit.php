<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Channel_Edit extends Engine_Form {

    protected $_defaultProfileId;
    protected $_item;

    public function getDefaultProfileId() {
        return $this->_defaultProfileId;
    }

    public function setDefaultProfileId($default_profile_id) {
        $this->_defaultProfileId = $default_profile_id;
        return $this;
    }

    public function getItem() {
        return $this->_item;
    }

    public function setItem(Core_Model_Item_Abstract $item) {
        $this->_item = $item;
        return $this;
    }

    public function init() {
        $user = Engine_Api::_()->user()->getViewer();

        $this->setTitle('Edit Channel Info')
                ->setAttrib('name', 'channels_edit');

        $this->addElement('Text', 'title', array(
            'label' => 'Channel Title',
            'required' => true,
            'notEmpty' => true,
            'validators' => array(
                'NotEmpty',
            ),
            'filters' => array(
                new Engine_Filter_Censor(),
                //new Engine_Filter_HtmlSpecialChars(),
                'StripTags',
                new Engine_Filter_StringLength(array('max' => '63'))
            )
        ));
        $this->title->getValidator('NotEmpty')->setMessage("Please specify an channel title");
        // Element: channel_url
        $channel_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('channel_id');
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
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
                'filters' => array(
              new Engine_Filter_Censor(),
              'StripTags',
            ),
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
                'filters' => array(
              new Engine_Filter_Censor(),
              'StripTags',
            ),
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
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.category.enabled', 1)) {
            // prepare categories
            $categories = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getCategories(array('fetchColumns' => array('category_id', 'category_name'), 'sponsored' => 0, 'cat_depandancy' => 1));
            if (count($categories) != 0) {
                $categories_prepared[0] = "";
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
                    'multiOptions' => $categories_prepared,
                    'onchange' => $onChangeEvent,
                ));


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
        }

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.category.enabled', 1)) {
            $defaultProfileId = "0_0_" . $this->getDefaultProfileId();

            $customFields = new Sitevideo_Form_Custom_Standard(array(
                'item' => $this->_item,
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
        }
        $this->addElement('Textarea', 'description', array(
            'label' => 'Channel Description',
            'rows' => 2,
            'filters' => array(
                new Engine_Filter_Censor(),
                'StripTags',
                //new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
            )
        ));

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

        //NETWORK BASE CHANNEL
        if (Engine_Api::_()->sitevideo()->channelBaseNetworkEnable()) {
            // Make Network List
            $table = Engine_Api::_()->getDbtable('networks', 'network');
            $select = $table->select()
                    ->from($table->info('name'), array('network_id', 'title'))
                    ->order('title');
            $result = $table->fetchAll($select);

            $networksOptions = array('0' => 'Everyone');
            foreach ($result as $value) {
                $networksOptions[$value->network_id] = $value->title;
            }
            if (count($networksOptions) > 0) {
                $viewPricavyEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.networkprofile.privacy', 0);
                if ($viewPricavyEnable) {
                    $desc = 'Select the networks, members of which should be able to see your channel. (Press Ctrl and click to select multiple networks. Applied privacy will be a combination of the privacy chosen above in "View Privacy" and the privacy chosen here.)';
                } else {
                    $desc = 'Select the networks, members of which should be able to see your Channel in browse and search channels. (Press Ctrl and click to select multiple networks. Applied privacy will be a combination of the privacy chosen above in "View Privacy" and the privacy chosen here.)';
                }
                $this->addElement('Multiselect', 'networks_privacy', array(
                    'label' => 'Networks Selection',
                    'description' => $desc,
                    'multiOptions' => $networksOptions,
                    'value' => array(0)
                ));
            }
        }

        // View
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
                $this->addElement('hidden', 'auth_view', array('order'=>1001,'value' => key($viewOptions)));
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
                $this->addElement('hidden', 'auth_comment', array('order'=>1002,'value' => key($commentOptions)));
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

        $this->addElement('Checkbox', 'search', array(
            'label' => "Show this channel in search results",
        ));

        // Submit or succumb!
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'decorators' => array(
                'ViewHelper'
            )
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'edit', 'channel_id' => $this->getItem()->channel_id), 'sitevideo_specific', true),
            'onclick' => '',
            'decorators' => array(
                'ViewHelper'
            )
        ));

        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    }

}
