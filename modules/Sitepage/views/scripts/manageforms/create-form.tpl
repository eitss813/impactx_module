<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: create-form.tpl 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */ 
?>
<div style="width:400px;">
<?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
</div>
<style type="text/css">
.form-label label{
	margin-bottom:5px;
	font-weight:bold;
	display:block;
}
.form-element input[type="text"]{
	width:100%;
}
</style>
