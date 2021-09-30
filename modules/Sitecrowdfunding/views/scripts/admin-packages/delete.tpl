<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo 'Delete Package ?'; ?></h3>
    <p>
      <?php echo 'Are you sure that you want to delete this Package ? It will not be recoverable after being deleted.'; ?>
    </p>
    <br />
    <p>
      <input type="hidden" name="confirm" value="<?php echo $this->project_id ?>"/>
      <button type='submit'><?php echo 'Delete'; ?></button>
      or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo 'cancel'; ?></a>
    </p>
  </div>
</form>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>
