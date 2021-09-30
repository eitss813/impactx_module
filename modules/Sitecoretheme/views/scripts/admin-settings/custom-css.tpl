<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: custom-css.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<h2>
    <?php echo SITECORETHEME_PLUGIN_NAME; ?>
</h2>

<div class='seaocore_admin_tabs tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
</div>
<div class='seaocore_sub_tabs tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->subNavigation)->render() ?>
</div>

<h3>
    <?php echo $this->translate('Custom CSS') ?>
</h3>

<p>
  <?php echo $this->translate('Here, you can write CSS code to customize this theme. CSS code written here will get saved along with the other CSS code of the website.') ?>
</p>
<br>

<?php if( !empty($this->sucess) ):?>
  <?php $class = 'sitecoretheme_custom_css_sucess_msg';?>
<?php else:?>
  <?php $class = 'sitecoretheme_custom_css_sucess_msg';?>
<?php endif;?>

<?php if(!empty($this->message)):?>
  <div class="<?php echo $class; ?>">
    <?php echo $this->translate($this->message); ?>
  </div>
<?php endif;?>

<form method="post" action="">
  <div class="admin_theme_editor">
      <?php echo $this->formTextarea('sitecoretheme_custom_css', $this->fileContent, array('spellcheck' => 'false')) ?>
  </div>
    <!--<input type="submit" value="<?php echo $this->translate("Save Changes") ?>" />-->
  <button class="activate_button" type="submit"><?php echo $this->translate("Save Changes") ?></button>
</form>