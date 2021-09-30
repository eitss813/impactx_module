<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: sitepage.php 10072 2013-07-24 22:38:42Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitepage_Form_Editservice extends Sitepage_Form_Service
{

  public function init()
  {   
		parent::init();
		$this->setTitle('Edit Service')
				->setDescription('Edit your service');
		$this->submit->setLabel('Save Changes');

		$this->photo
			->setDescription('If not want to change logo then not required to select image')
			->setRequired(false);

   }
    
}
  ?>