<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: enabled.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo 'Disable Package ?'; ?></h3>
    <p>
      <?php echo 'Are you sure you want to disable this package? Disabling this package will make it unavailable to users while creating projects, and while changing package of their projects. Disabling will not affect the existing projects of this package.'; ?>
    </p>
    <br />
    <p>
      <input type="hidden" name="confirm" value="<?php echo $this->project_id ?>"/>
      <button type='submit'><?php echo 'Disable'; ?></button>
      or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo 'cancel'; ?></a>
    </p>
  </div>
</form>
<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>