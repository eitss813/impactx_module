<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: utility.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/pluginLink.tpl'; ?>
<h2><?php echo $this->translate("Directory / Pages - Videos Extension") ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<h3>
  <?php echo $this->translate('Page Video Utilities'); ?>
</h3>
<p>
  <?php echo $this->translate("This page contains utilities to help configure and troubleshoot the page video plugin.") ?>
</p>
<br/>

<div class="settings">
  <form>
    <div>
      <h3><?php echo $this->translate("Ffmpeg Version") ?></h3>
      <p class="form-description"><?php echo $this->translate("This will display the current installed version of ffmpeg.") ?></p>
      <textarea><?php echo $this->version; ?></textarea><br/><br/><br/>

      <h3><?php echo $this->translate("Supported Video Formats") ?></h3>
      <p class="form-description"><?php echo $this->translate('This will run and show the output of "ffmpeg -formats". Please see this page for more info.') ?></p>
      <textarea><?php echo $this->format; ?></textarea><br/><br/>
      <?php if (TRUE): ?>
      <?php else: ?>
      <?php endif; ?>
    </div>
  </form>
</div>