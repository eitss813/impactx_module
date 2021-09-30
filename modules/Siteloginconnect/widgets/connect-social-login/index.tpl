<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteloginconnect
 * @copyright  Copyright 2015-2016 SocialEngineAddons
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z SocialEngineAddons $
 * @author     SocialEngineAddons
 */
?>
<div class="quicklinks">
	<ul class="navigation">
		<?php
		foreach($this->enabledSites as $site):
			$sitename = $siteclass = $site;
			if($site=="vk") {
				$sitename="Vkontakte";
			}
			if($site=="linkedin") {
				$sitename="LinkedIn";
			}
			if($site=="outlook") {
				$siteclass="hotmail";
			}
			if($this->connetionStatus[$site]) {
				$text = "Connected to " . ucwords($sitename);
				$getBaseURL = "javascript:void(0)";				
			} else {
				$text = "Connect to " . ucwords($sitename);
				$getBaseURL = $this->base_url . '/siteloginconnect/link/' . $site;				
			} ?>
			<li>

				<div class="social-btn btn-<?php echo $siteclass?> social-btn-lg social-btn-square <?php echo $siteclass?>-seboxshadow seboxshadow">
        			<a href="<?php echo $getBaseURL ?>" aria-label="<?php echo ucwords($sitename) ?>">
            			<i class="fa fa-<?php echo $siteclass?> icon-with-bg" aria-hidden="true" title="<?php echo $this->translate($text); ?>"></i>
            			<span><?php echo $this->translate($text); ?></span>
        			</a>
    			</div>

				
			</li>
		<?php 
		endforeach; ?>	
	</ul>
</div>