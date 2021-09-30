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
<div id='profile_options'>
    <?php
    // This is rendered by application/modules/core/views/scripts/_navIcons.tpl
    echo $this->navigation()
            ->menu()
            ->setContainer($this->navigation)
            ->setPartial(array('_navIcons.tpl', 'core'))
            ->render()
    ?>
</div>