<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css');

$this->headScript()
        ->appendFile($baseUrl . 'externals/moolasso/Lasso.js')
        ->appendFile($baseUrl . 'externals/moolasso/Lasso.Crop.js')
        ->appendFile($baseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.Request.js')
        ->appendFile($baseUrl . 'externals/tagger/tagger.js')
        ->appendFile($baseUrl . 'application/modules/Album/externals/scripts/core.js');
$this->headTranslate(array('Save', 'Cancel', 'delete'));
?>
<?php if (empty($this->isajax)): ?>
 <!-- <h2>    
  <?php if( !$this->message_view): ?>
  <?php echo $this->translate('%1$s\'s Album: %2$s', $this->album->getOwner()->__toString(), $this->htmlLink($this->album, $this->translate($this->album->getTitle()))); ?>
  <?php else: ?>
    <?php echo $this->translate('%1$s\'s Album: %2$s', $this->album->getOwner()->__toString(), $this->album->getTitle()); ?>
  <?php endif; ?>
  </h2>-->

  <!--<?php if ("" != $this->album->getDescription()): ?>
    <p class="photo-description">
      <?php echo $this->album->getDescription() ?>
    </p>
  <?php endif ?>-->
  <div id='default_image_div'>
  <?php endif; ?>
  <?php include_once APPLICATION_PATH . '/application/modules/Sitealbum/views/scripts/_photoView.tpl'; ?>
  <?php if (empty($this->isajax)): ?>
  </div>
<?php endif; ?>
