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
        <!--START-ICON OR PLUS-MINUS VIEW-->
        <div class="collapsible_icon" >
          <?php if(!empty($category['file_id'])): ?>
          <a class="ui-link-inherit" href="<?php echo $category->getHref() ?>"  >
          <img alt="" class="ui-icon ui-icon-shadow" src="<?php echo $this->storage->get($category['file_id'], '')->getPhotoUrl(); ?>" />
          </a>
          <?php endif;?>
        </div>        
        <!--END-ICON OR PLUS-MINUS VIEW-->
      <div class="ui-btn-inner ui-li" ><div class="ui-btn-text">
          <a class="ui-link-inherit" href="<?php echo $category->getHref() ?>"  >
            <?php echo $this->translate($category->getTitle(true)); ?></a>
        </div><span class="ui-icon ui-icon-angle-right">&nbsp;</span></div>
    </li>
  <?php endforeach;
  ?>
</ul>
</div>
