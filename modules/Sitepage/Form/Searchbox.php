<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Searchbox.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepage_Form_Searchbox extends Engine_Form
{
  public function init()
  {
    $this->addElement('Text', 'title', array(
        'label' => '',
        'autocomplete' => 'off',
        'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        ));
    $this->addElement('Hidden', 'page_id', array( 'order' => 400,));   
  }
}
?>