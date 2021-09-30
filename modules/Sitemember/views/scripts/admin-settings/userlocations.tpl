<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: userlocations.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2><?php echo $this->translate("Advanced Members Plugin - Better Browse & Search, User Reviews, Ratings & Location Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<?php if (!empty($this->map_count)): ?>
  <?php $memberCount = count($this->row); ?>
  <?php if ($memberCount > 0): ?>
    <div class="seaocore_settings_form">
      <div class="tip">
        <span>
          <?php echo 'To enable Proximity and Geo-location search for Members on your site, you need to sync the locations of all the members on your site with Google Places. Thus,<a href="' . $this->url(array('action' => 'usersink-location')) . '" class="smoothbox"> click here</a> to sync ' . $memberCount . ' members on your site with Google Places.'; ?>
        </span>
      </div>
    </div>
  <?php else: ?>
    <div class="seaocore_settings_form">
      <div class="tip">
        <span>
          <?php echo 'You are not having any site member to synchronize its location via Google Places API.'; ?>
        </span>
      </div>
    </div>
  <?php endif; ?>
<?php else : ?>
  <div class="tip">
    <span>  
      <?php echo $this->translate('You have currently not mapped “Location” type fields for any Profile Type on your site. Please map “Location” type fields by using the ‘Profile Type - Location Field Mapping’ field in the ‘Global Settings’ section of this plugin. This mapping will sync member locations which they enter from their ‘Edit Profile’ page and their ‘Edit My Location’ page, after they click on ‘Save’ button on these pages.'); ?>
    </span>
  </div>
<?php endif; ?>