<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _clickableLink.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<div class="sitecoretheme_footer_exp_colps">
    <span >Click on arrow to expand and collapse this setting in multiple languages enabled on your website:</span>
    <span id="clickable_link"></span>
</div>

<script type="text/javascript">
    window.addEvent('domready', function(){
        new Fx.Slide('slideable_language_options-wrapper', {mode: 'vertical', resetHeight: true}).toggle();
    });
    $('clickable_link').addEvent('click', function(){   
        new Fx.Slide('slideable_language_options-wrapper', {mode: 'vertical', resetHeight: true}).toggle();
        if($('clickable_link').hasClass('open')) {
            $('clickable_link').removeClass('open');
            $('clickable_link').addClass('close');
        } else {
            $('clickable_link').removeClass('close');
            $('clickable_link').addClass('open');
        }
    });
</script>