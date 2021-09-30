<?php

class Sitevideo_Api_Siteapi_Core extends Core_Api_Abstract {

    private $_validateSearchProfileFields = false;
    private $_profileFieldsArray = array();
    private $_create = false;

    const IMAGE_WIDTH = 1600;
    const IMAGE_HEIGHT = 1600;
    const THUMB_WIDTH = 140;
    const THUMB_HEIGHT = 160;
    const THUMB_LARGE_WIDTH = 250;
    const THUMB_LARGE_HEIGHT = 250;

    public function getChannelForm($edit = 0, $item = null, $profileType = null) {
        $createForm = array();
        $user = Engine_Api::_()->user()->getViewer();
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        //profile fields
        $profileFields = $this->_getProfileTypes(array(), 'sitevideo_channel');
        if (!empty($profileFields)) {
            $this->_profileFieldsArray = $profileFields;
        }
        $createFormFields = $this->_getProfileFields(array(), 'sitevideo_channel');

        $createForm[] = array(
            'type' => 'Text',
            'name' => 'title',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Channel Title'),
            'hasValidator' => true
        );

        //ony of  create channel
        if (empty($edit)) {
            $show_url = $coreSettings->getSetting('sitevideo.channel.showurl.column', 1);
            $change_url = $coreSettings->getSetting('sitevideo.channel.change.url', 1);
            $edit_url = $coreSettings->getSetting('sitevideo.channel.edit.url', 0);
            $front = Zend_Controller_Front::getInstance();
            $link = $_SERVER['HTTP_HOST'] . $front->getBaseUrl();
            $link = 'http://' . $link . '/CHANNEL-NAME';
            if (!empty($change_url)) {
                $baseUrl = $front->getBaseUrl();
                $CHANNEL_NAME = Engine_Api::_()->getApi('Core', 'siteapi')->translate('CHANNEL-NAME');

                $link2 = $_SERVER['HTTP_HOST'] . $baseUrl . '/' . $CHANNEL_NAME;
                $limit = $coreSettings->getSetting('sitevideo.channel.likelimit.forurlblock', 0);
            }


            $createForm[] = array(
                'type' => 'Text',
                'name' => 'channel_uri',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Url'),
                'hasValidator' => true
            );
        }
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.tags.enabled', 1)) {
            $createForm[] = array(
                'type' => 'Text',
                'name' => 'tags',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Tags (Keywords)'),
                'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Separate tags with commas.')
            );
        }



        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.category.enabled', 1)) {
            // prepare categories
            $categories = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getCategories(array('fetchColumns' => array('category_id', 'category_name', 'profile_type'), 'sponsored' => 0, 'cat_depandancy' => 1, 'orderBy' => 'category_name'));
            if (count($categories) != 0) {
                $getCategories[0] = '';
                foreach ($categories as $category) {
                    $subCategories = array();
                    $subCategoriesObj = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getSubCategories(array('fetchColumns' => '*', 'category_id' => $category->category_id));
                    $getCategories[$category->category_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($category->category_name);

                    if (isset($category->profile_type) && !empty($category->profile_type))
                        $categoryProfileTypeMapping[$category->category_id] = $category->profile_type;

                    $getsubCategories = array();
                    $getsubCategories[0] = "";
                    foreach ($subCategoriesObj as $subcategory) {
                        $subsubCategoriesObj = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getSubCategories(array('fetchColumns' => '*', 'category_id' => $subcategory->category_id));
                        $getsubCategories[$subcategory->category_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($subcategory->category_name);

                        $subsubCategories = array();
                        $subsubCategories[0] = "";
                        foreach ($subsubCategoriesObj as $subsubcategory) {
                            $subsubCategories[$subsubcategory->category_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($subsubcategory->category_name);

                            if (isset($subsubcategory->profile_type) && !empty($subsubcategory->profile_type)) {
                                $categoryProfileTypeMapping[$subsubcategory->category_id] = $subsubcategory->profile_type;
                            }
                        }

                        if (isset($subsubCategories) && count($subsubCategories) > 1) {
                            $subsubCategoriesForm[$subcategory->category_id] = array(
                                'type' => 'Select',
                                'name' => 'subsubcategory_id',
                                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('3rd Level Category'),
                                'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($subsubCategories)
                            );
                        }
//$getsubCategories[$subcategory->category_id] = $subcategory->category_name;
                        if (isset($subcategory->profile_type) && !empty($subcategory->profile_type))
                            $categoryProfileTypeMapping[$subcategory->category_id] = $subcategory->profile_type;
                    }

                    if (isset($getsubCategories) && count($getsubCategories) > 1) {
                        $subcategoriesForm = array(
                            'type' => 'Select',
                            'name' => 'subcategory_id',
                            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Sub-Category'),
                            'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($getsubCategories),
                        );
                    }
                    if (isset($subcategoriesForm) && !empty($subcategoriesForm) && count($subcategoriesForm) > 0) {
                        $form[$category->category_id]['form'] = $subcategoriesForm;
                        $subcategoriesForm = array();
                    }
                    if (isset($subsubCategoriesForm) && count($subsubCategoriesForm) > 0) {
                        $form[$category->category_id]['subsubcategories'] = $subsubCategoriesForm;
                        $subsubCategoriesForm = array();
                    }
                }

                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'category_id',
                    'allowEmpty' => 0,
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Category'),
                    'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($getCategories),
                    'hasValidator' => true
                );
            }


            if (!empty($item) && $edit == 1) {
                $subCategoriesObj = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getSubCategories(array('fetchColumns' => '*', 'category_id' => $item->category_id));

                $getSubCategories[0] = "";
                foreach ($subCategoriesObj as $subcategory) {
                    $getSubCategories[$subcategory->category_id] = $subcategory->category_name;
                }

                if (isset($getSubCategories) && !empty($getSubCategories) && count($getSubCategories) > 1) {
                    $createForm[] = array(
                        'type' => 'Select',
                        'name' => 'subcategory_id',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('SubCategory'),
                        'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($getSubCategories),
                    );
                }
                $subsubCategoriesObj = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getSubCategories(array('fetchColumns' => '*', 'category_id' => $item->subcategory_id));

                $getSubSubCategories[0] = "";
                foreach ($subsubCategoriesObj as $subsubcategory) {
                    $getSubSubCategories[$subsubcategory->category_id] = $subsubcategory->category_name;
                }
                if (isset($getSubSubCategories) && !empty($getSubSubCategories) && count($getSubSubCategories) > 1) {
                    $createForm[] = array(
                        'type' => 'Select',
                        'name' => 'subsubcategory_id',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('3rd Level Category'),
                        'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($getSubSubCategories),
                    );
                }
            }

            // Set profile fields along with create form on editing
            if (isset($item) && !empty($item) && isset($profileType) && !empty($profileType) && is_array($createFormFields)) {
                if (isset($createFormFields[$profileType]) && !empty($createFormFields[$profileType])) {
                    $createForm = array_merge($createForm, $createFormFields[$profileType]);
                }
            }
        }

        // Init descriptions
        $createForm[] = array(
            'type' => 'Textarea',
            'name' => 'description',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Description'),
            'hasValidator' => true
        );
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
            $createForm[] = array(
                'type' => 'Select',
                'name' => 'auth_view',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Privacy'),
                'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($viewOptions),
                'value' => key($viewOptions),
                'hasValidator' => true
            );
        }

        // Element: auth_comment
        $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitevideo_channel', $user, 'auth_comment');
        $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));

        if (!empty($commentOptions) && count($commentOptions) >= 1) {
            // Make a hidden field
            $createForm[] = array(
                'type' => 'Select',
                'name' => 'auth_comment',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Comment Privacy'),
                'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($commentOptions),
                'value' => key($commentOptions),
                'hasValidator' => true
            );
        }

        $createForm[] = array(
            'type' => 'Checkbox',
            'name' => 'search',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Show this channel in search result'),
            'value' => 1,
        );
        if (empty($edit)) {
            $createForm[] = array(
                'type' => 'File',
                'name' => 'photo',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Main Photo'),
            );
        }

        $createForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Submit'),
        );
        // print_r($categoryProfileTypeMapping);die;
        if (isset($createForm) && !empty($createForm))
            $responseForm['form'] = $createForm;

        if (isset($form) && !empty($form))
            $responseForm['subcategories'] = $form;
        if (is_array($createFormFields) && is_array($categoryProfileTypeMapping)) {
            foreach ($categoryProfileTypeMapping as $key => $value) {
                if (isset($createFormFields[$value]) && !empty($createFormFields[$value])) {
                    $createFormFieldsForm[$key] = $createFormFields[$value];
                }
            }
            if (isset($createFormFieldsForm) && !empty($createFormFieldsForm))
                $responseForm['fields'] = $createFormFieldsForm;
        }


        return $responseForm;
    }

    public function getAddToPlaylistForm($createplaylist = 0) {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $playlistDatasCount = 0;
        if (empty($createplaylist)) {
            $playlistDatas = $this->userplaylists($viewer);
            $playlistDatasCount = Count($playlistDatas);
            $video_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('video_id', null);

            $video = Engine_Api::_()->getItem('video', $video_id);
            $playlistIdsDatas = $this->pageplaylists($video_id, $viewer_id);

            if (!empty($playlistIdsDatas)) {

                $playlistIdsDatas = $playlistIdsDatas->toArray();
                $playlistIds = array();
//                if (_CLIENT_TYPE && (_CLIENT_TYPE == 'ios') && $playlistDatasCount > 0) {
//                    $add[] = array(
//                        "type" => "Label",
//                        "name" => "add_playlist_description",
//                        "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Please select the Playlist in which you want to add this Video.')
//                    );
//                }
                foreach ($playlistIdsDatas as $playlistIdsData) {
                    $playlistIds[] = $playlistIdsData['playlist_id'];
                }
            }

            foreach ($playlistDatas as $playlistData) {

                if (in_array($playlistData->playlist_id, $playlistIds)) {
                    $add[] = array(
                        'type' => 'Checkbox',
                        'name' => 'inplaylist_' . $playlistData->playlist_id,
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($playlistData->title),
                        'value' => 1,
                    );
                } else {
                    $add[] = array(
                        'type' => 'Checkbox',
                        'name' => 'playlist_' . $playlistData->playlist_id,
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($playlistData->title),
                        'value' => 0,
                    );
                }
            }

//            if (_CLIENT_TYPE && (_CLIENT_TYPE == 'ios') && $playlistDatasCount > 0) {
//                $add[] = array(
//                    "type" => "Label",
//                    "name" => "create_play_description",
//                    "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate('You can also add this video in a new playlist below:')
//                );
//            } else {
//                $add[] = array(
//                    "type" => "Label",
//                    "name" => "create_play_description",
//                    "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate('You have not created any playlist yet. Get Started by creating and adding Videos.')
//                );
//            }
        }

        if ($playlistDatasCount) {
            $add[] = array(
                'type' => 'Text',
                'name' => 'title',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Playlist Name'),
                    // 'hasValidator' => true
            );
        } else {
            $add[] = array(
                'type' => 'Text',
                'name' => 'title',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Playlist Name'),
            );
        }

        $add[] = array(
            'type' => 'Textarea',
            'name' => 'description',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Description'),
        );

        $availableLabels = array(
            'everyone' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone'),
            'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
            'owner_network' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends and Networks'),
            'owner_member_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends of Friends'),
            'owner_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends Only'),
            'owner' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Just Me')
        );

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewOptionsReverse = array_reverse($availableLabels);
        if (count($availableLabels) > 1) {
            $add[] = array(
                'type' => 'Select',
                'name' => 'privacy',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('View Privacy'),
                'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who may see this playlist?'),
                'multiOptions' => array(
                    "public" => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Public'),
                    "private" => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Private')
                ),
            );
        }
        $add[] = array(
            'type' => 'File',
            'name' => 'photo',
            "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Main Photo')
        );

        $add[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Submit'),
        );

        $response['form'] = $add;
        if (empty($createplaylist)) {
            $response['add_playlist_description'] = 'Please select the playlist in which you want to add this Video';
            $response['create_playlist_description'] = 'You can also add this Video in a new playlist below';
        }
        return $response;
    }

    public function getVideoFrom($fetch_type = 0, $item = array(), $profileType = 0) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        // Get profile fields
        $profileFields = $this->_getProfileTypes(array(), 'video');
        if (!empty($profileFields)) {
            $this->_profileFieldsArray = $profileFields;
        }
        $createFormFields = $this->_getProfileFields(array(), 'video');
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.allow', 1)) {
            // Init channel
            $channelTable = Engine_Api::_()->getItemTable('sitevideo_channel');
            $myChannels = $channelTable->select()
                    ->from($channelTable, array('channel_id', 'title'))
                    ->where('owner_type = ?', 'user')
                    ->where('owner_id = ?', $viewer_id)
                    ->query()
                    ->fetchAll();
            $channelOptions = array('0' => '');
            foreach ($myChannels as $myChannel) {
                $channelOptions[$myChannel['channel_id']] = $myChannel['title'];
            }
            if (empty($fetch_type)) {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'main_channel_id',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Choose Channel'),
                    'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($channelOptions),
                );
            }
        }



        $createForm[] = array(
            'type' => 'Text',
            'name' => 'title',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Video Title'),
            'hasValidator' => true
        );

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.tags.enabled', 1)) {
            $createForm[] = array(
                'type' => 'Text',
                'name' => 'tags',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Tags (Keywords)'),
            );
        }
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.category.enabled', 1)) {
            $categories = Engine_Api::_()->getDbtable('videoCategories', 'sitevideo')->getCategories(array('fetchColumns' => array('category_id', 'category_name', 'profile_type'), 'sponsored' => 0, 'cat_depandancy' => 1, 'orderBy' => 'category_name'));
            if (count($categories) != 0) {
                $getCategories[0] = '';
                foreach ($categories as $category) {
                    $subCategories = array();
                    $subCategoriesObj = Engine_Api::_()->getDbtable('videoCategories', 'sitevideo')->getSubCategories(array('fetchColumns' => '*', 'category_id' => $category->category_id));
                    $getCategories[$category->category_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($category->category_name);

                    if (isset($category->profile_type) && !empty($category->profile_type))
                        $categoryProfileTypeMapping[$category->category_id] = $category->profile_type;

                    $getsubCategories = array();
                    $getsubCategories[0] = "";
                    foreach ($subCategoriesObj as $subcategory) {
                        $subsubCategoriesObj = Engine_Api::_()->getDbtable('videoCategories', 'sitevideo')->getSubCategories(array('fetchColumns' => '*', 'category_id' => $subcategory->category_id));
                        $getsubCategories[$subcategory->category_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($subcategory->category_name);

                        $subsubCategories = array();
                        $subsubCategories[0] = "";
                        foreach ($subsubCategoriesObj as $subsubcategory) {
                            $subsubCategories[$subsubcategory->category_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($subsubcategory->category_name);

                            if (isset($subsubcategory->profile_type) && !empty($subsubcategory->profile_type)) {
                                $categoryProfileTypeMapping[$subsubcategory->category_id] = $subsubcategory->profile_type;
                            }
                        }

                        if (isset($subsubCategories) && count($subsubCategories) > 1) {
                            $subsubCategoriesForm[$subcategory->category_id] = array(
                                'type' => 'Select',
                                'name' => 'subsubcategory_id',
                                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('3rd Level Category'),
                                'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($subsubCategories)
                            );
                        }
                        if (isset($subcategory->profile_type) && !empty($subcategory->profile_type))
                            $categoryProfileTypeMapping[$subcategory->category_id] = $subcategory->profile_type;
                    }

                    if (isset($getsubCategories) && count($getsubCategories) > 1) {
                        $subcategoriesForm = array(
                            'type' => 'Select',
                            'name' => 'subcategory_id',
                            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Sub-Category'),
                            'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($getsubCategories),
                        );
                    }
                    if (isset($subcategoriesForm) && !empty($subcategoriesForm) && count($subcategoriesForm) > 0) {
                        $form[$category->category_id]['form'] = $subcategoriesForm;
                        $subcategoriesForm = array();
                    }
                    if (isset($subsubCategoriesForm) && count($subsubCategoriesForm) > 0) {
                        $form[$category->category_id]['subsubcategories'] = $subsubCategoriesForm;
                        $subsubCategoriesForm = array();
                    }
                }

                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'category_id',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Category'),
                    'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($getCategories),
                );
            }


            if (!empty($item) && $fetch_type == 1) {
                $subCategoriesObj = $this->getSubCategories($item->category_id);
                $getSubCategories[0] = "";
                foreach ($subCategoriesObj as $subcategory) {
                    $getSubCategories[$subcategory->category_id] = $subcategory->category_name;
                }

                if (isset($getSubCategories) && !empty($getSubCategories) && count($getSubCategories) > 1) {
                    $createForm[] = array(
                        'type' => 'Select',
                        'name' => 'subcategory_id',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('SubCategory'),
                        'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($getSubCategories),
                    );
                }
                $subsubCategoriesObj = $this->getSubCategories($item->subcategory_id);

                $getSubSubCategories[0] = "";
                foreach ($subsubCategoriesObj as $subsubcategory) {
                    $getSubSubCategories[$subsubcategory->category_id] = $subsubcategory->category_name;
                }
                if (isset($getSubSubCategories) && !empty($getSubSubCategories) && count($getSubSubCategories) > 1) {
                    $createForm[] = array(
                        'type' => 'Select',
                        'name' => 'subsubcategory_id',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('3rd Level Category'),
                        'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($getSubSubCategories),
                    );
                }
            }

            // Set profile fields along with create form on editing
            if (isset($item) && !empty($item) && isset($profileType) && !empty($profileType) && is_array($createFormFields)) {
                if (isset($createFormFields[$profileType]) && !empty($createFormFields[$profileType])) {
                    $createForm = array_merge($createForm, $createFormFields[$profileType]);
                }
            }
        }

        $createForm[] = array(
            'type' => 'Textarea',
            'name' => 'description',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Video Description'),
            'hasValidator' => true
        );

        $createForm[] = array(
            'type' => 'Text',
            'name' => 'location',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Location'),
        );

        if (Engine_Api::_()->sitevideo()->videoBaseNetworkEnable()) {
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
                $viewPricavyEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.networkprofile.privacy', 0);
                if ($viewPricavyEnable) {
                    $desc = 'Select the networks, members of which should be able to see your video. (Press Ctrl and click to select multiple networks. Applied privacy will be a combination of the privacy chosen above in "View Privacy" and the privacy chosen here.)';
                } else {
                    $desc = 'Select the networks, members of which should be able to see your Video in browse and search video. (Press Ctrl and click to select multiple networks. Applied privacy will be a combination of the privacy chosen above in "View Privacy" and the privacy chosen here.)';
                }

                if (count($networksOptions) > 1) {
                    $createForm[] = array(
                        'type' => 'Select',
                        'name' => 'networks_privacy',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Privacy'),
                        'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($networksOptions),
                        'value' => array(0),
                    );
                }
            }
        }

        $availableLabels = array(
            'everyone' => 'Everyone',
            'registered' => 'All Registered Members',
            'owner_network' => 'Friends and Networks',
            'owner_member_member' => 'Friends of Friends',
            'owner_member' => 'Friends Only',
            'owner' => 'Just Me'
        );


// Element: auth_view
        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $viewer, 'auth_view');
        $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
        if (!empty($viewOptions) && count($viewOptions) > 1) {
            $createForm[] = array(
                'type' => 'Select',
                'name' => 'auth_view',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Privacy'),
                'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($viewOptions),
                'value' => key($viewOptions),
                'hasValidator' => true
            );
        }

// Element: auth_comment
        $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $viewer, 'auth_comment');
        $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));

        if (!empty($commentOptions) && count($commentOptions) >= 1) {

            if ($commentOptions > 1) {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'auth_comment',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Comment Privacy'),
                    'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($commentOptions),
                    'value' => key($commentOptions),
                    'hasValidator' => true
                );
            }
        }

        $createForm[] = array(
            'type' => 'Checkbox',
            'name' => 'search',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Show this video in search result'),
            'value' => 1,
        );


        if ($fetch_type == 1) {
            $createForm[] = array(
                'type' => 'Submit',
                'name' => 'submit',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Submit'),
            );
            if (isset($createForm) && !empty($createForm))
                $responseForm['form'] = $createForm;

            if (isset($form) && !empty($form))
                $responseForm['subcategories'] = $form;
            if (is_array($createFormFields) && is_array($categoryProfileTypeMapping)) {
                foreach ($categoryProfileTypeMapping as $key => $value) {
                    if (isset($createFormFields[$value]) && !empty($createFormFields[$value])) {
                        $createFormFieldsForm[$key] = $createFormFields[$value];
                    }
                }
                if (isset($createFormFieldsForm) && !empty($createFormFieldsForm))
                    $responseForm['fields'] = $createFormFieldsForm;
            }
            return $responseForm;
        }


        $createForm[] = array(
            'type' => 'Select',
            'name' => 'rotation',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Video Rotation'),
            'multiOptions' => array(0 => '',
                90 => '90°',
                180 => '180°',
                270 => '270°'),
        );

        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $allowPasswordProtected = Engine_Api::_()->authorization()->getPermission($level_id, 'video', 'video_password_protected');
        if ($allowPasswordProtected) {
            $createForm[] = array(
                'type' => 'Password',
                'name' => 'password',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Password'),
                'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Enter password to protect your video.'),
            );
        }

        //Allowed Video Types
        $allowedSources = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.allowed.video', array(1, 2, 3, 4, 5, 6));
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $allowedSources_level = $permissionsTable->getAllowed('video', Engine_Api::_()->user()->getViewer()->level_id, 'source');
        $allowedSources_level = array_flip($allowedSources_level);
        $allowedSources = array_flip($allowedSources);
        $video_options = Array();
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $key = $coreSettings->getSetting('sitevideo.youtube.apikey', $coreSettings->getSetting('video.youtube.apikey'));

        if (isset($allowedSources[1]) && $key && isset($allowedSources_level[1])) {
            $video_options[1] = 'YouTube';
        }
        if (isset($allowedSources[2]) && isset($allowedSources_level[2]))
            $video_options[2] = 'Vimeo';
        if (isset($allowedSources[3]) && isset($allowedSources_level[3]))
            $video_options[4] = 'Dailymotion';

//My Computer
        $allowed_upload = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $viewer, 'create');
        $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->sitevideo_ffmpeg_path;
        if (isset($allowedSources[4]) && !empty($ffmpeg_path) && $allowed_upload && isset($allowedSources_level[4])) {
            if (Engine_Api::_()->hasModuleBootstrap('mobi') && Engine_Api::_()->mobi()->isMobile()) {
                $video_options[3] = 'My Device';
            } else {
                $video_options[3] = 'My Device';
            }
        }
        // if (isset($allowedSources[5]) && isset($allowedSources_level[5])) {
        //$video_options[5] = 'Embed Code';
        //}
        if (isset($allowedSources[6]) && isset($allowedSources_level[6])) {
//          $video_options[6] = 'External Sites';
        }

        $createForm[] = array(
            'type' => 'Select',
            'name' => 'type',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Choose Video Source'),
            'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($video_options),
            "hasValidator" => true
        );
        //Allowed type work end here

        $createForm[] = array(
            'type' => 'Text',
            'name' => 'url',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Video Link (URL)'),
            'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Paste the web address of the video here.'),
        );

        $createForm[] = array(
            'type' => 'File',
            'name' => 'filedata',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Add Video'),
        );

        $createForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Submit'),
        );

        if (isset($createForm) && !empty($createForm))
            $responseForm['form'] = $createForm;

        if (isset($form) && !empty($form))
            $responseForm['subcategories'] = $form;
        if (is_array($createFormFields) && is_array($categoryProfileTypeMapping)) {
            foreach ($categoryProfileTypeMapping as $key => $value) {
                if (isset($createFormFields[$value]) && !empty($createFormFields[$value])) {
                    $createFormFieldsForm[$key] = $createFormFields[$value];
                }
            }
            if (isset($createFormFieldsForm) && !empty($createFormFieldsForm))
                $responseForm['fields'] = $createFormFieldsForm;
        }

        return $responseForm;
    }

    public function getCategories($category_ids = null, $count_only = 0, $sponsored = 0, $cat_depandancy = 0, $limit = 0, $orderBy = 'cat_order', $visibility = 0, $fetchColumns = array()) {
        $table = Engine_Api::_()->getDbtable('categories', 'video');
        $select = $table->select();

//GET CATEGORY TABLE NAME
        $categoryTableName = $table->info('name');

        if ($orderBy == 'category_name') {
            $select->order('category_name');
        } else {
            $select->order('cat_order');
        }

        if (!empty($cat_depandancy)) {
            $select->where('cat_dependency = ?', 0);
        }

        if (!empty($count_only)) {
            $select->from($this->info('name'), 'category_id');
        } elseif (!empty($fetchColumns)) {
            $select->from($categoryTableName, $fetchColumns);
        }

        if (!empty($sponsored)) {
            $select->where('sponsored = ?', 1);
        }

        if (!empty($category_ids)) {
            foreach ($category_ids as $ids) {
                $categoryIdsArray[] = "category_id = $ids";
            }
            $select->where("(" . join(") or (", $categoryIdsArray) . ")");
        }

        if (!empty($count_only)) {
            return $select->query()->fetchColumn();
        }

        if (!empty($limit)) {
            $select->limit($limit);
        }
//RETURN DATA
        return $table->fetchAll($select);
    }

    public function getCategory($category_id) {
        return Engine_Api::_()->getDbtable('categories', 'video')->find($category_id)->current();
    }

    public function getChannelCategory($category_id) {
        return Engine_Api::_()->getItem('sitevideo_channel_category', $category_id);
    }

    public function getVideoURL($video, $autoplay = true) {
// YouTube
        if ($video->type == 1 || $video->type == 'youtube') {
            return 'www.youtube.com/embed/' . $video->code . '?wmode=opaque' . ($autoplay ? "&autoplay=1" : "");
        } elseif ($video->type == 2 || $video->type == 'vimeo') { // Vimeo
            return 'player.vimeo.com/video/' . $video->code . '?title=0&amp;byline=0&amp;portrait=0&amp;wmode=opaque' . ($autoplay ? "&amp;autoplay=1" : "");
        } elseif ($video->type == 4 || $video->type == 'dailymotion') {
            return 'www.dailymotion.com/embed/video/' . $video->code . '?wmode=opaque' . ($autoplay ? "&amp;autoplay=1" : "");
        } elseif ($video->type == 3 || $video->type == 'upload' || $video->type == 'mydevice') { // Uploded Videos
            $staticBaseUrl = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.static.baseurl', null);

            $getHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
            $getDefaultStorageId = Engine_Api::_()->getDbtable('services', 'storage')->getDefaultServiceIdentity();
            $getDefaultStorageType = Engine_Api::_()->getDbtable('services', 'storage')->getService($getDefaultStorageId)->getType();

            $host = '';
            if ($getDefaultStorageType == 'local')
                $host = !empty($staticBaseUrl) ? $staticBaseUrl : $getHost;

            $video_location = Engine_Api::_()->storage()->get($video->file_id, $video->getType());
            if (!empty($video_location)) {
                $video_location = $video_location->getHref();
            } else
                return '';


            $video_location = strstr($video_location, 'http') ? $video_location : $host . $video_location;

            return $video_location;
        }
        elseif ($video->type == 5 || $video->type == 6 || $video->type == 'embedcode' || $video->type == 'iframely') {

            if (isset($video->code) && !empty($video->code))
                return $video->code;
            else
                return '';
        }
        elseif ( $video->type == 'stream') {

           $storage_file = Engine_Api::_()->storage()->get($video->file_id, $video->getType());
          if( $storage_file ) {
            return $storage_file->getHref();
          }
        }
    }

    public function deleteVideo($video) {


// delete video ratings
        Engine_Api::_()->getDbtable('ratings', 'sitevideo')->delete(array(
            'resource_id = ?' => $video->video_id,
        ));

// check to make sure the video did not fail, if it did we wont have files to remove
        if ($video->status == 1) {
// delete storage files (video file and thumb)
            if ($video->type == 3)
                Engine_Api::_()->getItem('storage_file', $video->file_id)->remove();
            if ($video->photo_id)
                Engine_Api::_()->getItem('storage_file', $video->photo_id)->remove();
        }

// delete activity feed and its comments/likes
        $item = Engine_Api::_()->getItem('video', $video->video_id);
        if ($item) {
            $item->delete();
        }
    }

    public function getVideosCount($id, $column_name) {

        $table = Engine_Api::_()->getDbTable('videos', 'sitevideo');
        $select = $table->select()
                ->from($table->info('name'), array('COUNT(*) AS count'));

        if (!empty($column_name) && !empty($id)) {
            $select->where("$column_name = ?", $id);
        }

        $totalVideos = $select->query()->fetchColumn();

//RETURN EVENTS COUNT
        return $totalVideos;
    }

    public function getCategorieshasVideo($category_id = null, $fieldname, $limit = null, $params = array(), $fetchColumns = array()) {
        $tableCategories = Engine_Api::_()->getDbTable('categories', 'video');
        $tableCategoriesName = $tableCategories->info('name');
//GET Video TABLE
        $tableVideos = Engine_Api::_()->getDbTable('videos', 'video');
        $tableVideoName = $tableVideos->info('name');

//MAKE QUERY
        $select = $tableCategories->select()->setIntegrityCheck(false);

        if (!empty($fetchColumns)) {
            $select->from($tableCategoriesName, $fetchColumns);
        } else {
            $select->from($tableCategoriesName);
        }

        $select = $select->join($tableVideoName, $tableVideoName . '.' . $fieldname . '=' . $tableCategoriesName . '.category_id', null);


        if (!empty($order)) {
            $select->order("$order");
        }

        $select = $select->where($tableCategoriesName . '.cat_dependency = ' . $category_id)
                ->group($tableCategoriesName . '.category_id')
                ->order('cat_order');

        if (!empty($limit)) {
            $select = $select->limit($limit);
        }


//RETURN DATA
        return $tableCategories->fetchAll($select);
    }

    public function getSubCategories($category_id, $fetchColumns = array()) {

        $tableCategories = Engine_Api::_()->getDbTable('videoCategories', 'sitevideo');
//RETURN IF CATEGORY ID IS EMPTY
        if (empty($category_id)) {
            return;
        }

//MAKE QUERY
        $select = $tableCategories->select();

        if (!empty($fetchColumns)) {
            $select->from($tableCategories->info('name'), $fetchColumns);
        }

        $select->where('cat_dependency = ?', $category_id)
                ->order('cat_order');

//RETURN RESULTS

        return $tableCategories->fetchAll($select);
    }

    public function userPlaylists($owner, $playlist = 0) {
//GET VIEWER DETAIL

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $playlistTable = Engine_Api::_()->getDbtable('playlists', 'sitevideo');
//GET playlistLIST TABLE
        $playlistTableName = $playlistTable->info('name');

//MAKE QUERY
        $select = $playlistTable->select()->setIntegrityCheck(false);

        if (!empty($playlist)) {
            $select->from($playlistTableName, array('playlist_id'));
        } else {
            $select->from($playlistTableName);
        }

        $select->where($playlistTableName . '.owner_id = ?', $owner->getIdentity())
                ->group($playlistTableName . '.playlist_id')
                ->order('playlist_id DESC');


//LOGGED IN USER
        if (!empty($viewer_id) && $viewer_id != $owner->getIdentity()) {

//GET AUTHORIZATION TABLE
            $authorizationTable = Engine_Api::_()->getDbtable('allow', 'authorization');
            $authorizationTableName = $authorizationTable->info('name');

            $authorizationAllow = array('everyone');
            $authorizationAllow[] = 'registered';

//SAME AS OWNER NETWORK
            $owner_network = $authorizationTable->is_network($owner, $viewer);
            if (!empty($owner_network)) {
                $authorizationAllow[] = 'owner_network';
            }

//OWNERS FRIEND
            $owner_member = $owner->membership()->isMember($viewer, true);
            if (!empty($owner_member)) {
                $authorizationAllow[] = 'owner_member';
            }

//OWNERS FRIEND AND FRIREND OF OWNERS FRIEND
            $owner_member_member = $authorizationTable->is_owner_member_member($owner, $viewer);
            if (!empty($owner_member_member)) {
                $authorizationAllow[] = 'owner_member_member';
            }

            $select->join($authorizationTableName, "$authorizationTableName.resource_id = $playlistTableName.playlist_id", array())
                    ->where("$authorizationTableName.resource_type = ?", 'sitevideo_playlist')
                    ->where("$authorizationTableName.role IN (?)", (array) $authorizationAllow);
        }

        if (!empty($playlist)) {
            return $select->query()
                            ->fetchColumn();
        } else {
            if (!empty($total_item)) {
                $select = $select->limit($total_item);
            }

//RETURN RESULTS
            return $playlistTable->fetchAll($select);
        }
    }

    public function pageplaylists($video_id, $owner_id = 0) {
        if (empty($video_id)) {
            return;
        }
        $mapTable = Engine_Api::_()->getDbtable('playlistmaps', 'sitevideo');
        $playlistTable = Engine_Api::_()->getDbTable('playlists', 'sitevideo');
        $playlistTableName = $playlistTable->info('name');
        $playlistMapTableName = $mapTable->info('name');

//MAKE QUERY
        $select = $playlistTable->select()
                ->setIntegrityCheck(false)
                ->from($playlistTableName)
                ->join($playlistMapTableName, "$playlistMapTableName.playlist_id = $playlistTableName.playlist_id")
                ->where($playlistTableName . '.owner_id = ?', $owner_id)
                ->where($playlistMapTableName . '.video_id = ?', $video_id);

//RETURN RESULTS

        return $playlistTable->fetchAll($select);
    }

    public function setPhoto($photo, $values, $setRow = true,$isFilterImage = false) {

        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
        } else {
            throw new Banner_Model_Exception('invalid argument passed to setPhoto');
        }
        $imageName = $photo['name'];
        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

        if($isFilterImage)
        {
            $params = array(
            'parent_type' => 'story_video',
            'parent_id' => $values->getIdentity(),
            );
        }
        else
        $params = array(
            'parent_type' => $values->getType(),
            'parent_id' => $values->getIdentity(),
        );


// Save
        $storage = Engine_Api::_()->storage();

// Resize image (main)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(720, 750)
                ->write($path . '/m_' . $imageName)
                ->destroy();

// Resize image (icon)
        $image = Engine_Image::factory();
        $image->open($file);

        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
                ->write($path . '/is_' . $imageName)
                ->destroy();


// Store
        $iMain = $storage->create($path . '/m_' . $imageName, $params);
        $iSquare = $storage->create($path . '/is_' . $imageName, $params);

        $iMain->bridge($iSquare, 'thumb.icon');

// Remove temp files

        @unlink($path . '/m_' . $imageName);
        @unlink($path . '/is_' . $imageName);


        if($isFilterImage){
            return $iMain->getIdentity();
        }
// Update row
        if (!empty($setRow)) {
            $values->file_id = $iMain->getIdentity();
            $values->save();
        } else {
            $values->photo_id = $iMain->getIdentity();
            $values->save();
        }

//return $photoItem;
    }

    public function getSearchForm() {

        $searchFormSettings = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getModuleOptions('sitevideo_video');
        $searchForm = array();

// Get profile fields array
        $profileFields = $this->_getProfileTypes(array(), 'video');

        if (!empty($profileFields)) {
            $this->_profileFieldsArray = $profileFields;
        }

        $createFormFields = $this->_getProfileFields(array(), 'video');

        $settings = Engine_Api::_()->getApi('settings', 'core');
        if (!empty($searchFormSettings['search']) && !empty($searchFormSettings['search']['display'])) {
            $searchForm[] = array(
                'type' => 'Text',
                'name' => 'search',
                "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Name / Keyword')
            );
        }
        if (!empty($searchFormSettings['view']) && !empty($searchFormSettings['view']['display'])) {
            $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            if (!empty($viewer_id)) {
                $show_multiOptions = array();
                $show_multiOptions["0"] = 'Everyone\'s Videos';
                $show_multiOptions["1"] = 'Only My Friends\' Videos';
                $value_deault = 0;
                $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.network', 0);
                if (empty($enableNetwork)) {
                    $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
                    $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer_id));
                    if (!empty($viewerNetwork) || Engine_Api::_()->sitevideo()->videoBaseNetworkEnable()) {
                        $show_multiOptions["3"] = 'Only My Networks';
                        $browseDefaulNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.default.show', 0);
                    }
                }
                $searchForm[] = array(
                    'type' => 'Select',
                    'name' => 'view_view',
                    "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate('View'),
                    'multiOptions' => $show_multiOptions,
                    'value' => $value_deault
                );
            }
        }

        if (!empty($searchFormSettings['orderby']) && !empty($searchFormSettings['orderby']['display'])) {
            $multiOPtionsOrderBy = array(
                '' => '',
                'creation_date' => 'Most Recent',
                'modified_date' => 'Recently Updated',
                'view_count' => 'Most View',
                'like_count' => 'Most Liked',
                'comment_count' => 'Most Commented',
                'rating' => 'Most Rated',
                'favourite_count' => 'Most Favourite',
                'featured' => 'Featured',
                'best_video' => 'Best Video',
                'best_channel' => 'Best Channel',
                'sponsored' => 'Sponsored',
                'title' => "Alphabetical (A-Z)",
                'title_reverse' => 'Alphabetical (Z-A)'
            );
//GET API

            $enableRating = $settings->getSetting('sitevideo.rating', 1);

            if ($enableRating) {
                $multiOPtionsOrderBy = array_merge($multiOPtionsOrderBy, array('rating' => 'Most Rated'));
            }

            $searchForm[] = array(
                'type' => 'Select',
                'name' => 'orderby',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Browse By'),
                'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($multiOPtionsOrderBy)
            );
        }

        if (!empty($searchFormSettings['location']) && !empty($searchFormSettings['location']['display']) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.location', 0)) {

            $searchForm[] = array(
                'type' => 'Text',
                'name' => 'location',
                ' label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Location'),
            );

            if (!empty($searchFormSettings['proximity']) && !empty($searchFormSettings['proximity']['display'])) {
                $flage = $settings->getSetting('sitevideo.video.proximity.search.kilometer', 0);
                if ($flage) {
                    $locationLable = "Within Kilometers";
                    $locationOption = array(
                        '0' => '',
                        '1' => '1 Kilometer',
                        '2' => '2 Kilometers',
                        '5' => '5 Kilometers',
                        '10' => '10 Kilometers',
                        '20' => '20 Kilometers',
                        '50' => '50 Kilometers',
                        '100' => '100 Kilometers',
                        '250' => '250 Kilometers',
                        '500' => '500 Kilometers',
                        '750' => '750 Kilometers',
                        '1000' => '1000 Kilometers',
                    );
                } else {
                    $locationLable = "Within Miles";
                    $locationOption = array(
                        '0' => '',
                        '1' => '1 Mile',
                        '2' => '2 Miles',
                        '5' => '5 Miles',
                        '10' => '10 Miles',
                        '20' => '20 Miles',
                        '50' => '50 Miles',
                        '100' => '100 Miles',
                        '250' => '250 Miles',
                        '500' => '500 Miles',
                        '750' => '750 Miles',
                        '1000' => '1000 Miles',
                    );
                }

                $searchForm[] = array(
                    'type' => 'Select',
                    'name' => 'locationmiles',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($locationLable),
                    'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($locationOption),
                    'value' => 0,
                );
            }
            if (!empty($searchFormSettings['street']) && !empty($searchFormSettings['street']['display'])) {
                $searchForm[] = array(
                    'type' => 'Text',
                    'name' => 'video_street',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Street'),
                );
            }

            if (!empty($searchFormSettings['city']) && !empty($searchFormSettings['city']['display'])) {
                $searchForm[] = array(
                    'type' => 'Text',
                    'name' => 'video_city',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('City'),
                );
            }

            if (!empty($searchFormSettings['state']) && !empty($searchFormSettings['state']['display'])) {
                $searchForm[] = array(
                    'type' => 'Text',
                    'name' => 'video_state',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('State'),
                );
            }

            if (!empty($searchFormSettings['country']) && !empty($searchFormSettings['country']['display'])) {

                $searchForm[] = array(
                    'type' => 'Text',
                    'name' => 'video_country',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Country'),
                );
            }
        }

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.category.enabled', 1)) {
            if (!empty($searchFormSettings['category_id']) && !empty($searchFormSettings['category_id']['display'])) {
                $categories = $this->getCategories(null, 0, 0, 1, 0, 'category_name', 0, array('category_id', 'category_name', 'cat_order', 'profile_type'));
                if (count($categories) != 0) {
                    $getCategories[0] = '';
                    foreach ($categories as $category) {
                        $subCategories = array();
                        $subCategoriesObj = $this->getSubCategories($category->category_id);
                        $getCategories[$category->category_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($category->category_name);

                        if (isset($category->profile_type) && !empty($category->profile_type))
                            $categoryProfileTypeMapping[$category->category_id] = $category->profile_type;

                        $getsubCategories = array();
                        $getsubCategories[0] = "";
                        foreach ($subCategoriesObj as $subcategory) {
                            $subsubCategoriesObj = $this->getSubCategories($subcategory->category_id);
                            $getsubCategories[$subcategory->category_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($subcategory->category_name);

                            $subsubCategories = array();
                            $subsubCategories[0] = "";
                            foreach ($subsubCategoriesObj as $subsubcategory) {
                                $subsubCategories[$subsubcategory->category_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($subsubcategory->category_name);

                                if (isset($subsubcategory->profile_type) && !empty($subsubcategory->profile_type)) {
                                    $categoryProfileTypeMapping[$subsubcategory->category_id] = $subsubcategory->profile_type;
                                }
                            }

                            if (isset($subsubCategories) && count($subsubCategories) > 1) {
                                $subsubCategoriesForm[$subcategory->category_id] = array(
                                    'type' => 'Select',
                                    'name' => 'subsubcategory_id',
                                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('3rd Level Category'),
                                    'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($subsubCategories)
                                );
                            }
//$getsubCategories[$subcategory->category_id] = $subcategory->category_name;
                            if (isset($subcategory->profile_type) && !empty($subcategory->profile_type))
                                $categoryProfileTypeMapping[$subcategory->category_id] = $subcategory->profile_type;
                        }

                        if (isset($getsubCategories) && count($getsubCategories) > 1) {
                            $subcategoriesForm = array(
                                'type' => 'Select',
                                'name' => 'subcategory_id',
                                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Sub-Category'),
                                'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($getsubCategories),
                            );
                        }
                        if (isset($subcategoriesForm) && !empty($subcategoriesForm) && count($subcategoriesForm) > 0) {
                            $form[$category->category_id]['form'] = $subcategoriesForm;
                            $subcategoriesForm = array();
                        }
                        if (isset($subsubCategoriesForm) && count($subsubCategoriesForm) > 0) {
                            $form[$category->category_id]['subsubcategories'] = $subsubCategoriesForm;
                            $subsubCategoriesForm = array();
                        }
                    }

                    $searchForm[] = array(
                        'type' => 'Select',
                        'name' => 'category_id',
                        'allowEmpty' => 0,
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Category'),
                        'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($getCategories)
                    );
                }
            }
        }
        $search = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');
        $row = $search->getFieldsOptions('sitevideo_video', 'content_type');
        if (!empty($row) && !empty($row->display)) {
            $contentTypes = Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array("enabled" => 1));
            $contentTypeArray = array();
            if (!empty($contentTypes)) {
                $contentTypeArray[] = 'All';
                $moduleTitle = '';
                foreach ($contentTypes as $contentType) {
                    $contentTypeArray['user'] = Zend_Registry::get('Zend_Translate')->translate('Member Videos');
                    $contentTypeArray[$contentType['item_type']] = $contentType['item_title'];
                }
            }
            if (!empty($contentTypeArray)) {
                $searchForm[] = array(
                    'type' => 'Select',
                    'name' => 'videoType',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Video Type'),
                    'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($contentTypeArray)
                );
            }
        }

        $searchForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => 'Submit'
        );

        if (isset($searchForm) && !empty($searchForm))
            $responseForm['form'] = $searchForm;

        if (isset($form) && !empty($form))
            $responseForm['subcategories'] = $form;

        if (is_array($createFormFields) && is_array($categoryProfileTypeMapping)) {
            foreach ($categoryProfileTypeMapping as $key => $value) {
                if (isset($createFormFields[$value]) && !empty($createFormFields[$value])) {
                    $createFormFieldsForm[$key] = $createFormFields[$value];
                }
            }
            if (isset($createFormFieldsForm) && !empty($createFormFieldsForm))
                $responseForm['fields'] = $createFormFieldsForm;
        }

        return $responseForm;
    }

    private function _getProfileTypes($profileFields = array(), $table = null) {

        if (empty($table))
            return;
        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop($table);

        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
            $profileTypeField = $topStructure[0]->getChild();
            $options = $profileTypeField->getOptions();

            $options = $profileTypeField->getElementParams($table);
            if (isset($options['options']['multiOptions']) && !empty($options['options']['multiOptions']) && is_array($options['options']['multiOptions'])) {
// Make exist profile fields array.         
                foreach ($options['options']['multiOptions'] as $key => $value) {
                    if (!empty($key)) {
                        $profileFields[$key] = $value;
                    }
                }
            }
        }

        return $profileFields;
    }

    private function _getProfileFields($fieldsForm = array(), $table = null) {
        if (empty($table))
            return;

        foreach ($this->_profileFieldsArray as $option_id => $prfileFieldTitle) {

            if (!empty($option_id)) {
                $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps($table);
                $getRowsMatching = $mapData->getRowsMatching('option_id', $option_id);

                $fieldArray = array();
                $getFieldInfo = Engine_Api::_()->fields()->getFieldInfo();

                $getHeadingName = '';
                foreach ($getRowsMatching as $map) {
                    $meta = $map->getChild();
                    $type = $meta->type;

                    if (!empty($type) && ($type == 'heading')) {
                        $getHeadingName = $meta->label;
                        continue;
                    }

                    if (!empty($this->_validateSearchProfileFields) && (!isset($meta->search) || empty($meta->search)))
                        continue;


                    $fieldForm = $getMultiOptions = array();
                    $key = $map->getKey();


// Findout respective form element field array.
                    if (isset($getFieldInfo['fields'][$type]) && !empty($getFieldInfo['fields'][$type])) {
                        $getFormFieldTypeArray = $getFieldInfo['fields'][$type];

// In case of Generic profile fields.
                        if (isset($getFormFieldTypeArray['category']) && ($getFormFieldTypeArray['category'] == 'generic')) {
// If multiOption enabled then perpare the multiOption array.

                            if (($type == 'select') || ($type == 'radio') || (isset($getFormFieldTypeArray['multi']) && !empty($getFormFieldTypeArray['multi']))) {
                                $getOptions = $meta->getOptions();
                                if (!empty($getOptions)) {
                                    foreach ($getOptions as $option) {
                                        $getMultiOptions[$option->option_id] = $option->label;
                                    }
                                }
                            }

// Prepare Generic form.
                            $fieldForm['type'] = ucfirst($type);
                            $fieldForm['name'] = $key . '_field_' . $meta->field_id;
                            $fieldForm['label'] = (isset($meta->label) && !empty($meta->label)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->label) : '';
                            $fieldForm['description'] = (isset($meta->description) && !empty($meta->description)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->description) : '';

// Add multiOption, If available.
                            if (!empty($getMultiOptions)) {
                                $fieldForm['multiOptions'] = $getMultiOptions;
                            }
// Add validator, If available.
                            if (isset($meta->required) && !empty($meta->required))
                                $fieldForm['hasValidator'] = true;

                            if (COUNT($this->_profileFieldsArray) > 1) {

                                if (isset($this->_create) && !empty($this->_create) && $this->_create == 1) {
                                    $optionCategoryName = Engine_Api::_()->getDbtable('options', 'video')->getProfileTypeLabel($option_id);
                                    $fieldsForm[$option_id][] = $fieldForm;
                                } else {

                                    $fieldsForm[$option_id][] = $fieldForm;
                                }
                            } else
                                $fieldsForm[$option_id][] = $fieldForm;
                        }else if (isset($getFormFieldTypeArray['category']) && ($getFormFieldTypeArray['category'] == 'specific') && !empty($getFormFieldTypeArray['base'])) { // In case of Specific profile fields.
// Prepare Specific form.
                            $fieldForm['type'] = ucfirst($getFormFieldTypeArray['base']);
                            $fieldForm['name'] = $key . '_field_' . $meta->field_id;
                            $fieldForm['label'] = (isset($meta->label) && !empty($meta->label)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->label) : '';
                            $fieldForm['description'] = (isset($meta->description) && !empty($meta->description)) ? $meta->description : '';

// Add multiOption, If available.
                            if ($getFormFieldTypeArray['base'] == 'select') {
                                $getOptions = $meta->getOptions();
                                foreach ($getOptions as $option) {
                                    $getMultiOptions[$option->option_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($option->label);
                                }
                                $fieldForm['multiOptions'] = $getMultiOptions;
                            }

// Add validator, If available.
                            if (isset($meta->required) && !empty($meta->required))
                                $fieldForm['hasValidator'] = true;

                            if (COUNT($this->_profileFieldsArray) > 1) {
                                if (isset($this->_create) && !empty($this->_create) && $this->_create == 1) {
                                    $optionCategoryName = Engine_Api::_()->getDbtable('options', 'video')->getProfileTypeLabel($option_id);
                                    $fieldsForm[$option_id][] = $fieldForm;
                                } else {
                                    $fieldsForm[$option_id][] = $fieldForm;
                                }
                            } else
                                $fieldsForm[$option_id][] = $fieldForm;
//                                $fieldsForm[] = $fieldForm;
                        }
                    }
                }
            }
        }

        return $fieldsForm;
    }

    public function getCustomVideoField($category_id, $table = null) {
// Get profile fields array
        $profileFields = $this->_getProfileTypes(array(), $table);

        if (!empty($profileFields)) {
            $this->_profileFieldsArray = $profileFields;
        }
        $categoryProfileTypeMapping = array();
        $createFormFieldsForm = array();
        $category = $this->getCategory($category_id);
        if (isset($category->profile_type) && !empty($category->profile_type))
            $categoryProfileTypeMapping[$category->category_id] = $category->profile_type;

        $createFormFields = $this->_getProfileFields(array(), $table);

        if (is_array($createFormFields) && is_array($categoryProfileTypeMapping)) {
            foreach ($categoryProfileTypeMapping as $key => $value) {
                if (isset($createFormFields[$value]) && !empty($createFormFields[$value])) {
                    $createFormFieldsForm[$key] = $createFormFields[$value];
                }
            }
            if (isset($createFormFieldsForm) && !empty($createFormFieldsForm))
                return $createFormFieldsForm;
        }
    }

    public function getCustomChannelField($category_id, $table = null) {
// Get profile fields array

        $profileFields = $this->_getProfileTypes(array(), $table);

        if (!empty($profileFields)) {
            $this->_profileFieldsArray = $profileFields;
        }
        $categoryProfileTypeMapping = array();
        $createFormFieldsForm = array();

        $category = $this->getChannelCategory($category_id);

        if (isset($category->profile_type) && !empty($category->profile_type))
            $categoryProfileTypeMapping[$category->category_id] = $category->profile_type;

        $createFormFields = $this->_getProfileFields(array(), $table);

        if (is_array($createFormFields) && is_array($categoryProfileTypeMapping)) {
            foreach ($categoryProfileTypeMapping as $key => $value) {
                if (isset($createFormFields[$value]) && !empty($createFormFields[$value])) {
                    $createFormFieldsForm[$key] = $createFormFields[$value];
                }
            }

            if (isset($createFormFieldsForm) && !empty($createFormFieldsForm))
                return $createFormFieldsForm;
        }
    }

    public function getInformation($subject, $table) {

        $profileFields = $this->_getProfileTypes(array(), $table);
        if (!empty($profileFields)) {
            $this->_profileFieldsArray = $profileFields;
        }

        $information = $this->getProfileInfo($subject, $table);

        return $information;
    }

// Get the Profile Fields Information, which will show on profile page.
    public function getProfileInfo($subject, $table, $setKeyAsResponse = false) {
// Getting the default Profile Type id.

        $getFieldId = $this->getDefaultProfileTypeId($subject);

// Start work to get form values.
        $values = Engine_Api::_()->fields()->getFieldsValues($subject);
        $fieldValues = array();
// In case if Profile Type available. like User module.
        if (!empty($getFieldId)) {

// Set the default profile type.
            $this->_profileFieldsArray[$getFieldId] = $getFieldId;
            $_getProfileFields = $this->_getProfileFields(array(), $table);
            foreach ($_getProfileFields as $heading => $tempValue) {
                foreach ($tempValue as $value) {
                    $key = $value['name'];
                    $label = $value['label'];
                    $type = $value['type'];
                    $parts = @explode('_', $key);

                    if (count($parts) < 3)
                        continue;

                    list($parent_id, $option_id, $field_id) = $parts;

                    $valueRows = $values->getRowsMatching(array(
                        'field_id' => $field_id,
                        'item_id' => $subject->getIdentity()
                    ));

                    if (!empty($valueRows)) {
                        foreach ($valueRows as $fieldRow) {

                            $tempValue = $fieldRow->value;

// In case of Select or Multi send the respective label.
                            if (isset($value['multiOptions']) && !empty($value['multiOptions']) && isset($value['multiOptions'][$fieldRow->value]))
                                $tempValue = $value['multiOptions'][$fieldRow->value];
                            $tempKey = !empty($setKeyAsResponse) ? $key : $label;

                            if (isset($tempValue) && !empty($tempValue))
                                $fieldValues[$tempKey] = $tempValue;
                        }
                    }
                }
            }
        } else { // In case, If there are no Profile Type available and only Profile Fields are available. like Classified.
            $getType = $subject->getType();
            $_getProfileFields = $this->_getProfileFields(array(), $table);

            foreach ($_getProfileFields as $heading => $tempValue) {
                foreach ($tempValue as $value) {

                    $key = $value['name'];
                    $label = $value['label'];

                    $parts = @explode('_', $key);

                    if (count($parts) < 3)
                        continue;

                    list($parent_id, $option_id, $field_id) = $parts;

                    $valueRows = $values->getRowsMatching(array(
                        'field_id' => $field_id,
                        'item_id' => $subject->getIdentity()
                    ));

                    if (!empty($valueRows)) {
                        foreach ($valueRows as $fieldRow) {
                            if (!empty($fieldRow->value)) {
                                $tempKey = !empty($setKeyAsResponse) ? $key : $label;
                                if (isset($fieldRow->value) && !empty($fieldRow->value))
                                    $fieldValues[$tempKey] = $fieldRow->value;
                            }
                        }
                    }
                }
            }
        }

        return $fieldValues;
    }

    public function getDefaultProfileTypeId($subject) {
        $getFieldId = null;
        $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($subject);
        if (!empty($fieldsByAlias['profile_type'])) {
            $optionId = $fieldsByAlias['profile_type']->getValue($subject);
            $getFieldId = $optionId->value;
        }

        if (empty($getFieldId)) {
            return;
        }
    }

    public function watchLater($video_id, $owner_id) {

        if (empty($video_id) || empty($owner_id))
            return;
        $table = Engine_Api::_()->getDbTable('watchlaters', 'sitevideo');
        $select = $table->select()
                ->from($table->info('name'))
                ->where('owner_id = ?', $owner_id)
                ->where('video_id= ?', $video_id);
        $row = count($table->fetchAll($select));
        if ($row == 0)
            return false;
        else
            return true;
    }

    public function subscribed($params) {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $channel_id = $params['id'];
        if (empty($channel_id))
            return;
        $subscribeTable = Engine_Api::_()->getDbTable('subscriptions', 'sitevideo');
        $channel = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);

        if (isset($params['value']) && empty($params['value'])) {

            $subscribeTable->delete(array('channel_id = ?' => $channel_id, 'owner_id = ?' => $viewer_id));
            $channel->subscribe_count--;
            $channel->save();
        } elseif (isset($params['value']) && !empty($params['value'])) {

            $sName = $subscribeTable->info('name');
            $select = $subscribeTable->select()
                    ->where('channel_id = ?', $channel_id)
                    ->where('owner_id = ?', $viewer_id)
                    ->limit(1);

            $row = $subscribeTable->fetchAll($select);
            $totalVideo = count($row);
            if (empty($totalVideo)) {

                $db = Engine_Db_Table::getDefaultAdapter();
                $db->beginTransaction();
                try {
                    $subscribe = $subscribeTable->createRow();
                    $subscribe->channel_id = $channel_id;
                    $subscribe->owner_id = $viewer_id;
                    $subscribe->owner_type = $viewer->getType();
                    $subscribe->creation_date = new Zend_Db_Expr('NOW()');
                    $notificationArr['email'] = empty($params['email']) ? 0 : 1;
                    $notificationArr['notification'] = empty($params['notification']) ? 0 : 1;

                    if (empty($notificationArr['email']))
                        $params['action_email'] = null;

                    if (empty($notificationArr['notification']))
                        $params['action_notification'] = null;

                    $notificationArr['action_notification'] = empty($params['action_notification']) ? array() : $params['action_notification'];
                    $notificationArr['action_email'] = empty($params['action_email']) ? array() : $params['action_email'];
                    $notification = Zend_Json_Encoder::encode($notificationArr);
                    $subscribe->notification = $notification;
                    $subscribe->save();

                    $channel->subscribe_count++;
                    $channel->save();


//$ownerObj = $channel->getOwner();
//$notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
// $notificationTable->addNotification($ownerObj, $viewer, $channel, "sitevideo_channel_subscribe", array("label" => "channel", 'user_name' => $viewer->getTitle(), 'channel_title' => $channel->getTitle()));

                    $db->commit();
                } catch (Exception $ex) {
                    $db->rollback();
                }
            }
        }
    }

    public function isSubscribedUser($channel_id, $user_id) {
        $subscribeTable = Engine_Api::_()->getDbTable('subscriptions', 'sitevideo');

        $sName = $subscribeTable->info('name');
        $select = $subscribeTable->select()
                ->from($sName)
                ->where('channel_id = ?', $channel_id)
                ->where('owner_id = ?', $user_id)
                ->limit(1);
        return $subscribeTable->fetchAll($select);
    }

    public function countSubscriber($channel_id) {
        $subscribeTable = Engine_Api::_()->getDbTable('subscriptions', 'sitevideo');
        $sName = $subscribeTable->info('name');
        $select = $subscribeTable->select()
                ->from($sName, array('COUNT(*) AS count'))
                ->where('channel_id=?', $channel_id);


        return $select->query()->fetchColumn();
    }

    public function countPhoto($channel_id) {
        $photoTable = Engine_Api::_()->getDbtable('photos', 'sitevideo');
        $sName = $photoTable->info('name');
        $select = $photoTable->select()
                ->from($sName, array('COUNT(*) AS count'))
                ->where('channel_id=?', $channel_id);


        return $select->query()->fetchColumn();
    }

    public function createPhoto($params, $file) {

        if ($file instanceof Storage_Model_File) {
            $params['file_id'] = $file->getIdentity();
        } else {

//GET IMAGE INFO AND RESIZE
            $name = basename($file['tmp_name']);
            $path = dirname($file['tmp_name']);
            $extension = ltrim(strrchr($file['name'], '.'), '.');

            $mainName = $path . '/m_' . $name . '.' . $extension;
            $thumbName = $path . '/t_' . $name . '.' . $extension;
            $thumbLargeName = $path . '/t_l_' . $name . '.' . $extension;

            $image = Engine_Image::factory();
            $image->open($file['tmp_name'])
                    ->resize(self::IMAGE_WIDTH, self::IMAGE_HEIGHT)
                    ->write($mainName)
                    ->destroy();

            $image = Engine_Image::factory();
            $image->open($file['tmp_name'])
                    ->resize(self::THUMB_WIDTH, self::THUMB_HEIGHT)
                    ->write($thumbName)
                    ->destroy();
            $image = Engine_Image::factory();
            $image->open($file['tmp_name'])
                    ->resize(self::THUMB_LARGE_WIDTH, self::THUMB_LARGE_HEIGHT)
                    ->write($thumbLargeName)
                    ->destroy();

//RESIZE IMAGE (ICON)
            $iSquarePath = $path . '/is_' . $name . '.' . $extension;
            $image = Engine_Image::factory();
            $image->open($file['tmp_name']);

            $size = min($image->height, $image->width);
            $x = ($image->width - $size) / 2;
            $y = ($image->height - $size) / 2;

            $image->resample($x, $y, $size, $size, 48, 48)
                    ->write($iSquarePath)
                    ->destroy();

//STORE PHOTO
            $photo_params = array(
                'parent_id' => $params['channel_id'],
                'parent_type' => 'sitevideo_channel',
            );

            $photoFile = Engine_Api::_()->storage()->create($mainName, $photo_params);
            $thumbFile = Engine_Api::_()->storage()->create($thumbName, $photo_params);
            $photoFile->bridge($thumbFile, 'thumb.normal');

            $thumbLargeFile = Engine_Api::_()->storage()->create($thumbLargeName, $photo_params);
            $photoFile->bridge($thumbLargeFile, 'thumb.large');

            $iSquare = Engine_Api::_()->storage()->create($iSquarePath, $photo_params);
            $photoFile->bridge($iSquare, 'thumb.icon');
            $params['file_id'] = $photoFile->file_id;
// $params['photo_id'] = $photoFile->file_id;
//REMOVE TEMP FILES
            @unlink($mainName);
            @unlink($thumbName);
            @unlink($thumbLargeName);
            @unlink($iSquarePath);
        }

        $row = Engine_Api::_()->getDbtable('photos', 'sitevideo')->createRow();
        $row->setFromArray($params);
        $row->save();

        return $row;
    }

    public function getAllPlaylistVideo($playlist_id) {

        if (empty($playlist_id))
            return;
        $playlistTable = Engine_Api::_()->getDbTable('playlists', 'sitevideo');
        $playlistTableName = $playlistTable->info('name');
        $playlistMapsTable = Engine_Api::_()->getDbtable('playlistmaps', 'sitevideo');
        $playlistMapsTableName = $playlistMapsTable->info('name');
        $videosTable = Engine_Api::_()->getDbtable('videos', 'sitevideo');
        $videosTableName = $videosTable->info('name');

        $select = $videosTable->select()->setIntegrityCheck(false)->from($videosTableName, '*');

        $select->join($playlistMapsTableName, "$videosTableName.video_id=$playlistMapsTableName.video_id")
                ->where("$playlistMapsTableName.playlist_id=$playlist_id");



        return $videosTable->fetchAll($select);
    }

//second release
    public function getChannels($colunms = array()) {
        $channelTable = Engine_Api::_()->getDbtable('channels', 'sitevideo');
        $channelTableName = $channelTable->info('name');

        $select = $channelTable->select();
        if (!empty($colunms))
            $select->from($channelTableName, $colunms);
        else {
            $select->from($channelTableName);
        }
        return $select->query()->fetchColumn();
    }

    /**
     * Set the profile fields value to newly created listing.
     * 
     * @return array
     */
    public function setProfileFields($subject, $data) {
// Iterate over values
        $values = Engine_Api::_()->fields()->getFieldsValues($subject);

        $fVals = $data;
        $privacyOptions = Fields_Api_Core::getFieldPrivacyOptions();
        foreach ($fVals as $key => $value) {
            if (strstr($key, 'oauth'))
                continue;
            $parts = explode('_', $key);
            if (count($parts) < 3)
                continue;
            list($parent_id, $option_id, $field_id) = $parts;

            $valueParts = explode(',', $value);

// Array mode
            if (is_array($valueParts) && count($valueParts) > 1) {
// Lookup
                $valueRows = $values->getRowsMatching(array(
                    'field_id' => $field_id,
                    'item_id' => $subject->getIdentity()
                ));
// Delete all
                foreach ($valueRows as $valueRow) {
                    $valueRow->delete();
                }
                if ($field_id == 0)
                    continue;
// Insert all
                $indexIndex = 0;
                if (is_array($valueParts) || !empty($valueParts)) {
                    foreach ((array) $valueParts as $singleValue) {

                        $valueRow = $values->createRow();
                        $valueRow->field_id = $field_id;
                        $valueRow->item_id = $subject->getIdentity();
                        $valueRow->index = $indexIndex++;
                        $valueRow->value = $singleValue;
                        $valueRow->save();
                    }
                } else {
                    $valueRow = $values->createRow();
                    $valueRow->field_id = $field_id;
                    $valueRow->item_id = $subject->getIdentity();
                    $valueRow->index = 0;
                    $valueRow->value = '';
                    $valueRow->save();
                }
            }

// Scalar mode
            else {

                try {
// Lookup
                    $valueRows = $values->getRowsMatching(array(
                        'field_id' => $field_id,
                        'item_id' => $subject->getIdentity()
                    ));
// Delete all
                    $prevPrivacy = null;
                    foreach ($valueRows as $valueRow) {
                        $valueRow->delete();
                    }

// Remove value row if empty
                    if (empty($value)) {
                        if ($valueRow) {
                            $valueRow->delete();
                        }
                        continue;
                    }

                    if ($field_id == 0)
                        continue;
// Lookup
                    $valueRow = $values->getRowMatching(array(
                        'field_id' => $field_id,
                        'item_id' => $subject->getIdentity(),
                        'index' => 0
                    ));
// Create if missing
                    $isNew = false;
                    if (!$valueRow) {

                        $isNew = true;
                        $valueRow = $values->createRow();
                        $valueRow->field_id = $field_id;
                        $valueRow->item_id = $subject->getIdentity();
                    }
                    $valueRow->value = htmlspecialchars($value);
                    $valueRow->save();
                } catch (Exception $ex) {
                    
                }
            }
        }

        return;
    }

    public function saveChannel($values) {



        $params = Array();
        if ((empty($values['owner_type'])) || (empty($values['owner_id']))) {
            $params['owner_id'] = Engine_Api::_()->user()->getViewer()->user_id;
            $params['owner_type'] = 'user';
        } else {
            $params['owner_id'] = $values['owner_id'];
            $params['owner_type'] = $values['owner_type'];
        }
        $params['title'] = $values['title'];
        $params['category_id'] = (int) @$values['category_id'];
        $params['subcategory_id'] = (int) @$values['subcategory_id'];
        $params['subsubcategory_id'] = (int) @$values['subsubcategory_id'];
        $params['description'] = $values['description'];
        $params['profile_type'] = $values['profile_type'];
        if (!empty($values['search']))
            $params['search'] = $values['search'];
        else {
            $params['search'] = 0;
        }
        $channel = Engine_Api::_()->getDbtable('channels', 'sitevideo')->createRow();
        $channel->setFromArray($params);
        $channel->save();
        // CREATE AUTH STUFF HERE
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

        if (isset($values['auth_comment']))
            $auth_comment = $values['auth_comment'];
        else
            $auth_comment = "everyone";

        if (isset($values['auth_view']))
            $auth_view = $values['auth_view'];
        else
            $auth_view = "everyone";

        $commentMax = array_search($auth_comment, $roles);
        $viewMax = array_search($auth_view, $roles);

        foreach ($roles as $i => $role) {
            $auth->setAllowed($channel, $role, 'view', ($i <= $viewMax));
            $auth->setAllowed($channel, $role, 'comment', ($i <= $commentMax));
        }
        return $channel;
    }

    public function checkPasswordProtection($params = array()) {

        if (empty($params['video_id']) || empty($params['password']))
            return false;

        $table = Engine_Api::_()->getDbtable('videos', 'sitevideo');
        $tableName = $table->info('name');
        $select = $table->select()
                ->from($tableName)
                ->where('video_id = ?', $params['video_id'])
                ->where('password = ?', $params['password'])
                ->limit(1)
        ;

        $row = $select->query()->fetchColumn();

        if ($row > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function advancedActivityVideoForm() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $allowedSources = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.allowed.video', array(1, 2, 3, 4, 5));
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $allowedSources_level = $permissionsTable->getAllowed('video', Engine_Api::_()->user()->getViewer()->level_id, 'source');

        $allowedSources_level = array_flip($allowedSources_level);
        $allowedSources = array_flip($allowedSources);
        $video_options = Array();
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $key = $coreSettings->getSetting('sitevideo.youtube.apikey', $coreSettings->getSetting('video.youtube.apikey'));

        if (isset($allowedSources[1]) && $key && isset($allowedSources_level[1])) {
            $video_options[1] = 'YouTube';
        }
        if (isset($allowedSources[2]) && isset($allowedSources_level[2]))
            $video_options[2] = 'Vimeo';
        if (isset($allowedSources[3]) && isset($allowedSources_level[3]))
            $video_options[4] = 'Dailymotion';

        $allowed_upload = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $viewer, 'create');
        $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->sitevideo_ffmpeg_path;
        if (isset($allowedSources[4]) && !empty($ffmpeg_path) && $allowed_upload && isset($allowedSources_level[4]) && empty($_GET['message'])) {
            if (Engine_Api::_()->hasModuleBootstrap('mobi') && Engine_Api::_()->mobi()->isMobile()) {
                $video_options[3] = 'My Device';
            } else {
                $video_options[3] = 'My Device';
            }
        }

        $createForm[] = array(
            'type' => 'Select',
            'name' => 'type',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Choose Video Source'),
            'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($video_options),
            "hasValidator" => true
        );

        $createForm[] = array(
            'type' => 'Text',
            'name' => 'url',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Video Link (URL)'),
            'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Paste the web address of the video here.'),
        );

        $createForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Attach'),
        );
        return $createForm;
    }

    public function getBrowseChannelForm() {
        $searchFormSettings = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getModuleOptions('sitevideo_channel');
        $searchForm = array();

// Get profile fields array
        $profileFields = $this->_getProfileTypes(array(), 'sitevideo_channel');

        if (!empty($profileFields)) {
            $this->_profileFieldsArray = $profileFields;
        }

        $createFormFields = $this->_getProfileFields(array(), 'sitevideo_channel');

        $settings = Engine_Api::_()->getApi('settings', 'core');
        if (!empty($searchFormSettings['search']) && !empty($searchFormSettings['search']['display'])) {
            $searchForm[] = array(
                'type' => 'Text',
                'name' => 'search',
                "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Name / Keyword')
            );
        }
        if (!empty($searchFormSettings['view']) && !empty($searchFormSettings['view']['display'])) {
            $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            if (!empty($viewer_id)) {
                $show_multiOptions = array();
                $show_multiOptions["0"] = 'Everyone\'s Channels';
                $show_multiOptions["1"] = 'Only My Friends\' Channels';
                $value_deault = 0;
                $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.network', 0);
                if (empty($enableNetwork)) {
                    $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
                    $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer_id));
                    if (!empty($viewerNetwork) || Engine_Api::_()->sitevideo()->channelBaseNetworkEnable()) {
                        $show_multiOptions["3"] = 'Only My Networks';
                        $browseDefaulNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.default.show', 0);

                        if (!isset($_GET['view_view']) && !empty($browseDefaulNetwork)) {
                            $value_deault = 3;
                        } elseif (isset($_GET['view_view'])) {
                            $value_deault = $_GET['view_view'];
                        }
                    }
                }

                $searchForm[] = array(
                    'type' => 'Select',
                    'name' => 'view_view',
                    "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate('View'),
                    'multiOptions' => $show_multiOptions,
                    'value' => $value_deault
                );
            }
        }

        if (!empty($searchFormSettings['orderby']) && !empty($searchFormSettings['orderby']['display'])) {
            $multiOPtionsOrderBy = array(
                '' => '',
                'creation_date' => 'Most Recent',
                'modified_date' => 'Recently Updated',
                'view_count' => 'Most View',
                'like_count' => 'Most Liked',
                'comment_count' => 'Most Commented',
                'favourite_count' => 'Most Favourite',
                'featured' => 'Featured',
                'best_channel' => 'Best Channel',
                'videos_count' => 'Most Videos',
                'sponsored' => 'Sponsored',
                'title' => "Alphabetical (A-Z)",
                'title_reverse' => 'Alphabetical (Z-A)'
            );
//GET API

            $enableRating = $settings->getSetting('sitevideo.rating', 1);

            if ($enableRating) {
                $multiOPtionsOrderBy = array_merge($multiOPtionsOrderBy, array('rating' => 'Most Rated'));
            }

            $searchForm[] = array(
                'type' => 'Select',
                'name' => 'orderby',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Browse By'),
                'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($multiOPtionsOrderBy)
            );
        }

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.category.enabled', 1)) {
            if (!empty($searchFormSettings['category_id']) && !empty($searchFormSettings['category_id']['display'])) {
                $categories = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getCategories(array('fetchColumns' => array('category_id', 'category_name', 'profile_type'), 'sponsored' => 0, 'cat_depandancy' => 1, 'orderBy' => 'category_name'));
                if (count($categories) != 0) {
                    $getCategories[0] = '';
                    foreach ($categories as $category) {
                        $subCategories = array();
                        $subCategoriesObj = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getSubCategories(array("category_id" => $category->category_id, 'havingChannels' => 0, 'fetchColumns' => '*'));
                        $getCategories[$category->category_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($category->category_name);

                        if (isset($category->profile_type) && !empty($category->profile_type))
                            $categoryProfileTypeMapping[$category->category_id] = $category->profile_type;

                        $getsubCategories = array();
                        $getsubCategories[0] = "";
                        foreach ($subCategoriesObj as $subcategory) {
                            $subsubCategoriesObj = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getSubCategories(array("category_id" => $subcategory->category_id, 'havingChannels' => 0, 'fetchColumns' => '*'));
                            $getsubCategories[$subcategory->category_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($subcategory->category_name);

                            $subsubCategories = array();
                            $subsubCategories[0] = "";
                            foreach ($subsubCategoriesObj as $subsubcategory) {
                                $subsubCategories[$subsubcategory->category_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($subsubcategory->category_name);

                                if (isset($subsubcategory->profile_type) && !empty($subsubcategory->profile_type)) {
                                    $categoryProfileTypeMapping[$subsubcategory->category_id] = $subsubcategory->profile_type;
                                }
                            }

                            if (isset($subsubCategories) && count($subsubCategories) > 1) {
                                $subsubCategoriesForm[$subcategory->category_id] = array(
                                    'type' => 'Select',
                                    'name' => 'subsubcategory_id',
                                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('3rd Level Category'),
                                    'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($subsubCategories)
                                );
                            }
//$getsubCategories[$subcategory->category_id] = $subcategory->category_name;
                            if (isset($subcategory->profile_type) && !empty($subcategory->profile_type))
                                $categoryProfileTypeMapping[$subcategory->category_id] = $subcategory->profile_type;
                        }

                        if (isset($getsubCategories) && count($getsubCategories) > 1) {
                            $subcategoriesForm = array(
                                'type' => 'Select',
                                'name' => 'subcategory_id',
                                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Sub-Category'),
                                'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($getsubCategories),
                            );
                        }
                        if (isset($subcategoriesForm) && !empty($subcategoriesForm) && count($subcategoriesForm) > 0) {
                            $form[$category->category_id]['form'] = $subcategoriesForm;
                            $subcategoriesForm = array();
                        }
                        if (isset($subsubCategoriesForm) && count($subsubCategoriesForm) > 0) {
                            $form[$category->category_id]['subsubcategories'] = $subsubCategoriesForm;
                            $subsubCategoriesForm = array();
                        }
                    }

                    $searchForm[] = array(
                        'type' => 'Select',
                        'name' => 'category_id',
                        'allowEmpty' => 0,
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Category'),
                        'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($getCategories)
                    );
                }
            }
        }

        $searchForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => 'Submit'
        );

        if (isset($searchForm) && !empty($searchForm))
            $responseForm['form'] = $searchForm;

        if (isset($form) && !empty($form))
            $responseForm['subcategories'] = $form;

        if (is_array($createFormFields) && is_array($categoryProfileTypeMapping)) {
            foreach ($categoryProfileTypeMapping as $key => $value) {
                if (isset($createFormFields[$value]) && !empty($createFormFields[$value])) {
                    $createFormFieldsForm[$key] = $createFormFields[$value];
                }
            }
            if (isset($createFormFieldsForm) && !empty($createFormFieldsForm))
                $responseForm['fields'] = $createFormFieldsForm;
        }

        return $responseForm;
    }

    /*
     * Playlist search form
     */

    public function playlistBrowse() {
        $responseForm = array();
        $searchForm = array();
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1)) {
            $searchForm[] = array(
                'type' => 'Text',
                'name' => 'search',
                "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Playlist Title')
            );

            $searchForm[] = array(
                'type' => 'Text',
                'name' => 'video_title',
                "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Video Title')
            );

            $searchForm[] = array(
                'type' => 'Text',
                'name' => 'membername',
                "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Member's name")
            );

            $searchForm[] = array(
                'type' => 'Submit',
                'name' => 'submit',
                'label' => 'Submit'
            );
            $responseForm['form'] = $searchForm;
        }
        return $responseForm;
    }

    public function addVideoMap($item) {
        $videoMapTable = Engine_Api::_()->getDbtable('videomaps', 'sitevideo');
        try {
            $videomap = $videoMapTable->createRow();
            if ($item->main_channel_id)
                $videomap->channel_id = $item->main_channel_id;
            else
                $videomap->channel_id = 0;
            $videomap->video_id = $item->video_id;
            $videomap->owner_type = $item->owner_type;
            $videomap->owner_id = $item->owner_id;
            $videomap->save();
            return $videomap->videomap_id;
        } catch (Exception $ex) {
            
        }
    }

//end of playlist search form

    public function videoType($type) {
        switch ($type) {
            case 1:
            case 'youtube':
                return 1;
            case 2:
            case 'vimeo':
                return 2;
            case 3:
            case 'mydevice':
            case 'upload':
            case 'stream':
                return 3;
            case 4:
            case 'dailymotion':
                return 4;
            case 5:
            case 'embedcode':
                return 5;
            case 'iframely':
                return 6;

            default : return $type;
        }
    }

    public function getVideoType($type) {
        switch ($type) {
            case 1:
            case 'youtube':
                return 'youtube';
            case 2:
            case 'vimeo':
                return 'vimeo';
            case 3:
            case 'mydevice':
            case 'upload':

                return 'upload';
            case 4:
            case 'dailymotion':
                return 'dailymotion';
            case 5:
            case 'embedcode':
                return 'embedcode';
            case 6:
                return 'iframely';
            default : return $type;
        }
    }

    public function favourite($resource_id, $resource_type, $isFavourite) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            return;
        $favouriteTable = Engine_Api::_()->getItemTable('seaocore_favourite');
        $object = Engine_Api::_()->getItem($resource_type, $resource_id);
        if (isset($isFavourite) && empty($isFavourite)) {
            $favouriteTable->delete(array('resource_type = ?' => $resource_type, 'resource_id = ?' => $resource_id, 'poster_id= ?' => $viewer_id));
            if ($object->favourite_count > 0) {
                $object->favourite_count = $object->favourite_count - 1;
                $object->save();
            }
        } else {

            $fName = $favouriteTable->info('name');
            $select = $favouriteTable->select()
                    ->where('resource_type = ?', $resource_type)
                    ->where('resource_id = ?', $resource_id)
                    ->where('poster_id = ?', $viewer_id)
                    ->limit(1);

            $row = $favouriteTable->fetchAll($select);
            if (count($row) == 0) {
                $db = Engine_Db_Table::getDefaultAdapter();
                $db->beginTransaction();
                try {
                    $favourite = $favouriteTable->createRow();
                    $favourite->resource_type = $resource_type;
                    $favourite->resource_id = $resource_id;
                    $favourite->poster_type = $viewer->getType();
                    $favourite->poster_id = $viewer_id;
                    $favourite->creation_date = new Zend_Db_Expr('NOW()');

                    $favourite->save();
                    $object->favourite_count = $object->favourite_count + 1;
                    $object->save();
                    $db->commit();
                } catch (Exception $ex) {
                    $db->rollback();
                }
            }
        }
    }

    public function isFavourite($resource_id, $resource_type, $poster_id) {
        if (empty($resource_id) && empty($poster_id))
            return;

        $favouriteTable = Engine_Api::_()->getItemTable('seaocore_favourite');
        $fName = $favouriteTable->info('name');
        $select = $favouriteTable->select()
                ->where('resource_id = ?', $resource_id)
                ->where('poster_id = ?', $poster_id);
        if (!empty($resource_type))
            $select->where('resource_type = ?', $resource_type);
        $select->limit(1);

        $row = $favouriteTable->fetchAll($select);

        if (count($row) == 0)
            return false;
        else
            return true;
    }

    public function getFormattedDate($date = '') {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $tz = Engine_Api::_()->getApi('settings', 'core')->core_locale_timezone;
        if (!empty($viewer_id)) {
            $tz = $viewer->timezone;
        }
        if (isset($date) && !empty($date) && isset($tz)) {
            $startDateObject = new Zend_Date(strtotime($date));
            $startDateObject->setTimezone($tz);
            $date = $startDateObject->get('YYYY-MM-dd HH:mm:ss');
        }
        return $date;
    }

    // handle video upload
    public function createVideo($params, $file, $values) {
        if ($file instanceof Storage_Model_File) {
            $params['file_id'] = $file->getIdentity();
        } else {
            // create video item
            $video = Engine_Api::_()->getDbtable('videos', 'sitevideo')->createRow();
            $file_ext = pathinfo($file['name']);
            $file_ext = $file_ext['extension'];
            $video->code = $file_ext;
            $video->synchronized = 1;
            $video->status = 0;
            $video->save();

            // Channel video in temporary storage object for ffmpeg to handle
            $storage = Engine_Api::_()->getItemTable('storage_file');
            $storageObject = $storage->createFile($file, array(
                'parent_id' => $video->getIdentity(),
                'parent_type' => $video->getType(),
                'user_id' => $video->owner_id,
            ));

            // Remove temporary file
            @unlink($file);

            $video->file_id = $storageObject->file_id;
            $video->save();

            $db = Engine_Db_Table::getDefaultAdapter();
            $select = new Zend_Db_Select($db);
            $restapiVersion = $select
                    ->from('engine4_core_modules', 'version')
                    ->where('name = ?', 'siteapi')
                    ->query()
                    ->fetchColumn();

            if (((_IOS_VERSION && _IOS_VERSION >= '2.4.1') || (_ANDROID_VERSION && _ANDROID_VERSION >= '3.0'))) {
                //temporarly thumbnail upload................
                if (!empty($_FILES['photo'])) {
                    $video->status = 1;
                     $video->save();
                    $this->setPhoto($_FILES['photo'], $video, false);
                     return $video;
                }
                //...........................................
                // Add to jobs
                if (strtolower($file_ext) == 'mp4' || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.html5', true)) {
                    Engine_Api::_()->getDbtable('jobs', 'core')->addJob('siteapi_sitevideo_encode', array(
                        'video_id' => $video->getIdentity(),
                        'type' => 'mp4',
                        "subject_type" => $video->getType()
                    ));
                } else {
                    Engine_Api::_()->getDbtable('jobs', 'core')->addJob('siteapi_sitevideo_encode', array(
                        'video_id' => $video->getIdentity(),
                        'type' => 'flv',
                        "subject_type" => $video->getType()
                    ));
                }
            } else {
                Engine_Api::_()->getDbtable('jobs', 'core')->addJob('sitevideo_encode', array(
                    'video_id' => $video->getIdentity(),
                    'type' => 'mp4',
                ));
            }
        }

        return $video;
    }

    public function getVideoFilterImage($video){
        //Video Filter Work..............
        try
        {
            $filterImage ='';
            $select = Engine_Api::_()->getItemTable('storage_file')->select()
            ->where('parent_type = ?', 'story_video')
            ->where('parent_id = ?', $video->getIdentity())
            ->limit(1);
            $filterImage = Engine_Api::_()->getItemTable('storage_file')->fetchRow($select);

            if(!empty($filterImage)){
            $filterImage = Engine_Api::_()->getApi('Siteapi_Feed', 'advancedactivity')->getPhotoUrl($filterImage);
            
            }
        } 
        catch (Exception $ex) 
        {
        }
        $filterImage = $filterImage?$filterImage:'';
        return $filterImage;
    }

}

?>