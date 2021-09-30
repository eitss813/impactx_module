<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: messageowner.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="global_form_popup">
  <?php echo $this->form->render($this) ?>
</div>
<style type="text/css">
  /*.global_form div.form-label {
    width: 50px;
  }*/
  .global_form_popup #submit-wrapper, .global_form_popup #cancel-wrapper{
    float:none;
  }
  .global_form input[type="text"] {width:304px;}
</style>