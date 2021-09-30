<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Edit.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_Form_Edit extends Engine_Form
{
  protected $_defaultProfileId;
  protected $_fromApi;
  public function getFromApi() {
    return $this->_fromApi;
  }
  public function setFromApi($fromApi) {
    $this->_fromApi = $fromApi;
    return $this;
  }

  public function getDefaultProfileId() {
    return $this->_defaultProfileId;
  }

  public function setDefaultProfileId($default_profile_id) {
    $this->_defaultProfileId = $default_profile_id;
    return $this;
  }

  public function init() {

    $translate = Zend_Registry::get('Zend_Translate');

    if (Engine_Api::_()->core()->hasSubject('sesblog_blog'))
        $blog = Engine_Api::_()->core()->getSubject();
        
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

    $this->setTitle('Edit Blog Entry')
      ->setDescription('Edit your entry below, then click "Save Changes" to publish the entry on your blog.')->setAttrib('name', 'sesblogs_edit');
    $user = Engine_Api::_()->user()->getViewer();
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;

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
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblogcre.ecust.url', 0)) {
      // Custom Url
      $this->addElement('Dummy', 'custom_url_blog', array(
        'label' => 'Custom Url',
        'content' => '<input type="text" name="custom_url" id="custom_url" value="' . $custom_url_value . '"><i class="fa fa-check" id="sesblog_custom_url_correct" style="display:none;"></i><i class="fa fa-times" id="sesblog_custom_url_wrong" style="display:none;"></i><span class="sesblog_check_availability_btn"><img src="application/modules/Core/externals/images/loading.gif" id="sesblog_custom_url_loading" alt="Loading" style="display:none;" /><button id="check_custom_url_availability"  type="button" name="check_availability" >Check Availability</button></span> <p id="suggestion_tooltip" class="check_tooltip" style="display:none;">' . $translate->translate("You can use letters, numbers and periods.") . '</p>',
      ));
    }

    // init to
    if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblogcre.enable.tags', 1)) {
      $this->addElement('Text', 'tags', array(
        'label' => 'Tags (Keywords)',
        'autocomplete' => 'off',
        'description' => 'Separate tags with commas.',
        'filters' => array(
          new Engine_Filter_Censor(),
        ),
      ));
      $this->tags->getDecorator("Description")->setOption("placement", "append");
    }
    if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.start.date', 1)) {
			if(isset($blog)){
				$start = strtotime($blog->publish_date);
				$start_date = date('m/d/Y',($start));
				$start_time = date('g:ia',($start));
				$viewer = Engine_Api::_()->user()->getViewer();
				$publishDate = $start_date.' '.$start_time;
				if($viewer->timezone){
					$start = strtotime($blog->publish_date);
					$oldTz = date_default_timezone_get();
					date_default_timezone_set($viewer->timezone);
					$start_date = date('m/d/Y',($start));
          $start_date_y = date('Y',strtotime($start_date));
          $start_date_m = date('m',strtotime($start_date));
          $start_date_d = date('d',strtotime($start_date));
					$start_time = date('g:ia',($start));
					date_default_timezone_set($oldTz);
				}
			}
			if(isset($blog) && $blog->publish_date != '' && strtotime($publishDate) > time()){
				$this->addElement('dummy', 'blog_custom_datetimes', array(
						'decorators' => array(array('ViewScript', array(
						'viewScript' => 'application/modules/Sesblog/views/scripts/_customdates.tpl',
						'class' => 'form element',
						'start_date'=>$start_date,
						'start_time'=>$start_time,
						'start_time_check'=>1,
						'subject'=> '',
				  )))
				));
			  if($this->getFromApi()){
          // Start time
          $start = new Engine_Form_Element_Date('starttime');
          $start->setLabel("Start Time");
          if(!empty($start_date_y)){
            $start_date_cal = array('year'=>$start_date_y,'month'=>$start_date_m,'day'=>$start_date_d);
            $start->setValue($start_date_cal);
          }
          $start->setAllowEmpty(true);
          $start->setRequired(false);
          $this->addElement($start);
        }
      }
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
          $itemlocation = Engine_Api::_()->getDbTable('locations', 'sesbasic')->getLocationData('sesblog_blog', $blog->getIdentity());
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
      $categories = Engine_Api::_()->getDbtable('categories', 'sesblog')->getCategoriesAssoc(array('member_level' => 1));
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


        $sesblog = Engine_Api::_()->core()->getSubject();
        // General form w/o profile type
        $aliasedFields = $sesblog->fields()->getFieldsObjectsByAlias();
        $topLevelId = $topLevelId = 0;
        $topLevelValue = $topLevelValue = null;

        if (isset($aliasedFields['profile_type'])) {
          $aliasedFieldValue = $aliasedFields['profile_type']->getValue($sesblog);
          $topLevelId = $aliasedFields['profile_type']->field_id;
          $topLevelValue = (is_object($aliasedFieldValue) ? $aliasedFieldValue->value : null);
          if (!$topLevelId || !$topLevelValue) {
            $topLevelId = null;
            $topLevelValue = null;
          }
          $topLevelId = $topLevelId;
          $topLevelValue = $topLevelValue;
        }
        // Get category map form data
        $defaultProfileId = "0_0_" . $this->getDefaultProfileId();
        $customFields = new Sesblog_Form_Custom_Fields(array(
          'item' => Engine_Api::_()->core()->getSubject(),
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

    if($this->getFromApi()){
      $this->addElement('File', 'file', array(
        'label' => 'Main Photo',
        'description' => '',
      ));
    }
    
    if(((isset($modulesEnable) && array_key_exists('modules',$modulesEnable) && in_array('photo',$modulesEnable['modules'])) || empty($modulesEnable))){
			//silence
		}else{
			// Photo
			$this->addElement('File', 'photo_file', array(
				'label' => 'Main Photo'
			));
			$this->photo_file->addValidator('Extension', false, 'jpg,png,gif,jpeg');

		}

    $allowed_html = Engine_Api::_()->authorization()->getPermission($user_level, 'sesblog_blog', 'auth_html');
    $upload_url = "";
    if(Engine_Api::_()->authorization()->isAllowed('album', $user, 'create')){
      $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sesbasic', 'controller' => 'index', 'action' => "upload-image"), 'default', true);
    }

    $editorOptions = array(
      'upload_url' => $upload_url,
      'html' => true,
      'extended_valid_elements'=>$allowed_html,
    );

    if (!empty($upload_url))
    {
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
        'fontselect','fontsizeselect','bold','italic','underline','strikethrough','forecolor','backcolor','|','alignleft','aligncenter','alignright','alignjustify','|','bullist','numlist','|','outdent','indent','blockquote',
      );
    }

    if((isset($modulesEnable) && array_key_exists('enable_tinymce',$modulesEnable) && $modulesEnable['enable_tinymce']) || empty($modulesEnable)) {
			$textarea = 'TinyMce';
		}else
			$textarea = 'Textarea';

    $descriptionMan= Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblogcre.des.req', '1');
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
      if(in_array(1, $chooselayoutVal)) {
        $designoptions[1] = '<a href="" onclick="showPreview(1);return false;"><img src="./application/modules/Sesblog/externals/images/layout_1.jpg" alt="" /></a> '.$translate->translate("Design 1");
      }
      if(in_array(2, $chooselayoutVal)) {
        $designoptions[2] = '<a href="" onclick="showPreview(2);return false;"><img src="./application/modules/Sesblog/externals/images/layout_2.jpg" alt="" /></a> '.$translate->translate("Design 2");
      }
      if(in_array(3, $chooselayoutVal)) {
        $designoptions[3] = '<a href="" onclick="showPreview(3);return false;"><img src="./application/modules/Sesblog/externals/images/layout_3.jpg" alt="" /></a> '.$translate->translate("Design 3");
      }
      if(in_array(4, $chooselayoutVal)) {
        $designoptions[4] = '<a href="" onclick="showPreview(4);return false;"><img src="./application/modules/Sesblog/externals/images/layout_4.jpg" alt="" /></a> '.$translate->translate("Design 4");
      }

      $this->addElement('Radio', 'blogstyle', array(
        'label' => 'Blog Layout',
        'description' => 'Set Your Blog Template',
        'multiOptions' => $designoptions,
        'escape' => false,
      ));
		} else {
      $this->addElement('Hidden', 'blogstyle', array(
        'value' =>Engine_Api::_()->authorization()->getPermission(Engine_Api::_()->user()->getViewer()->level_id, 'sesblog_blog', 'sesblog_deflay'),
      ));
		}
		if(Engine_Api::_()->authorization()->isAllowed('sesblog_blog', Engine_Api::_()->user()->getViewer(), 'cotinuereading')){
			 $this->addElement('Radio', 'cotinuereading', array(
				'label' => 'Enable Continue Reading Button',
				'description' => "Do you want to enable 'Continue Reading' button for your Blog?",
				'multiOptions' => array(
					'1'=>'Yes',
					'0'=>'No',
				),
				'onchange' => 'showHideHeight(this.value)',
				'value'=>'1',
        ));
        $this->addElement('Text', 'continue_height', array(
            'label' => 'Enter Truncation limit',
            'description' => 'Enter the truncation limit after you want to show continue reading button. 0 for unlimited.',
            'value' => '0'
        ));
		}else{
			if(Engine_Api::_()->authorization()->isAllowed('sesblog_blog', Engine_Api::_()->user()->getViewer(), 'cntrdng_dflt')){
				$val = 1;
				$continue_height = Engine_Api::_()->authorization()->getPermission(Engine_Api::_()->user()->getViewer()->level_id, 'sesblog_blog', 'continue_height');
			}else{
				$val = 0;
				$continue_height = 0;
			}
			$this->addElement('Hidden', 'cotinuereading', array(
                'value' => $val,
                'order' => '8765'
			));
			$this->addElement('Hidden', 'continue_height', array(
                'value' => $continue_height,
                'order'=>9879
			));
		}

    $this->addElement('Checkbox', 'search', array(
      'label' => 'Show this blog entry in search results',
      'value' => 1,
    ));

    $availableLabels = array(
      'everyone'            => 'Everyone',
      'registered'          => 'All Registered Members',
      'owner_network'       => 'Friends and Networks',
      'owner_member_member' => 'Friends of Friends',
      'owner_member'        => 'Friends Only',
      'owner'               => 'Just Me'
    );

    // Element: auth_view
    $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sesblog_blog', $user, 'auth_view');
    $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));

    if( !empty($viewOptions) && count($viewOptions) >= 1 ) {
      // Make a hidden field
      if(count($viewOptions) == 1) {
        $this->addElement('hidden', 'auth_view', array('value' => key($viewOptions), 'order' => 7852569));
      // Make select box
      } else {
        $this->addElement('Select', 'auth_view', array(
            'label' => 'Privacy',
            'description' => 'Who may see this blog entry?',
            'multiOptions' => $viewOptions,
            'value' => key($viewOptions),
        ));
        $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
      }
    }

    // Element: auth_comment
    $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sesblog_blog', $user, 'auth_comment');
    $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));

    if( !empty($commentOptions) && count($commentOptions) >= 1 ) {
      // Make a hidden field
      if(count($commentOptions) == 1) {
        $this->addElement('hidden', 'auth_comment', array('value' => key($commentOptions), 'order' => 1852569));
      // Make select box
      } else {
        $this->addElement('Select', 'auth_comment', array(
            'label' => 'Comment Privacy',
            'description' => 'Who may post comments on this blog entry?',
            'multiOptions' => $commentOptions,
            'value' => key($commentOptions),
        ));
        $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
      }
    }
    $this->addElement('Select', 'draft', array(
      'label' => 'Status',
      'multiOptions' => array(""=>"Published", "1"=>"Saved As Draft"),
      'description' => 'If this entry is published, it cannot be switched back to draft mode.'
    ));
		$this->draft->getDecorator('Description')->setOption('placement', 'append');
    // Element: submit
  // Element: execute
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
    ));
  }
}
