<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: readme.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2><?php echo $this->translate('Advanced Photo Albums Plugin') ?></h2>
<div class="seaocore_admin_tabs clr">
  <ul class="navigation">
    <li class="active">
      <a href="<?php echo $this->baseUrl() . '/admin/sitealbum/settings/readme' ?>" ><?php echo $this->translate('Please go through these important points and proceed by clicking the button at the bottom of this page.') ?></a>

    </li>
  </ul>
</div>

<?php include_once APPLICATION_PATH . '/application/modules/Sitealbum/views/scripts/admin-settings/faq_help.tpl'; ?>
<br />
<button onclick="form_submit();"><?php echo $this->translate('Continue') ?> </button>

<script type="text/javascript" >

  function form_submit() {
	
    var url='<?php echo $this->url(array('module' => 'sitealbum', 'controller' => 'settings', 'action'=>'index'), 'admin_default', true) ?>';
    window.location.href=url;
  }

</script>