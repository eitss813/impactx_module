<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: readme.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<h2>
  <?php echo $this->translate("Advanced Members Plugin - Better Browse & Search, User Reviews, Ratings & Location Plugin"); ?>
</h2>
<div class="seaocore_admin_tabs">
  <ul class="navigation">
    <li class="active">
      <a href="<?php echo $this->baseUrl() . '/admin/sitemember/settings/readme' ?>" ><?php echo $this->translate('Please go through these important points and proceed by clicking the button at the bottom of this page.') ?></a>

    </li>
  </ul>
</div>

<?php include_once APPLICATION_PATH . '/application/modules/Sitemember/views/scripts/admin-settings/faq_help.tpl'; ?>
<br />
<button onclick="form_submit();"><?php echo $this->translate('Proceed to enter License Key') ?> </button>

<script type="text/javascript" >
  function form_submit() {
    var url = '<?php echo $this->url(array('module' => 'sitemember', 'controller' => 'settings'), 'admin_default', true) ?>';
    window.location.href = url;
  }
</script>