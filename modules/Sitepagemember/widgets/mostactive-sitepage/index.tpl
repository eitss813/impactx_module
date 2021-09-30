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
	<?php foreach ($this->sitepages as $sitepage): $statistics = ''; ?>
		<li>
			<?php  $this->partial()->setObjectKey('sitepage');
				echo $this->partial('application/modules/Sitepage/views/scripts/partial_widget.tpl', $sitepage);
			?>
			<?php if(is_array($this->statistics) && in_array("comments", $this->statistics)):?>
				<?php $statistics .= $this->translate(array('%s comment', '%s comments', $sitepage->comment_count), $this->locale()->toNumber($sitepage->comment_count)) . ', ' ?>
			<?php endif;?>
			<?php if(is_array($this->statistics) && in_array("likes", $this->statistics)):?>
				<?php $statistics .= $this->translate(array('%s like', '%s likes', $sitepage->like_count), $this->locale()->toNumber($sitepage->like_count)) . ', '  ?>
			<?php endif;?>
			<?php if(is_array($this->statistics) && in_array("views", $this->statistics)):?>
				<?php $statistics .= $this->translate(array('%s view', '%s views', $sitepage->view_count), $this->locale()->toNumber($sitepage->view_count)) . ', ' ?>
			<?php endif;?>
      <?php if(is_array($this->statistics) && in_array("members", $this->statistics)):?>
				<?php $statistics .=  $this->translate(array('%s member', '%s members', $sitepage->member_count), $this->locale()->toNumber($sitepage->member_count)) ?>
      <?php endif;?>
      <?php
        $statistics = trim($statistics);
        $statistics = rtrim($statistics, ',');
      ?>
      <?php echo $statistics;?>
				</div>
			</div>
		</li>
	<?php endforeach; ?>
</ul>