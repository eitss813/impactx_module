<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: make-page-profile-photo.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class='global_form_popup' style="padding: 10px;">
  <form method="post" >
    <div>
      <div>
        <h3><?php echo $this->translate('Make Page Profile Photo'); ?></h3>
        <p>
          <?php echo $this->translate("Do you want to make this photo your page profile photo?"); ?>
        </p>
        <br />
        <?php echo $this->itemPhoto($this->photo) ?>
        <br /><br />
        <p>
          <input type="hidden" name="confirm" value="true"/>
          <button type='submit'><?php echo $this->translate('Save'); ?></button>
          <?php echo $this->translate('or'); ?> <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
        </p>
      </div>
    </div>
  </form>
</div>
<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>