<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: tell-a-friend.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<style>
  /*Page View Print and Tell a friend Popup*/
  .sitealbum_tellafriend_popup {
    margin:15px 0 0 15px;
    width:560px;
  }
  .sitealbum_tellafriend_popup .global_form > div > div {
    width:530px;
    max-height:480px;
    overflow:auto;
    padding:8px !important;
  }
  .sitealbum_tellafriend_popup p.description {
    font-size:11px !important;
  }
  .sitealbum_tellafriend_popup .global_form div.form-element{
    max-width:350px;	
  }
  .sitealbum_tellafriend_popup .global_form input + label{
    width:300px;
  }
</style>
<div class="sitealbum_tellafriend_popup">
  <?php echo $this->form->render($this); ?>
</div>