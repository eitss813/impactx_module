<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: shoe-error.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
<ul class="form-errors">
  <li>
    <?php if (!empty($this->show)): ?>
      <?php echo $this->translate("There are currently no enabled payment gateways. Please contact the site admin to get this issue resolved."); ?>
    <?php else: ?>
      <?php echo $this->translate("There are currently no paid packages available of the site. Please upgrade your package."); ?>
    <?php endif; ?>
  </li>
</ul>