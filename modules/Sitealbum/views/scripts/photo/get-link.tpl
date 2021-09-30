<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-link.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css'); ?>

<div class="global_form_popup">
  <?php if ($this->subjectType == 'album'): ?>
    <h3><?php echo $this->translate("Share Album"); ?></h3>
  <?php else: ?>
    <h3><?php echo $this->translate("Share Photo"); ?></h3> 
  <?php endif; ?>
  <div class="mtop10">
    <?php if ($this->subjectType == 'album'): ?>
      <?php echo $this->translate("You can use this link to share this  photo album with anyone, even if they don't have an account on this website. Anyone with the link will be able to see your  photo album."); ?>
    <?php else: ?>
      <?php echo $this->translate("You can use this link to share this photo with anyone, even if they don't have an account on this website. Anyone with the link will be able to see your photo."); ?>
    <?php endif;?>
  </div>
  <div class="mtop10">
    
    <textarea style="height:65px;width:450px" id="text-box" class="text-box" onclick="select_all();"> <?php echo $this->url; ?> </textarea>
  </div>

  <button  class="fright" onclick="parent.Smoothbox.close();" ><?php echo $this->translate('Okay') ?></button>
  <?php if (empty($this->noSendMessege)): ?>
    <div>
      <a href= "<?php echo $this->url(array('controller' => 'photo', 'action' => 'compose', 'subject' => $this->subject->getGuid(),), 'sitealbum_extended', true); ?>" class="buttonlink fleft sitealbum_icon_message"><?php echo $this->translate('Send in message'); ?></a>
    </div>
  <?php endif; ?>
</div>
<script>
//      window.addEvent('load', function() {
//        select_all();
//      });
      function select_all()
      {
        var text_val = document.getElementById('text-box');
        text_val.select();
      }
</script>