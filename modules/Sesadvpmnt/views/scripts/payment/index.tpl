<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvpmnt
 * @package    Sesadvpmnt
 * @copyright  Copyright 2019-2020 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl  2019-04-25 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
 ?>
<?php if($this->error): ?>
	<p><?php echo $this->message; ?></p>
<?php else: ?>
	<script src="https://js.stripe.com/v3/"></script>
	<script>
	  var stripe = Stripe("<?php echo $this->publishKey; ?>");
	  stripe.redirectToCheckout({
	    sessionId: '<?php echo $this->session->id; ?>'
	  }).then(function (result) {
	    console.log(result);
	  });
	</script>
<?php endif; ?>
