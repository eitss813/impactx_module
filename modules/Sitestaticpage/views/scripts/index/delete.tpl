<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete.tpl 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div>
  <form method="post" class="global_form_popup">
    <div>
      <h3><?php echo "Delete Form Data" ?></h3>
      <br />
      <p>
        <?php echo "Are you sure you want to delete this form data ?" ?>
      </p>
      <br />
      <button type='submit'><?php echo $this->translate("Delete Data"); ?></button>
      <?php echo " or " ?> 
      <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
        <?php echo $this->translate("cancel"); ?></a>
    </div>
  </form>

  <?php if (@$this->closeSmoothbox): ?>
    <script type="text/javascript">
          TB_close();
    </script>
  <?php endif; ?>
</div>