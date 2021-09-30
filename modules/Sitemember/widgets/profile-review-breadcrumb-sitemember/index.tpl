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

<?php $url = $this->url(array('user_id' => $this->user->user_id), 'sitemember_review_memberreviews', true); ?>    

<div class="sm_listing_breadcrumb">
  <a href="<?php echo $this->url(array(), 'default', true); ?>">
<?php echo $this->translate('Home') ?></a>
    <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
    <?php echo $this->htmlLink($this->user->getHref(), $this->user->getTitle()) ?>
  <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
  <a href="<?php echo $url; ?>"><?php echo $this->translate('Reviews'); ?></a>
  <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
  <?php echo $this->reviews->getTitle(); ?>
</div>