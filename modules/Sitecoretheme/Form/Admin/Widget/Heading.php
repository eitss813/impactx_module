<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Heading.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_Admin_Widget_Heading extends Core_Form_Admin_Widget_Standard
{

  public function init()
  {
    parent::init();

    // Set form attributes
    $this
      ->setTitle('Add the Attractive Heading with Descriptions');

    $this->removeElement('title');
    $this->addElement('Text', 'title', array(
      'label' => 'Heading',
      'value' => '',
    ));
    $this->addElement('Textarea', 'description', array(
      'label' => 'Description',
      'value' => '',
    ));
  }

}