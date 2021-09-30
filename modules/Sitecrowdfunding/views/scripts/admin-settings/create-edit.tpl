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

<?php if (count($this->navigationGeneral)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigationGeneral)->render() ?>
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
    window.addEvent('domready', function () {
        showAnnouncements('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.announcement', 1) ?>');
    });

    function showAnnouncements(option) {
        if ($('sitecrowdfunding_announcementeditor-wrapper')) {
            if (option == 1) {
                $('sitecrowdfunding_announcementeditor-wrapper').style.display = 'block';
            } else {
                $('sitecrowdfunding_announcementeditor-wrapper').style.display = 'none';
            }
        }
    }
</script>