<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: make-album-cover.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div style="padding: 10px;">
  <?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>

  <?php echo $this->itemPhoto($this->photo) ?>
</div>