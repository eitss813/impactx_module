<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Delete.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Post_Delete extends Engine_Form {

  public function init() {

    $this
            ->setTitle('Delete Post?')
            ->setDescription('Are you sure that you want to delete this post?')
    ;

    $this->addElement('Button', 'submit', array(
        'label' => 'Delete Post',
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