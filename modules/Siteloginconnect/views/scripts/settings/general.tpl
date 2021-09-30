<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 SocialEngineAddons
 * @license    http://www.socialengine.com/license/
 * @version    $Id: general.tpl 9874 2013-02-13 00:48:05Z SocialEngineAddons $
 * @author     SocialEngineAddons
 */
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitelogin/externals/styles/sitelogin_style.css');
?>
<style type="text/css">
#facebook-wrapper,
#twitter-wrapper {
  display: block !important;
}
</style>
<div class="global_form">
  <?php if ($this->form->saveSuccessful): ?>
    <h3><?php echo $this->translate('Settings were successfully saved.');?></h3>
  <?php endif; ?>
  <?php echo $this->form->render($this) ?>
</div>

<?php if( Zend_Controller_Front::getInstance()->getRequest()->getParam('format') == 'html' ): ?>
  <script type="text/javascript">
    en4.core.runonce.add(function(){
      var req = new Form.Request($$('.global_form')[0], $('global_content'), {
        requestOptions : {
          url : '<?php echo $this->url(array()) ?>'
        },
        extraData : {
          format : 'html'
        }
      });
    });
  </script>
<?php endif; ?>