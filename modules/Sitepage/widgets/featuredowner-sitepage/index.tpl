<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
	$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/sitepage-tooltip.css');
?>

<ul class="sitepagelike_users_block">
  <li>
    <?php foreach ($this->featuredowners as $item): ?>
      <div class="likes_member_sitepage sitepage_show_owner_tooltip_wrapper">
        <?php echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon')) ?>	
        <div class='sitepage_show_owner_tooltip'>
          <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/tooltip_arrow.png" alt="" />
          <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
        </div>
      </div>
    <?php endforeach; ?>
  </li>	
</ul>