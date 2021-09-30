<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesmultipleform/externals/styles/styles.css'); ?>
<div class="sesmultipleform_banner_container_wrapper sesbasic_bxs <?php echo $this->fullwidth ? 'isfull_width' : '' ; ?>" style="height:<?php echo $this->height.'px'; ?>;">
	<div class="sesmultipleform_banner_container">
    <div class="sesmultipleform_banner_img_container" style="height:<?php echo $this->height.'px'; ?>;">
        <?php if($this->banner_image): ?>
	        <img src="<?php echo Engine_Api::_()->sesmultipleform()->getFileUrl($this->banner_image) ?>" alt="" />
        <?php endif; ?>
    </div>
    <div class="sesmultipleform_banner_content" style="height:<?php echo $this->height.'px'; ?>;">
      <div class="sesmultipleform_banner_content_inner">
        <?php if($this->banner_title != '' || $this->description  != '') { ?>	
          <?php if($this->banner_title != ''){ ?>
            <h2 class="sesmultipleform_banner_title" style="color:#<?php echo $this->title_button_color; ?>"><?php echo $this->banner_title; ?></h2>
          <?php } ?>
        <?php } ?>
        <?php if($this->description  != ''){ ?>
          <p class="sesmultipleform_banner_des" style="color:#<?php echo $this->description_button_color; ?>"><?php echo $this->description ; ?></p>
        <?php } ?>
        <?php if($this->button1 || $this->button2 || $this->button3){ ?>
          <div class="sesmultipleform_banner_btns">
            <?php if($this->button1){ ?>
              <a data-effect="" href="<?php echo $this->button1_link ? $this->button1_link : 'javascript:void(0)'; ?>" onMouseOver="this.style.backgroundColor='#<?php echo $this->button1_mouseover_color; ?>'"   onMouseOut="this.style.backgroundColor='#<?php echo $this->button1_color; ?>'" style="color:#<?php echo $this->button1_text_color; ?>; background-color:#<?php echo $this->button1_color; ?>"><?php echo $this->translate($this->button1_text); ?></a>
            <?php } ?>
            <?php if($this->button2){ ?>
              <a data-effect="" href="<?php echo $this->button2_link ? $this->button2_link : 'javascript:void(0)'; ?>" onMouseOver="this.style.backgroundColor='#<?php echo $this->button2_mouseover_color; ?>'"   onMouseOut="this.style.backgroundColor='#<?php echo $this->button2_color; ?>'" style="color:#<?php echo $this->button2_text_color; ?>;background-color:#<?php echo $this->button2_color; ?>"><?php echo $this->translate($this->button2_text); ?></a>
            <?php } ?>
            <?php if($this->button3){ ?>
              <a  href="<?php echo $this->button3_link ? $this->button3_link : 'javascript:void(0)'; ?>" onMouseOver="this.style.backgroundColor='#<?php echo $this->button3_mouseover_color; ?>'"   onMouseOut="this.style.backgroundColor='#<?php echo $this->button3_color; ?>'" style="color:#<?php echo $this->button3_text_color; ?>;background-color:#<?php echo $this->button3_color; ?>"><?php echo $this->translate($this->button3_text); ?></a>
            <?php } ?>
          <?php } ?>
        </div> 
      </div>
    </div>
  </div>
</div>
