<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/getCategorySlug
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css'); ?>

<ul id="sitealbum_stats" class="sitealbum_side_widget sitealbum_profile_album_info">
  <li class="sitealbum_profile_album_info_btns txt_center">
    <?php if (!empty($this->showContent) && in_array('likeButton', $this->showContent) && $this->viewer->getIdentity()): ?>  
      <?php echo $this->content()->renderWidget("seaocore.like-button") ?>
    <?php endif; ?>
  </li>

  <!-- ALBUM INFO WORK -->
  <?php if (!empty($this->showContent)) : ?>

    <?php if (in_array('totalPhotos', $this->showContent)): ?>
      <div class="seao_listings_stats">
        <i class="seao_icon_strip seao_icon seao_icon_photo" title="Photos"></i>
        <div title="<?php echo $this->translate(array('%s photo', '%s photos', $this->sitealbum->photos_count), $this->locale()->toNumber($this->sitealbum->photos_count)) ?>" class="o_hidden"><?php echo $this->translate(array('%s photo', '%s photos', $this->sitealbum->photos_count), $this->locale()->toNumber($this->sitealbum->photos_count)) ?></div>
      </div>
    <?php endif; ?>

    <?php if (in_array('creationDate', $this->showContent)) : ?>
      <div class="seao_listings_stats"><i class="seao_icon_strip seao_icon seao_icon_time" title="<?php echo $this->translate("Creation Date") ?>"></i><div class="o_hidden"><?php echo $this->translate("Created: %1s", $this->timestamp($this->sitealbum->creation_date)); ?>
        </div></div>
    <?php endif;
    ?>

    <?php if (in_array('updateDate', $this->showContent)) : ?>
      <div class="seao_listings_stats"><i class="seao_icon_strip seao_icon seao_icon_edit" title="<?php echo $this->translate("Updated Date") ?>"></i><div class="o_hidden"><?php
    echo $this->translate('Updated about %1s', $this->timestamp($this->sitealbum->modified_date)) . '</div></div>';
  endif;
    ?>

        <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.location', 1) && !empty($this->sitealbum->location) && in_array('location', $this->showContent)): ?>
          <li>
            <div class="seao_listings_stats">
              <i class="seao_icon_strip seao_icon seao_icon_location" title="<?php echo $this->translate("Location") ?>"></i>
              <div class="o_hidden">
                <?php echo $this->sitealbum->location; ?>
                <?php if (in_array('directionLink', $this->showContent)): ?>
                  - <b><?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $this->sitealbum->seao_locationid, 'resouce_type' => 'seaocore'), $this->translate("Get Directions"), array('class' => 'smoothbox')); ?></b>
                <?php endif; ?>
              </div>
            </div>
          </li>
        <?php endif; ?>

        <?php
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.category.enabled', 1) && in_array('categoryLink', $this->showContent) && $this->sitealbum->category_id && Engine_Api::_()->getItem('album_category', $this->sitealbum->category_id)) :
          $categoryName = Engine_Api::_()->getDbtable('categories', 'sitealbum')->getCategoryName($this->sitealbum->category_id);
          ?>
          <div class="seao_listings_stats"><i class="seao_icon_strip seao_icon seao_icon_category" title="<?php echo $this->translate('Category') ?>"></i>
            <div class="o_hidden">
              <a href="<?php echo $this->url(array('category_id' => $this->sitealbum->category_id, 'categoryname' => Engine_Api::_()->getItem('album_category', $this->sitealbum->category_id)->getCategorySlug()), 'sitealbum_general_category', true) ?>">
                <span><?php echo  $this->translate($categoryName); ?></span>
              </a> 
            </div>
          </div>
        <?php endif;
        ?>

        <?php
        $statistics = '';

        if (!empty($this->showContent) && in_array('commentCount', $this->showContent)) {
          $statistics .= $this->translate(array('%s comment', '%s comments', $this->sitealbum->comment_count), $this->locale()->toNumber($this->sitealbum->comment_count)) . ', ';
        }

        if (!empty($this->showContent) && in_array('viewCount', $this->showContent)) {
          $statistics .= $this->translate(array('%s view', '%s views', $this->sitealbum->view_count), $this->locale()->toNumber($this->sitealbum->view_count)) . ', ';
        }

        if (!empty($this->showContent) && in_array('likeCount', $this->showContent)) {
          $statistics .= $this->translate(array('%s like', '%s likes', $this->sitealbum->like_count), $this->locale()->toNumber($this->sitealbum->like_count)) . ', ';
        }

        $statistics = trim($statistics);
        $statistics = rtrim($statistics, ',');
        ?>
        <?php if (!empty($statistics)) : ?>
          <li>
            <div class="seao_listings_stats">
              <i class="seao_icon_strip seao_icon seao_icon_stats" title="<?php echo $this->translate("Statistics") ?>"></i>
              <div class="o_hidden">
                <?php echo $statistics; ?>
              </div>
            </div>
          </li>
        <?php endif; ?>
      <?php endif; ?>

      <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.tags.enabled', 0) && !empty($this->showContent) && in_array('tags', $this->showContent) && count($this->sitealbumTags) > 0): $tagCount = 0; ?>
        <li>
          <div class="seao_listings_stats">
            <i class="seao_icon_strip seao_icon sitealbum_icon_tag_link" title="<?php echo $this->translate("Tags") ?>"></i>
            <div class="o_hidden">
              <?php foreach ($this->sitealbumTags as $tag): ?>
                <?php if (!empty($tag->getTag()->text)): ?>
                  <?php $tag->getTag()->text = $this->string()->escapeJavascript($tag->getTag()->text) ?>
                  <?php if (empty($tagCount)): ?>
                    <a href='<?php echo $this->url(array('action' => 'browse'), "sitealbum_general"); ?>?tag=<?php echo urlencode($tag->getTag()->text) ?>&tag_id=<?php echo $tag->getTag()->tag_id ?>'>#<?php echo $tag->getTag()->text ?></a>
                    <?php
                    $tagCount++;
                  else:
                    ?>
                    <a href='<?php echo $this->url(array('action' => 'browse'), "sitealbum_general"); ?>?tag=<?php echo urlencode($tag->getTag()->text) ?>&tag_id=<?php echo $tag->getTag()->tag_id ?>'>#<?php echo $tag->getTag()->text ?></a>
                  <?php endif; ?>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
          </div>
        </li>
      <?php endif; ?>

      <?php if (!empty($this->showContent) && in_array('socialShare', $this->showContent)): ?>  
        <li class="mtop10">
          <div class="o_hidden"> 
            <div class="sitealbum_social_share">
              <?php echo $this->code; ?>
            </div>
          </div>
        </li>    
      <?php endif; ?>  
      </ul>