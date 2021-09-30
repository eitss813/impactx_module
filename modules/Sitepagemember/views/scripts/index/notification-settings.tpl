<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: notification-settings.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepagemember/externals/styles/style_sitepagemember.css'); ?>
<div class="global_form_popup sitepagemember_notification_setting">
	<?php echo $this->form->render($this); ?>
</div>
<script type="text/javascript">
  var emailSettings = '<?php echo $this->emailSettings ?>';
  var notificationSettings = '<?php echo $this->notificationSettings ?>';
  
  var email = '<?php echo $this->email ?>';
  var notification = '<?php echo $this->notification ?>';
  
  window.addEvent('domready', function() {
		showNotificationOptionsAction(notification);
		showEmailOptionsAction(email);
		if(emailSettings == 1) {
			showEmailOptionsAction(0);
			$('email').checked = false;
		}
		if(notificationSettings == 1) {
			showNotificationOptionsAction(0);
			$('notification').checked = false;
		}
		
  });

  function showEmailAction() {
		if($('email').checked == true) { 
			$('emailposted-wrapper').style.display = 'block';
			$('emailcreated-wrapper').style.display = 'block';
		} else {
			$('emailposted-wrapper').style.display = 'none';
			$('emailcreated-wrapper').style.display = 'none';
		}
  }
  
  function showEmailOptionsAction(email) {
		if(email == 1) {
			$('emailposted-wrapper').style.display = 'block';
			$('emailcreated-wrapper').style.display = 'block';
		} else {
			$('emailposted-wrapper').style.display = 'none';
			$('emailcreated-wrapper').style.display = 'none';
		}
  }
  
  function showNotificationAction() {
		if($('notification').checked == true) {
			$('notificationposted-wrapper').style.display = 'block';
			$('notificationcreated-wrapper').style.display = 'block';
			$('notificationfollow-wrapper').style.display = 'block';
			$('notificationlike-wrapper').style.display = 'block';
			$('notificationcomment-wrapper').style.display = 'block';
			$('notificationjoin-wrapper').style.display = 'block';
		} else {
			$('notificationposted-wrapper').style.display = 'none';
			$('notificationcreated-wrapper').style.display = 'none';
			$('notificationfollow-wrapper').style.display = 'none';
			$('notificationlike-wrapper').style.display = 'none';
			$('notificationcomment-wrapper').style.display = 'none';
			$('notificationjoin-wrapper').style.display = 'none';
		}
  }
  
  function showNotificationOptionsAction(notification) {
		if(notification == 1) {
			$('notificationposted-wrapper').style.display = 'block';
			$('notificationcreated-wrapper').style.display = 'block';
			$('notificationfollow-wrapper').style.display = 'block';
			$('notificationlike-wrapper').style.display = 'block';
			$('notificationcomment-wrapper').style.display = 'block';
			$('notificationjoin-wrapper').style.display = 'block';
		} else {
			$('notificationposted-wrapper').style.display = 'none';
			$('notificationcreated-wrapper').style.display = 'none';
			$('notificationfollow-wrapper').style.display = 'none';
			$('notificationlike-wrapper').style.display = 'none';
			$('notificationcomment-wrapper').style.display = 'none';
			$('notificationjoin-wrapper').style.display = 'none';
		}
  }
  
</script>