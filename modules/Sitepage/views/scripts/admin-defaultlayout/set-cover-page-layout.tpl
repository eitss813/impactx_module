<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: savelayout.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="global_form_popup">
  <h3><?php echo $this->translate('Change Column for Page Cover Photo?'); ?></h3>
  <p>
    <?php echo $this->translate('Are you sure you want to change the column for Page Cover Photo of profiles of directory items / pages on your site to the one selected by you? The profile layouts of all the existing directory items / pages on your site will also be reset to the one selected by you.'); ?>
  </p>
  <br />
  <p>    
    <button onclick='window.parent.continuePageLayout(); return false;'><?php echo $this->translate('Continue'); ?></button>
    or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
  </p>
</div>
<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>