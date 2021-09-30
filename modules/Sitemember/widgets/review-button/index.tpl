<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if ($this->createAllow == 2): ?>
  <?php
  include_once APPLICATION_PATH . '/application/modules/Sitemember/views/scripts/_formCreateReview.tpl';
  ?>
<?php endif; ?>
<?php if($this->cover_photo):?>
  <div class="seaocore_button profile_button">
  <?php if ($this->createAllow == 1): ?>
    <a href="javascript:void(0);" onclick="writeAReview('create');"><i class="fa fa-plus"></i><span><?php echo $this->translate("Write a Review") ?></span></a>
  <?php elseif ($this->createAllow == 2): ?>
    <a href="javascript:void(0);" onclick="showForm();"><i class="fa fa-pencil"></i><span><?php echo $this->translate("Update your Review") ?></span></a>
  <?php endif; ?>
  </div>
<?php else:?>
<?php if ($this->createAllow == 1): ?>
    <a href="javascript:void(0);" onclick="writeAReview('create');"><i class="fa fa-plus"></i><span><?php echo $this->translate("Write a Review") ?></span></a>
    <?php elseif ($this->createAllow == 2): ?>
    <a href="javascript:void(0);" onclick="showForm();"><i class="fa fa-pencil"></i><span><?php echo $this->translate("Update your Review") ?></span></a>
<?php endif; ?>
<?php endif;?>
<script type="text/javascript">
    function writeAReview(option) {
<?php if ($this->member_profile_page): ?>
        if ($('main_tabs') && $('main_tabs').getElement('.tab_layout_sitemember_user_review_sitemember')) {
          if ($('sitemember_create') && $('main_tabs').getElement('.tab_layout_sitemember_user_review_sitemember').hasClass('active')) {
            window.location.hash = 'sitemember_create';
            return;
          } else if ($('sitemember_update') && $('main_tabs').getElement('.tab_layout_sitemember_user_review_sitemember').hasClass('active')) {
            window.location.hash = 'sitemember_update';
            return;
          }
          tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitemember_user_review_sitemember'));
  <?php if ($this->contentDetails && isset($this->contentDetails->params['loaded_by_ajax']) && $this->contentDetails->params['loaded_by_ajax']): ?>
            var params = {
              requestParams:<?php echo json_encode($this->contentDetails->params) ?>,
              responseContainer: $$('.layout_sitemember_user_review_sitemember')
            }

            params.requestParams.content_id = '<?php echo $this->contentDetails->content_id ?>';
            en4.sitemember.ajaxTab.sendReq(params);
  <?php endif; ?>
          if (option == 'create') {
            (function() {
              window.location.hash = 'sitemember_create';
            }).delay(3000);
          } else if (option == 'update') {
            (function() {
              window.location.hash = 'sitemember_update';
            }).delay(3000);
          }
        } else {
          if (option == 'create') {
            // 						(function(){
            window.location.hash = 'sitemember_create';
            // 						}).delay(3000);
          } else if (option == 'update') {
            // 						(function(){
            window.location.hash = 'sitemember_update';
            // 						}).delay(3000);
          }
        }
<?php else: ?>
        window.location.href = "<?php echo $this->user->getHref(); ?>";
<?php endif; ?>
    }
</script>
