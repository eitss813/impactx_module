<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: contact-detail.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $defaultLogo = $this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/nophoto_page_thumb_profile.png'; ?>

<?php $initiativesURL = $this->url(array('action' => 'landing-page','page_id' => $this->page_id, 'initiative_id' => $this->initiative_id), "sitepage_initiatives");?>

<div class="sitepage_cover_photo cover_photo_wap b_dark">
    <?php if (empty($this->can_edit)) : ?>
      <a href="<?php echo $initiativesURL?>">
    <?php endif; ?>

    <!-- Show before reposition clicked -->
    <img id="before_reposition_image" src="<?php echo !empty($this->initiative['logo']) ? $this->initiative->getLogoUrl('thumb.cover') : $defaultLogo; ?>" alt="" align="left" class="cover_photo thumb_cover item_photo_sitepage_photo " style="top: <?php echo $this->coverTop ?>px">

    <?php if (empty($this->can_edit)) : ?></a><?php endif; ?>

    <?php if(!empty($this->initiative['title'])):?>
      <div class="initiative_title" id="initiative_title">
        <h2><?php echo $this->initiative['title']; ?></h2>
      </div>
    <?php endif; ?>

    <?php if (!empty($this->can_edit)) : ?>
      <div class="cover_tip_wrap dnone">
        <div class="cover_tip"><?php echo $this->translate("Drag to Reposition Cover Photo") ?></div>
      </div>
    <?php endif; ?>

</div>

<?php if (!empty($this->can_edit)) : ?>
  <div id="seao_cover_options" class="sitepage_cover_options <?php if (empty($this->initiative['logo'])) : ?> dblock <?php endif; ?>">
    <ul class="edit-button">
      <li >
        <span class="sitepage_cover_photo_btn sitepage_icon_photos_settings cover_photo_btn"><?php if (!empty($this->initiative['logo'])) : ?><?php echo $this->translate("Edit Cover Photo"); ?><?php else: ?><?php echo $this->translate("Add Cover Photo"); ?><?php endif; ?></span>

        <ul class="sitepage_cover_options_pulldown">

          <li>
            <a href='<?php echo $this->url(array('action' => 'upload-cover-photo', 'page_id' => $this->page_id , 'initiative_id' => $this->initiative_id ), 'sitepage_initiatives', true); ?>'  class="icon_sitepage_photo_new smoothbox"><?php echo $this->translate('Upload Cover Photo'); ?></a>
          </li>

          <?php if (!empty($this->initiative['logo'])) : ?>

            <li><a href="javascript:void(0);" onclick="startReposition()" class="cover_reposition sitepage_icon_move"><?php echo $this->translate("Reposition"); ?></a></li>

            <li>
              <?php echo $this->htmlLink(array('route' => 'sitepage_initiatives', 'action' => 'remove-cover-photo', 'page_id' => $this->page_id , 'initiative_id' => $this->initiative_id ), $this->translate('Remove Cover Photo'), array(' class' => 'smoothbox sitepage_icon_photos_delete')); ?>
            </li>

          <?php endif; ?>

        </ul>
      </li>
    </ul>
    <?php if (!empty($this->initiative['logo'])) : ?>
      <ul class="save-button dnone">
        <li >
          <span class="positions-save sitepage_cover_action"><?php echo $this->translate("Save Position"); ?></span>
        </li>
        <li>
          <span class="positions-cancel sitepage_cover_action"><?php echo $this->translate("Cancel"); ?></span>
        </li>
      </ul>
    <?php endif; ?>
  </div>
<?php endif; ?>
<div class="clr"></div>

<script type="text/javascript">
function startReposition(){
  // Show the orginal image
  document.getElementById("before_reposition_image").src = "<?php echo !empty($this->initiative['logo']) ? $this->initiative->getLogoUrl() : $defaultLogo; ?>";
  document.seaoCoverPhoto.reposition.start();
  document.getElementById('initiative_title').style.display = "none";
}
</script>

<style>
  .initiative_title > h2{
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(0, 0, 0, 0.3) !important;
    padding: 10px !important;
    border-radius: 5px;
    color: white;
    font-size: 40px;
    font-weight: bold !important;
    line-height: 45px;
    letter-spacing: 2px;
    border: none;
    text-shadow: 0px 1px 1px rgba(0, 0, 0, 0.7);
  }
</style>