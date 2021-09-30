<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
    <?php echo $this->translate("Advanced Videos / Channels / Playlists Plugin") ?>
</h2>
<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs clr'>
        <?php
        // Render the menu
        //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>
<h3>
    <?php echo $this->translate("Ajax based Tabbed widget for Videos") ?>
</h3>
<?php if (count($this->subNavigation)): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->subNavigation)->render()
        ?>
    </div>
<?php endif; ?>
<div class="tip">
    <span>
        <?php echo $this->translate('We have moved these "Tabbed Video Widget" to "Layout Editor". You can change the desired settings of the respective widgets from "Layout Editor" by clicking on the "edit" link.'); ?></span>
</div>