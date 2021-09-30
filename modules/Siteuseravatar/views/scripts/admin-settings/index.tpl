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
<style type="text/css">
  .button {
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
    border-radius: 3px;
    padding: .5em 1em;
    font-weight: 700;
    border: none;
    background-color: #619dbe;
    border: 1px solid #50809b;
    color: #fff;
    text-shadow: 0 -1px 0 rgba(0,0,0,.3);
    font-family: arial,sans-serif;
  }
  .button:hover{text-decoration: none;}
</style>
<?php if( count($this->navigation) ): ?>
  <div class='tabs seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>
<?php if( !file_exists(APPLICATION_PATH . '/public/Siteuseravatar/fonts') ) : ?>
  <div class="tip" >
    <span style="float: none; display: block;">
      You can add more font styles for Avatar Initials. These font styles can also be used for characters of other supported languages. To download these font styles, please  
      <a href="<?php
      echo $this->url(array('module' => 'siteuseravatar',
        'controller' => 'settings',
        'action' => 'download'
        ), 'admin_default', true);
      ?>" onclick='Smoothbox.open(en4.core.loader)'>click here</a>.
    </span>
  </div>

<?php endif; ?>
<div style='margin-bottom: 10px;' >
  <span style="float: none; display: block; line-height:30px; ">
    If you want to check how the below configured settings will get reflected at user side then you check its preview by clicking on: 
    <a class='button' href="<?php
    echo $this->url(array('module' => 'siteuseravatar',
      'controller' => 'settings',
      'action' => 'preview'
      ), 'admin_default', true);
    ?>">Preview</a>
  </span>
</div>
<?php if( !empty($this->noPhotosUsers) ): ?>
  <div class="tip" >
    <span style="float: none; display: block;">
      <?php echo $this->translate('There are %s users which have not set any profile photos. To set default avatar initials for these user, please
', count($this->noPhotosUsers)); ?>
      <a href="<?php
      echo $this->url(array('module' => 'siteuseravatar',
        'controller' => 'settings',
        'action' => 'add-avatar'
        ), 'admin_default', true);
      ?>" onclick='Smoothbox.open(en4.core.loader)'>click here</a>.
    </span>
  </div>
<?php endif; ?>
<div class='seaocore_settings_form'>
  <div class='settings'>
    <?php
    echo $this->form->render($this);
    ?>
  </div>
</div>