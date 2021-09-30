<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create-edit.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->tinyMCESEAO()->addJS(); ?>

<h2>
    <?php echo $this->translate('Crowdfunding / Fundraising / Donations Plugin'); ?>
</h2>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>

<?php if (count($this->navigationManageMember)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigationManageMember)->render() ?>
    </div>
<?php endif; ?>

<div class='seaocore_settings_form'>
    <div class='settings'>
        <?php
        echo $this->form->render($this);
        ?>
    </div>
</div>

<script type="text/javascript">
    window.addEvent('domready', function() {
        showInviteOption("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.member.invite.option', 1) ?>");
        showApprovalOption("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.member.approval.option', 1) ?>");
    });

    function showInviteOption(option) {
        if($('sitecrowdfunding_member_invite_automatically-wrapper')) {
            if(option == 1) {
                $('sitecrowdfunding_member_invite_automatically-wrapper').style.display='none';
            } else{
                $('sitecrowdfunding_member_invite_automatically-wrapper').style.display='block';
            }
        }
    }

    function showApprovalOption(option) {
        if($('sitecrowdfunding_member_approval_automatically-wrapper')) {
            if(option == 1) {
                $('sitecrowdfunding_member_approval_automatically-wrapper').style.display='none';
            } else{
                $('sitecrowdfunding_member_approval_automatically-wrapper').style.display='block';
            }
        }
    }

</script>