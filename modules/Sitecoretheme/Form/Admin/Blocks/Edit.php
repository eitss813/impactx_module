<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_Admin_Blocks_Edit extends Sitecoretheme_Form_Admin_Blocks_Create
{
  public function init()
  {
    parent::init();
    $this->setTitle('Edit Block');
    $this->setDescription('Follow this guide to design block.');

    $this->submit->setLabel('Save Changes');
  }
}