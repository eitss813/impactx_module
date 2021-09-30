<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Siteloginconnect
 * @copyright  Copyright 2006-2010 SocialEngineAddons
 * @license    http://www.socialengine.com/license/
 * @version    $Id: general.tpl 9874 2013-02-13 00:48:05Z SocialEngineAddons $
 * @author     SocialEngineAddons
 */
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitelogin/externals/styles/sitelogin_style.css');
?>
<h3><?php echo $this->translate('Synchronize Profile Data');?></h3>
<br/>
<div>
<p> You can synchronize your profile information with your existing profiles on the below social networks. Please click on the button of social site with which you want to sync your profile information. </p>
</div>
<?php if(!empty($this->already_integrated)&& !empty($this->socialsite)):?>
<div class="tip">
  <span>
    <?php echo ucfirst($this->socialsite)." account you\'re trying to connect is already connected to another account."; ?>
  </span>
</div>	
<?php endif; ?>	
<br/>
<?php foreach ($this->socialsitehref as $key => $value): $sitename=$key; 
      if($key=='linkedin') {$sitename="LinkedIn";}?>

	<div style="max-width:260px;clear:both; display: block;" class="social-btn btn-<?php echo $key ?> social-btn-lg social-btn-square <?php echo $key ?>-seboxshadow seboxshadow">
        <a href="<?php echo $value?>" aria-label="<?php echo ucfirst($sitename) ?>">
            <i class="fa fa-<?php echo $key ?> icon-with-bg" aria-hidden="true" title="Synchronize Data"></i>
            <span>Synchronize With <?php echo ucfirst($sitename) ?></span>
        </a>
    </div>
<?php endforeach; ?>




