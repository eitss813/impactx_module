<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Searchtagcloud.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Searchtagcloud extends Engine_Form {

  public function init() {
    $this
            ->setAttribs(array(
                'id' => 'filter_form_tagscloud',
                'class' => 'global_form_box_tagscloud',
            ))
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index'), 'sitepage_general'));

    $this->addElement('Hidden', 'tag', array(
        'order' => 200
    ));
  }

}

?>