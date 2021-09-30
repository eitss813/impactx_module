<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: tell-a-friend.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<style>
    /*Page View Print and Tell a friend Popup*/
    .sitevideo_tellafriend_popup {
        margin:15px 0 0 15px;
        width:560px;
    }
    .sitevideo_tellafriend_popup .global_form > div > div {
        width:530px;
        max-height:480px;
        overflow:auto;
        padding:8px !important;
    }
    .sitevideo_tellafriend_popup p.description {
        font-size:11px !important;
    }
    .sitevideo_tellafriend_popup .global_form div.form-element{
        max-width:350px;	
    }
    .sitevideo_tellafriend_popup .global_form input + label{
        width:300px;
    }
</style>
<div class="sitevideo_tellafriend_popup">
    <?php echo $this->form->render($this); ?>
</div>