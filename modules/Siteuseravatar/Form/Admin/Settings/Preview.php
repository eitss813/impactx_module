<?php

/**
 * SocialEngine
 *
 * @category   Application_Module
 * @package    Siteuseravatar
 * @copyright  Copyright 2017-2018 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Global.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteuseravatar_Form_Admin_Settings_Preview extends Siteuseravatar_Form_Admin_Settings_Global
{
  public function init()
  {
    $this->addElement('Text', 'name', array(
      'label' => 'Preview Initials',
      'description' => "Enter the initials which you want to look in preivew.",
      'required' => true,
      'allowEmpty' => false,
      'value' => 'GARRISON UMBERTO'
    ));
    parent::init();
    $this->setTitle('Check the Preview of Avatar Initials')
      ->setDescription('These settings are use only for preview purpose.')
    ;
    $this->save->setLabel('Generate Preview');
  }

}
