<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: unhidephoto.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if (!$this->is_ajax) : ?>
  <?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
<?php endif; ?>

<style type="text/css">
  .disable button{background-color:#ccc;border-color:#ddd;}
  .global_form_popup > div > div > h3 + p + div{margin-top:5px;}
</style>
