<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: verify.tpl  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
 ?>
 <?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesnewsletter/externals/styles/styles.css'); ?>
<div class="sesnewsletter_verify_page">
<p>
<?php if($this->message == 2) { ?>
<?php echo $this->translate("This link is not valid for your email id."); ?>
<?php } else if($this->message == 1) { ?>
<?php echo $this->translate("Your mail is verified successfully."); ?>
<?php } else if($this->message == 0) { ?>
<?php echo $this->translate("This link is not valid for your email id."); ?>
<?php } ?>
</p>
<a href=""><?php echo $this->translate("Go to Home Page"); ?></a>
</div>
