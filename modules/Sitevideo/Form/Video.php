<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Video.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Video extends Engine_Form
{

  protected $_defaultProfileId;

  public function getDefaultProfileId()
  {
    return $this->_defaultProfileId;
  }

  public function setDefaultProfileId($default_profile_id)
  {
    $this->_defaultProfileId = $default_profile_id;
    return $this;
  }

  public function init()
  {
    $hiddenOrderCount = 89760;
    $parent_type = Zend_Controller_Front::getInstance()->getRequest()->getParam('parent_type', null);
    $parent_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('parent_id', null);
    $user = Engine_Api::_()->user()->getViewer();
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $url = "'" . $view->url(array('action' => 'create'), 'sitevideo_general', true) . "'";

    // Init form
    $this
      ->setAttrib('id', 'form-upload')
      ->setAttrib('name', 'channels_create')
      ->setAttrib('enctype', 'multipart/form-data')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    if( Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.allow', 1) && empty($parent_type) && empty($parent_id) ) {
      // Init channel
      $channelTable = Engine_Api::_()->getItemTable('sitevideo_channel');
      $myChannels = $channelTable->select()
        ->from($channelTable, array('channel_id', 'title'))
        ->where('owner_type = ?', 'user')
        ->where('owner_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity())
        ->query()
        ->fetchAll();
      $channelOptions = array('0' => '');
      foreach( $myChannels as $myChannel ) {
        $channelOptions[$myChannel['channel_id']] = $myChannel['title'];
      }

      if (count($channelOptions) > 1) {
        $this->addElement('Select', 'channel', array(
          'label' => 'Choose Channel',
          'multiOptions' => $channelOptions,
          // 'onchange' => "updateTextFields()",
          'style' => 'width:205px;'
        ));

        $this->addElement('Button', 'createChannel', array(
          'label' => 'Create New Channel',
          'type' => 'button',
          'onclick' => "window.location=" . $url
        ));
      }
    }
    // Init name
    $this->addElement('Text', 'title', array(
      'label' => 'Video Title',
      'maxlength' => '100',
      //  'allowEmpty' => false,
      'onkeyup' => 'setHiddenVideoTitle();',
      'required' => true,
      'filters' => array(
        //new Engine_Filter_HtmlSpecialChars(),
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '63')),
      )
    ));

    /*
    if( Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.tags.enabled', 1) ) {
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
    */

    /*
    if( Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.category.enabled', 1) ) {
      // prepare categories
      $categories = Engine_Api::_()->getDbtable('videoCategories', 'sitevideo')->getCategories(array('fetchColumns' => array('category_id', 'category_name'), 'sponsored' => 0, 'cat_depandancy' => 1, 'orderBy' => 'category_name'));
      if( count($categories) != 0 ) {
        $categories_prepared[""] = "";
        foreach( $categories as $category ) {
          $categories_prepared[$category->category_id] = $category->category_name;
        }
      }
      $onChangeEvent = "showFields(this.value, 1); subcategories(this.value, '', '');";
      $categoryFiles = 'application/modules/Sitevideo/views/scripts/_formVideoSubcategory.tpl';
      if( count($categories) > 0 ) {
        $this->addElement('Select', 'category_id', array(
          'label' => 'Category',
          'multiOptions' => $categories_prepared,
          'onchange' => $onChangeEvent,
          //'allowEmpty' => false,
          // 'required' => true,
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
    }*/

    $defaultProfileId = "0_0_" . $this->getDefaultProfileId();

    $customFields = new Sitevideo_Form_Custom_Standard(array(
      'item' => 'video',
      'decorators' => array(
        'FormElements'
    )));

    $customFields->removeElement('submit');
    if( $customFields->getElement($defaultProfileId) ) {
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
      'label' => 'Video Description',
      'onkeyup' => 'setHiddenVideoDescription();',
      //  'allowEmpty' => false,
      // 'required' => true,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
      ),
    ));
    if( Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.photo.field.enabled', 1) ) {
      $this->addElement('File', 'art', array(
      'label' => 'Video Thumbnail Image',
    ));
    $this->art->addValidator('Extension', false, 'jpg,png,gif,jpeg');
    }
    if( Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.location', 0) ) {
      $this->addElement('Text', 'sitevideo_location', array(
        'label' => 'Location',
        'filters' => array(
          new Engine_Filter_Censor(),
          'StripTags',
        ),
      ));
      $this->addElement('Hidden', 'locationParams', array('order' => $hiddenOrderCount++));
      $this->addElement('Hidden', 'dataParams', array('order' => $hiddenOrderCount++));

      $locationFieldName = 'sitevideo_location';
      include_once APPLICATION_PATH . '/application/modules/Seaocore/Form/specificLocationElement.php';
    }

    //NETWORK BASE VIDEO
    if( Engine_Api::_()->sitevideo()->videoBaseNetworkEnable() ) {
      // Make Network List
      $table = Engine_Api::_()->getDbtable('networks', 'network');
      $select = $table->select()
        ->from($table->info('name'), array('network_id', 'title'))
        ->order('title');
      $result = $table->fetchAll($select);

      $networksOptions = array('0' => 'Everyone');
      foreach( $result as $value ) {
        $networksOptions[$value->network_id] = $value->title;
      }
      if( count($networksOptions) > 0 ) {
        $viewPricavyEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.networkprofile.privacy', 0);
        if( $viewPricavyEnable ) {
          $desc = 'Select the networks, members of which should be able to see your video. (Press Ctrl and click to select multiple networks. Applied privacy will be a combination of the privacy chosen above in "View Privacy" and the privacy chosen here.)';
        } else {
          $desc = 'Select the networks, members of which should be able to see your Video in browse and search video. (Press Ctrl and click to select multiple networks. Applied privacy will be a combination of the privacy chosen above in "View Privacy" and the privacy chosen here.)';
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

    $explodeParentType = explode('_', $parent_type);
    if (!empty($explodeParentType) && isset($explodeParentType[0]) && isset($explodeParentType[1])) {
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($explodeParentType[0] . 'member') && (in_array($parent_type, array('sitepage_page', 'sitebusiness_business', 'sitegroup_group'))) && (Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
        $shortTypeName = ucfirst($explodeParentType[1]);
        $viewOptions['parent_member'] = $shortTypeName . ' Members Only';
        $availableLabels = array(
          'everyone' => 'Everyone',
          'registered' => 'All Registered Members',
          'owner_network' => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member' => 'Friends Only',
          'parent_member' => $shortTypeName . ' Members Only',
          'owner' => 'Just Me'
          ); 
      } elseif (($parent_type == 'siteevent_event') && (Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) { 
        $availableLabels = array(
          'everyone' => 'Everyone',
          'registered' => 'All Registered Members',
          'owner_network' => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member' => 'Friends Only',
          'parent_member' => 'Event Guests Only', 
          'owner' => 'Just Me'
          );
      }
    }


    // Element: auth_view
    /*
    $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $user, 'auth_view');
    $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
    if( !empty($viewOptions) && count($viewOptions) >= 1 ) {
      // Make a hidden field
      if( count($viewOptions) == 1 ) {
        $this->addElement('hidden', 'auth_view', array('order' => $hiddenOrderCount++, 'value' => key($viewOptions)));
        // Make select box
      } else {
        $this->addElement('Select', 'auth_view', array(
          'label' => 'Privacy',
          'description' => 'Who may see this video?',
          'multiOptions' => $viewOptions,
          'value' => key($viewOptions),
        ));
        $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
      }
    }*/

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    if( !empty($viewer_id) ) {
      $level_id = $viewer->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

    $allowPasswordProtected = Engine_Api::_()->authorization()->getPermission($level_id, 'video', 'video_password_protected');
    if( $allowPasswordProtected ) {
      // Element: password
      $this->addElement('Text', 'password', array(
        'label' => 'Password',
        'description' => "Protect this video with a password. [Leave it blank if you do not want password protection on this video.]",
        'required' => false,
        'allowEmpty' => true,
        'filters' => array(
          new Engine_Filter_Censor(),
          'StripTags'),
        'validators' => array(
          array('NotEmpty', true),
          array('StringLength', false, array(6, 32)),
        )
      ));
      $this->password->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));
    }

    $availableLabels = array(
      'everyone' => 'Everyone',
      'registered' => 'All Registered Members',
      'owner_network' => 'Friends and Networks',
      'owner_member_member' => 'Friends of Friends',
      'owner_member' => 'Friends Only',
      'owner' => 'Just Me'
      );  
    $explodeParentType = explode('_', $parent_type);
    if (!empty($explodeParentType) && isset($explodeParentType[0]) && isset($explodeParentType[1])) {
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($explodeParentType[0] . 'member') && ($parent_type == 'sitepage_page' || $parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group') && (Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
        $shortTypeName = ucfirst($explodeParentType[1]);
        $commentOptions['parent_member'] = $shortTypeName . ' Members Only';
        $availableLabels = array(
          'everyone' => 'Everyone',
          'registered' => 'All Registered Members',
          'owner_network' => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member' => 'Friends Only',
          'parent_member' => $shortTypeName . ' Members Only',
          'owner' => 'Just Me'
          ); 
      } elseif (($parent_type == 'siteevent_event') && (Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) { 
        $availableLabels = array(
          'everyone' => 'Everyone',
          'registered' => 'All Registered Members',
          'owner_network' => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member' => 'Friends Only',
          'parent_member' => 'Event Guests Only', 
          'owner' => 'Just Me'
          );
      }
    }
            
    // Element: auth_comment
    /*
    $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $user, 'auth_comment');
    $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));

    if( !empty($commentOptions) && count($commentOptions) >= 1 ) {
      // Make a hidden field
      if( count($commentOptions) == 1 ) {
        $this->addElement('hidden', 'auth_comment', array('order' => $hiddenOrderCount++, 'value' => key($commentOptions)));
        // Make select box
      } else {
        $this->addElement('Select', 'auth_comment', array(
          'label' => 'Comment Privacy',
          'description' => 'Who may post comments on this video?',
          'multiOptions' => $commentOptions,
          'value' => key($commentOptions),
        ));
        $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
      }
    }

    // Init search
    $this->addElement('Checkbox', 'search', array(
      'label' => Zend_Registry::get('Zend_Translate')->_("Show this video in search results"),
      'value' => 1,
      'disableTranslator' => true
    ));

    /*
    // Video rotation
    $this->addElement('Select', 'rotation', array(
      'label' => 'Video Rotation',
      'multiOptions' => array(
        0 => '',
        90 => '90°',
        180 => '180°',
        270 => '270°'
      ),
    ));
    */

    // Init video
    $this->addElement('Radio', 'type', array(
      'label' => 'Choose Video Source',
      // 'multiOptions' => array('0' => ' '),
      'onchange' => "updateTextFields(this.value)",
      'escape' => false,
    ));

    $allowedSources = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.allowed.video', array(1, 2, 3, 4, 5));
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $allowedSources_level = $permissionsTable->getAllowed('video', Engine_Api::_()->user()->getViewer()->level_id, 'source');

    $allowedSources_level = array_flip($allowedSources_level);
    $allowedSources = array_flip($allowedSources);
    //YouTube, Vimeo ,Dailymotion
    $video_options = Array();
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $key = $coreSettings->getSetting('sitevideo.youtube.apikey', $coreSettings->getSetting('video.youtube.apikey'));
    if( isset($allowedSources[1]) && $key && isset($allowedSources_level[1]) ) {
      $video_options['youtube'] = "<i class='sitevideo_icon_youtube' title='YouTube'></i>";
    }
    if( isset($allowedSources[2]) && isset($allowedSources_level[2]) )
      $video_options['vimeo'] = "<i class='sitevideo_icon_vimeo' title='Vimeo'></i>";
    if( isset($allowedSources[3]) && isset($allowedSources_level[3]) )
      $video_options['dailymotion'] = "<i class='sitevideo_icon_dailymotion' title='Dailymotion'></i>";
    
    if( isset($allowedSources[6]) && isset($allowedSources_level[6]) ){
      $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
      $compareVersion = Engine_Api::_()->seaocore()->checkVersion($coreversion, '4.9.0');
      if( $compareVersion > 0 )
      $video_options['iframely'] = "<i class='sitevideo_icon_externalsites' title='External Sites'></i>";
    }
    
    if( isset($allowedSources[7]) && isset($allowedSources_level[7]) && Engine_Api::_()->sitevideo()->enableStreamVideo()) {
      $video_options['stream'] = "<span class='sitevideo_icon_mysystem' title='Uplaod For Stream'>Uplaod For Stream</span>";
    }
    
    //My Computer
    $allowed_upload = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $user, 'create');
    $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->sitevideo_ffmpeg_path;
    if( isset($allowedSources[4]) && !empty($ffmpeg_path) && $allowed_upload && isset($allowedSources_level[4]) ) {
      if( Engine_Api::_()->hasModuleBootstrap('mobi') && Engine_Api::_()->mobi()->isMobile() ) {
        $video_options['upload'] = "<span class='sitevideo_icon_mysystem' title='My Device'>My Device</span>";
      } else {
        $myComputerString  = Zend_Registry::get('Zend_Translate')->_("My Computer");
        $video_options['upload'] = "<span class='sitevideo_icon_mysystem' title='My Computer'>$myComputerString</span>";
      }
    }
    if( isset($allowedSources[5]) && isset($allowedSources_level[5]) ) {
      $video_options['embedcode'] = "<i class='sitevideo_icon_embed' title='Embed Code'></i><br /><p class='description'>". Zend_Registry::get('Zend_Translate')->_('You need to upload a video thumbnail image for the videos which have different video sources other than Vimeo, Dailymotion and Youtube.')."</p>";
    }

    $this->type->addMultiOptions($video_options);

    //ADD AUTH STUFF HERE
    // Init url
    $this->addElement('Text', 'url', array(
      'label' => 'Video Link (URL)',
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
      ),
      'description' => 'Paste the web address of the video here.',
      'maxlength' => '5000'
    ));
    $this->url->getDecorator("Description")->setOption("placement", "append");
    $this->getElement('url')->setAttribs(array('style' => 'display: none'));
    $this->getElement('url')->getDecorator('Label')->setOption('style', 'display: none');
    $this->getElement('url')->getDecorator('Description')->setOption('style', 'display: none');

    if( !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.photo.field.enabled', 1) ) {
      $this->addElement('File', 'thumbnail', array(
        'label' => 'Video Thumbnail Image',
        'description' => 'Recommended size: 1600*1600'
      ));
      $this->getElement('thumbnail')->setAttribs(array('style' => 'display: none'));
      $this->getElement('thumbnail')->getDecorator('Label')->setOption('style', 'display: none');
      $this->thumbnail->getDecorator("Description")->setOption("placement", "append");
      $this->getElement('thumbnail')->getDecorator('Description')->setOption('style', 'display: none');
    }
    /*
      $this->addElement('Textarea', 'embed_code', array(
      'label' => 'Video Embed code',
      'description' => 'Paste the embed code of the video here.',
      'maxlength' => '50'
      ));
      $this->embed_code->getDecorator("Description")->setOption("placement", "append");
      $this->getElement('embed_code')->setAttribs(array('style' => 'display: none'));
      $this->getElement('embed_code')->getDecorator('Label')->setOption('style', 'display: none');
      $this->getElement('embed_code')->getDecorator('Description')->setOption('style', 'display: none');
     */

    $this->addElement('Hidden', 'code', array(
      'order' => $hiddenOrderCount++,
    ));
    $this->addElement('Hidden', 'id', array(
      'order' => $hiddenOrderCount++,
    ));
    $this->addElement('Hidden', 'ignore', array(
      'order' => $hiddenOrderCount++,
    ));

    $this->addElement('Hidden', 'videotitle', array(
      'order' => $hiddenOrderCount++,
    ));

    $this->addElement('Hidden', 'videodescription', array(
      'order' => $hiddenOrderCount++,
    ));
    $this->addElement('Hidden', 'vtype', array(
      'order' => $hiddenOrderCount++,
    ));
    // Init file

    $uploadUrl = $view->url(array('action' => 'create', 'format' => 'json'), 'sitevideo_video_general', true) . '?ul=1';
    $this->addElement('SeaoFancyUpload', 'file', array(
      'multiple' => false,
      'url' =>  $uploadUrl,
      'fileType' => 'video',
      'viewScript' => array('upload/upload-video.tpl', 'seaocore', array()),
      'data' => array(
        'linkClass' => 'seaocore_icon_add',
      )
    ));
    
    $uploadUrl = $view->url(array('action' => 'create', 'format' => 'json'), 'sitevideo_video_general', true) . '?ulStream=1';
    $this->addElement('SeaoFancyUpload', 'stream', array(
      'multiple' => false,
      'url' =>  $uploadUrl,
      'fileType' => 'video',
      'viewScript' => array('upload/upload-video.tpl', 'seaocore', array()),
      'data' => array(
        'linkClass' => 'seaocore_icon_add',
        'linkDescription' => "Click 'Add Video' to select a video from your computer which you want to uplaod for stream. After you have selected video, click on Save Video at the bottom to begin uploading the file. Please wait while your video is being uploaded. When your upload is finished, your video will be processed - you will be notified when it is ready to be viewed."
      )
    ));

    // Init submit
    $this->addElement('Button', 'upload', array(
      'label' => 'Save Videos',
      'type' => 'submit',
      'style' => 'display:none'
    ));
    //$this->getElement('upload')->getDecorator('Label')->setOption('style', 'display: none');
  }

  public function clearChannel()
  {
    $this->getElement('channel')->setValue(0);
  }

  public function saveValues($video)
  {

    $values = $this->getValues();
    $params = array();
    if( !isset($values['channel']) )
      $values['channel'] = 0;
    if( $values['channel'] == 0 )
      $params['main_channel_id'] = null;
    else
      $params['main_channel_id'] = $values['channel'];

    if( (empty($values['owner_type'])) || (empty($values['owner_id'])) ) {
      $params['owner_id'] = Engine_Api::_()->user()->getViewer()->user_id;
      $params['owner_type'] = 'user';
    } else {
      $params['owner_id'] = $values['owner_id'];
      $params['owner_type'] = $values['owner_type'];
      throw new Zend_Exception("Non-user video owners not yet implemented");
    }
    if( $values['title'] )
      $params['title'] = $values['title'];

    if( Engine_Api::_()->sitevideo()->videoBaseNetworkEnable() ) {
      if( isset($values['networks_privacy']) && !empty($values['networks_privacy']) ) {
        if( in_array(0, $values['networks_privacy']) ) {
          $params['networks_privacy'] = new Zend_Db_Expr('NULL');
        } else {
          $params['networks_privacy'] = (string) ( is_array($values['networks_privacy']) ? join(",", $values['networks_privacy']) : $netowrkIds );
        }
      }
    }

    $params['category_id'] = (int) @$values['category_id'];
    $params['subcategory_id'] = (int) @$values['subcategory_id'];
    $params['subsubcategory_id'] = (int) @$values['subsubcategory_id'];
    if( $values['description'] )
      $params['description'] = $values['description'];
//    $params['search'] = $values['search'];
    $params['search'] = 1;
    if( $values['type'] ) {
      $params['type'] = $values['type'];
      if( $values['type'] == 'embedcode' && $values['vtype'] )
        $params['type'] = $values['vtype'];
    }
    if( $values['code'] )
      $params['code'] = $values['code'];
//    $params['rotation'] = $values['rotation'];
    $params['rotation'] = 0;
    $video->setFromArray($params);
    $video->synchronized = 1;
    $video->save();

    if (!empty($values['art'])) {
      $video->saveVideoThumbnail($this->art);
    }
    // CREATE AUTH STUFF HERE
    $auth = Engine_Api::_()->authorization()->context;
    $roles = array('owner', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

    if( empty($values['auth_view']) ) {
      $values['auth_view'] = key($form->auth_view->options);
      if( empty($values['auth_view']) ) {
        $values['auth_view'] = 'everyone';
      }
    }
    if( empty($values['auth_comment']) ) {
      $values['auth_comment'] = key($form->auth_comment->options);
      if( empty($values['auth_comment']) ) {
        $values['auth_comment'] = 'owner_member';
      }
    }

    $viewMax = array_search($values['auth_view'], $roles);
    $commentMax = array_search($values['auth_comment'], $roles);
    foreach( $roles as $i => $role ) {
      $auth->setAllowed($video, $role, 'view', ($i <= $viewMax));
      $auth->setAllowed($video, $role, 'comment', ($i <= $commentMax));
    }

    // UPDATE VIDEOS COUNT COLUMN
    $channel = Engine_Api::_()->getItem('sitevideo_channel', $values['channel']);
    if( $channel ) {
      $channel->videos_count = $channel->videos_count + count($values['file']);
      $channel->save();
    }
    return $video;
  }

}
