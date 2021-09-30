<?php
/**
 * SocialEngine
 *
 * @category   Application_Module
 * @package    Siteuseravatar
 * @copyright  Copyright 2017-2018 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    index.tpl 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2><?php echo $this->translate('Member Avatars Plugin') ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>


<?php if( $this->imageSRC ): ?>
  <script type="text/javascript">
    function saveFormSettings() {
      $('review_global').set('action', $('review_global').get('action').replace('/preview', '')).submit();
    }
  </script>
  <div style="margin: 0 auto 20px; background-color: #f5f5f5;padding: 10px;">

    <div style="background: #fff;border: 1px solid #ccc;overflow: hidden;padding: 30px;text-align: center;">
      <h2>Preview of Avatar Initials</h2>
      <div style="text-align: center;">
        <img src="<?php echo $this->imageSRC ?>" width="200px;" height="200px;" />
        <img src="<?php echo $this->imageSRC ?>" width="48px;" height="48px;"/>
      </div>
      <?php if( $this->needToSubmit ): ?>
        <br />
        <button style="margin-top: 10px;" onclick="saveFormSettings()"> Save Settings </button>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

<div class='seaocore_settings_form'>
  <div class='settings'>
    <?php
    echo $this->form->render($this);
    ?>
  </div>
</div>