<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Post_Edit extends Engine_Form {

  public function init() {

    $this
            ->setTitle('Edit Post');


    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.tinymceditor', 1)) {
      $upload_url = "";
      $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
      $sitepagealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum');
      $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'spcreate');
      if ($sitepagealbumEnabled && (!empty($isManageAdmin) || Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit'))) {
        $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => "upload-photo", 'page_id' => $page_id, 'special' => 'discussions'), 'sitepage_dashboard', true);
      }
      /* Some time overview is not work so use body
       */
      // Overview
      $this->addElement('TinyMce', 'body', array(
          'label' => '',
          //             'required' => true,
          'allowEmpty' => false,
          'attribs' => array('rows' => 180, 'cols' => 350, 'style' => 'width:740px; max-width:740px;height:858px;'),

          'editorOptions' => Engine_Api::_()->seaocore()->tinymceEditorOptions($upload_url),
          'filters' => array(
              new Engine_Filter_Censor(),
              new Engine_Filter_Html(array('AllowedTags' => "strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr, iframe"))),
      ));
    } else {
      $this->addElement('textarea', 'body', array(
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
          )
      ));
    }

    $this->addElement('Button', 'submit', array(
        'label' => 'Edit Post',
        'ignore' => true,
        'type' => 'submit',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'prependText' => ' or ',
        'type' => 'link',
        'link' => true,
        'onclick' => 'parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }

}

?>