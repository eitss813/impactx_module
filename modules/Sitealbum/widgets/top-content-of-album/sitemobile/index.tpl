<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if (!empty($this->showInformationOptions)): ?>
  <?php if (in_array('title', $this->showInformationOptions) && !Engine_Api::_()->seaocore()->isSitemobileApp()): ?>
    <h2 class="ui-title">
      <?php
        echo $this->translate('%1$s', ( '' != trim($this->album->getTitle()) ? $this->album->getTitle() : '<em>' . $this->translate('Untitled') . '</em>')
      );
      ?>
    </h2>
  <?php endif; ?>
  <div class="sm-ui-cont-head">
    <?php if (in_array('owner', $this->showInformationOptions)): ?>   
      <div class="sm-ui-cont-author-photo">
        <?php  echo $this->htmlLink($this->album->getOwner()->getHref(), $this->itemPhoto($this->subject()->getOwner(), 'thumb.icon')); ?>
      </div>
    <?php endif; ?>
    <div class="sm-ui-cont-cont-info">
      <?php if (in_array('owner', $this->showInformationOptions)): ?>   
        <div class="sm-ui-cont-author-name">
          <?php echo $this->translate("By "); ?><?php echo $this->htmlLink($this->album->getOwner()->getHref(), $this->album->getOwner(), array('title' => $this->album->getOwner(), 'target' => '_parent', 'class' => 'seao_common_add_tooltip_link', 'rel' => 'user' . ' ' . $this->subject()->getIdentity())); ?>
        </div>
      <?php endif; ?>

      <?php if (in_array('updateddate', $this->showInformationOptions) || in_array('creationDate', $this->showInformationOptions)|| in_array('location', $this->showInformationOptions)): ?>
        <?php if (in_array('updateddate', $this->showInformationOptions)): ?>
          <div class="sm-ui-cont-cont-date t_l"><?php echo $this->translate('Updated about %1$s', $this->timestamp($this->album->modified_date)) ?></div>
        <?php endif ?>
        <?php if (in_array('creationDate', $this->showInformationOptions)): ?>
          <div class="sm-ui-cont-cont-date t_l"><?php echo $this->translate('Created: %1$s', $this->timestamp($this->album->creation_date)) ?></div>
        <?php endif ?>
        <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.location', 1) && ($this->album->location) && in_array('location', $this->showInformationOptions)): ?>
          <div class="sm-ui-cont-cont-date t_l"><?php echo $this->translate("Taken at: "); ?>
          <?php echo $this->htmlLink('https://maps.google.com/?q=' . urlencode($this->subject()->location), $this->album->location, array('target' => 'blank')) ?>
          </div>
        <?php endif ?>
      <?php endif; ?>
          
      <?php if ((in_array('categoryLink', $this->showInformationOptions) && $this->album->category_id) || (in_array('tags', $this->showInformationOptions) && count($this->sitealbumTags) > 0)): ?> 
        <?php
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.category.enabled', 1) && in_array('categoryLink', $this->showInformationOptions) && $this->album->category_id) :
          $categoryName = Engine_Api::_()->getDbtable('categories', 'sitealbum')->getCategoryName($this->album->category_id);
          ?>
          <div class="sm-ui-cont-cont-date t_l">
            <?php echo $this->translate("Category:"); ?>
            <a href="<?php echo $this->url(array('category_id' => $this->album->category_id, 'categoryname' => Engine_Api::_()->getItem('album_category', $this->album->category_id)->getCategorySlug()), 'sitealbum_general_category', true) ?>">
              <span><?php echo $categoryName; ?></span>
            </a> 
          </div>
        <?php endif; ?>

        <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.tags.enabled', 0) && in_array('tags', $this->showInformationOptions) && count($this->sitealbumTags) > 0): $tagCount = 0; ?>
          <div class="sm-ui-cont-cont-date t_l">
            <?php echo $this->translate("Tags: "); ?>
            <?php foreach ($this->sitealbumTags as $tag): ?>
              <?php if (!empty($tag->getTag()->text)): ?>
                <?php $tag->getTag()->text = $this->string()->escapeJavascript($tag->getTag()->text) ?>
                <?php if (empty($tagCount)): ?>
                  <a href='<?php echo $this->url(array('action' => 'browse'), "sitealbum_general"); ?>?tag=<?php echo urlencode($tag->getTag()->text) ?>&tag_id=<?php echo $tag->getTag()->tag_id ?>'><?php echo $tag->getTag()->text ?></a><?php if (count($this->sitealbumTags) != $tagCount): echo ','; ?><?php endif; ?>
                  <?php
                  $tagCount++;
                else:
                  ?>
                  <a href='<?php echo $this->url(array('action' => 'browse'), "sitealbum_general"); ?>?tag=<?php echo urlencode($tag->getTag()->text) ?>&tag_id=<?php echo $tag->getTag()->tag_id ?>'><?php echo $tag->getTag()->text ?></a><?php $tagCount++;
              if (count($this->sitealbumTags) != $tagCount): echo ','; ?><?php endif; ?>
                <?php endif; ?>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      <?php endif; ?>  
          
          <?php if (in_array('viewCount', $this->showInformationOptions) || in_array('likeCount', $this->showInformationOptions) || in_array('commentCount', $this->showInformationOptions) || in_array('totalPhotos', $this->showInformationOptions)): ?> 
          <div class="sm-ui-cont-cont-date t_l">
            <?php
            $statistics = '';

            if (is_array($this->showInformationOptions) && in_array('commentCount', $this->showInformationOptions)) {
              $statistics .= $this->translate(array('%s comment', '%s comments', $this->album->comment_count), $this->locale()->toNumber($this->album->comment_count)) . ' - ';
            }

            if (is_array($this->showInformationOptions) && in_array('viewCount', $this->showInformationOptions)) {
              $statistics .= $this->translate(array('%s view', '%s views', $this->album->view_count), $this->locale()->toNumber($this->album->view_count)) . ' - ';
            }

            if (is_array($this->showInformationOptions) && in_array('likeCount', $this->showInformationOptions)) {
              $statistics .= $this->translate(array('%s like', '%s likes', $this->album->like_count), $this->locale()->toNumber($this->album->like_count)) . ' - ';
            }

            if (is_array($this->showInformationOptions) && in_array('totalPhotos', $this->showInformationOptions) && isset($this->subject()->photos_count)) {
              $statistics .= $this->translate(array('%s Photo', '%s Photos', $this->album->photos_count), $this->locale()->toNumber($this->album->photos_count)) . ' - ';
            }

            $statistics = trim($statistics);
            $statistics = rtrim($statistics, '-');
            ?>
            <?php echo $statistics; ?>
          </div>
          <?php endif;?>
    </div>
    <?php if (in_array('description', $this->showInformationOptions) && ('' != trim($this->album->getDescription()))): ?> 
          <div class="sm-ui-cont-cont-date t_l"><?php echo $this->album->getDescription() ?></div>
    <?php endif; ?>
    <?php if (!empty($this->showInformationOptions) && in_array('rating', $this->showInformationOptions)): ?>
      <?php echo $this->content()->renderWidget("sitealbum.user-ratings"); ?>
    <?php endif; ?>
  </div>
<?php endif; ?>

<div class="sitealbum_album_options">
  <?php if ($this->canMakeFeatured && !$this->allowView): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate("SITEALBUM_VIEW_PRIVACY_MESSAGE"); ?>
      </span>
    </div>
  <?php endif; ?>
</div>