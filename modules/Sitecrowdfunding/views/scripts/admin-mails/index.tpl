<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>


<script type="text/javascript">

    window.addEvent('domready', function() { 
      
        
        var e1 = $('sitecrowdfunding_reminder_before_project_completion-1');
        // var e2 = ('sitecrowdfunding_reminder_for_payment_gateway_configuration-1');
        // var e3 = ('sitecrowdfunding_reminder_before_project_completion-1');
        // var e4 = ('sitecrowdfunding_reminder_for_project_payment-1'); 
        var e5 = $('sitecrowdfunding_reminder_demo');

        $('sitecrowdfunding_reminder_project_completion_options-wrapper').setStyle('display', e1.checked ? 'block' : 'none');
        $('sitecrowdfunding_admin_mail-wrapper').setStyle('display', e5.checked ? 'block' : 'none');
        // $('sitecrowdfunding_reminder_duration_options-wrapper').setStyle('display', (e2.checked || e3.checked || e4.checked) ? 'block' : 'none');  
         
        $('sitecrowdfunding_reminder_before_project_completion-1').addEvent('click', function() {
            $('sitecrowdfunding_reminder_project_completion_options-wrapper').setStyle('display', $(this).checked ? 'block' : 'none');
        }); 
        $('sitecrowdfunding_reminder_before_project_completion-0').addEvent('click', function() {
            $('sitecrowdfunding_reminder_project_completion_options-wrapper').setStyle('display', $(this).checked ? 'none' : 'block');
        });



        $('sitecrowdfunding_reminder_demo').addEvent('click', function() {
            $('sitecrowdfunding_admin_mail-wrapper').setStyle('display', $(this).checked ? 'block' : 'none');
        });

    });
</script>

<h2 class="fleft">
    Crowdfunding / Fundraising / Donations Plugin
</h2>
<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>

<div class='clear siteevent_settings_form'>
    <div class='settings'>
        <?php echo $this->form->render($this) ?>
    </div>
</div>