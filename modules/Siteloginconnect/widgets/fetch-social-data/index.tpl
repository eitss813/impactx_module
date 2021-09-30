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
				$sitename=$site;
				if($site=="linkedin") {
					$sitename="LinkedIn";
				}
				$getBaseURL = $this->base_url . '/siteloginconnect/sync/' . $site;
				$text = "Sync with " . ucwords($sitename);
			 ?>
			<li>
				<div class="social-btn btn-<?php echo $site?> social-btn-lg social-btn-square <?php echo $site?>-seboxshadow seboxshadow">
        			<a href="<?php echo $getBaseURL ?>" aria-label="<?php echo ucwords($sitename) ?>">
            			<i class="fa fa-<?php echo $site?> icon-with-bg" aria-hidden="true" title="<?php echo $this->translate($text); ?>"></i>
            			<span><?php echo $this->translate($text); ?></span>
        			</a>
    			</div>
			</li>
		<?php 
		endforeach; ?>	
	</ul>
</div>