<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Photos.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Album_Photos extends Engine_Form {

  public function init() {

    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
    $this->addElement('Radio', 'cover', array(
        'label' => 'Album Cover',
    ));
    
//    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
//			$this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
//			$this->addElement('Radio', 'page_cover', array(
//					'label' => 'Make Cover Photo',
//			));
//    }
  }

}

?>