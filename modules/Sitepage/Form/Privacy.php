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

class Sitepage_Form_Privacy extends Engine_Form
{

    public $_error = array();
    protected $_packageId;
    protected $_owner;
    protected $_quick;
    protected $_create;
    protected $_layoutId;

    public function getOwner()
    {
        return $this->_owner;
    }

    public function setOwner($owner)
    {
        $this->_owner = $owner;
        return $this;
    }

    public function getPackageId()
    {
        return $this->_packageId;
    }

    public function setPackageId($package_id)
    {
        $this->_packageId = $package_id;
        return $this;
    }

    public function setQuick($flage)
    {
        $this->_quick = $flage;
        return $this;
    }

    public function getQuick()
    {
        return $this->_quick;
    }

    public function setCreate($value)
    {
        $this->_create = $value;
        return $this;
    }

    public function getCreate()
    {
        return $this->_create;
    }

    public function getlayoutId()
    {
        return $this->_layoutId;
    }

    public function setlayoutId($layoutId)
    {
        $this->_layoutId = $layoutId;
        return $this->_layoutId;
    }

    public function init()
    {

        $this->loadDefaultDecorators();
        parent::init();
        $i = 800000;
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $createFormFields = array(
            'viewPrivacy',
            'commentPrivacy',
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

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecrowdfunding') && (Engine_Api::_()->hasModuleBootstrap('sitecrowdfundingintegration') && Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {
            $createFormFields = array_merge($createFormFields, array('projectPrivacy'));
        }
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemusic') && (Engine_Api::_()->hasModuleBootstrap('sitemusic') && Engine_Api::_()->getDbtable('modules', 'sitemusic')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {
            $createFormFields = array_merge($createFormFields, array('sitemusicPrivacy'));
        }
        $createFormFields = array_merge($createFormFields, array(
            'subPagePrivacy',
            'claimThisPage',
            'status'
        ));

        $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id');

        if (empty($page_id) && Engine_Api::_()->getApi('settings', 'core')->hasSetting('sitepage.createFormFields')) {
            $createFormFields = $settings->getSetting('sitepage.createFormFields', $createFormFields);
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $user = $this->getOwner();
        $viewer_id = $viewer->getIdentity();
        $userlevel_id = $user->level_id;
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $sitepageMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
        $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setAttrib('name', 'sitepages_edit_privacy');
        $this->getDecorator('Description')->setOption('escape', false);

        // Element: page_url
        $parent_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('parent_id', null);

        // Privacy
        $pageadminsetting = $coreSettings->getSetting('sitepage.manageadmin', 1);
        if (!empty($pageadminsetting)) {
            $ownerTitle = "Page Admins";
        } else {
            $ownerTitle = "Just Me";
        }

        $allowMemberInthisPackage = false;
        $allowMemberInthisPackage = Engine_Api::_()->sitepage()->allowPackageContent($this->getPackageId(), "modules", "sitepagemember");
        $allowMemberInLevel = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitepage_page', 'smecreate');

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

        // viewPrivacy
        if (!empty($createFormFields) && in_array('viewPrivacy', $createFormFields)) {
            $orderPrivacyHiddenFields = 786590;
            $view_options = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'auth_view');
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

        // n/w based privacy
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
                    'multiOptions' => $networksOptions,
                    'value' => array(0)
                ));
            }
        }

        // allPostPrivacy
        if (!empty($createFormFields) && in_array('allPostPrivacy', $createFormFields)) {
            $this->addElement('Select', 'all_post', array(
                'label' => 'Post in Updates Tab',
                'multiOptions' => array("1" => "Everyone", "0" => "Page Admins"),
                'description' => 'Who is allowed to post in this page?',
                'attribs' => array('class' => 'sp_quick_advanced')
            ));
            $this->all_post->getDecorator('Description')->setOption('placement', 'append');
        }

        // commentPrivacy
        if (!empty($createFormFields) && in_array('commentPrivacy', $createFormFields)) {
            // Comment
            $comment_options = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'auth_comment');
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

        // discussionPrivacy
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

                $options = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'auth_sdicreate');
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

        // photoPrivacy
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

                $options = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'auth_spcreate');
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

        // documentPrivacy
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

                $options = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'auth_sdcreate');
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

        // videoPrivacy
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

                $options = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'auth_svcreate');
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

        // pollPrivacy
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

                $options = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'auth_splcreate');
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

        // notePrivacy
        if (!empty($createFormFields) && in_array('notePrivacy', $createFormFields)) {
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

                $options = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'auth_sncreate');
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

        // eventPrivacy
        if (!empty($createFormFields) && in_array('eventPrivacy', $createFormFields)) {
            if ((Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))) || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent')) {
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

                $options = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'auth_secreate');
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

        // musicPrivacy
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

                $options = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'auth_smcreate');
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

        // projectPrivacy
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

                $options = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'auth_sprcreate');
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

        // sitemusicPrivacy
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

                $options = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'auth_samcreate');
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

        // subPagePrivacy
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

                $subpagecreate_options = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'auth_sspcreate');
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

        // claimThisPage
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
        // notification to users @todo client want to hide in this page
//        $this->addElement('MultiCheckbox', 'notify_project_comment', array(
//            'label' => 'Send notification when someone comments',
//            'description' => ' will send email and notification to specified users only',
//            'multiOptions' => array(
//                '0' => 'Project Owner',
//                '1' => 'Project Members',
//                '2' => 'Project Followers',
//                '3' => 'Project Admins',
//                '4' => 'Organisation Owner',
//                '5' => 'Organisation Member',
//                '6' => 'Organisation Followers',
//                '7' => 'Organisation Admins',
//            ),
//            'required' => true,
//        ));

        // notification to users @todo client want to hide in this page
//        $this->addElement('MultiCheckbox', 'notify_project_donate', array(
//            'label' => 'Send notification when someone funds',
//            'description' => ' will send email and notification to specified users only',
//            'multiOptions' => array(
//                '0' => 'Project Owner',
//                '1' => 'Project Members',
//                '2' => 'Project Followers',
//                '3' => 'Project Admins',
//                '4' => 'Organisation Owner',
//                '5' => 'Organisation Member',
//                '6' => 'Organisation Followers',
//                '7' => 'Organisation Admins',
//            ),
//            'required' => true,
//        ));
//        $this->addElement('Checkbox', 'is_user_followed_after_comment_yn', array(
//            'label' => "Make users as followed when someone donated.",
//            'value' => 0,
//            'attribs' => array('class' => 'se_quick_advanced'),
//        ));
//        $this->addElement('Checkbox', 'is_user_followed_after_donate_yn', array(
//            'label' => "Make users as followed when someone comments.",
//            'value' => 0,
//            'attribs' => array('class' => 'se_quick_advanced'),
//        ));
        // Element: execute
        $this->addElement('Button', 'execute', array(
            'label' => 'Save Changes',
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
