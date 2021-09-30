<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<ul class="sitepage_sidebar_list">
	<?php foreach ($this->sitepages as $sitepage): ?>
		<li>
			<?php  $this->partial()->setObjectKey('sitepage');
				echo $this->partial('application/modules/Sitepage/views/scripts/partial_widget.tpl', $sitepage); ?>
				<?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.member.title' , 1);
				if ($sitepage->member_title && $memberTitle) : ?>
				<?php if ($sitepage->member_count == 1) : ?><?php echo $sitepage->member_count . ' member'; ?> <?php else: ?>	<?php echo $sitepage->member_count . ' ' .  $sitepage->member_title; ?><?php endif; ?>
				<?php else : ?>
				<?php echo $this->translate(array('%s member', '%s members', $sitepage->member_count), $this->locale()->toNumber($sitepage->member_count)) ?>
				<?php endif; ?>
				</div>
			</div>
		</li>
	<?php endforeach; ?>
	<?php if($this->sitepagesCount > 3):?>
		<?php $myPagesUrl = $this->url(array('action' => 'home'), 'sitepage_general', true);?>
		<a target="_blank" class="viewlink" href="<?php echo $myPagesUrl; ?>">View All<i class="fa-angle-double-right fa"></i></a>
	<?php endif;?>
</ul>