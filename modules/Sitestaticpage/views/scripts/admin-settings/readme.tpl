<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: readme.tpl 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2><?php echo 'Static Pages, HTML Blocks and Multiple Forms Plugin' ?></h2>

<div class="tabs">
  <ul class="navigation">
    <li class="active">
      <a href="<?php echo $this->baseUrl() . '/admin/sitestaticpage/settings/readme' ?>" ><?php echo 'Please go through these important points and proceed by clicking the button at the bottom of this page.' ?></a>
    </li>
  </ul>
</div>		

<?php include_once APPLICATION_PATH . '/application/modules/Sitestaticpage/views/scripts/admin-settings/faq_help.tpl'; ?>
<br />
<button onclick="form_submit();"><?php echo 'Proceed to enter License Key' ?> </button>

<script type="text/javascript" >
  function form_submit() {
		
    var url='<?php echo $this->url(array('module' => 'sitestaticpage', 'controller' => 'settings'), 'admin_default', true) ?>';
    window.location.href=url;
  }
</script>
