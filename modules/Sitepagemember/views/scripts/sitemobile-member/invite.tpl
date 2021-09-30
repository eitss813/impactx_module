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
	sm4.core.runonce.add(function(){
		$('#selectall').bind('click', function(event) {
			if(this.checked) {
				$("input[type='checkbox']").prop("checked",true).checkboxradio("refresh"); 
			} else {
				$("input[type='checkbox']").prop("checked",false).checkboxradio("refresh"); 
			}
		});
	});
</script>
<div class="ui-member-invite-popup">
	<?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
</div>