<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Editicon.php 6590 2012-18-05 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepage_Form_Admin_Settings_Editicon extends Sitepage_Form_Admin_Settings_Addicon
{
  public function init()
  {
    parent::init();

		$this
		->setTitle('Edit Icon');
	}

}