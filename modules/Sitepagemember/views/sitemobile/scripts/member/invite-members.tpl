<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: invite-members.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
?>

<?php echo $this->form->render($this) ?>

<script type="text/javascript">

	sm4.core.runonce.add(function() { 
		sm4.core.Module.autoCompleter.attach("user_ids", '<?php echo $this->url(array('module' => 'sitepagemember', 'controller' => 'index', 'action' => 'getmembers', 'page_id' => $this->page_id), 'default', true) ?>', {'singletextbox': false, 'limit':10, 'minLength': 1, 'showPhoto' : true, 'search' : 'text'}, 'toValues'); 
	});

</script>
