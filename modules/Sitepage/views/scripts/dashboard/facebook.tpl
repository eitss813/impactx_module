<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: foursquare.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitepage/externals/styles/style_sitepage_dashboard.css');
?>
<?php echo $this->form->setAttrib('class', 'global_form_popup global_form sitepage_fbconnect_form')->render($this) ?>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>
<style type="text/css">
  .global_form{width:550px;}
  .global_form > div{
  	float:none;
  }
</style>