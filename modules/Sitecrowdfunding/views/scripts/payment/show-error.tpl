<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: show-error.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if (!Engine_Api::_()->seaocore()->checkModuleNameAndNavigation()): ?>
    <?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/navigation_views.tpl'; ?>
<?php endif; ?>
<ul class="form-errors">
    <li>
        <?php if (!empty($this->show)): ?>
            <?php echo $this->translate("There are currently no enabled payment gateways. Please contact the site admin to get this issue resolved."); ?>
        <?php else: ?>
            <?php echo $this->translate("There are currently no paid packages available of the site. Please upgrade your package."); ?>
        <?php endif; ?>
    </li>
</ul>