<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Create.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_Form_Index_Create extends Engine_Form {

  public $_error = array();
  protected $_defaultProfileId;
  protected $_fromApi;
  protected $_smoothboxType;

  public function init() {
  
    $settings = Engine_Api::_()->getApi('settings', 'core');
    if ($this->getSmoothboxType())
      $hideClass = 'sesblog_hideelement_smoothbox';
    else
      $hideClass = '';
      
    $viewer = Engine_Api::_()->user()->getViewer();
    $translate = Zend_Registry::get('Zend_Translate');
    
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $moduleName = $request->getModuleName();
    $controllerName = $request->getControllerName();
    $actionName = $request->getActionName();
    
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesblogpackage') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblogpackage.enable.package', 0)) {
      if (isset($blog)) {
        $package = Engine_Api::_()->getItem('sesblogpackage_package', $blog->package_id);
      } else {
        if ($request->getParam('package_id', 0)) {
          $package = Engine_Api::_()->getItem('sesblogpackage_package', $request->getParam('package_id', 0));
        } elseif ($request->getParam('existing_package_id', 0)) {
          $packageId = Engine_Api::_()->getItem('sesblogpackage_orderspackage', $request->getParam('existing_package_id', 0))->package_id;
          $package = Engine_Api::_()->getItem('sesblogpackage_package', $packageId);
        }
      }
      if (!isset($package)) {
        $packageId = Engine_Api::_()->getDbTable('packages', 'sesblogpackage')->getDefaultPackage();
        $package = Engine_Api::_()->getItem('sesblogpackage_package', $packageId);
      }
      $params = json_decode($package->params, true);
    }

    $this->setTitle('Write New Entry')
        ->setDescription('Compose your new blog entry below, then click "Post" to publish the entry to your blog.')
        ->setAttrib('name', 'sesblogs_create');

    if ($this->getSmoothboxType())
      $this->setAttrib('class', 'global_form sesblog_smoothbox_create');

    $user = Engine_Api::_()->user()->getViewer();
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;

    if (Engine_Api::_()->core()->hasSubject('sesblog_blog'))
      $blog = Engine_Api::_()->core()->getSubject();

    $this->addElement('Text', 'title', array(
      'label' => 'Title',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '224'))
      ),
    ));

    $custom_url_value = isset($blog->custom_url) ? $blog->custom_url : (isset($_POST["custom_url"]) ? $_POST["custom_url"] : "");
    // Custom Url
    $this->addElement('Dummy', 'custom_url_blog', array(
      'label' => 'Custom Url',
      'content' => '<input type="text" name="custom_url" id="custom_url" value="' . $custom_url_value . '"><i class="fa fa-check" id="sesblog_custom_url_correct" style="display:none;"></i><i class="fa fa-times" id="sesblog_custom_url_wrong" style="display:none;"></i><span class="sesblog_check_availability_btn"><img src="application/modules/Core/externals/images/loading.gif" id="sesblog_custom_url_loading" alt="Loading" style="display:none;" /><button id="check_custom_url_availability" type="button" name="check_availability" >Check Availability</button></span> <p id="suggestion_tooltip" class="check_tooltip" style="display:none;">' . $translate->translate("You can use letters, numbers and periods.") . '</p>',
    ));

    // init to
    if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblogcre.enable.tags', 1)) {
      $this->addElement('Text', 'tags', array(
        'label' => 'Tags (Keywords)',
        'autocomplete' => 'off',
        'description' => 'Separate tags with commas.',
        'filters' => array(
          new Engine_Filter_Censor(),
        )
      ));

      $this->tags->getDecorator("Description")->setOption("placement", "append");
    }

    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.start.date', 1)) {

      $this->addElement('Radio', 'show_start_time', array(
        'label' => 'Start Date',
        'description' => '',
        'multiOptions' => array(
          "" => 'Choose Start Date',
          "1" => 'Publish Now',
        ),
        'value' => 1,
        'onclick' => "showStartDate(this.value);",
      ));
      if ($this->getFromApi()) {
        // Start time
        $start = new Engine_Form_Element_Date('starttime');
        $start->setLabel("Start Time");
        $start->setAllowEmpty(true);
        $start->setRequired(false);
        $this->addElement($start);
      }
      if (empty($_POST)) {
        $startDate = date('Y-m-d h:i:s', strtotime(date('Y-m-d h:i:s') . ' + 2 minutes'));
        $start_date = date('m/d/Y', strtotime($startDate));
        $start_time = date('g:ia', strtotime($startDate));

        if ($viewer->timezone) {
          $start = strtotime(date('Y-m-d h:i:s', strtotime(date('Y-m-d h:i:s') . ' + 2 minutes')));
          $selectedTime = "00:02:00";
          $startTime = time() + strtotime($selectedTime);
          $oldTz = date_default_timezone_get();
          date_default_timezone_set($viewer->timezone);
          $start_date = date('m/d/Y', ($start));
          $start_time = date('g:ia', $startTime);
          date_default_timezone_set($oldTz);
        }
      } else {
        $start_date = date('m/d/Y', strtotime($_POST['start_date']));
        $start_time = date('g:ia', strtotime($_POST['start_time']));
      }
      $this->addElement('dummy', 'blog_custom_datetimes', array(
        'decorators' => array(array('ViewScript', array(
          'viewScript' => 'application/modules/Sesblog/views/scripts/_customdates.tpl',
          'class' => 'form element',
          'start_date' => $start_date,
          'start_time' => $start_time,
          'start_time_check' => 1,
          'subject' => isset($blog) ? $blog : '',
        )))
      ));
    }
    
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('enableglocation', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.location', 1)) {
      $locale = Zend_Registry::get('Zend_Translate')->getLocale();
      $territories = Zend_Locale::getTranslationList('territory', $locale, 2);
      asort($territories);
      $countrySelect = '';
      $countrySelected = '';
      if (count($territories)) {
        $countrySelect = '<option value="">Choose Country</option>';
        if (isset($blog)) {
          $itemlocation = Engine_Api::_()->getDbTable('locations', 'sesbasic')->getLocationData('sesblog', $blog->getIdentity());
          if ($itemlocation)
            $countrySelected = $itemlocation->country;
        }
        foreach ($territories as $key => $valCon) {
          if ($valCon == $countrySelected)
            $countrySelect .= '<option value="' . $valCon . '" selected >' . $valCon . '</option>';
          else
            $countrySelect .= '<option value="' . $valCon . '" >' . $valCon . '</option>';
        }
      }
      $this->addElement('dummy', 'blog_location', array(
        'decorators' => array(array('ViewScript', array(
          'viewScript' => 'application/modules/Sesblog/views/scripts/_location.tpl',
          'class' => 'form element',
          'blog' => isset($blog) ? $blog : '',
          'countrySelect' => $countrySelect,
          'itemlocation' => isset($itemlocation) ? $itemlocation : '',
        )))
      ));
    }
    
    if(Engine_Api::_()->getApi('settings', 'core')->getSetting('enableglocation', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog_enable_location', 1) && ((isset($modulesEnable) && array_key_exists('enable_location',$modulesEnable) && $modulesEnable['enable_location']) || empty($modulesEnable))) {
      $this->addElement('Text', 'location', array(
        'label' => 'Location',
        'id' => 'locationSes',
        'filters' => array(
            new Engine_Filter_Censor(),
            new Engine_Filter_HtmlSpecialChars(),
        ),
      ));
      $this->addElement('Text', 'lat', array(
        'label' => 'Lat',
        'id' => 'latSes',
        'filters' => array(
            new Engine_Filter_Censor(),
            new Engine_Filter_HtmlSpecialChars(),
        ),
      ));
      $this->addElement('dummy', 'map-canvas', array());
      $this->addElement('dummy', 'ses_location', array('content'));
      $this->addElement('Text', 'lng', array(
        'label' => 'Lng',
        'id' => 'lngSes',
        'filters' => array(
            new Engine_Filter_Censor(),
            new Engine_Filter_HtmlSpecialChars(),
        ),
      ));
    }

    // prepare categories
    if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblogcre.enb.category', 1)) {
      $categories = Engine_Api::_()->getDbtable('categories', 'sesblog')->getCategoriesAssoc(array('member_levels' => 1));
      if (count($categories) > 0) {
        $categorieEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblogcre.cat.req', '1');
        if ($categorieEnable == 1) {
          $required = true;
          $allowEmpty = false;
        } else {
          $required = false;
          $allowEmpty = true;
        }
        $categories = array('' => '') + $categories;
        // category field
        $this->addElement('Select', 'category_id', array(
          'label' => 'Category',
          'multiOptions' => $categories,
          'allowEmpty' => $allowEmpty,
          'required' => $required,
          'onchange' => "showSubCategory(this.value);",
        ));
        //Add Element: 2nd-level Category
        $this->addElement('Select', 'subcat_id', array(
          'label' => "2nd-level Category",
          'allowEmpty' => true,
          'required' => false,
          'multiOptions' => array('0' => ''),
          'registerInArrayValidator' => false,
          'onchange' => "showSubSubCategory(this.value);"
        ));
        //Add Element: Sub Sub Category
        $this->addElement('Select', 'subsubcat_id', array(
          'label' => "3rd-level Category",
          'allowEmpty' => true,
          'registerInArrayValidator' => false,
          'required' => false,
          'multiOptions' => array('0' => ''),
          'onchange' => 'showCustom(this.value);'
        ));

        if ((isset($package) && $package->custom_fields) || (!isset($package))) {
          $defaultProfileId = "0_0_" . $this->getDefaultProfileId();
          $customFields = new Sesbasic_Form_Custom_Fields(array(
              'packageId' => isset($package) ? $package->package_id : '',
              'resourceType' => 'sesblogpackage_package',
              'item' => 'sesblog_blog',
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
      }
    }

    if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.cre.photo', 1)) {
    
      $blogMainPhoto = true;
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesblogpackage') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblogpackage.enable.package', 0)) {
        if ($request->getParam('package_id', 0))
          $package = Engine_Api::_()->getItem('sesblogpackage_package', $request->getParam('package_id', 0));
        elseif ($request->getParam('existing_package_id', 0)) {
          $packageId = Engine_Api::_()->getItem('sesblogpackage_orderspackage', $request->getParam('existing_package_id', 0))->package_id;
          $package = Engine_Api::_()->getItem('sesblogpackage_package', $packageId);
        }
        if (!isset($package)) {
          $packageId = Engine_Api::_()->getDbTable('packages', 'sesblogpackage')->getDefaultPackage();
          $package = Engine_Api::_()->getItem('sesblogpackage_package', $packageId);
        }
        $params = json_decode($package->params, true);
        if ($params['upload_mainphoto'])
          $blogMainPhoto = true;
        else
          $blogMainPhoto = false;
      }

      $mainPhotoEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.photo.mandatory', '1');
      if ($mainPhotoEnable == 1) {
        $required = true;
        $allowEmpty = false;
      } else {
        $required = false;
        $allowEmpty = true;
      }

      $photouploadoptions = unserialize(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.photouploadoptions', 'a:3:{i:0;s:8:"dragdrop";i:1;s:11:"multiupload";i:2;s:7:"fromurl";}'));

      if($photouploadoptions) {
      
        // Init submit
        if($this->getFromApi() && $blogMainPhoto){
          $this->addElement('File', 'file', array(
            'label' => 'Main Photo',
            'description' => '',
          ));
        }
        
        if ($blogMainPhoto) {

          $this->addElement('Dummy', 'fancyuploadfileids', array('content' => '<input id="fancyuploadfileids" name="file" type="hidden" value="" >'));
          
          $dragdrop = $multiUpload = $fromurl = '';
          if(in_array('dragdrop', $photouploadoptions))
            $dragdrop = '<li class="active sesbm"><i class="fa fa-arrows-alt sesbasic_text_light"></i><a href="javascript:;" class="drag_drop">' . $translate->translate('Drag & Drop') . '</a></li>';
            
          if(in_array('fromurl', $photouploadoptions))
            $fromurl = '<li class=" sesbm"><i class="fa fa-link sesbasic_text_light"></i><a href="javascript:;" class="from_url">' . $translate->translate('From URL') . '</a></li>';
            
          if(in_array('multiupload', $photouploadoptions))
            $multiUpload = '<li class=" sesbm"><i class="fa fa-upload sesbasic_text_light"></i><a href="javascript:;" class="multi_upload">' . $translate->translate('Multi Upload') . '</a></li>';
          
          $this->addElement('Dummy', 'tabs_form_blogcreate', array(
            'label' => 'Upload photos',
            'Description' => 'Image should be ' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.mainheight', 1600) . '* ' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.mainwidth', 1600) . ' px size for better view in website.',
            'content' => '<div class="sesblog_create_form_tabs sesbasic_clearfix sesbm"><ul id="sesblog_create_form_tabs" class="sesbasic_clearfix">'.$dragdrop.$multiUpload.$fromurl.'</ul></div>',
          ));
          
          if(in_array('dragdrop', $photouploadoptions)) {
            $this->addElement('Dummy', 'drag-drop', array(
              'content' => '<div id="dragandrophandler" class="sesblog_upload_dragdrop_content sesbasic_bxs">' . $translate->translate('Drag & Drop Photos Here') . '</div>',
            ));
          }
          
          if(in_array('fromurl', $photouploadoptions)) {
            $this->addElement('Dummy', 'from-url', array(
              'content' => '<div id="from-url" class="sesblog_upload_url_content sesbm"><input type="text" name="from_url" id="from_url_upload" value="" placeholder="' . $translate->translate('Enter Image URL to upload') . '"><span id="loading_image"></span><span></span><button id="upload_from_url">' . $translate->translate('Upload') . '</button></div>',
            ));
          }

          $this->addElement('Dummy', 'file_multi', array('content' => '<input type="file" accept="image/x-png,image/jpeg" onchange="readImageUrl(this)" multiple="multiple" id="file_multi" name="file_multi">'));
          
          $this->addElement('Dummy', 'uploadFileContainer', array('content' => '<div id="show_photo_container" class="sesblog_upload_photos_container sesbasic_bxs sesbasic_custom_scroll clear"><div id="show_photo"></div></div>'));
          
        } else if($blogMainPhoto) {
          //make main photo upload btn
          $this->addElement('File', 'photo_file', array(
            'label' => 'Main Photo',
            'required' => $required,
            'allowEmpty' => $allowEmpty,
          ));
          $this->photo_file->addValidator('Extension', false, 'jpg,png,gif,jpeg');
        }
      }
    }

    $allowed_html = Engine_Api::_()->authorization()->getPermission($user_level, 'sesblog_blog', 'auth_html');
    $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sesbasic', 'controller' => 'index', 'action' => "upload-image"), 'default', true);
    $editorOptions = array(
      'upload_url' => $upload_url,
      'html' => true,
      'extended_valid_elements' => $allowed_html,
    );
    
    if (!empty($upload_url)) {
      $editorOptions['plugins'] = array(
        'table', 'fullscreen', 'media', 'preview', 'paste',
        'code', 'image', 'textcolor', 'jbimages', 'link'
      );
      $editorOptions['toolbar1'] = array(
        'undo', 'redo', 'removeformat', 'pastetext', '|', 'code',
        'media', 'image', 'jbimages', 'link', 'fullscreen',
        'preview'
      );
      $editorOptions['toolbar2'] = array(
        'fontselect', 'fontsizeselect', 'bold', 'italic', 'underline', 'strikethrough', 'forecolor', 'backcolor', '|', 'alignleft', 'aligncenter', 'alignright', 'alignjustify', '|', 'bullist', 'numlist', '|', 'outdent', 'indent', 'blockquote',
      );
    }

    if ((isset($modulesEnable) && array_key_exists('enable_tinymce', $modulesEnable) && $modulesEnable['enable_tinymce']) || empty($modulesEnable)) {
      $textarea = 'TinyMce';
    } else
      $textarea = 'Textarea';


    $descriptionMan =Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblogcre.des.req', '1');
    if ($descriptionMan == 1) {
      $required = true;
      $allowEmpty = false;
    } else {
      $required = false;
      $allowEmpty = true;
    }

    if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblogcre.enb.des', '1')) {

      $this->addElement($textarea, 'body', array(
        'label' => 'Blog Description',
        'required' => $required,
        'allowEmpty' => $allowEmpty,
        'class' => 'tinymce',
        'editorOptions' => $editorOptions,
      ));
    }
    if (Engine_Api::_()->authorization()->isAllowed('sesblog_blog', $viewer, 'allow_levels')) {

      $levelOptions = array();
      $levelValues = array();
      foreach (Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $level) {
//             if($level->getTitle() == 'Public')
//                 continue;
        $levelOptions[$level->level_id] = $level->getTitle();
        $levelValues[] = $level->level_id;
      }
      // Select Member Levels
      $this->addElement('multiselect', 'levels', array(
        'label' => 'Member Levels',
        'multiOptions' => $levelOptions,
        'description' => 'Choose the Member Levels to which this Blog will be displayed. (Note: Hold down the CTRL key to select or de-select specific member levels.)',
        'value' => $levelValues,
      ));
    }

    if (Engine_Api::_()->authorization()->isAllowed('sesblog_blog', $viewer, 'allow_network')) {
      $networkOptions = array();
      $networkValues = array();
      foreach (Engine_Api::_()->getDbTable('networks', 'network')->fetchAll() as $network) {
        $networkOptions[$network->network_id] = $network->getTitle();
        $networkValues[] = $network->network_id;
      }

      // Select Networks
      $this->addElement('multiselect', 'networks', array(
        'label' => 'Networks',
        'multiOptions' => $networkOptions,
        'description' => 'Choose the Networks to which this Page will be displayed. (Note: Hold down the CTRL key to select or de-select specific networks.)',
        'value' => $networkValues,
      ));
    }

      if (Engine_Api::_()->authorization()->isAllowed('sesblog_blog', $viewer, 'sesblog_endes')) {

      $chooselayout = serialize(Engine_Api::_()->authorization()->getPermission(Engine_Api::_()->user()->getViewer()->level_id, 'sesblog_blog', 'sesblog_cholay'));
      $chooselayoutVal_str = unserialize($chooselayout);
        $chooselayoutVal_str=str_replace('[','',$chooselayoutVal_str);
        $chooselayoutVal_str=str_replace(']','',$chooselayoutVal_str);
        $chooselayoutVal_str=str_replace('"','',$chooselayoutVal_str);
      if(!empty($chooselayoutVal_str)) {
        $chooselayoutVal = explode(',', $chooselayoutVal_str);
      }
      else
      {
        $chooselayoutVal[]=1;
      }

      $designoptions = array();
      if (in_array(1, $chooselayoutVal)) {
        $designoptions[1] = '<a href="" onclick="showPreview(1);return false;"><img src="./application/modules/Sesblog/externals/images/layout_1.jpg" alt="" /></a> ' . $translate->translate("Design 1");
      }
      if (in_array(2, $chooselayoutVal)) {
        $designoptions[2] = '<a href="" onclick="showPreview(2);return false;"><img src="./application/modules/Sesblog/externals/images/layout_2.jpg" alt="" /></a> ' . $translate->translate("Design 2");
      }
      if (in_array(3, $chooselayoutVal)) {
        $designoptions[3] = '<a href="" onclick="showPreview(3);return false;"><img src="./application/modules/Sesblog/externals/images/layout_3.jpg" alt="" /></a> ' . $translate->translate("Design 3");
      }
      if (in_array(4, $chooselayoutVal)) {
        $designoptions[4] = '<a href="" onclick="showPreview(4);return false;"><img src="./application/modules/Sesblog/externals/images/layout_4.jpg" alt="" /></a> ' . $translate->translate("Design 4");
      }


      $this->addElement('Radio', 'blogstyle', array(
        'label' => 'Blog Layout',
        'description' => 'Set Your Blog Template',
        'multiOptions' => $designoptions,
        'escape' => false,
        'value' => $chooselayoutVal[0],
      ));
    } else {
      $this->addElement('Hidden', 'blogstyle', array(
        'value' =>Engine_Api::_()->authorization()->getPermission(Engine_Api::_()->user()->getViewer()->level_id, 'sesblog_blog', 'sesblog_deflay'),
      ));
    }
    if (Engine_Api::_()->authorization()->isAllowed('sesblog_blog', Engine_Api::_()->user()->getViewer(), 'cotinuereading')) {
      $this->addElement('Radio', 'cotinuereading', array(
        'label' => 'Enable Continue Reading Button',
        'description' => "Do you want to enable 'Continue Reading' button for your Blog?",
        'multiOptions' => array(
          '1' => 'Yes',
          '0' => 'No',
        ),
        'onchange' => 'showHideHeight(this.value)',
        'value' => '1',
      ));
      $this->addElement('Text', 'continue_height', array(
        'label' => 'Enter Truncation limit',
        'description' => 'Enter the truncation limit after you want to show continue reading button. 0 for unlimited.',
        'value' => '0'
      ));
    } else {
      if (Engine_Api::_()->authorization()->isAllowed('sesblog_blog', Engine_Api::_()->user()->getViewer(), 'cntrdng_dflt')) {
        $val = 1;
        $continue_height = Engine_Api::_()->authorization()->getPermission(Engine_Api::_()->user()->getViewer()->level_id, 'sesblog_blog', 'continue_height');
      } else {
        $val = 0;
        $continue_height = 0;
      }
      $this->addElement('Hidden', 'cotinuereading', array(
        'value' => $val,
        'order' => 9878
      ));
      $this->addElement('Hidden', 'continue_height', array(
        'value' => $continue_height,
        'order' => 9879
      ));
    }


    if($settings->getSetting('sesblogcre.people.search', 1)) {
      $this->addElement('Checkbox', 'search', array(
        'label' => 'Show this blog entry in search results',
        'value' => 1,
      ));
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
    $viewOptions = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sesblog_blog', $user, 'auth_view');
    $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));

    if (!empty($viewOptions) && count($viewOptions) >= 1) {
      // Make a hidden field
      if (count($viewOptions) == 1) {
        $this->addElement('hidden', 'auth_view', array('value' => key($viewOptions), 'order' => 7852569));
        // Make select box
      } else {
        $this->addElement('Select', 'auth_view', array(
          'label' => 'Privacy',
          'description' => 'Who may see this blog entry?',
          'multiOptions' => $viewOptions,
          'value' => key($viewOptions),
          'class' => $hideClass,
        ));
        $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
      }
    }

    // Element: auth_comment
    $commentOptions = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sesblog_blog', $user, 'auth_comment');
    $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));

    if (!empty($commentOptions) && count($commentOptions) >= 1) {
      // Make a hidden field
      if (count($commentOptions) == 1) {
        $this->addElement('hidden', 'auth_comment', array('value' => key($commentOptions), 'order' => 712569));
        // Make select box
      } else {
        $this->addElement('Select', 'auth_comment', array(
          'label' => 'Comment Privacy',
          'description' => 'Who may post comments on this blog entry?',
          'multiOptions' => $commentOptions,
          'value' => key($commentOptions),
          'class' => $hideClass,
        ));
        $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
      }
    }
    $this->addElement('Select', 'draft', array(
      'label' => 'Status',
      'multiOptions' => array("" => "Published", "1" => "Saved As Draft"),
      'description' => 'If this entry is published, it cannot be switched back to draft mode.',
      'class' => $hideClass,
    ));
    $this->draft->getDecorator('Description')->setOption('placement', 'append');

    $this->addElement('Button', 'POST', array(
      'type' => 'submit',
    ));

    // Element: submit
    //$this->addElement('Button', 'submit', array(
      //'label' => 'Post Entry',
     // 'type' => 'submit',
      //'ignore' => true,
      //'decorators' => array(
       // 'ViewHelper',
      //),
    //));

    if ($this->getSmoothboxType()) {
      $this->addElement('Cancel', 'advanced_sesblogoptions', array(
        'label' => 'Show Advanced Settings',
        'link' => true,
        'class' => 'active',
        'href' => 'javascript:;',
        'onclick' => 'return false;',
        'decorators' => array(
          'ViewHelper'
        )
      ));
      $this->addElement('Dummy', 'brtag', array(
        'content' => '<span style="margin-top:5px;"></span>',
      ));
      $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'href' => '',
        'prependText' => ' or ',
        'onclick' => 'sessmoothboxclose();',
        'decorators' => array(
          'ViewHelper'
        )
      ));
      $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
        'decorators' => array(
          'FormElements',
          'DivDivDivWrapper',
        ),
      ));
    }
  }

  public function getSmoothboxType()
  {
    return $this->_smoothboxType;
  }

  public function setSmoothboxType($smoothboxType)
  {
    $this->_smoothboxType = $smoothboxType;
    return $this;
  }

  public function getFromApi()
  {
    return $this->_fromApi;
  }

  public function setFromApi($fromApi)
  {
    $this->_fromApi = $fromApi;
    return $this;
  }

  public function getDefaultProfileId()
  {
    return $this->_defaultProfileId;
  }

  public function setDefaultProfileId($default_profile_id)
  {
    $this->_defaultProfileId = $default_profile_id;
    return $this;
  }
}
