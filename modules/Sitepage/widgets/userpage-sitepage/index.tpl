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
<?php if (count($this->userPages)): ?>
  <h3> <?php echo $this->sitepage->getOwner()->toString() ?><?php echo $this->translate("'s Pages"); ?></h3>
  <ul class="sitepage_sidebar_list">
       <?php  $this->partialLoop()->setObjectKey('sitepage');
              echo $this->partialLoop('application/modules/Sitepage/views/scripts/partialloop_widget.tpl', $this->userPages);
			//echo $this->partialLoop('partialloop_widget.tpl', $this->userPages)
		?>
  </ul>
<?php endif; ?>