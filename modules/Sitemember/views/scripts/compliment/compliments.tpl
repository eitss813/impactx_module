<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: compliments.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>

<?php if ($this->direction == 1) { ?>
  <?php $j = 0; ?>
  <?php foreach ($this->sitemembers as $sitemember): ?>
    <?php
    echo $this->partial(
        'list_carousel_compliment.tpl', 'sitemember', array(
        'sitemember' => $sitemember,
        'title_truncation' => $this->title_truncation,
        'vertical' => $this->vertical,
        'showOptions' => $this->showOptions,
        'blockHeight' => $this->blockHeight,
        'blockWidth' => $this->blockWidth,
        'customParams' => $this->customParams,
        'custom_field_title' => $this->custom_field_title,
        'custom_field_heading' => $this->custom_field_heading,
        'titlePosition' => $this->titlePosition,
        'links' => $this->links,
        'circularImage' => $this->circularImage,
        'circularImageHeight' => $this->circularImageHeight,
        'compliment'=>$this->complimentTable,
        'complimentItem' =>$this->complimentItem,
        'itemViewType' =>$this->itemViewType,
        'compliment_category'=> $this->view->compliment_category
    ));
    ?>
  <?php endforeach; ?>
  <?php if ($j < ($this->sponserdSitemembersCount) && !$this->itemViewType): ?>
    <?php for ($j; $j < ($this->sponserdSitemembersCount); $j++): ?>
      <li class="sitemember_grid_view sitemember_carousel_content_item" style="height: <?php echo ($this->blockHeight) ?>px;width : <?php echo ($this->blockWidth) ?>px;">
      </li>
    <?php endfor; ?>
    <?php elseif($j < ($this->sponserdSitemembersCount)) : ?>
     <?php for ($j; $j < ($this->sponserdSitemembersCount); $j++): ?>
        <li class="compliment_list_crousal sitemember_carousel_content_item" style="height: <?php echo ($this->blockHeight) ?>px;width : <?php echo ($this->blockWidth) ?>px;">
        </li>
        <?php endfor; ?>
  <?php endif; ?>
<?php } else { ?>

  <?php for ($i = $this->sponserdSitemembersCount; $i < Count($this->sitemembers); $i++): ?>
    <?php $sitemember = $this->sitemembers[$i]; ?>
    <?php
    echo $this->partial(
        'list_carousel_compliment.tpl', 'sitemember', array(
        'sitemember' => $sitemember,
        'title_truncation' => $this->title_truncation,
        'vertical' => $this->vertical,
        'showOptions' => $this->showOptions,
        'blockHeight' => $this->blockHeight,
        'blockWidth' => $this->blockWidth,
        'customParams' => $this->customParams,
        'custom_field_title' => $this->custom_field_title,
        'custom_field_heading' => $this->custom_field_heading,
        'titlePosition' => $this->titlePosition,
        'links' => $this->links,
        'circularImage' => $this->circularImage,
        'circularImageHeight' => $this->circularImageHeight,
        'compliment'=>$this->complimentTable,
        'complimentItem' =>$this->complimentItem,
        'itemViewType' =>$this->itemViewType,
        'compliment_category'=> $this->view->compliment_category
    ));
    ?>
  <?php endfor; ?>

  <?php for ($i = 0; $i < $this->sponserdSitemembersCount; $i++): ?>
    <?php $sitemember = $this->sitemembers[$i]; ?>
    <?php
    echo $this->partial(
            'list_carousel_compliment.tpl', 'sitemember', array(
            'sitemember' => $sitemember,
            'title_truncation' => $this->title_truncation,
            'vertical' => $this->vertical,
            'showOptions' => $this->showOptions,
            'blockHeight' => $this->blockHeight,
            'blockWidth' => $this->blockWidth,
            'customParams' => $this->customParams,
            'custom_field_title' => $this->custom_field_title,
            'custom_field_heading' => $this->custom_field_heading,
            'titlePosition' => $this->titlePosition,
            'links' => $this->links,
            'circularImage' => $this->circularImage,
            'circularImageHeight' => $this->circularImageHeight,
            'compliment'=>$this->complimentTable,
            'complimentItem' =>$this->complimentItem,
            'itemViewType' =>$this->itemViewType,
            'compliment_category'=> $this->view->compliment_category
    ));
    ?>
  <?php endfor; ?>
<?php } ?>

