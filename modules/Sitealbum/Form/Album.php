<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Album.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Form_Album extends Engine_Form {

    protected $_defaultProfileId;

    public function getDefaultProfileId() {
        return $this->_defaultProfileId;
    }

    public function setDefaultProfileId($default_profile_id) {
        $this->_defaultProfileId = $default_profile_id;
        return $this;
    }

    public function init() {

        $user = Engine_Api::_()->user()->getViewer();

        // Init form
        $this
                //->setTitle('Add New Photos')
                ->setDescription('Choose photos on your computer to add to this album.')
                ->setAttrib('id', 'form-upload')
                ->setAttrib('name', 'albums_create')
                ->setAttrib('enctype', 'multipart/form-data')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

        // Init album
        $albumTable = Engine_Api::_()->getItemTable('album');
        $myAlbums = $albumTable->select()
                ->from($albumTable, array('album_id', 'title'))
                ->where('owner_type = ?', 'user')
                ->where('owner_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity())
                ->query()
                ->fetchAll();

        $albumOptions = array('0' => 'Create A New Album');
        foreach ($myAlbums as $myAlbum) {
            $albumOptions[$myAlbum['album_id']] = $myAlbum['title'];
        }

        $this->addElement('Select', 'album', array(
            'label' => 'Choose Album',
            'multiOptions' => $albumOptions,
            'onchange' => "updateTextFields()",
        ));

        // Init name
        $this->addElement('Text', 'title', array(
            'label' => 'Album Title',
            'maxlength' => '40',
            'filters' => array(
                //new Engine_Filter_HtmlSpecialChars(),
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '63')),
            )
        ));
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.category.enabled', 1)) {
            // prepare categories
            $categories = Engine_Api::_()->getDbtable('categories', 'sitealbum')->getCategories(array('fetchColumns' => array('category_id', 'category_name'), 'sponsored' => 0, 'cat_depandancy' => 1, 'orderBy' => 'category_name'));
            if (count($categories) != 0) {
                $categories_prepared[0] = "";
                foreach ($categories as $category) {
                    $categories_prepared[$category->category_id] = $category->category_name;
                }
            }

            if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
                $onChangeEvent = "showFields(this.value, 1); subcategories(this.value, '', '');";
                $categoryFiles = 'application/modules/Sitealbum/views/scripts/_formSubcategory.tpl';
            } else {
                $onChangeEvent = "showSMFields(this.value, 1);sm4.core.category.set(this.value, 'subcategory');";
                $categoryFiles = 'application/modules/Sitealbum/views/sitemobile/scripts/_subCategory.tpl';
            }

            if (count($categories) > 0) {
                $this->addElement('Select', 'category_id', array(
                    'label' => 'Category',
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
        }

        $defaultProfileId = "0_0_" . $this->getDefaultProfileId();

        $customFields = new Sitealbum_Form_Custom_Standard(array(
            'item' => 'album',
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
            'label' => 'Album Description',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_EnableLinks(),
            ),
        ));

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.location', 1)) {
            $this->addElement('Text', 'sitealbum_location', array(
                'label' => 'Location',
                'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                ),
            ));
            $this->addElement('Hidden', 'locationParams', array('order' => 800000));
            $this->addElement('Hidden', 'dataParams', array('order' => 800001));

            $locationFieldName = 'sitealbum_location';
            include_once APPLICATION_PATH . '/application/modules/Seaocore/Form/specificLocationElement.php';
        }

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.tags.enabled', 0)) {
            $this->addElement('Text', 'tags', array(
                'label' => 'Tags (Keywords)',
                'autocomplete' => 'off',
                'description' => Zend_Registry::get('Zend_Translate')->_('Separate tags with commas.'),
                'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                ),
            ));
            $this->tags->getDecorator("Description")->setOption("placement", "append");
        }

        //NETWORK BASE ALBUM
        if (Engine_Api::_()->sitealbum()->albumBaseNetworkEnable()) {
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
                $viewPricavyEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.networkprofile.privacy', 0);
                if ($viewPricavyEnable) {
                    $desc = 'Select the networks, members of which should be able to see your album. (Press Ctrl and click to select multiple networks. Applied privacy will be a combination of the privacy chosen above in "View Privacy" and the privacy chosen here.)';
                } else {
                    $desc = 'Select the networks, members of which should be able to see your Album in browse and search albums. (Press Ctrl and click to select multiple networks. Applied privacy will be a combination of the privacy chosen above in "View Privacy" and the privacy chosen here.)';
                }
                $this->addElement('Multiselect', 'networks_privacy', array(
                    'label' => 'Networks Selection',
                    'description' => $desc,
                    'multiOptions' => $networksOptions,
                    'value' => array(0)
                ));
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
        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('album', $user, 'auth_view');
        $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
        if (!empty($viewOptions) && count($viewOptions) >= 1) {
            // Make a hidden field
            if (count($viewOptions) == 1) {
                $this->addElement('hidden', 'auth_view', array('order' => 863,'value' => key($viewOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_view', array(
                    'label' => 'Privacy',
                    'description' => 'Who may see this album?',
                    'multiOptions' => $viewOptions,
                    'value' => key($viewOptions),
                ));
                $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
            }
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $allowPasswordProtected = Engine_Api::_()->authorization()->getPermission($level_id, 'album', 'album_password_protected');
        if ($allowPasswordProtected) {
            // Element: password
            $this->addElement('Text', 'password', array(
                'label' => 'Password',
                'description' => "Protect this Photo Album with a password. [Leave it blank if you do not want password protection on this photo album.]",
                'required' => false,
                'allowEmpty' => true,
                'validators' => array(
                    array('NotEmpty', true),
                    array('StringLength', false, array(6, 32)),
                ),
                'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                ),
            ));
            $this->password->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));
        }

        // Element: auth_comment
        $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('album', $user, 'auth_comment');
        $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));

        if (!empty($commentOptions) && count($commentOptions) >= 1) {
            // Make a hidden field
            if (count($commentOptions) == 1) {
                $this->addElement('hidden', 'auth_comment', array('order' => 864,'value' => key($commentOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_comment', array(
                    'label' => 'Comment Privacy',
                    'description' => 'Who may post comments on this album?',
                    'multiOptions' => $commentOptions,
                    'value' => key($commentOptions),
                ));
                $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
            }
        }

        // Element: auth_tag
        $tagOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('album', $user, 'auth_tag');
        $tagOptions = array_intersect_key($availableLabels, array_flip($tagOptions));

        if (!empty($tagOptions) && count($tagOptions) >= 1) {
            // Make a hidden field
            if (count($tagOptions) == 1) {
                $this->addElement('hidden', 'auth_tag', array('order' =>865,'value' => key($tagOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_tag', array(
                    'label' => 'Tagging',
                    'description' => 'Who may tag photos in this album?',
                    'multiOptions' => $tagOptions,
                    'value' => key($tagOptions),
                ));
                $this->auth_tag->getDecorator('Description')->setOption('placement', 'append');
            }
        }

        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode') && function_exists('exif_read_data')) {
            $this->addElement('Radio', 'sitealbum_photo_date_method', array(
                'label' => 'Choose Date for Photos',
                'multiOptions' => array(
                    1 => 'Use date from photos',
                    2 => 'Change date for each photo'
                ),
                'value' => 2,
                'onclick' => "setPhotosDate(this);",
            ));
        }

        // Init search
        $this->addElement('Checkbox', 'search', array(
            'label' => Zend_Registry::get('Zend_Translate')->_("Show this album in search results"),
            'value' => 1,
            'disableTranslator' => true
        ));

        $uploadUrl = Zend_Controller_Front::getInstance()->getRouter()->assemble(array()) . '?ul=1';
        $deleteUrl = $this->getView()->url(array('module' => 'sitealbum', 'controller' => 'photo', 'action' => 'delete'), 'default');

        $this->addElement('SeaoFancyUpload', 'file', array(
            'url' =>  $uploadUrl,
            'deleteUrl' =>  $deleteUrl,
            'accept' => 'image/*',
            'formId' => $this->getAttrib('id'),
            'viewScript' => array('upload/upload.tpl', 'sitealbum', array()),
        ));

    // Init submit
        $this->addElement('Button', 'submitForm', array(
            'label' => 'Save Photos',
            'type' => 'submit',
            'onclick' => 'return albumSubmitForm()'
        ));
    }

    public function clearAlbum() {
        $this->getElement('album')->setValue(0);
    }

    public function saveValues() {
        $set_cover = false;
        $values = $this->getValues();

        $params = Array();
        if ((empty($values['owner_type'])) || (empty($values['owner_id']))) {
            $params['owner_id'] = Engine_Api::_()->user()->getViewer()->user_id;
            $params['owner_type'] = 'user';
        } else {
            $params['owner_id'] = $values['owner_id'];
            $params['owner_type'] = $values['owner_type'];
            throw new Zend_Exception("Non-user album owners not yet implemented");
        }

        if (($values['album'] == 0)) {
            $params['title'] = $values['title'];
            if (empty($params['title'])) {
                $params['title'] = "Untitled Album";
            }
            $params['category_id'] = (int) @$values['category_id'];
            $params['subcategory_id'] = (int) @$values['subcategory_id'];
            $params['description'] = $values['description'];
            $params['search'] = $values['search'];
            $album = Engine_Api::_()->getDbtable('albums', 'sitealbum')->createRow();

            if (Engine_Api::_()->sitealbum()->albumBaseNetworkEnable()) {
                if (isset($values['networks_privacy']) && !empty($values['networks_privacy'])) {
                    if (in_array(0, $values['networks_privacy'])) {
                        $params['networks_privacy'] = new Zend_Db_Expr('NULL');
                    } else {
                        $params['networks_privacy'] = (string) ( is_array($values['networks_privacy']) ? join(",", $values['networks_privacy']) : $netowrkIds );
                    }
                }
            }

            $generateFeed = true;
            if (isset($values['password']) && !empty($values['password'])) {
                $params['search'] = 0;
                $params['password'] = $values['password'];
                $generateFeed = false;
            } else {
                $params['password'] = '';
            }

            $album->setFromArray($params);
            $album->save();
            $set_cover = true;

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
                $auth->setAllowed($album, $role, 'view', ($i <= $viewMax));
                $auth->setAllowed($album, $role, 'comment', ($i <= $commentMax));
                $auth->setAllowed($album, $role, 'tag', ($i <= $tagMax));
            }
        } else {
            if (!isset($album)) {
                $album = Engine_Api::_()->getItem('album', $values['album']);
                $photoIdArray = array();
                $photos = Engine_Api::_()->getItemTable('album_photo')->getPhotoPaginator(array(
                    'album' => $album,
                ));

                foreach ($photos as $photo) {
                    $photoIdArray[] = $photo->photo_id;
                }

                if (empty($album->photo_id) || (count($photoIdArray) <= 0) || (!in_array($album->photo_id, $photoIdArray))) {
                    $set_cover = true;
                }
            }
        }
            $generateFeed = true;
            if (isset($values['password']) && !empty($values['password'])) {
                $params['search'] = 0;
                $params['password'] = $values['password'];
                $generateFeed = false;
            } else {
                $params['password'] = '';
            }
        // Add action and attachments
        $api = Engine_Api::_()->getDbtable('actions', 'activity');
        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            if (!is_array($values['file'])) {
                $values['file'] = explode(" ", trim($values['file']));
            }

            if ($generateFeed) {
                if(count($values['file']) == 1) {
                   $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $album, 'album_single_photo_upload', null);
                } else {
                    $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $album, 'album_photo_new', null, array('count' => count($values['file'])));
                }
            }
            // Do other stuff
            $count = 0;
            foreach ($values['file'] as $photo_id) {
                $photo = Engine_Api::_()->getItem("album_photo", $photo_id);
                if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity())
                    continue;

                if ($set_cover) {
                    $album->photo_id = $photo_id;
                    
                    $album->save();
                    $set_cover = false;
                    
                }
                if(isset($album->album_cover) && !$album->album_cover) {
                    $album->album_cover = $photo_id;
                    $album->save();
                }
                $photo->album_id = $album->album_id;
                $photo->order = $photo_id;
                if (isset($_POST['media_photo_title_' . $photo_id])) {
                    $photo->title = $_POST['media_photo_title_' . $photo_id];
                }

                if (isset($_POST['media_photo_description_' . $photo_id])) {
                    $photo->description = $_POST['media_photo_description_' . $photo_id];
                }

                if (isset($_POST['media_photo_previous_date_' . $photo_id]) && isset($photo->date_taken)) {
                    $photo->date_taken = $_POST['media_photo_previous_date_' . $photo_id];
                }
                if (isset($_POST['media_photo_location_value_' . $photo_id])) {
                    $location = $_POST['media_photo_location_value_' . $photo_id];
                    $seaoLocationId = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocationItemId($location, '', $photo->getType(), $photo_id);
                    $photo->seao_locationid = $seaoLocationId;
                    $photo->location = $location;
                }
                $photo->save();
                if ($generateFeed && $action instanceof Activity_Model_Action ) {
                    $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
                }
                $count++;
            }

            if (isset($_POST['media_main_photo'])) {
                $album->photo_id = $_POST['media_main_photo'];
                $album->save();
            }
        } else {
            if ($generateFeed) {
                 if(count($values['file']) == 1) {
                   $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $album, 'album_single_photo_upload', null);
                } else {
                $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $album, 'album_photo_new', null, array('count' => count($values['file'])));
                }
            }
            // Do other stuff
            $count = 0;
            foreach ($values['file'] as $photo_id) {
                $photo = Engine_Api::_()->getItem("album_photo", $photo_id);
                if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity())
                    continue;

                if ($set_cover) {

                    $album->photo_id = $photo_id;
                    $album->save();

                    $set_cover = false;
                }

                $photo->album_id = $album->album_id;
                $photo->order = $photo_id;

                $photo->save();

                if ($generateFeed && $action instanceof Activity_Model_Action) {
                    $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
                }
                $count++;
            }
        }


        // UPDATE PHOTOS COUNT COLUMN
        if (($values['album'] == 0)) {
            $album->photos_count = count($values['file']);
        } else {
            $album = Engine_Api::_()->getItem('album', $values['album']);
            $album->photos_count = $album->photos_count + count($values['file']);
        }
        $album->save();

        // Get a list of all photos in this album, by order
        $photoTable = Engine_Api::_()->getItemTable('album_photo');
        $currentOrder = $photoTable->select()
                ->from($photoTable, 'photo_id')
                ->where('album_id = ?', $album->getIdentity())
                ->order('order ASC')
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN)
        ;

        $order = $values['file'];
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
        } else {
            $photo_id = 0;

            for ($i = 0, $l = count($currentOrder); $i < $l; $i++) {
                if ($i >= $start && $i <= $end) {
                    if (isset($order[$i - $start]))
                        $photo_id = $order[$i - $start];
                } else {
                    if (isset($currentOrder[$i]))
                        $photo_id = $currentOrder[$i];
                }
                $photoTable->update(array(
                    'order' => $i,
                        ), array(
                    'photo_id = ?' => $photo_id,
                ));
            }
        }

        return $album;
    }

}
