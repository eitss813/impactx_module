<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="categories-block">
<ul  class="ui-listview collapsible-listview" >
  <?php foreach ($this->categories[0] as $category): ?>
    <li class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-btn-up-c">
      <?php if (isset($this->categories[$category->category_id])) : ?>
        <div class="collapsible_icon" ><span class="ui-icon ui-icon-plus ui-icon-shadow">&nbsp;</span></div>
      <?php else: ?>
        <div class="collapsible_icon_none" ><span class="ui-icon ui-icon-circle ui-icon-shadow">&nbsp;</span></div>
      <?php endif; ?>
      <div class="ui-btn-inner ui-li" ><div class="ui-btn-text">
          <a class="ui-link-inherit" href="<?php echo $category->getHref() ?>"  >
            <?php echo $this->translate($category->getTitle(true)); ?></a>
        </div><span class="ui-icon ui-icon-arrow-r">&nbsp;</span></div>
      <?php if (isset($this->categories[$category->category_id])) : ?>
        <ul class="collapsible">
          <?php foreach ($this->categories[$category->category_id] as $category): ?>
            <li class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li  ui-btn-up-c">
              <?php if (isset($this->categories[$category->category_id])) : ?>
                <div class="collapsible_icon" ><span class="ui-icon ui-icon-plus ui-icon-shadow">&nbsp;</span></div>
              <?php else: ?>
                <div class="collapsible_icon_none" ><span class="ui-icon ui-icon-circle ui-icon-shadow">&nbsp;</span></div>
              <?php endif; ?>
              <div class="ui-btn-inner ui-li"><div class="ui-btn-text">
                  <a class="ui-link-inherit" href="<?php echo $category->getHref() ?>"  >
                    <?php echo $this->translate($category->getTitle(true)); ?></a>
                </div><span class="ui-icon ui-icon-arrow-r">&nbsp;</span></div>
            </li>
          <?php endforeach;
          ?>
        </ul>
      <?php endif; ?>
    </li>
  <?php endforeach;
  ?>
</ul>
</div>
