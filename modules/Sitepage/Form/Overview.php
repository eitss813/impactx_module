<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Overview.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Overview extends Engine_Form {

  public $_error = array();

  public function init() {
    $this->setTitle('Edit Page Overview')
            ->setDescription('Overview enables you to create a rich profile for your Page using the editor below. Compose the overview and click "Save Overview" to save it.')
            ->setAttrib('name', 'sitepages_overview');
    $upload_url = "";
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'spcreate');
    if (!empty($isManageAdmin)) {
      $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => "upload-photo", 'page_id' => $page_id), 'sitepage_dashboard', true);
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
        'filters' => array(new Engine_Filter_Censor()),
    ));

    $this->addElement('Button', 'save', array(
        'label' => 'Save Overview',
        'type' => 'submit',
    ));
  }

}

?>
