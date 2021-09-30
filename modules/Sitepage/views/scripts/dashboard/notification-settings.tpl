<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: contact.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript" >
  var submitformajax = 1;
  //var manage_admin_formsubmit = 1;
</script>

<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/sitepage_dashboard_main_header.tpl'; ?>

<?php if (empty($this->is_ajax)) : ?>
<div class="generic_layout_container layout_middle">
<div class="generic_layout_container layout_core_content">
	<?php //include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
  <div class="layout_middle">
    <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
    <?php echo $this->partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array( 'sitepage_id'=>$this->sitepage->page_id,'sectionTitle'=> 'Manage Notifications', 'sectionDescription' => '')); ?>
    <div class="sitepage_edit_content">
      <div id="show_tab_content">
      <?php endif; ?>
			<h3> <?php echo $this->translate('Manage Notifications'); ?> </h3>
			<p class="form-description"><?php echo $this->translate("Below you can manage settings for receiving notifications for peoples various activities in your page.") ?></p>
      <?php
      echo $this->form->render($this);
      ?>
      <br />
      <div id="show_tab_content_child">
      </div>

      <?php if (empty($this->is_ajax)) : ?>
      </div>
    </div>
  </div>
    </div>
  </div>
<?php endif; ?>
<script type="text/javascript">
en4.core.runonce.add(function() {
		<?php if (!empty($this->notification)) : ?>
			notificationEmail('block');
		<?php else : ?>
			notificationEmail('none');
		<?php endif; ?>
		
		<?php if (!empty($this->email)) : ?>
			EmailAction('block');
		<?php else : ?>
			EmailAction('none');
		<?php endif; ?>
		
});
  window.addEvent('domready', function() {
		<?php if (!empty($this->notification)) : ?>
			notificationEmail('block');
		<?php else : ?>
			notificationEmail('none');
		<?php endif; ?>
		
		<?php if (!empty($this->email)) : ?>
			EmailAction('block');
		<?php else : ?>
			EmailAction('none');
		<?php endif; ?>
		//en4.core.runonce.trigger();
  });
  
  function showEmailAction() {
		if($('email').checked == true) {
			EmailAction('block');
			$('action_email-posted').checked = true;
			$('action_email-created').checked = true;
		} else {
			EmailAction('none');
			$('action_email-posted').checked = false;
			$('action_email-created').checked = false;
		}
  }
  
  function showNotificationAction() {
		if($('notification').checked == true) {
			notificationEmail('block');
			$('action_notification-posted').checked = true;
			$('action_notification-created').checked = true;
			$('action_notification-follow').checked = true;
			$('action_notification-like').checked = true;
			$('action_notification-comment').checked = true;
			$('action_notification-join').checked = true;
		} else {
			notificationEmail('none');
			$('action_notification-posted').checked = false;
			$('action_notification-created').checked = false;
			$('action_notification-follow').checked = false;
			$('action_notification-like').checked = false;
			$('action_notification-comment').checked = false;
			$('action_notification-join').checked = true;
		}
  }
  
  function notificationEmail(display) {
		if($('action_notification-wrapper')) {
			$('action_notification-wrapper').style.display=display;
		} 
		
		/*else {
			//$('action_notification-wrapper').style.display='none';
		//}*/
  }
  
  function EmailAction(display) {
		if($('action_email-wrapper'))
			$('action_email-wrapper').style.display=display;
		
		
		/*else {
			//$('action_notification-wrapper').style.display='none';
		//}*/
  }
</script>