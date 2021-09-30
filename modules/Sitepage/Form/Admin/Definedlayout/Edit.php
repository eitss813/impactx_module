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

class Sitepage_Form_Admin_Definedlayout_Edit extends Sitepage_Form_Admin_Definedlayout_Create
{

  public function init()
  {   
    parent::init();
    $this->setTitle('Edit Layout')
       ->setDescription('Edit your layout');
    $this->submit->setLabel('Save Changes');
    $this->photo->setOptions(array('required' => false));
    $this->removeElement('duplicate');
   }
    
}
  ?>