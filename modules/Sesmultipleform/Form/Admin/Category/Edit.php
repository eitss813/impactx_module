<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Edit.php 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesmultipleform_Form_Admin_Category_Edit extends Sesmultipleform_Form_Admin_Category_Add {

  public function init() {
    parent::init();
    $this->submit->setLabel('Save Changes');
  }

}
