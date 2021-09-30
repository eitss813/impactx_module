<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: page-join.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 
   $this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
            . 'application/modules/Seaocore/externals/styles/style_infotooltip.css');
$settings = Engine_Api::_()->getApi('settings', 'core');
?>

<div class="sm-content-list" id="profile_pages">
  <?php $user = Engine_Api::_()->user()->getUser($this->user_id); ?>
  <h3><?php echo $this->translate('Pages joined by ')?><a href="<?php echo $user->getHref();?>"><?php echo $user->displayname ?></a></h3>
	<ul data-role="listview" data-icon="arrow-r">
		<?php foreach ($this->paginator as $value): ?>
      <?php $sitepage = Engine_Api::_()->getItem('sitepage_page', $value->page_id); ?>
			<li>
				<a href="<?php echo $sitepage->getHref(); ?>">
					<?php echo $this->itemPhoto($sitepage, 'thumb.icon'); ?>
          <h3><?php echo $this->string()->chunk($this->string()->truncate($sitepage->getTitle(), 45), 10); ?></h3>
					<p><?php echo $this->translate(array('%s Member', '%s Members', $sitepage->member_count), $this->locale()->toNumber($sitepage->member_count)) ?></p>
				</a> 
			</li>
		<?php endforeach; ?>   
	</ul>
</div>  
<?php if ($this->paginator->count() > 1): ?>
	<?php
		echo $this->paginationAjaxControl(
					$this->paginator, $this->identity, 'profile_pages');
	?>
<?php endif; ?>