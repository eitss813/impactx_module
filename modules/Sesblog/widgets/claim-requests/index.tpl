<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: index.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/scripts/core.js'); ?> 

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/styles/styles.css'); ?>
<?php ?>
<?php $checkClaimRequest = Engine_Api::_()->getDbTable('claims', 'sesblog')->claimCount();?>
<div class="sesblog_profile_tebs sesbasic_clearfix">
<div class="sesblog_profile_tabs_top claim_request  sesbasic_clearfix">
	<a href="<?php echo $this->url(array('action' => 'claim'), 'sesblog_general', true);?>" class=" fa fa-plus"><?php echo $this->translate('Claim New Blog');?></a>
</div>
<?php if($checkClaimRequest):?>
	<div class="sesblog_profile_tabs_top claim_new_blog active sesbasic_clearfix">
		<a href="<?php echo $this->url(array('action' => 'claim-requests'), 'sesblog_general', true);?>" class="far fa-file-alt"><?php echo $this->translate('Your Claim Requests');?></a>
	</div>
<?php endif;?>

</div>

<div class="sesblog_claims_con_blog sesbasic_clearfix sesbasic_bxs">
<?php foreach($this->paginator as $claim):?>
<div class="sesblog_claims_contant sesbasic_clearfix">
<div class="sesblog_claims_cont_inner">
  <p class="claims-request_img"><?php $blogItem = Engine_Api::_()->getItem('sesblog_blog', $claim->blog_id);  ?>
    <?php $user = Engine_Api::_()->getItem('user', $blogItem->owner_id);?>
  <?php echo $this->htmlLink($blogItem->getHref(), $this->itemPhoto($blogItem, 'thumb.icon', $blogItem->getTitle())) ?></p>
  <p class="claims-request_title"><?php echo $this->htmlLink($blogItem->getHref(), $blogItem->getTitle()) ?></p>
  <p class="claims_tegs claims_by"><?php echo $this->translate('Blog Owner: '. $this->htmlLink($user->getHref(), $user->getTitle()));?></p>
  <?php  $locale = new Zend_Locale($localeLanguage);?>
  <?php $date = new Zend_Date(strtotime($claim->creation_date), false, $locale);?>
  <p class="claims_tegs claims_date"><?php echo $this->translate('Claim Date: ');?><span class="_date sesbasic_text_light" title=""><i class="fa fa-calendar" ></i> <?php echo $date->toString('F');?></span></p>
  <?php $status = $claim->status ? 'approved' : 'pending'; ?>
  <p class="claims_tegs claims_status"><?php echo $this->translate('Claim Status: ');?><span class="<?php echo $status; ?>"><?php echo $status; ?></span></p>
  <?php if($claim->status == 'pending') { ?>
    <a href="<?php echo $this->url(array('action' => 'cancel-claim-request', 'claim_id' => $claim->getIdentity()), 'sesblog_general', true); ?>" class="cancel_claim smoothbox"><?php echo $this->translate("Cancel Request"); ?></a>
  <?php } ?>
  </div>
  </div>
<?php endforeach;?>
</div>
<script type="text/javascript">
	sesJqueryObject('.sesblog_main_claim').parent().addClass('active');
</script>

