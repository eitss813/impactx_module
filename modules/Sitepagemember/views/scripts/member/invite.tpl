<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: invite.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  en4.core.runonce.add(function(){
    $('selectall').addEvent('click', function(event) {
      var el = $(event.target);
      $$('input[type=checkbox]').set('checked', el.get('checked'));
    })
  });
</script>
<?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
