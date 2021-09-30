<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css')
?>
<?php if (!$this->upload_button): ?>
    <div class="seaocore_button">
        
        <?php if(Engine_Api::_()->sitealbum()->openAddNewPhotosInLightbox()):?>
        <a href="<?php echo $this->url(array('action' => 'upload'), 'sitealbum_general', true) ?>" data-SmoothboxSEAOClass="seao_add_photo_lightbox" class="menu_album_quick album_quick_upload seao_smoothbox"><?php echo $this->translate($this->upload_button_title) ?>
        </a>
        <?php else:?>
        <a href="<?php echo $this->url(array('action' => 'upload'), 'sitealbum_general', true) ?>" class="menu_album_quick album_quick_upload"><?php echo $this->translate($this->upload_button_title) ?>
        </a>
        
        <?php endif;?>
    </div>
<?php else: ?>
    <div>
        
        <?php if(Engine_Api::_()->sitealbum()->openAddNewPhotosInLightbox()):?>
        <a href="<?php echo $this->url(array('action' => 'upload'), 'sitealbum_general', true) ?>" data-SmoothboxSEAOClass="seao_add_photo_lightbox" class="seao_smoothbox button"><?php echo $this->translate($this->upload_button_title) ?>
        </a>
        <?php else:?>
        <a href="<?php echo $this->url(array('action' => 'upload'), 'sitealbum_general', true) ?>" class="button"><?php echo $this->translate($this->upload_button_title) ?>
        </a>
        <?php endif;?>
    </div>
<?php endif; ?>