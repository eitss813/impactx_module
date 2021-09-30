
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class='clear seaocore_settings_form sitestaticpage_form'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>

<?php
	  /* Include the common user-end field switching javascript */
	  echo $this->partial('_jsSwitch.tpl', 'fields', array(
	      'topLevelId' => (int) @$this->topLevelId,
	      'topLevelValue' => (int) @$this->topLevelValue
	    ))
	?>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>