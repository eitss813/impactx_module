<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Sitelogin
* @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    faq_help.tpl 2015-09-17 00:00:00Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>
<script type="text/javascript">
    function faq_show(id) {
        if ($(id).style.display == 'block') {
            $(id).style.display = 'none';
        } else {
            $(id).style.display = 'block';
        }
    }
</script>
<div class="admin_sitelogin_files_wrapper">
    <ul class="admin_sitelogin_files sitelogin_faq">	    
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("What if i want all the social login options at the time of Login only?"); ?></a>
            <div class='faq' style='display: none;' id='faq_3'>
                <?php echo $this->translate('Ans: You can enable / disable the social login options using three ways:<br />

- Enable / Disable them from the GLobal Settings<br/>
- Enable / Disable them from the Manage Social Sites Services Tab<br />
- Enable / Disable them from the Social Site Integration tabs for all the options separately
'); ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("How can i enable Quick Signup option?"); ?></a>
            <div class='faq' style='display: none;' id='faq_4'>
                <?php echo $this->translate('Ans: First, you can enable Quick Signup option only for the social integrations where that option is provided.<br/>For enabling, you can simply go to that integration tab and tick the Quick Signup enable check box. You also need to choose a Member Level and Profile Type for the users joining via Quick Signup or the other simplest way is Manage Social Sites Services tab where you can manage quick signup option for all the social integrations.'); ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("What will be the Signup steps if Quick Signup option is enabled?"); ?></a>
            <div class='faq' style='display: none;' id='faq_5'>
                <?php echo $this->translate("Ans: Following will be the sequence for Signup process if Quick Signup is enabled:<br />
                - User needs to login using that social network account.<br />
                - All the required details will be fetched from the account depending on what that network allows.<br />
                - Admin can set any plan as the default plan for smooth Quick Signup, otherwise choose subscription will be the last option.<br />
                - Admin needs to choose profile type and member level for the users joining via quick signup.<br />
                [Note: Invite Friends option should be disabled for Quick Signup case].<br />
                "); ?>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("Why email verification is not working when Quick Signup is enabled?"); ?></a>
            <div class='faq' style='display: none;' id='faq_6'>
                <?php echo $this->translate("Ans: Because in case of Quick Signup, user will be auto verified despite of the email verification being enabled. "); ?>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("Why is Quick Signup option unavailable for some of the integrations?"); ?></a>
            <div class='faq' style='display: none;' id='faq_7'>
                <?php echo $this->translate("Ans: Some of the Social Networking Sites share email ids, some share ids on special requests whereas some does not share email ids while creating applications. Quick Signup option is not possible without fetching the email ids hence, it is unavailable for some of the integrations."); ?>
            </div>
        </li>
    </ul>
</div>
