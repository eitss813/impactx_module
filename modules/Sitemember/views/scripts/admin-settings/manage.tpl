<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<h2><?php echo $this->translate("Advanced Members Plugin - Better Browse & Search, User Reviews, Ratings & Location Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>
<div>
  <?php echo $this->htmlLink(array('action' => 'index', 'reset' => false), $this->translate('Back to Global Settings'), array('class' => 'icon_sitemember_admin_back buttonlink')) ?>
</div>
<div class='clear'>
  <div class='settings'>
    <form class="global_form">
      <div>
        <h3><?php echo $this->translate("Profile Type - Location Field Mapping") ?> </h3>
        <p class="form-description">
          <?php echo $this->translate('This mapping will assign a "Location" type field for each Profile Type as the main location field for its users. After such a mapping for a Profile Type, the location entered by users in the mapped "Location" type field will be synced with the location entered by them from the ‘Edit Location’ page. The Members Location & Proximity Search results will be based on the locations entered in the mapped "Location" type fields for the different profile types.<br />Note: If you do not map a Location field for a Profile Type, then the search results for that profile type will be based on the locations entered from the ‘Edit Location’ page by the members belonging to that profile type. If at anytime you map a new location field for any profile type, then you must also sync member locations from ‘Member Locations’ section of this plugin.') ?>
        </p>
        <?php if (count($this->paginator) > 0): ?>
          <table class='admin_table' width="100%">
            <thead>
              <tr>
                <th align="left"><?php echo $this->translate("Profile Types") ?></th>
                <th align="left"><?php echo $this->translate("Location Type Field") ?></th>
                <th align="left"><?php echo $this->translate("Mapping") ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($this->paginator as $category): ?>
                <tr>
                  <td><?php echo $this->translate($category->label) ?></td>
                  <?php if (!empty($category->profile_type)): ?>
                    <td><?php echo $category->labelLocation ?></td>
                  <?php else: ?>
                    <td>---</td>
                  <?php endif; ?>
                  <td width="150">
                    <?php if (empty($category->profile_type)): ?>
                      <?php
                      echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitemember', 'controller' => 'settings', 'action' => 'map', 'option_id' => $category->option_id), $this->translate('Add'), array(
                          'class' => 'smoothbox',
                      ))
                      ?>
                    <?php else: ?>
                      <?php
                      echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitemember', 'controller' => 'settings', 'action' => 'delete', 'profilemap_id' => $category->profilemap_id), $this->translate('Remove'), array(
                          'class' => 'smoothbox',
                      ))
                      ?>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <br/>
          <div class="tip">
            <span><?php echo $this->translate("To enable Proximity and Geo-location search for Members on your site, please choose ‘Yes’ option for ‘Members Location & Proximity Search’ field from the ‘Global Settings’ section of this plugin.") ?></span>
          </div>
        <?php endif; ?>
      </div>
    </form>
  </div>
</div>