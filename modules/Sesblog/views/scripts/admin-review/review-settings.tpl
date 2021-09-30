<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: review-settings.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php include APPLICATION_PATH .  '/application/modules/Sesblog/views/scripts/dismiss_message.tpl';?>
<?php $settings = Engine_Api::_()->getApi('settings', 'core'); ?>
<div class='clear sesbasic-form'>
  <div>
    <?php if( count($this->subnavigation) ): ?>
      <div class='sesbasic-admin-sub-tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->subnavigation)->render(); ?>
      </div>
    <?php endif; ?>
    <div class="sesbasic-form-cont">
      <div class='settings sesbasic_admin_form'>
        <?php echo $this->form->render($this); ?>
      </div>
    </div>
  </div>
</div>
<style type="text/css">
.sesbasic_back_icon{
  background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/back.png);
}
</style>
<script>  
   scriptJquery( document ).ready(function() {
    showEditor("<?php echo $settings->getSetting('sesblog.review.summary', 1) ?>");
	allowReview("<?php echo $settings->getSetting('sesblog.allow.review', 1) ?>");
  });
  
function showEditor(value) {
  if(value == 1) {
    if(scriptJquery('#sesblog_show_tinymce-wrapper'))
      scriptJquery('#sesblog_show_tinymce-wrapper').show();
  } else {
    if(scriptJquery('#sesblog_show_tinymce-wrapper'))
    scriptJquery('#sesblog_show_tinymce-wrapper').hide();
  }
  
}
function allowReview(value) { 
  if(value == 1) {
    if(scriptJquery('#sesblog_allow_owner-wrapper'))
      scriptJquery('#sesblog_allow_owner-wrapper').show();
    if(scriptJquery('#sesblog_show_pros-wrapper'))
        scriptJquery('#sesblog_show_pros-wrapper').show();
    if(scriptJquery('#sesblog_show_cons-wrapper'))
        scriptJquery('#sesblog_show_cons-wrapper').show();
    if(scriptJquery('#sesblog_review_title-wrapper'))
        scriptJquery('#sesblog_review_title-wrapper').show();
    if(scriptJquery('#sesblog_review_summary-wrapper'))
        scriptJquery('#sesblog_review_summary-wrapper').show();
    if(scriptJquery('#sesblog_show_tinymce-wrapper'))
        scriptJquery('#sesblog_show_tinymce-wrapper').show();
    if(scriptJquery('#sesblog_show_recommended-wrapper'))
        scriptJquery('#sesblog_show_recommended-wrapper').show();
    if(scriptJquery('#sesblog_allow_share-wrapper'))
        scriptJquery('#sesblog_allow_share-wrapper').show();
    if(scriptJquery('#sesblog_show_report-wrapper'))
        scriptJquery('#sesblog_show_report-wrapper').show();
    showEditor("<?php echo $settings->getSetting('sesblog.review.summary', 1) ?>");
  } else {
    if(scriptJquery('#sesblog_allow_owner-wrapper'))
        scriptJquery('#sesblog_allow_owner-wrapper').hide();
    if(scriptJquery('#sesblog_show_pros-wrapper'))
        scriptJquery('#sesblog_show_pros-wrapper').hide();
    if(scriptJquery('#sesblog_show_cons-wrapper'))
        scriptJquery('#sesblog_show_cons-wrapper').hide();
    if(scriptJquery('#sesblog_review_title-wrapper'))
        scriptJquery('#sesblog_review_title-wrapper').hide();
    if(scriptJquery('#sesblog_review_summary-wrapper'))
        scriptJquery('#sesblog_review_summary-wrapper').hide();
    if(scriptJquery('#sesblog_show_tinymce-wrapper'))
        scriptJquery('#sesblog_show_tinymce-wrapper').hide();
    if(scriptJquery('#sesblog_show_recommended-wrapper'))
        scriptJquery('#sesblog_show_recommended-wrapper').hide();
    if(scriptJquery('#sesblog_allow_share-wrapper'))
        scriptJquery('#sesblog_allow_share-wrapper').hide();
    if(scriptJquery('#sesblog_show_report-wrapper'))
        scriptJquery('#sesblog_show_report-wrapper').hide();
    showEditor(0);
  }
}
</script>
