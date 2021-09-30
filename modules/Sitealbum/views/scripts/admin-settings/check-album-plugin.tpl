<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: check-album-plugin.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2><?php echo $this->translate('Advanced Photo Albums Plugin') ?></h2>
<div class="admin_seaocore_files_wrapper" style="width:500px;">
<?php if ($this->albumPluginTips): ?>
  <div class="tip">
      <span>
        <?php echo $this->translate('You have installed some other third-party plugin for albums on your site. The Advanced Photo Albums Plugin is dependent on SocialEngine Photo Albums Plugin and does not work with other third-party albums plugin. To make this plugin work correctly, you need to overwrite the other third-party album plugin on your site with the SocialEngine Photo Albums Plugin. Please follow the below steps for doing this:') ?>
      </span>
    </div>
    <ul class="admin_seaocore_files seaocore_faq">
    <li>
      <?php echo $this->translate('Download the latest version of the SocialEngine Photo Albums Plugin and upgrade it by clicking on the below button: "Go to Manage Packages & Plugins".') ?>
    </li>
     <li>
      <?php echo $this->translate('After upgrading the SocialEngine Photo Albums Plugin on your site, please go again to the Admin Panel of Advanced Photo Albums Plugin and activate that plugin.') ?>
    </li>
    </ul>
    <br />
    <form   method="POST">
       <button name="submit"><?php echo $this->translate('Go to Manage Packages & Plugins') ?> </button>
    </form>
  <?php else: ?>  
    <div class="tip">
      <span>
         <?php echo $this->translate('The Advanced Photo Albums Plugin is dependent on the SocialEngine Photo Albums Plugin. So, in future if you want to disable the SocialEngine Photo Albums Plugin on your website, then the Advanced Photo Albums Plugin should also be disabled.') ?>
      </span>
    </div> 
    <button onclick="form_submit();"><?php echo $this->translate('Proceed to activate plugin now') ?> </button>
 <?php endif;?>  
</div>
<script type="text/javascript" >

  function form_submit() {

    var url='<?php echo $this->url(array('module' => 'sitealbum', 'controller' => 'settings'), 'admin_default', true) ?>';
    window.location.href=url;
  }

</script>