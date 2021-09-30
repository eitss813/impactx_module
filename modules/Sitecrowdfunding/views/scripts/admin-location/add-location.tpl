<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: add-location.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
  <?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>

<script type="text/javascript">  
  function allRegion(){
      document.getElementById('regions-wrapper').style.display = 'none';
   
  }
  
  window.addEvent('domready', function() {
    allRegion();
  });
  
</script>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>

<style type="text/css">
	.form-label{
		width:95px !important;
	}
	#regions-element input{
		margin-bottom:5px;
	}
	#regions-element a{
		float:left;
	}
	select{
		max-width:300px;
	}
</style>