<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: homesponsored.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $settings = Engine_Api::_()->getApi('settings', 'core'); ?>
<?php if ($this->direction == 1) { ?>
  <?php $j = 0; ?>
  <?php foreach ($this->sitepages as $sitepage): ?>
    <?php
    echo $this->partial(
            'list_carousel.tpl', 'sitepage', array(
        'sitepage' => $sitepage,
        'title_truncation' => $this->title_truncation,
        'vertical' => $this->vertical,
        'featuredIcon' => $this->featuredIcon,
        'sponsoredIcon' => $this->sponsoredIcon,
        'showOptions' => $this->showOptions,
        'blockHeight' => $this->blockHeight,
        'blockWidth' => $this->blockWidth,
        'statistics' => $this->statistics,
    ));
    ?>	 
  <?php endforeach; ?>
  <?php if ($j < ($this->sponserdSitepagesCount)): ?>
    <?php for ($j; $j < ($this->sponserdSitepagesCount); $j++): ?>
      <div class="sr_carousel_content_item b_medium" style="height: <?php echo ($this->blockHeight) ?>px;width : <?php echo ($this->blockWidth) ?>px;">
      </div>
    <?php endfor; ?>
  <?php endif; ?>
<?php } else { ?>

  <?php for ($i = $this->sponserdSitepagesCount; $i < Count($this->sitepages); $i++): ?>
    <?php $sitepage = $this->sitepages[$i]; ?>
    <?php
    echo $this->partial(
            'list_carousel.tpl', 'sitepage', array(
        'sitepage' => $sitepage,
        'title_truncation' => $this->title_truncation,
        'vertical' => $this->vertical,
        'featuredIcon' => $this->featuredIcon,
        'sponsoredIcon' => $this->sponsoredIcon,
        'showOptions' => $this->showOptions,
        'blockHeight' => $this->blockHeight,
        'statistics' => $this->statistics,
        'blockWidth' => $this->blockWidth,        
    ));
    ?>	
  <?php endfor; ?>

  <?php for ($i = 0; $i < $this->sponserdSitepagesCount; $i++): ?>
    <?php $sitepage = $this->sitepages[$i]; ?>
    <?php
    echo $this->partial(
            'list_carousel.tpl', 'sitepage', array(
        'sitepage' => $sitepage,
        'title_truncation' => $this->title_truncation,
        'vertical' => $this->vertical,
        'featuredIcon' => $this->featuredIcon,
        'sponsoredIcon' => $this->sponsoredIcon,
        'showOptions' => $this->showOptions,
        'blockHeight' => $this->blockHeight,
        'blockWidth' => $this->blockWidth,  
        'statistics' => $this->statistics,
    ));
    ?>	
  <?php endfor; ?>
<?php } ?>

