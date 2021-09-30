<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: make-channel-cover.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div style="padding: 10px;">
    <?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>

    <?php echo $this->itemPhoto($this->video) ?>
</div>