<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: update-confirmation.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<form method="post" class="global_form_popup">
  <div class="sitecrowdfunding_popup">
    <div>
        <h3><?php echo $this->translate('Change Package?'); ?></h3>
        <p>
            <?php echo $this->translate("Are you sure you want to change package for this Project? Once you change package, all the settings of this Project will be applied according to the new package, including features available, price, etc."); ?>
        </p>
        <br />
        <input type="hidden" name="package_id" value="<?php echo $this->package_id ?>" />
        <p>     
            <button type='submit'><?php echo $this->translate('Change'); ?></button>
            <!--or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>-->
        </p>
    </div>
  </div>
<a style="position: fixed;" href="javascript:void(0);" onclick="javascript:parent.Smoothbox.close();" class="popup_close fright"></a>
</form>
<?php if (@$this->closeSmoothbox): ?>
    <script type="text/javascript">
        TB_close();
    </script>
<?php endif; ?>