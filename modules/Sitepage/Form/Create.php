<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Create extends Engine_Form {

    public $_error = array();
    protected $_packageId;
    protected $_owner;
    protected $_quick;
    protected $_create;
    protected $_layoutId;

    public function getOwner() {
        return $this->_owner;
    }

    public function setOwner($owner) {
        $this->_owner = $owner;
        return $this;
    }

    public function getPackageId() {
        return $this->_packageId;
    }

    public function setPackageId($package_id) {
        $this->_packageId = $package_id;
        return $this;
    }

    public function setQuick($flage) {
        $this->_quick = $flage;
        return $this;
    }

    public function getQuick() {
        return $this->_quick;
    }
    public function setCreate($value) {
        $this->_create = $value;
        return $this;
    }

    public function getCreate() {
        return $this->_create;
    }

    public function getlayoutId() {
        return $this->_layoutId;
    }

    public function setlayoutId($layoutId) {
        $this->_layoutId = $layoutId;
        return $this->_layoutId;
    }

    public function init() {

        $this->loadDefaultDecorators();
        parent::init();
        $i=800000;
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $createFormFields = array(
            'location',
            'tags',
            'photo',
            'description',
            'overview',
            'price',
            'viewPrivacy',
            'commentPrivacy',
            'showHideAdvancedOptions' => 'Advanced Show / Hide Options',
            'allPostPrivacy'
        );

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion')) {
            $createFormFields = array_merge($createFormFields, array('discussionPrivacy'));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum')) {
            $createFormFields = array_merge($createFormFields, array('photoPrivacy'));
        }

        if ((Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {
            $createFormFields = array_merge($createFormFields, array('videoPrivacy'));
        } elseif (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo')) {
            $createFormFields = array_merge($createFormFields, array('videoPrivacy'));
        }

         if ((Engine_Api::_()->hasModuleBootstrap('document') && Engine_Api::_()->getDbtable('modules', 'document')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {
            $createFormFields = array_merge($createFormFields, array('documentPrivacy'));
        } elseif (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument')) {
            $createFormFields = array_merge($createFormFields, array('documentPrivacy'));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll')) {
            $createFormFields = array_merge($createFormFields, array('pollPrivacy'));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote')) {
            $createFormFields = array_merge($createFormFields, array('notePrivacy'));
        }

        if ((Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {
            $createFormFields = array_merge($createFormFields, array('eventPrivacy'));
        } elseif (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent')) {
            $createFormFields = array_merge($createFormFields, array('eventPrivacy'));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic')) {
            $createFormFields = array_merge($createFormFields, array('musicPrivacy'));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
            $createFormFields = array_merge($createFormFields, array('memberTitle'));
            $createFormFields = array_merge($createFormFields, array('memberInvite'));
            $createFormFields = array_merge($createFormFields, array('memberApproval'));
        }
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecrowdfunding') && (Engine_Api::_()->hasModuleBootstrap('sitecrowdfundingintegration') && Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {
            $createFormFields = array_merge($createFormFields, array('projectPrivacy'));
        }
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemusic') && (Engine_Api::_()->hasModuleBootstrap('sitemusic') && Engine_Api::_()->getDbtable('modules', 'sitemusic')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {
            $createFormFields = array_merge($createFormFields, array('sitemusicPrivacy'));
        }
        $createFormFields = array_merge($createFormFields, array(
            'subPagePrivacy',
            'claimThisPage',
            'status',
            'search'
        ));

        $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
        if (empty($page_id) && Engine_Api::_()->getApi('settings', 'core')->hasSetting('sitepage.createFormFields')) {
            $createFormFields = $settings->getSetting('sitepage.createFormFields', $createFormFields);
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $user = $this->getOwner();
        $viewer_id = $viewer->getIdentity();
        $userlevel_id = $user->level_id;
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $sitepageMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
        $this->setTitle('Create New Page')
                ->setDescription('Configure your page to showcase your offerings and connect to your customers.')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setAttrib('name', 'sitepages_create');
        $this->getDecorator('Description')->setOption('escape', false);
        if ($this->getQuick()) {
            $this->setAttrib('id', 'sitepages_create_quick');
            // $this->setDescription('');
        }

        // TITLE
        $this->addElement('Text', 'title', array(
            'label' => 'Title',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '128')),
        )));

        // Element: page_url
        $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
        $parent_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('parent_id', null);
        $sitepageUrlEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageurl');
        $show_url = $coreSettings->getSetting('sitepage.showurl.column', 1);
        $change_url = $coreSettings->getSetting('sitepage.change.url', 1);
        $edit_url = $coreSettings->getSetting('sitepage.edit.url', 0);
        //if (empty($page_id)) {
// // This will be the end of your page URL. It should be indicative of the title and can contain alphabets, numbers, underscores and  dashes only. Its length should be in the range of 3-255 characters. The complete URL of your page will be:
//  <br /> <span id="page_url_address">http://%s</span>
//       $description = Zend_Registry::get('Zend_Translate')->_('This will be the end of your page URL. It should be indicative of the title and can contain alphabets, numbers, underscores and  dashes only. Its length should be in the range of 3-255 characters. The complete URL of your page will be:');
        //$description = sprintf($description, $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page_url' => Zend_Registry::get('Zend_Translate')->_('PAGE-NAME')), 'sitepage_entry_view')).'<br />';

        $link = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page_url' => Zend_Registry::get('Zend_Translate')->_('PAGE-NAME')), 'sitepage_entry_view');

        if (!empty($sitepageUrlEnabled) && !empty($change_url)) {

            $front = Zend_Controller_Front::getInstance();
            $baseUrl = $front->getBaseUrl();
            $PAGE_NAME = Zend_Registry::get('Zend_Translate')->_("PAGE-NAME");
            $link2 = $_SERVER['HTTP_HOST'] . $baseUrl . '/' . $PAGE_NAME;
            $limit = $coreSettings->getSetting('sitepage.likelimit.forurlblock', 5);
            if (empty($limit)) {
                $description = sprintf(Zend_Registry::get('Zend_Translate')->_("This will be the end of your page URL. It should be indicative of the title and can contain alphabets, numbers, underscores and  dashes only. Its length should be in the range of 3-255 characters. The complete URL of your page will be: %s"), "<span id='short_page_url_address'>http://$link2</span>");
            } else {
                $description = sprintf(Zend_Registry::get('Zend_Translate')->_("This will be the end of your page URL. It should be indicative of the title and can contain alphabets, numbers, underscores and  dashes only. Its length should be in the range of 3-255 characters. The complete URL of your page will be: %s"), "<span id='page_url_address'>http://$link</span>");
                $description = $description . sprintf(Zend_Registry::get('Zend_Translate')->_('<br />and if your page has %1$s or more likes URL will be: <br />%2$s'), "$limit", "<span id='short_page_url_address'>http://$link2</span>");
            }
        } else {
            $description = sprintf(Zend_Registry::get('Zend_Translate')->_("This will be the end of your page URL. It should be indicative of the title and can contain alphabets, numbers, underscores and  dashes only. Its length should be in the range of 3-255 characters. The complete URL of your page will be: %s"), "<span id='page_url_address'>http://$link</span>");
        }

        if (!empty($sitepageUrlEnabled) && !empty($page_id) && !empty($show_url) && !empty($edit_url)) {
            $this->addElement('Text', 'page_url', array(
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
                'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
            ));
            $this->page_url->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
            $this->page_url->getValidator('NotEmpty')->setMessage('Please enter a valid page url.', 'isEmpty');
            $this->page_url->getValidator('Regex')->setMessage('Profile addresses must start with a letter.', 'regexNotMatch');
            $this->addElement('dummy', 'page_url_msg', array('value' => 0));
        } elseif (empty($page_id)) {
            $this->addElement('Text', 'page_url', array(
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
                    array('Db_NoRecordExists', true, array(Engine_Db_Table::getTablePrefix() . 'sitepage_pages', 'page_url'), array('field' => 'page_id', 'value != ?' => 1))
                ),
                'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
                    //'onblur' => 'var el = this; en4.user.checkpage_urlTaken(this.value, function(taken){ el.style.marginBottom = taken * 100 + "px" });'
            ));
            $this->page_url->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
            $this->page_url->getValidator('NotEmpty')->setMessage('Please enter a valid page url.', 'isEmpty');
            $this->page_url->getValidator('Db_NoRecordExists')->setMessage('Someone has already picked this page url, please use another one.', 'recordFound');
            $this->page_url->getValidator('Regex')->setMessage('Profile addresses must start with a letter.', 'regexNotMatch');
            //$this->page_url->getValidator('Alnum')->setMessage('Profile addresses must be alphanumeric.', 'notAlnum');
            $this->addElement('dummy', 'page_url_msg', array('value' => 0));
        }
        //}
        // init to
        if(($coreSettings->getSetting('sitepage.quick.enable', 0) == 0) || $this->getCreate() == 0) {
            if (!empty($createFormFields) && in_array('tags', $createFormFields)) {
                $this->addElement('Text', 'tags', array(
                    'label' => 'Tags (Keywords)',
                    'autocomplete' => 'off',
                    'description' => 'Separate tags with commas.',
                    'filters' => array(
                    'StripTags',
                    new Engine_Filter_Censor(),
                ),
                ));

                $this->tags->getDecorator("Description")->setOption("placement", "append");
            }
        }
        // prepare categories
        $categories = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategories();
        if (count($categories) != 0) {
            $categories_prepared[0] = "";
            foreach ($categories as $category) {
                $categories_prepared[$category->category_id] = $category->category_name;
            }

            //category field
        if (!$this->_item && $coreSettings->getSetting('sitepage.profile.fields', 1)) {
            $this->addElement('Select', 'category_id', array(
                'label' => 'Category',
                'allowEmpty' => false,
                'required' => true,
                'multiOptions' => $categories_prepared,
                'onchange' => " var profile_type = getProfileType($(this).value);
													if(profile_type == 0) profile_type = '';
													$('0_0_1').value = profile_type;
													changeFields($('0_0_1'));
													subcategory(this.value, '', '');",
            ));
        } else {
            $this->addElement('Select', 'category_id', array(
                'label' => 'Category',
                'allowEmpty' => false,
                'required' => true,
                'multiOptions' => $categories_prepared,
                'onchange' => "subcategory(this.value, '', '');",
            ));
        }
    }

        if(($coreSettings->getSetting('sitepage.quick.enable', 0) == 0) || $this->getCreate() == 0) {
            $this->addElement('Select', 'subcategory_id', array(
                'RegisterInArrayValidator' => false,
                'allowEmpty' => true,
                'required' => false,
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
                            'viewScript' => 'application/modules/Sitepage/views/scripts/_formSubcategory.tpl',
                            'class' => 'form element')))
            ));
            if (!$this->_item && $coreSettings->getSetting('sitepage.profile.fields', 1)) {
                $customFields = new Sitepage_Form_Custom_Standard(array(
                    'item' => 'sitepage_page',
                    'decorators' => array(
                        'FormElements'
                )));

                $customFields->removeElement('submit');

                $customFields->getElement("0_0_1")
                        ->clearValidators()
                        ->setRequired(false)
                        ->setAllowEmpty(true);

                $this->addSubForms(array(
                    'fields' => $customFields
                ));
            }
        }

        if ($coreSettings->getSetting('sitepage.description.allow', 1)) {
            if ($coreSettings->getSetting('sitepage.requried.description', 1)) {
                // body
                $this->addElement('textarea', 'body', array(
                    'label' => 'Description',
                    'required' => true,
                    'allowEmpty' => false,
                    'attribs' => array('rows' => 24, 'cols' => 180, 'style' => 'width:300px; max-width:400px;height:120px;'),
                    'filters' => array(
                        'StripTags',
                        //new Engine_Filter_HtmlSpecialChars(),
                        new Engine_Filter_EnableLinks(),
                        new Engine_Filter_Censor(),
                    ),
                ));
            } else {
                if (!empty($createFormFields) && in_array('description', $createFormFields)) {
                    $this->addElement('textarea', 'body', array(
                        'label' => 'Description',
                        'attribs' => array('rows' => 24, 'cols' => 180, 'style' => 'width:300px; max-width:400px;height:120px;'),
                        'filters' => array(
                            'StripTags',
                            //new Engine_Filter_HtmlSpecialChars(),
                            new Engine_Filter_EnableLinks(),
                            new Engine_Filter_Censor(),
                        ),
                    ));
                }
            }
        }

        // TITLE
        $this->addElement('Text', 'page_header_name', array(
            'label' => 'Header Name',
            'allowEmpty' => false,
            'required' => false,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '128')),
            )
        ));

        //$allowed_upload = Engine_Api::_()->authorization()->getPermission($user_level, 'sitepage', 'photo');
        //if ($allowed_upload) {

        /*if ($coreSettings->getSetting('sitepage.requried.photo', 1)) {
            $this->addElement('File', 'photo', array(
                'label' => 'Main Photo',
                'required' => true,
                'allowEmpty' => false,
            ));
            $this->photo->addValidator('Extension', false, 'jpg,jpeg,png,gif');
        } else {
            if (!empty($createFormFields) && in_array('photo', $createFormFields)) {
                $this->addElement('File', 'photo', array(
                    'label' => 'Main Photo',
                ));
                $this->photo->addValidator('Extension', false, 'jpg,jpeg,png,gif');
            }
        }*/

        //}
        // PRICE
        /*
        if(($coreSettings->getSetting('sitepage.quick.enable', 0) == 0) || $this->getCreate() == 0) {
            if (!empty($createFormFields) && in_array('price', $createFormFields)) {
                if ($coreSettings->getSetting('sitepage.price.field', 1)) {
                    $localeObject = Zend_Registry::get('Locale');
                    $currencyCode = $coreSettings->getSetting('payment.currency', 'USD');
                    $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
                    $this->addElement('Text', 'price', array(
                        'label' => sprintf(Zend_Registry::get('Zend_Translate')->_('Price (%s)'), $currencyName),
                        // 'description' => '(Zero will make this a free page.)',
                        'filters' => array(
                            'StripTags',
                            new Engine_Filter_Censor(),
                    )));
                    //$this->price->getDecorator('Description')->setOption('placement', 'append');
                }
            }
            // pre-defined layout section
            $layoutSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage_layout_enable', 1);
            if($layoutSetting == 3) {
                $this->addElement('dummy', 'layout', array(
                    'label' => 'Select layout'));
                    $this->addElement('text','layout_id',array(
                        'label' => 'Start',
                        'decorators' => array( array('ViewScript', array(
                              'viewScript' => '_definedlayoutElement.tpl',
                              'layout' => $this->_layoutId
                          ))
                        ),
                        ));
            }
            // LOCATION
            if (!empty($createFormFields) && in_array('location', $createFormFields) && $coreSettings->getSetting('sitepage.locationfield', 1)) {
                $this->addElement('Text', 'location', array(
                    'label' => 'Location',
                    'description' => 'Eg: Fairview Park, Berkeley, CA',
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                )));
                $this->location->getDecorator('Description')->setOption('placement', 'append');
                $this->addElement('Hidden', 'locationParams', array( 'order' => $i++,));


                include_once APPLICATION_PATH . '/application/modules/Seaocore/Form/specificLocationElement.php';
            }

            // Privacy
            // Privacy
            $pageadminsetting = $coreSettings->getSetting('sitepage.manageadmin', 1);
            if (!empty($pageadminsetting)) {
                $ownerTitle = "Page Admins";
            } else {
                $ownerTitle = "Just Me";
            }


            //START SITEPAGEMEMBER PLUGIN WORK
            $allowMemberInLevel = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitepage_page', 'smecreate');
            $allowMemberInthisPackage = false;
            $allowMemberInthisPackage = Engine_Api::_()->sitepage()->allowPackageContent($this->getPackageId(), "modules", "sitepagemember");
            if (!empty($createFormFields) && (in_array('memberTitle', $createFormFields) || in_array('memberInvite', $createFormFields) || in_array('memberApproval', $createFormFields))) {
                if ($sitepageMemberEnabled) {
                    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                        if ($allowMemberInthisPackage) {

                            $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('pagemember.member.title', 1);
                            if (in_array('memberTitle', $createFormFields) && !empty($memberTitle)) {
                                $this->addElement('Text', 'member_title', array(
                                    'label' => 'What will members be called?',
                                    'description' => 'Ex: Dance Lovers, Hikers, Innovators, Music Lovers, etc.',
                                    'filters' => array(
                                        'StripTags',
                                        new Engine_Filter_Censor(),
                                )));
                                $this->member_title->getDecorator('Description')->setOption('placement', 'append');
                            }

                            $memberInvite = Engine_Api::_()->getApi('settings', 'core')->getSetting('pagemember.invite.option', 1);
                            if (in_array('memberInvite', $createFormFields) && !empty($memberInvite)) {
                                $this->addElement('Radio', 'member_invite', array(
                                    'label' => 'Invite member',
                                    //'description' => 'Do you want page members to invite other people to this page?',
                                    'multiOptions' => array(
                                        '0' => 'Yes, members can invite other people.',
                                        '1' => 'No, only page admins can invite other people.',
                                    ),
                                    'value' => '1',
                                    'attribs' => array('class' => 'sp_quick_advanced')
                                ));
                            }

                            $member_approval = Engine_Api::_()->getApi('settings', 'core')->getSetting('pagemember.member.approval.option', 1);
                            if (in_array('memberApproval', $createFormFields) && !empty($member_approval)) {
                                $this->addElement('Radio', 'member_approval', array(
                                    'label' => 'Approve members?',
                                    'description' => 'When people try to join this page, should they be allowed ' .
                                    'to join immediately, or should they be forced to wait for approval?',
                                    'multiOptions' => array(
                                        '1' => 'New members can join immediately.',
                                        '0' => 'New members must be approved.',
                                    ),
                                    'value' => '1',
                                ));
                            }
                        }
                    } else if (!empty($allowMemberInLevel)) {

                        $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('pagemember.member.title', 1);
                        if (in_array('memberTitle', $createFormFields) && !empty($memberTitle)) {
                            $this->addElement('Text', 'member_title', array(
                                'label' => 'What will members be called?',
                                'description' => 'Ex: Dance Lovers, Hikers, Innovators, Music Lovers, etc.',
                                'filters' => array(
                                    'StripTags',
                                    new Engine_Filter_Censor(),
                            )));
                            $this->member_title->getDecorator('Description')->setOption('placement', 'append');
                        }

                        if (in_array('memberInvite', $createFormFields)) {
                            $this->addElement('Radio', 'member_invite', array(
                                'label' => 'Invite member',
                                'multiOptions' => array(
                                    '0' => 'Yes, members can invite other people.',
                                    '1' => 'No, only page admins can invite other people.',
                                ),
                                'value' => '1',
                                'attribs' => array('class' => 'sp_quick_advanced')
                            ));
                        }

                        if (in_array('memberApproval', $createFormFields)) {
                            $this->addElement('Radio', 'member_approval', array(
                                'label' => 'Approve members?',
                                'description' => ' When people try to join this page, should they be allowed ' .
                                'to join immediately, or should they be forced to wait for approval?',
                                'multiOptions' => array(
                                    '1' => 'New members can join immediately.',
                                    '0' => 'New members must be approved.',
                                ),
                                'value' => '1',
                            ));
                        }
                    }
                }
            }

            if (!empty($createFormFields) && in_array('allPostPrivacy', $createFormFields)) {
                $this->addElement('Select', 'all_post', array(
                    'label' => 'Post in Updates Tab',
                    'multiOptions' => array("1" => "Everyone", "0" => "Page Admins"),
                    'description' => 'Who is allowed to post in this page?',
                    'attribs' => array('class' => 'sp_quick_advanced')
                ));
                $this->all_post->getDecorator('Description')->setOption('placement', 'append');
            }
            //END PAGE MEMBER WORK
            $availableLabels = array(
                'everyone' => 'Everyone',
                'registered' => 'All Registered Members',
                'owner_network' => 'Friends and Networks',
                'owner_member_member' => 'Friends of Friends',
                'owner_member' => 'Friends Only',
            );
            if (!empty($sitepageMemberEnabled) && $allowMemberInthisPackage) {
                $availableLabels['member'] = 'Page Members Only';
            } elseif (!empty($sitepageMemberEnabled) && $allowMemberInLevel) {
                $availableLabels['member'] = 'Page Members Only';
            }
            $availableLabels['owner'] = $ownerTitle;

            // View
            if (!empty($createFormFields) && in_array('viewPrivacy', $createFormFields)) {
                $orderPrivacyHiddenFields = 786590;
                $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'auth_view');
                $view_options = array_intersect_key($availableLabels, array_flip($view_options));

                if (count($view_options) > 1) {
                    $this->addElement('Select', 'auth_view', array(
                        'label' => 'View Privacy',
                        'description' => 'Who may see this page? (Note: Page information will always be displayed to everyone.)',
                        'multiOptions' => $view_options,
                        'value' => key($view_options),
                    ));
                    $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
                } elseif (count($view_options) == 1) {
                    $this->addElement('Hidden', 'auth_view', array(
                        'value' => key($view_options),
                        'order' => ++$orderPrivacyHiddenFields,
                    ));
                } else {
                    $this->addElement('Hidden', 'auth_view', array(
                        'value' => "everyone",
                        'order' => ++$orderPrivacyHiddenFields,
                    ));
                }
            }
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feature.extension',0)) {
                $this->addElement('Radio', 'show_name', array(
                    'label' => 'Display page owner name?',
                    'description' => 'Do you want to show page owner name.',
                    'multiOptions' => array(
                        1 => 'Yes',
                        0 => 'No'
                    ),
                    'value' => '1'
                ));
                $this->show_name->getDecorator('Description')->setOption('placement', 'append');
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
                    $this->addElement('Radio', 'join_enable', array(
                        'label' => 'Enable Joining?',
                        'description' => 'Do you want to enable joining of other members to this page.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1'
                    ));
                    $this->join_enable->getDecorator('Description')->setOption('placement', 'append');
                }
            }
            //NETWORK BASE PAGE VIEW PRIVACY
            if (Engine_Api::_()->getApi('subCore', 'sitepage')->pageBaseNetworkEnable()) {
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
                    $viewPricavyEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.networkprofile.privacy', 0);
                    if ($viewPricavyEnable) {
                        $desc = 'Select the networks, members of which should be able to see your page. (Press Ctrl and click to select multiple networks. Applied privacy will be a combination of the privacy chosen above in "View Privacy" and the privacy chosen here.)';
                    } else {
                        $desc = 'Select the networks, members of which should be able to see your Page in browse and search pages. (Press Ctrl and click to select multiple networks. Applied privacy will be a combination of the privacy chosen above in "View Privacy" and the privacy chosen here.)';
                    }
                    $this->addElement('Multiselect', 'networks_privacy', array(
                        'label' => 'Networks Selection',
                        'description' => $desc,
    //            'attribs' => array('style' => 'max-height:150px; '),
                        'multiOptions' => $networksOptions,
                        'value' => array(0)
                    ));
                }
            }

            if (!empty($createFormFields) && in_array('commentPrivacy', $createFormFields)) {
                // Comment
                $comment_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'auth_comment');
                $comment_options = array_intersect_key($availableLabels, array_flip($comment_options));

                if (count($comment_options) > 1) {
                    $this->addElement('Select', 'auth_comment', array(
                        'label' => 'Comment Privacy',
                        'description' => 'Who may post comments on this page?',
                        'multiOptions' => $comment_options,
                        'value' => key($comment_options),
                    ));
                    $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
                } elseif (count($comment_options) == 1) {
                    $this->addElement('Hidden', 'auth_comment', array(
                        'value' => key($comment_options),
                        'order' => ++$orderPrivacyHiddenFields,
                    ));
                } else {
                    $this->addElement('Hidden', 'auth_comment', array(
                        'value' => "everyone",
                        'order' => ++$orderPrivacyHiddenFields,
                    ));
                }
            }

            //START DISCUSSION PRIVACY WORK
            if (!empty($createFormFields) && in_array('discussionPrivacy', $createFormFields)) {
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion')) {
                    $availableLabels = array(
                        'registered' => 'All Registered Members',
                        'owner_network' => 'Friends and Networks',
                        'owner_member_member' => 'Friends of Friends',
                        'owner_member' => 'Friends Only',
                        'like_member' => 'Who Liked This Page',
                    );
                    if (!empty($sitepageMemberEnabled) && $allowMemberInthisPackage) {
                        $availableLabels['member'] = 'Page Members Only';
                    } elseif (!empty($sitepageMemberEnabled) && $allowMemberInLevel) {
                        $availableLabels['member'] = 'Page Members Only';
                    }
                    $availableLabels['owner'] = $ownerTitle;

                    $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'auth_sdicreate');
                    $options_create = array_intersect_key($availableLabels, array_flip($options));

                    if (!empty($options_create)) {
                        $can_show_list = true;
                        if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                            if (!Engine_Api::_()->sitepage()->allowPackageContent($this->getPackageId(), "modules", "sitepagediscussion")) {
                                $can_show_list = false;
                                $this->addElement('Hidden', 'sdicreate', array(
                                     'order' => $i++,
                                    'value' => @array_search(@end($options_create), $options_create)
                                ));
                            }
                        } else {
                            $can_create = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitepage_page', 'sdicreate');
                            if (!$can_create) {
                                $can_show_list = false;
                                $this->addElement('Hidden', 'sdicreate', array(
                                     'order' => $i++,
                                    'value' => @array_search(@end($options_create), $options_create)
                                ));
                            }
                        }

                        if ($can_show_list) {
                            if (count($options_create) > 1) {
                                $this->addElement('Select', 'sdicreate', array(
                                    'label' => 'Discussion Topic Post Privacy',
                                    'description' => 'Who may post discussion topics for this page?',
                                    'multiOptions' => $options_create,
                                    'value' => key($options_create),
                                    'attribs' => array('class' => 'sp_quick_advanced')
                                ));
                                $this->sdicreate->getDecorator('Description')->setOption('placement', 'append');
                            } elseif (count($options_create) == 1) {
                                $this->addElement('Hidden', 'sdicreate', array(
                                    'value' => key($options_create),
                                    'order' => ++$orderPrivacyHiddenFields,
                                ));
                            } else {
                                $this->addElement('Hidden', 'sdicreate', array(
                                    'value' => 'registered',
                                    'order' => ++$orderPrivacyHiddenFields,
                                ));
                            }
                        }
                    } else {
                        $this->addElement('Hidden', 'sdicreate', array(
                            'value' => 'registered',
                            'order' => ++$orderPrivacyHiddenFields,
                        ));
                    }
                }
            }
            //END DISCUSSION PRIVACY WORK
            //START PHOTO PRIVACY WORK
            if (!empty($createFormFields) && in_array('photoPrivacy', $createFormFields)) {
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum')) {
                    $availableLabels = array(
                        'registered' => 'All Registered Members',
                        'owner_network' => 'Friends and Networks',
                        'owner_member_member' => 'Friends of Friends',
                        'owner_member' => 'Friends Only',
                        'like_member' => 'Who Liked This Page',
                    );
                    if (!empty($sitepageMemberEnabled) && $allowMemberInthisPackage) {
                        $availableLabels['member'] = 'Page Members Only';
                    } elseif (!empty($sitepageMemberEnabled) && $allowMemberInLevel) {
                        $availableLabels['member'] = 'Page Members Only';
                    }
                    $availableLabels['owner'] = $ownerTitle;

                    $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'auth_spcreate');
                    $options_create = array_intersect_key($availableLabels, array_flip($options));

                    if (!empty($options_create)) {
                        $can_show_list = true;
                        if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                            if (!Engine_Api::_()->sitepage()->allowPackageContent($this->getPackageId(), "modules", "sitepagealbum")) {
                                $can_show_list = false;
                                $this->addElement('Hidden', 'spcreate', array(
                                     'order' => $i++,
                                    'value' => @array_search(@end($options_create), $options_create)
                                ));
                            }
                        } else {
                            $can_create = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitepage_page', 'spcreate');
                            if (!$can_create) {
                                $can_show_list = false;
                                $this->addElement('Hidden', 'spcreate', array(
                                     'order' => $i++,
                                    'value' => @array_search(@end($options_create), $options_create)
                                ));
                            }
                        }

                        if ($can_show_list) {
                            if (count($options_create) > 1) {
                                $this->addElement('Select', 'spcreate', array(
                                    'label' => 'Photo Creation Privacy',
                                    'description' => 'Who may upload photos for this page?',
                                    'multiOptions' => $options_create,
                                    'value' => @array_search(@end($options_create), $options_create),
                                    'attribs' => array('class' => 'sp_quick_advanced')
                                ));
                                $this->spcreate->getDecorator('Description')->setOption('placement', 'append');
                            } elseif (count($options_create) == 1) {
                                $this->addElement('Hidden', 'spcreate', array(
                                    'value' => key($options_create),
                                    'order' => ++$orderPrivacyHiddenFields,
                                ));
                            } else {
                                $this->addElement('Hidden', 'spcreate', array(
                                    'value' => 'registered',
                                    'order' => ++$orderPrivacyHiddenFields,
                                ));
                            }
                        }
                    } else {
                        $this->addElement('Hidden', 'spcreate', array(
                            'value' => 'registered',
                            'order' => ++$orderPrivacyHiddenFields,
                        ));
                    }
                }
            }
            //END PHOTO PRIVACY WORK
            //START SITEPAGEDOCUMENT PLUGIN WORK
            if (!empty($createFormFields) && in_array('documentPrivacy', $createFormFields)) {
                $sitepageDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument');
                if ((Engine_Api::_()->hasModuleBootstrap('document') && Engine_Api::_()->getDbtable('modules', 'document')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))) || $sitepageDocumentEnabled) {
                    $availableLabels = array(
                        'registered' => 'All Registered Members',
                        'owner_network' => 'Friends and Networks',
                        'owner_member_member' => 'Friends of Friends',
                        'owner_member' => 'Friends Only',
                        'like_member' => 'Who Liked This Page',
                    );
                    if (!empty($sitepageMemberEnabled) && $allowMemberInthisPackage) {
                        $availableLabels['member'] = 'Page Members Only';
                    } elseif (!empty($sitepageMemberEnabled) && $allowMemberInLevel) {
                        $availableLabels['member'] = 'Page Members Only';
                    }
                    $availableLabels['owner'] = $ownerTitle;

                    $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'auth_sdcreate');
                    $options_create = array_intersect_key($availableLabels, array_flip($options));

                    if (!empty($options_create)) {
                        $can_show_list = true;
                        if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                            if (!Engine_Api::_()->sitepage()->allowPackageContent($this->getPackageId(), "modules", "sitepagedocument")) {
                                $can_show_list = false;
                                $this->addElement('Hidden', 'sdcreate', array(
                                     'order' => $i++,
                                    'value' => @array_search(@end($options_create), $options_create)
                                ));
                            }
                        } else {
                            $can_create = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitepage_page', 'sdcreate');
                            if (!$can_create) {
                                $can_show_list = false;
                                $this->addElement('Hidden', 'sdcreate', array(
                                     'order' => $i++,
                                    'value' => @array_search(@end($options_create), $options_create)
                                ));
                            }
                        }
                        if ($can_show_list) {
                            if (count($options_create) > 1) {
                                $this->addElement('Select', 'sdcreate', array(
                                    'label' => 'Documents Creation Privacy',
                                    'description' => 'Who may create documents for this page?',
                                    'multiOptions' => $options_create,
                                    'value' => @array_search(@end($options_create), $options_create),
                                    'attribs' => array('class' => 'sp_quick_advanced')
                                ));
                                $this->sdcreate->getDecorator('Description')->setOption('placement', 'append');
                            } elseif (count($options_create) == 1) {
                                $this->addElement('Hidden', 'sdcreate', array(
                                    'value' => key($options_create),
                                    'order' => ++$orderPrivacyHiddenFields,
                                ));
                            } else {
                                $this->addElement('Hidden', 'sdcreate', array(
                                    'value' => 'registered',
                                    'order' => ++$orderPrivacyHiddenFields,
                                ));
                            }
                        }
                    } else {
                        $this->addElement('Hidden', 'sdcreate', array(
                            'value' => 'registered',
                            'order' => ++$orderPrivacyHiddenFields,
                        ));
                    }
                }
            }
            //END SITEPAGEDOCUMENT PLUGIN WORK
            //START SITEPAGEVIDEO PLUGIN WORK
            if (!empty($createFormFields) && in_array('videoPrivacy', $createFormFields)) {
                if ((Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))) || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo')) {
                    $availableLabels = array(
                        'registered' => 'All Registered Members',
                        'owner_network' => 'Friends and Networks',
                        'owner_member_member' => 'Friends of Friends',
                        'owner_member' => 'Friends Only',
                        'like_member' => 'Who Liked This Page',
                    );
                    if (!empty($sitepageMemberEnabled) && $allowMemberInthisPackage) {
                        $availableLabels['member'] = 'Page Members Only';
                    } elseif (!empty($sitepageMemberEnabled) && $allowMemberInLevel) {
                        $availableLabels['member'] = 'Page Members Only';
                    }
                    $availableLabels['owner'] = $ownerTitle;

                    $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'auth_svcreate');
                    $options_create = array_intersect_key($availableLabels, array_flip($options));

                    if (!empty($options_create)) {
                        $can_show_list = true;
                        if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                            if (!Engine_Api::_()->sitepage()->allowPackageContent($this->getPackageId(), "modules", "sitepagevideo")) {
                                $can_show_list = false;
                                $this->addElement('Hidden', 'svcreate', array(
                                     'order' => $i++,
                                    'value' => @array_search(@end($options_create), $options_create)
                                ));
                            }
                        } else {
                            $can_create = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitepage_page', 'svcreate');
                            if (!$can_create) {
                                $can_show_list = false;
                                $this->addElement('Hidden', 'svcreate', array(
                                     'order' => $i++,
                                    'value' => @array_search(@end($options_create), $options_create)
                                ));
                            }
                        }
                        if ($can_show_list) {

                            if (count($options_create) > 1) {
                                $this->addElement('Select', 'svcreate', array(
                                    'label' => 'Videos Creation Privacy',
                                    'description' => 'Who may create videos for this page?',
                                    'multiOptions' => $options_create,
                                    'value' => @array_search(@end($options_create), $options_create),
                                    'attribs' => array('class' => 'sp_quick_advanced'),
                                ));
                                $this->svcreate->getDecorator('Description')->setOption('placement', 'append');
                            } elseif (count($options_create) == 1) {
                                $this->addElement('Hidden', 'svcreate', array(
                                    'value' => key($options_create),
                                    'order' => ++$orderPrivacyHiddenFields,
                                ));
                            } else {
                                $this->addElement('Hidden', 'svcreate', array(
                                    'value' => 'registered',
                                    'order' => ++$orderPrivacyHiddenFields,
                                ));
                            }
                        }
                    } else {
                        $this->addElement('Hidden', 'svcreate', array(
                            'value' => 'registered',
                            'order' => ++$orderPrivacyHiddenFields,
                        ));
                    }
                }
            }
            //END SITEPAGEVIDEO PLUGIN WORK
            //START SITEPAGEPOLL PLUGIN WORK
            if (!empty($createFormFields) && in_array('pollPrivacy', $createFormFields)) {
                $sitepagePollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll');
                if ($sitepagePollEnabled) {

                    $availableLabels = array(
                        'registered' => 'All Registered Members',
                        'owner_network' => 'Friends and Networks',
                        'owner_member_member' => 'Friends of Friends',
                        'owner_member' => 'Friends Only',
                        'like_member' => 'Who Liked This Page',
                    );
                    if (!empty($sitepageMemberEnabled) && $allowMemberInthisPackage) {
                        $availableLabels['member'] = 'Page Members Only';
                    } elseif (!empty($sitepageMemberEnabled) && $allowMemberInLevel) {
                        $availableLabels['member'] = 'Page Members Only';
                    }
                    $availableLabels['owner'] = $ownerTitle;

                    $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'auth_splcreate');
                    $options_create = array_intersect_key($availableLabels, array_flip($options));

                    if (!empty($options_create)) {
                        $can_show_list = true;
                        if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                            if (!Engine_Api::_()->sitepage()->allowPackageContent($this->getPackageId(), "modules", "sitepagepoll")) {
                                $can_show_list = false;
                                $this->addElement('Hidden', 'splcreate', array(
                                     'order' => $i++,
                                    'value' => @array_search(@end($options_create), $options_create)
                                ));
                            }
                        } else {
                            $can_create = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitepage_page', 'splcreate');
                            if (!$can_create) {
                                $can_show_list = false;
                                $this->addElement('Hidden', 'splcreate', array(
                                     'order' => $i++,
                                    'value' => @array_search(@end($options_create), $options_create)
                                ));
                            }
                        }
                        if ($can_show_list) {
                            if (count($options_create) > 1) {
                                $this->addElement('Select', 'splcreate', array(
                                    'label' => 'Polls Creation Privacy',
                                    'description' => 'Who may create polls for this page?',
                                    'multiOptions' => $options_create,
                                    'value' => @array_search(@end($options_create), $options_create),
                                    'attribs' => array('class' => 'sp_quick_advanced'),
                                ));
                                $this->splcreate->getDecorator('Description')->setOption('placement', 'append');
                            } elseif (count($options_create) == 1) {
                                $this->addElement('Hidden', 'splcreate', array(
                                    'value' => key($options_create),
                                    'order' => ++$orderPrivacyHiddenFields,
                                ));
                            } else {
                                $this->addElement('Hidden', 'splcreate', array(
                                    'value' => 'registered',
                                    'order' => ++$orderPrivacyHiddenFields,
                                ));
                            }
                        }
                    } else {
                        $this->addElement('Hidden', 'splcreate', array(
                            'value' => 'registered',
                            'order' => ++$orderPrivacyHiddenFields,
                        ));
                    }
                }
            }
            //END SITEPAGEPOLL PLUGIN WORK

            if (!empty($createFormFields) && in_array('notePrivacy', $createFormFields)) {
                //START SITEPAGENOTE PLUGIN WORK
                $sitepageNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote');
                if ($sitepageNoteEnabled) {
                    $availableLabels = array(
                        'registered' => 'All Registered Members',
                        'owner_network' => 'Friends and Networks',
                        'owner_member_member' => 'Friends of Friends',
                        'owner_member' => 'Friends Only',
                        'like_member' => 'Who Liked This Page',
                    );
                    if (!empty($sitepageMemberEnabled) && $allowMemberInthisPackage) {
                        $availableLabels['member'] = 'Page Members Only';
                    } elseif (!empty($sitepageMemberEnabled) && $allowMemberInLevel) {
                        $availableLabels['member'] = 'Page Members Only';
                    }
                    $availableLabels['owner'] = $ownerTitle;

                    $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'auth_sncreate');
                    $options_create = array_intersect_key($availableLabels, array_flip($options));

                    if (!empty($options_create)) {
                        $can_show_list = true;
                        if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                            if (!Engine_Api::_()->sitepage()->allowPackageContent($this->getPackageId(), "modules", "sitepagenote")) {
                                $can_show_list = false;
                                $this->addElement('Hidden', 'sncreate', array(
                                     'order' => $i++,
                                    'value' => @array_search(@end($options_create), $options_create)
                                ));
                            }
                        } else {
                            $can_create = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitepage_page', 'sncreate');
                            if (!$can_create) {
                                $can_show_list = false;
                                $this->addElement('Hidden', 'sncreate', array(
                                     'order' => $i++,
                                    'value' => @array_search(@end($options_create), $options_create)
                                ));
                            }
                        }
                        if ($can_show_list) {
                            if (count($options_create) > 1) {
                                $this->addElement('Select', 'sncreate', array(
                                    'label' => 'Notes Creation Privacy',
                                    'description' => 'Who may create notes for this page?',
                                    'multiOptions' => $options_create,
                                    'value' => @array_search(@end($options_create), $options_create),
                                    'attribs' => array('class' => 'sp_quick_advanced'),
                                ));
                                $this->sncreate->getDecorator('Description')->setOption('placement', 'append');
                            } elseif (count($options_create) == 1) {
                                $this->addElement('Hidden', 'sncreate', array(
                                    'value' => key($options_create),
                                    'order' => ++$orderPrivacyHiddenFields,
                                ));
                            } else {
                                $this->addElement('Hidden', 'sncreate', array(
                                    'value' => 'registered',
                                    'order' => ++$orderPrivacyHiddenFields,
                                ));
                            }
                        }
                    } else {
                        $this->addElement('Hidden', 'sncreate', array(
                            'value' => 'registered',
                            'order' => ++$orderPrivacyHiddenFields,
                        ));
                    }
                }
            }
            //END SITEPAGENOTE PLUGIN WORK
            //START SITEPAGEEVENT PLUGIN WORK
            if (!empty($createFormFields) && in_array('eventPrivacy', $createFormFields)) {
                if ((Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))) || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent') ) {
                    $availableLabels = array(
                        'registered' => 'All Registered Members',
                        'owner_network' => 'Friends and Networks',
                        'owner_member_member' => 'Friends of Friends',
                        'owner_member' => 'Friends Only',
                        'like_member' => 'Who Liked This Page',
                    );
                    if (!empty($sitepageMemberEnabled) && $allowMemberInthisPackage) {
                        $availableLabels['member'] = 'Page Members Only';
                    } elseif (!empty($sitepageMemberEnabled) && $allowMemberInLevel) {
                        $availableLabels['member'] = 'Page Members Only';
                    }
                    $availableLabels['owner'] = $ownerTitle;

                    $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'auth_secreate');
                    $options_create = array_intersect_key($availableLabels, array_flip($options));

                    if (!empty($options_create)) {
                        $can_show_list = true;
                        if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                            if (!Engine_Api::_()->sitepage()->allowPackageContent($this->getPackageId(), "modules", "sitepageevent")) {
                                $can_show_list = false;
                                $this->addElement('Hidden', 'secreate', array(
                                     'order' => $i++,
                                    'value' => @array_search(@end($options_create), $options_create)
                                ));
                            }
                        } else {
                            $can_create = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitepage_page', 'secreate');
                            if (!$can_create) {
                                $can_show_list = false;
                                $this->addElement('Hidden', 'secreate', array(
                                     'order' => $i++,
                                    'value' => @array_search(@end($options_create), $options_create)
                                ));
                            }
                        }
                        if ($can_show_list) {
                            if (count($options_create) > 1) {
                                $this->addElement('Select', 'secreate', array(
                                    'label' => 'Event Creation Privacy',
                                    'description' => 'Who may create events for this page?',
                                    'multiOptions' => $options_create,
                                    'value' => @array_search(@end($options_create), $options_create),
                                    'attribs' => array('class' => 'sp_quick_advanced'),
                                ));
                                $this->secreate->getDecorator('Description')->setOption('placement', 'append');
                            } elseif (count($options_create) == 1) {
                                $this->addElement('Hidden', 'secreate', array(
                                    'value' => key($options_create),
                                    'order' => ++$orderPrivacyHiddenFields,
                                ));
                            } else {
                                $this->addElement('Hidden', 'secreate', array(
                                    'value' => 'registered',
                                    'order' => ++$orderPrivacyHiddenFields,
                                ));
                            }
                        }
                    } else {
                        $this->addElement('Hidden', 'secreate', array(
                            'value' => 'registered',
                            'order' => ++$orderPrivacyHiddenFields,
                        ));
                    }
                }
            }
            //END SITEPAGEEVENT PLUGIN WORK

            if (!empty($createFormFields) && in_array('musicPrivacy', $createFormFields)) {
                //START SITEPAGEMUSIC PLUGIN WORK
                $sitepageMusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic');
                if ($sitepageMusicEnabled) {
                    $availableLabels = array(
                        'registered' => 'All Registered Members',
                        'owner_network' => 'Friends and Networks',
                        'owner_member_member' => 'Friends of Friends',
                        'owner_member' => 'Friends Only',
                        'like_member' => 'Who Liked This Page',
                    );
                    if (!empty($sitepageMemberEnabled) && $allowMemberInthisPackage) {
                        $availableLabels['member'] = 'Page Members Only';
                    } elseif (!empty($sitepageMemberEnabled) && $allowMemberInLevel) {
                        $availableLabels['member'] = 'Page Members Only';
                    }
                    $availableLabels['owner'] = $ownerTitle;

                    $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'auth_smcreate');
                    $options_create = array_intersect_key($availableLabels, array_flip($options));

                    if (!empty($options_create)) {
                        $can_show_list = true;
                        if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                            if (!Engine_Api::_()->sitepage()->allowPackageContent($this->getPackageId(), "modules", "sitepagemusic")) {
                                $can_show_list = false;
                                $this->addElement('Hidden', 'smcreate', array(
                                     'order' => $i++,
                                    'value' => @array_search(@end($options_create), $options_create)
                                ));
                            }
                        } else {
                            $can_create = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitepage_page', 'smcreate');
                            if (!$can_create) {
                                $can_show_list = false;
                                $this->addElement('Hidden', 'smcreate', array(
                                     'order' => $i++,
                                    'value' => @array_search(@end($options_create), $options_create)
                                ));
                            }
                        }
                        if ($can_show_list) {
                            if (count($options_create) > 1) {
                                $this->addElement('Select', 'smcreate', array(
                                    'label' => 'Music Creation Privacy',
                                    'description' => 'Who may upload music for this page?',
                                    'multiOptions' => $options_create,
                                    'value' => @array_search(@end($options_create), $options_create),
                                    'attribs' => array('class' => 'sp_quick_advanced'),
                                ));
                                $this->smcreate->getDecorator('Description')->setOption('placement', 'append');
                            } elseif (count($options_create) == 1) {
                                $this->addElement('Hidden', 'smcreate', array(
                                    'value' => key($options_create),
                                    'order' => ++$orderPrivacyHiddenFields,
                                ));
                            } else {
                                $this->addElement('Hidden', 'smcreate', array(
                                    'value' => 'registered',
                                    'order' => ++$orderPrivacyHiddenFields,
                                ));
                            }
                        }
                    } else {
                        $this->addElement('Hidden', 'smcreate', array(
                            'value' => 'registered',
                            'order' => ++$orderPrivacyHiddenFields,
                        ));
                    }
                }
            }
            //END SITEPAGEMUSIC PLUGIN WORK

            //START SITECROWDFUNDING PLUGIN WORK
            if (!empty($createFormFields) && in_array('projectPrivacy', $createFormFields)) {
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecrowdfunding') && (Engine_Api::_()->hasModuleBootstrap('sitecrowdfundingintegration') && Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {
                    $availableLabels = array(
                        'registered' => 'All Registered Members',
                        'owner_network' => 'Friends and Networks',
                        'owner_member_member' => 'Friends of Friends',
                        'owner_member' => 'Friends Only',
                        'like_member' => 'Who Liked This Page',
                    );
                    if (!empty($sitepageMemberEnabled) && $allowMemberInthisPackage) {
                        $availableLabels['member'] = 'Page Members Only';
                    } elseif (!empty($sitepageMemberEnabled) && $allowMemberInLevel) {
                        $availableLabels['member'] = 'Page Members Only';
                    }
                    $availableLabels['owner'] = $ownerTitle;

                    $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'auth_sprcreate');
                    $options_create = array_intersect_key($availableLabels, array_flip($options));

                    if (!empty($options_create)) {
                        $can_show_list = true;
                        if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                            if (!Engine_Api::_()->sitepage()->allowPackageContent($this->getPackageId(), "modules", "sitecrowdfunding")) {
                                $can_show_list = false;
                                $this->addElement('Hidden', 'sprcreate', array(
                                     'order' => $i++,
                                    'value' => @array_search(@end($options_create), $options_create)
                                ));
                            }
                        } else {
                            $can_create = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitepage_page', 'sprcreate');
                            if (!$can_create) {
                                $can_show_list = false;
                                $this->addElement('Hidden', 'sprcreate', array(
                                     'order' => $i++,
                                    'value' => @array_search(@end($options_create), $options_create)
                                ));
                            }
                        }
                        if ($can_show_list) {

                            if (count($options_create) > 1) {
                                $this->addElement('Select', 'sprcreate', array(
                                    'label' => 'Crowdfunding Projects Creation Privacy',
                                    'description' => 'Who may create projects for this page?',
                                    'multiOptions' => $options_create,
                                    'value' => @array_search(@end($options_create), $options_create),
                                    'attribs' => array('class' => 'sp_quick_advanced'),
                                ));
                                $this->sprcreate->getDecorator('Description')->setOption('placement', 'append');
                            } elseif (count($options_create) == 1) {
                                $this->addElement('Hidden', 'sprcreate', array(
                                    'value' => key($options_create),
                                    'order' => ++$orderPrivacyHiddenFields,
                                ));
                            } else {
                                $this->addElement('Hidden', 'sprcreate', array(
                                    'value' => 'registered',
                                    'order' => ++$orderPrivacyHiddenFields,
                                ));
                            }
                        }
                    } else {
                        $this->addElement('Hidden', 'sprcreate', array(
                            'value' => 'registered',
                            'order' => ++$orderPrivacyHiddenFields,
                        ));
                    }
                }
            }
            //END SITECROWDFUNDING PLUGIN WORK

            //START SITEMUSIC PLUGIN WORK
            if (!empty($createFormFields) && in_array('sitemusicPrivacy', $createFormFields)) {
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemusic') && (Engine_Api::_()->hasModuleBootstrap('sitemusic') && Engine_Api::_()->getDbtable('modules', 'sitemusic')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {
                    $availableLabels = array(
                        'registered' => 'All Registered Members',
                        'owner_network' => 'Friends and Networks',
                        'owner_member_member' => 'Friends of Friends',
                        'owner_member' => 'Friends Only',
                        'like_member' => 'Who Liked This Page',
                    );
                    if (!empty($sitepageMemberEnabled) && $allowMemberInthisPackage) {
                        $availableLabels['member'] = 'Page Members Only';
                    } elseif (!empty($sitepageMemberEnabled) && $allowMemberInLevel) {
                        $availableLabels['member'] = 'Page Members Only';
                    }
                    $availableLabels['owner'] = $ownerTitle;

                    $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'auth_samcreate');
                    $options_create = array_intersect_key($availableLabels, array_flip($options));

                    if (!empty($options_create)) {
                        $can_show_list = true;
                        if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                            if (!Engine_Api::_()->sitepage()->allowPackageContent($this->getPackageId(), "modules", "sitmusic")) {
                                $can_show_list = false;
                                $this->addElement('Hidden', 'samcreate', array(
                                     'order' => $i++,
                                    'value' => @array_search(@end($options_create), $options_create)
                                ));
                            }
                        } else {
                            $can_create = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitepage_page', 'samcreate');
                            if (!$can_create) {
                                $can_show_list = false;
                                $this->addElement('Hidden', 'samcreate', array(
                                     'order' => $i++,
                                    'value' => @array_search(@end($options_create), $options_create)
                                ));
                            }
                        }
                        if ($can_show_list) {

                            if (count($options_create) > 1) {
                                $this->addElement('Select', 'samcreate', array(
                                    'label' => 'Music Albums Creation Privacy',
                                    'description' => 'Who may create music for this page?',
                                    'multiOptions' => $options_create,
                                    'value' => @array_search(@end($options_create), $options_create),
                                    'attribs' => array('class' => 'sp_quick_advanced'),
                                ));
                                $this->samcreate->getDecorator('Description')->setOption('placement', 'append');
                            } elseif (count($options_create) == 1) {
                                $this->addElement('Hidden', 'samcreate', array(
                                    'value' => key($options_create),
                                    'order' => ++$orderPrivacyHiddenFields,
                                ));
                            } else {
                                $this->addElement('Hidden', 'samcreate', array(
                                    'value' => 'registered',
                                    'order' => ++$orderPrivacyHiddenFields,
                                ));
                            }
                        }
                    } else {
                        $this->addElement('Hidden', 'samcreate', array(
                            'value' => 'registered',
                            'order' => ++$orderPrivacyHiddenFields,
                        ));
                    }
                }
            }
            //END SITEMUSIC PLUGIN WORK

            if (!empty($createFormFields) && in_array('subPagePrivacy', $createFormFields)) {
                //START SUB PAGE WORK
                if (empty($parent_id)) {
                    $available_Labels = array(
                        'registered' => 'All Registered Members',
                        'owner_network' => 'Friends and Networks',
                        'owner_member_member' => 'Friends of Friends',
                        'owner_member' => 'Friends Only',
                        'like_member' => 'Who Liked This Page',
                    );
                    if (!empty($sitepageMemberEnabled) && $allowMemberInthisPackage) {
                        $available_Labels['member'] = 'Page Members Only';
                    } elseif (!empty($sitepageMemberEnabled) && $allowMemberInLevel) {
                        $available_Labels['member'] = 'Page Members Only';
                    }
                    $available_Labels['owner'] = $ownerTitle;

                    $subpagecreate_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'auth_sspcreate');
                    $subpagecreate_options = array_intersect_key($available_Labels, array_flip($subpagecreate_options));

                    $can_create = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitepage_page', 'sspcreate');
                    $can_show_list = true;
                    if (!$can_create) {
                        $can_show_list = false;
                        $this->addElement('Hidden', 'sspcreate', array(
                             'order' => $i++,
                            'value' => @array_search(@end($subpagecreate_options), $subpagecreate_options)
                        ));
                    }

                    if (count($subpagecreate_options) > 1 && !empty($can_show_list)) {
                        $this->addElement('Select', 'auth_sspcreate', array(
                            'label' => 'Sub Pages Creation Privacy',
                            'description' => 'Who may create sub pages in this page?',
                            'multiOptions' => $subpagecreate_options,
                            'value' => @array_search(@end($subpagecreate_options), $subpagecreate_options),
                            'attribs' => array('class' => 'sp_quick_advanced'),
                        ));
                        $this->auth_sspcreate->getDecorator('Description')->setOption('placement', 'append');
                    } elseif (count($subpagecreate_options) == 1 && !empty($can_show_list)) {
                        $this->addElement('Hidden', 'auth_sspcreate', array(
                            'value' => key($subpagecreate_options),
                            'order' => ++$orderPrivacyHiddenFields,
                        ));
                    } elseif (!empty($can_show_list)) {
                        $this->addElement('Hidden', 'auth_sspcreate', array(
                            'value' => 'owner',
                            'order' => ++$orderPrivacyHiddenFields,
                        ));
                    }
                }
            }
            //END WORK FOR SUBPAGE WORK.

            if (!empty($createFormFields) && in_array('claimThisPage', $createFormFields)) {
                $table = Engine_Api::_()->getDbtable('listmemberclaims', 'sitepage');
                $select = $table->select()
                        ->where('user_id = ?', $viewer_id)
                        ->limit(1);

                $row = $table->fetchRow($select);
                if ($row !== null) {
                    $this->addElement('Checkbox', 'userclaim', array(
                        'label' => 'Show "Claim this Page" link on this page.',
                        'value' => 1,
                        'attribs' => array('class' => 'sp_quick_advanced'),
                    ));
                }
            }
    }*/

        /*if (!empty($createFormFields) && in_array('status', $createFormFields)) {
            $this->addElement('Select', 'draft', array(
                'label' => 'Status',
                //'multiOptions' => array("1" => "Published", "0" => "Saved As Draft"),
                'multiOptions' => array("1" => "Submit For Review", "0" => "Saved As Draft"),
                //'description' => 'If this entry is published, it cannot be switched back to draft mode.',
                'description' => 'If this entry is submitted for review, it cannot be switched back to draft mode.',
                'onchange' => 'checkDraft();',
                'attribs' => array('class' => 'sp_quick_advanced'),
            ));
            $this->draft->getDecorator('Description')->setOption('placement', 'append');
        }*/


        /*$searchText = "Show this page in search results.";
        if (!empty($createFormFields) && in_array('search', $createFormFields)) {
            $this->addElement('Checkbox', 'search', array(
                'label' => $searchText,
                'value' => 1,
                'attribs' => array('class' => 'sp_quick_advanced'),
            ));
        }*/

        // Element: execute
        $this->addElement('Button', 'execute', array(
            'label' => 'Create',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        // Element: cancel
        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            // 'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'sitepage_general', true),
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        // DisplayGroup: buttons
        $this->addDisplayGroup(array(
            'execute',
            'cancel',
                ), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper'
            ),
        ));
    }

}
