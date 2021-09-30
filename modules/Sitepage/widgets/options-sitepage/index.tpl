<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div id="profile_options">
  <?php
		echo $this->navigation()
          ->menu()
          ->setContainer($this->gutterNavigation)
          ->setUlClass('navigation sitepages_gutter_options')
          ->render();
  ?>
</div>