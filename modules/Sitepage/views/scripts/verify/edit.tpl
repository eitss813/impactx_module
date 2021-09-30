<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<script>
  window.addEvent('domready', function() {
    textCounter($('comments'), 'counter-wrapper', 300)
  });

  function textCounter(field, field2, maxlimit)
  {
    var countfield = document.getElementById(field2);
    if (field.value.length > maxlimit) {
      field.value = field.value.substring(0, maxlimit);
      return false;
    } else {
      countfield.innerHTML = (maxlimit - field.value.length)+' characters left.';
    }

  }
</script>
<div class="global_form_popup">
  <?php echo $this->form->setAttrib('class', 'global_form')->render($this) ?>
</div>
