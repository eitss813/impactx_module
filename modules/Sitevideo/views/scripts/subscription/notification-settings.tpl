<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: notification-settings.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
    var notification = 1;
    var email = 1;
    en4.core.runonce.add(function () {
        if (notification == 1) {
            notificationSettings('block');
        } else if (notification == '' || notification == 0) {
            notificationSettings('none');
        }

        if (email == 1) {
            emailSettings('block');
        } else if (email == '' || email == 0) {
            emailSettings('none');
        }
    });

    window.addEvent('domready', function () {
        if (notification == 1) {
            notificationSettings('block');
        } else if (notification == '' || notification == 0) {
            notificationSettings('none');
        }

        if (email == 1) {
            emailSettings('block');
        } else if (email == '' || email == 0) {
            emailSettings('none');
        }
    });

    function showNotificationAction() {
        if ($('notification').checked == true) {
            notificationSettings('block');
            if ($('action_notification-posted'))
                $('action_notification-posted').checked = true;
            if ($('action_notification-created'))
                $('action_notification-created').checked = true;
            if ($('action_notification-like'))
                $('action_notification-like').checked = true;
            if ($('action_notification-comment'))
                $('action_notification-comment').checked = true;
            if ($('action_notification-discussion'))
                $('action_notification-discussion').checked = true;
        } else {
            notificationSettings('none');
            if ($('action_notification-posted'))
                $('action_notification-posted').checked = false;
            if ($('action_notification-created'))
                $('action_notification-created').checked = false;
            if ($('action_notification-like'))
                $('action_notification-like').checked = false;
            if ($('action_notification-comment'))
                $('action_notification-comment').checked = false;
            if ($('action_notification-discussion'))
                $('action_notification-discussion').checked = false;
        }
    }

    function showEmailAction() {
        if ($('email').checked == true) {
            emailSettings('block');
            if ($('action_email-posted'))
                $('action_email-posted').checked = true;

        } else {
            emailSettings('none');
            if ($('action_email-posted'))
                $('action_email-posted').checked = false;
        }
    }

    function notificationSettings(option) {
        setTimeout(function () {
            if ($('action_notification-wrapper')) {
                if (option == 'block') {
                    $('action_notification-wrapper').style.display = 'block';
                } else if (option == 'none') {
                    $('action_notification-wrapper').style.display = 'none';
                }
            }
        }, '50');
    }

    function emailSettings(option) {
        setTimeout(function () {
            if ($('action_email-wrapper')) {
                if (option == 'block') {
                    $('action_email-wrapper').style.display = 'block';
                } else if (option == 'none') {
                    $('action_email-wrapper').style.display = 'none';
                }
            }
        }, '50');
    }

</script>

<script type="text/javascript" >
    var submitformajax = 1;
    //var manage_admin_formsubmit = 1;
</script>

<div class="global_form_popup">
<?php echo $this->form->render($this); ?>

</div>