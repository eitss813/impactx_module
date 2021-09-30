<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<ul class="sitepage_sidebar_list">
	<?php foreach ($this->sitepages as $sitepage): ?>
		<li>
			<?php  $this->partial()->setObjectKey('sitepage');
				echo $this->partial('application/modules/Sitepage/views/scripts/partial_widget.tpl', $sitepage);
	    ?>
					<?php echo $this->translate(array('%s Discussion', '%s Discussions', $sitepage->counttopics), $this->locale()->toNumber($sitepage->counttopics)) ?> 
				</div>
				<div class='sitepage_sidebar_list_details'>
					<?php echo $this->translate(array('%s Reply', '%s Replies', $sitepage->total_count), $this->locale()->toNumber($sitepage->total_count)) ?>
				</div>
			</div>
		</li>
	<?php endforeach; ?>
</ul>