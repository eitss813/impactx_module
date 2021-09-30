<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo $this->translate('Remove Mapping?'); ?></h3>
    <p>
      <?php echo $this->translate('Are you sure that you want to remove this “Location” type field as the primary location field for searching Members of this Profile Type? (Note that after removing this mapping, the location of members of this Profile Type for searching will still be the one of the previously mapped “Location” type field, until they edit their location.)'); ?>
    </p>
    <br />
    <p>
      <input type="hidden" name="confirm" value="<?php echo $this->profilemap_id ?>"/>
      <button type='submit'><?php echo $this->translate('Remove'); ?></button>
      or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
    </p>
  </div>
</form>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
        TB_close();
  </script>
<?php endif; ?>
