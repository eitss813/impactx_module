<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-source.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css'); ?>

<div class="global_form_popup">
	<h3><?php echo $this->translate( "Your Photos Badge code" ) ; ?></h3>
	<div>
	  <ul>
	    <li class="mtop10">
	      <?php echo $this->translate( "Copy the below HTML code and paste it into the source code for your web page." ) ; ?>
	    </li>
	    <li class="text-box">
	      <?php echo $this->code ; ?>
	    </li>
      <br /><br />
	    <li>
	      <button onclick="parent.Smoothbox.close();" ><?php echo $this->translate( 'Okay' ) ?></button>
	    </li>
	  </ul>
	</div>
</div>
<style type="text/css">
.text-box{
	border: 2px solid #ccc;
	padding:5px;
	width:600px;
	overflow:hidden;
	margin-top:10px;
}
</style>