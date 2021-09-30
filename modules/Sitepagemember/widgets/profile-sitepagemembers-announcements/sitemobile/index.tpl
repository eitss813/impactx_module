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
<?php if (count($this->announcements) > 0): ?>
	<div class="sm-content-list">
		<ul data-role="listview" data-icon="none">
			<?php foreach ($this->announcements as $item): ?>
				<li>
          <a>
					<strong><?php echo $item->getTitle(); ?></strong>
					<p>
						<?php echo $this->timestamp(strtotime($item->creation_date)) ?>
					</p>
          <?php if (!empty($item->body)): ?>
						<p><?php echo $item->body ?></p>
          <?php endif;?>
         </a> 
				</li>
			<?php endforeach; ?>
		</ul>
  </div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No announcements have been created yet.'); ?>
    </span>
  </div>
<?php endif; ?>