<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<div class="sitecoretheme_blocks_wapper _sitecoretheme_blocks_<?php echo $this->photosPositions ?> <?php if( $this->block->getPhotoUrl() ): ?> _two_colummns <?php endif; ?>">
  <?php if( $this->block->getPhotoUrl() ): ?>
    <div class="_column _left">
      <div class="_column-inner ">
        <div class="_image-holder <?php echo $this->backendBorder ? '_image-border': ''?>">
          <?php if( $this->block->getVideoURL() ): ?>
            <a href="<?php echo $this->block->getVideoURL() ?>" class="seao_smoothbox" data-SmoothboxSEAOType="iframe"> 
              <?php echo $this->itemPhoto($this->block, 'thumb.main'); ?>
              <span><i class="fa fa-play"></i></span>
            </a>
          <?php else: ?>
          <a>
            <?php echo $this->itemPhoto($this->block, 'thumb.main'); ?>
          </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php endif; ?>
  <div class="_column">
    <div class="_column-inner">
      <div class="_info">
        <div class="_info_wrapper">
          <?php if( $this->block->subheading ): ?>
            <p class="_pretitle"><?php echo $this->translate($this->block->subheading) ?></p>
          <?php endif; ?>
          <div class="_title">
            <?php echo $this->translate($this->block->getTitle()) ?>
          </div>
          <?php if( $this->block->getDescription() ): ?>
            <article class="_body">
              <?php echo $this->translate($this->block->getDescription()) ?>
            </article>
          <?php endif; ?>
          <span>
          <?php if( $this->block->getCTALabel('cta_1') ): ?>
            <a class="_cta_btn" href="<?php echo $this->block->getCTAHref('cta_1') ?>" <?php if( !empty($this->block->params['cta_1_uri_target']) ): ?> target="_blank"<?php endif; ?>>
              <?php echo $this->translate($this->block->getCTALabel('cta_1')) ?>
            </a>
          <?php endif; ?>
          <?php if( $this->block->getCTALabel('cta_2') ): ?>
            <a class="_cta_btn" href="<?php echo $this->block->getCTAHref('cta_2') ?>" <?php if( !empty($this->block->params['cta_2_uri_target']) ): ?> target="_blank"<?php endif; ?>>
              <?php echo $this->translate($this->block->getCTALabel('cta_2')) ?>
            </a>
          <?php endif; ?> 
          </span> 
        </div>
      </div>
    </div>
  </div>
  <?php if( $this->block->getPhotoUrl() ): ?>
    <div class="_column _right">
      <div class="_column-inner ">
        <div class="_image-holder">
          <div class="_image-holder <?php echo $this->backendBorder ? '_image-border': ''?>">
            <?php if( $this->block->getVideoURL() ): ?>
              <a href="<?php echo $this->block->getVideoURL() ?>" class="seao_smoothbox" data-SmoothboxSEAOType="iframe"> 
                <?php echo $this->itemPhoto($this->block, 'thumb.main'); ?>
                <span><i class="fa fa-play"></i></span>
              </a>
            <?php else: ?>
            <a>
              <?php echo $this->itemPhoto($this->block, 'thumb.main'); ?>
            </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>