<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Myaminpages.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Myadminpages extends Engine_Form {

  public function init() {

    $this
            ->setAttribs(array(
                'id' => 'filter_form'
            ));

    $this->addElement('Hidden', 'page', array(
        'order' => 10001,
    ));

    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
  }

}

?>