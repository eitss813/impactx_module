<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Takeaction.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Admin_Takeaction extends Engine_Form {

  public function init() {

    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $url = $view->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($page_id)), 'sitepage_entry_view', true);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    $sitepage_title = "<a href='$url' target='_blank'>$sitepage->title</a>";

    $this->setMethod('post');
    $this->setTitle("Take an Action")
            ->setDescription("Please take an appropriate action for this page:" . $sitepage_title);

    $this->addElement('Button', 'submit', array(
        'label' => 'Save',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}

?>