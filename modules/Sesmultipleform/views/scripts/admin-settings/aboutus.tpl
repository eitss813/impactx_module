<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: aboutus.tpl 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */ 
?>
<?php include APPLICATION_PATH .  '/application/modules/Sesmultipleform/views/scripts/dismiss_message.tpl';?>
<h2><?php echo $this->translate("") ?></h2>
<div class='clear sesbasic_admin_form'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>

