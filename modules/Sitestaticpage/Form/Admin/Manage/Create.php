<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestaticpage_Form_Admin_Manage_Create extends Engine_Form {

  public function init() {

    $this->loadDefaultDecorators();
    $this->setTitle('Create New Static Page / HTML Block')
            ->setDescription('Create a new static page or HTML block from here using the feature-rich WYSIWYG editor. You can embed a form in this page, configure the page URL, choose member levels to which this page should be visible, add link for this page in the navigation menus, and more.<br />
To add a link for static page to other menus, you can easily do so from the Menu Editor. Please note that if you would like to edit or change the embedded form, then you will have to edit the page, then remove the form from it, and then re-embed a new form.<br /> You can also choose to create static page or HTML Block by using the setting “Static Page or HTML Block” below. After creating an HTML Block, you can choose its content to be shown as an HTML Block using the “Static HTML Block” widget.');

    $this->getDecorator('Description')->setOption('escape', false);
    $filter = new Engine_Filter_Html();
    $level_id = Engine_Api::_()->user()->getViewer()->level_id;

    $sitestaticpage_api = Engine_Api::_()->sitestaticpage();
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $multilanguage_allow = $settings->getSetting('sitestaticpage.multilanguage', 0);
    $defaultLanguage = $settings->getSetting('core.locale.locale', 'en');
    $languages = $settings->getSetting('sitestaticpage.languages');
    if(!empty($languages))
    $total_allowed_languages = Count($languages);

    $this->addElement('Select', 'type', array(
        'label' => 'Static Page or HTML Block',
        'description' => 'Do you want to create a Static Page or an HTML Block?',
        'order' => -10001,
        'multiOptions' => array(
            1 => 'HTML Block',
            0 => 'Static Page'
        ),
        'value' => 0,
    ));

    $this->addElement('Text', "title", array(
        'label' => 'Title',
        'description' => 'This Title is for indicative purpose only and will not be visible to users.',
        'order' => -10000,
        'required' => true,
        'allowEmpty' => false,
        'filters' => array(
            'StringTrim',
        ),
    ));
    if (empty($total_allowed_languages)) {
      $languages = array( );
      $languages[$defaultLanguage] = 'English';
    }
    $localeMultiOptions = $sitestaticpage_api->getLanguageArray();
    $order = -9996;
    foreach ($languages as $label) {

      $lang_name = $label;
      if (isset($localeMultiOptions[$label])) {
        $lang_name = $localeMultiOptions[$label];
      }

      $required_title_body = false;
      $allowEmpty_title_body = true;
      if ($defaultLanguage == $label || empty($multilanguage_allow) || $total_allowed_languages == 1) {
        $required_title_body = true;
        $allowEmpty_title_body = false;
      }

      $title_field = "title_$label";
      $body_field = "body_$label";

      $title_label = sprintf("Page Title in %s", $lang_name);
      $body_label = sprintf("Description in %s", $lang_name);

      if (empty($multilanguage_allow) || (!empty($multilanguage_allow) && $total_allowed_languages <= 1)) {
        $title_field = "title";
        $body_field = "body";
        $title_label = "Page Title";
        $body_label = "Description";
      } elseif ($label == 'en' && $total_allowed_languages > 1) {
        $title_field = "title";
        $body_field = "body";
      }

      $title_order = $order++;
      $body_order = $order++;
      if (((!empty($multilanguage_allow) && $total_allowed_languages > 1 && $defaultLanguage == $label)) || ($title_field == 'title' && !in_array($defaultLanguage, $languages))) {
        $title_order = -9999;
        $body_order = -9998;
      }
      
      $this->addElement('TinyMce', "$body_field", array(
          'label' => $body_label,
          'description' => 'Click on the Fullscreen icon in the Editor to get the editor in fullscreen mode, thus enabling you to create your content better.',
          'required' => $required_title_body,
          'allowEmpty' => $allowEmpty_title_body,
          'order' => $body_order,
          'attribs' => array('rows' => 180, 'cols' => 350, 'style' => 'width:740px; max-width:740px;height:858px;'),
          'editorOptions' => Engine_Api::_()->seaocore()->tinymceEditorOptions(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sitestaticpage', 'controller' => 'manage', 'action' => "upload-photo"), 'admin_default', true)),
          'filters' => array(new Engine_Filter_Censor(), $filter),
      ));
    }

    $this->addElement('Radio', 'short_url', array(
        'label' => 'Short URL',
        'description' => 'Do you want the Page URLs to be shortened.',
        'attribs' => array('style' => 'max-height:100px; '),
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => 0,
    ));
    $front = Zend_Controller_Front::getInstance();
    $default_url = $settings->getSetting('sitestaticpage.manifestUrl', 'static');
    $baseUrl = $front->getBaseUrl();
    $URL_COMPONENT = "URL-COMPONENT";
    $link = $_SERVER['HTTP_HOST'] . $baseUrl.'/' . $default_url . '/' . $URL_COMPONENT;
    $description = sprintf("This will be the end of your page URL. It should be indicative of the title and can contain alphabets, numbers, underscores and  dashes only. Its length should be in the range of 3-255 characters.The complete URL of your page will be: %s", "<span id='page_url_address'>http://$link</span>");
    $this->addElement('Text', 'page_url', array(
        'label' => 'URL Component',
        'description' => $description,
        'validators' => array(
            array('NotEmpty', true),
            array('StringLength', true, array(3, 255)),
            array('Regex', true, array('/^[a-zA-Z0-9-_]+$/')),
        ),
    ));
    $this->page_url->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
    $this->page_url->getValidator('NotEmpty')->setMessage('Please enter a valid page url.', 'isEmpty');
    $this->page_url->getValidator('Regex')->setMessage('Profile addresses must start with a letter.', 'regexNotMatch');
    $this->addElement('dummy', 'page_url_msg', array('value' => 0));

    // Element: Member Leves
    $multiOptions = array('0' => 'All Levels');
    foreach (Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $level) {
      $multiOptions[$level->getIdentity()] = $level->getTitle();
    }
    $this->addElement('Multiselect', 'level_id', array(
        'label' => 'Member Levels',
        'description' => 'Select the Member Levels to which this Static Page should be available. Only users belonging to the selected Member Levels will be able to view this.Use CTRL-click to select or deselect multiple Member Levels.',
        'attribs' => array('style' => 'max-height:100px; '),
        'multiOptions' => $multiOptions,
        'value' => 0,
    ));


    $networksOptions = $sitestaticpage_api->getNetworks();

    $this->addElement('Multiselect', 'networks', array(
        'label' => 'Networks Selection',
        'description' => 'Select the networks, members of which will have this Static Page available. Use CTRL-click to select or deselect multiple Networks.',
        'multiOptions' => $networksOptions,
        'value' => 0,
    ));

    $this->addElement('Radio', 'menu', array(
        'label' => 'Add Navigation Link',
        'description' => 'You can automatically add a link for this static page in one of the below navigation menus. Choose a navigation menu and mention the Link Title below.',
        'multiOptions' => array(
            '0' => 'Main Navigation Menu',
            '1' => 'Mini Navigation Menu',
            '2' => 'Footer Navigation Menu',
            '3' => 'None of these'
        ),
        'value' => 3,
    ));

    $this->addElement('Text', "link_title", array(
        'label' => 'Link Title',
        'description' => 'Enter the title for the link.',
    ));

    // Element: Checkbox widgetized
    $this->addElement('Checkbox', 'page_widget', array(
        'description' => "Create Widgetized Page",
        'label' => 'Yes, create a widgetized page for this static page.',
        'value' => 0,
    ));

    $this->addElement('Text', "page_title", array(
        'label' => 'Page Title (HTML title tag for SEO)',
    ));

    $this->addElement('Text', "page_description", array(
        'label' => 'Page Description (HTML meta tag for SEO)',
    ));

    $this->addElement('Text', "keywords", array(
        'label' => 'Page Keywords (comma separated - for SEO)',
    ));
    
    $this->addElement('Checkbox', 'search', array(
        'description' => 'Show this page in search results?',
        'label' => 'Yes, display this page entry in Global Search.',
        'value' => 0,
    ));

    // Element: Button Create Page
    $this->addElement('Button', 'submit', array(
        'label' => 'Create',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper'),
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'prependText' => ' or ',
        'ignore' => true,
        'link' => true,
        'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index')),
        'decorators' => array('ViewHelper'),
    ));
  }

}
