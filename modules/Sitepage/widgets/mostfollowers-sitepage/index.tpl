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
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<ul class="sitepage_sidebar_list">
	<?php foreach ($this->sitepages as $sitepage): ?>
		<li>
			<?php  $this->partial()->setObjectKey('sitepage');
				echo $this->partial('application/modules/Sitepage/views/scripts/partial_widget.tpl', $sitepage);
	?>
					<?php echo $this->translate(array('%s follower', '%s followers', $sitepage->follow_count), $this->locale()->toNumber($sitepage->follow_count)) ?>
				</div>
			</div>
		</li>
	<?php endforeach; ?>
</ul>