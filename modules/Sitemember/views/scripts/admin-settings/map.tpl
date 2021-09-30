<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: map.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<?php
$this->form->setDescription($this->translate("Select a “Location” type profile field to be used as the primary location for searching members of this Profile Type based on location & proximity, then click 'Add'.<br /> Note: To sync locations of users who have already entered their location for the selected profile field with “Members Location & Proximity Search”, you need to sync the members from the ‘Member Locations’ section of this plugin. If for this Profile Type, you are mapping a new “Location” type field, then the location which users have entered from their ‘Edit My Location’ page will be automatically synced with the selected field.<br /> Please make sure that this is your final selection, because deleting this field might create inconsistencies on your site for location based members searching."));
$this->form->getDecorator('Description')->setOption('escape', false);
?>
<div class="settings global_form_popup">
  <?php echo $this->form->setAttrib('class', 'global_form')->render($this) ?>
</div>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>