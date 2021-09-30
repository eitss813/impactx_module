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
if( !empty($this->bgImage) ) : ?>
  <style>
    .generic_layout_container.layout_sitecoretheme_markers{
      background-image: url(<?php echo $this->bgImage; ?>);
    }
  </style>
<?php endif; ?>
<div class="ballon-marker-wapper">
  <div>
    <?php foreach( $this->data as $row ): ?>
      <?php if( !empty((int) $row['title']) ) : ?>
        <div class="_item-row wow pulse animated" data-wow-delay="300ms" data-wow-iteration="infinite" data-wow-duration="2s">
          <div class="_item-row-inner">
            <div class="_icon">
              <div>
                <img src="<?php echo $row['iconUrl']; ?>">
              </div>
            </div>
            <div class="_info">
              <div class="_title"><?php echo $this->translate($row['title']); ?></div>
              <div class="_sub-text"><?php echo $this->translate($row['subTitle']); ?></div>
            </div>
          </div>
        </div>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
</div>