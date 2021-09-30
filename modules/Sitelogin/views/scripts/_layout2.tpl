<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitelogin/externals/styles/sitelogin_style.css');
?>
<?php 
$textsize=$this->button_width*(16/80);
$fontsize=$this->button_width*(16/500);
$isEnableSocialAccount=$this->renderTwitter||$this->renderFacebook||$this->renderLinkedin||$this->renderGoogle||$this->renderInstagram||$this->renderPinterest||$this->renderYahoo||$this->renderOutlook||$this->renderVk;
    if($this->layout==4){
        $shapeClass='social-btn-circle';
    }elseif($this->layout==5){
        $shapeClass='social-btn-rounded-lg';
    }else{
        $shapeClass='social-btn-square';
    }
?>
<?php if( $isEnableSocialAccount ): ?>
  <div class="social-btn-square-box">
    <?php if( !empty($this->renderFacebook) ): ?>
    <div style="margin-left: 5px;margin-right:5px;float: left; text-align: center;">
    <div style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;" class="social-btn btn-facebook social-btn-icon <?php echo $shapeClass ?> <?php echo empty($this->showShadow)?'':'facebook-seboxshadow seboxshadow';?>">
        <a href="<?php echo $this->baseUrl() . '/sitelogin/auth/facebook' ?>" aria-label="Facebook">
            <i class="fa fa-facebook" style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;line-height:<?php echo $this->button_width ?>px;font-size:<?php echo $fontsize ?>em;" aria-hidden="true" title="Facebook"></i>
            <span>Facebook</span>
        </a>
    </div>
    </div>
    <?php endif; ?>  
    
    <?php if( !empty($this->renderTwitter) ): ?>
    <div style="margin-left: 5px;margin-right:5px;float: left; text-align: center;">
    <div style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;" class="social-btn btn-twitter social-btn-icon <?php echo $shapeClass ?> <?php echo empty($this->showShadow)?'':'twitter-seboxshadow seboxshadow';?> ">
        <a href="<?php echo $this->baseUrl() . '/sitelogin/auth/twitter' ?>" aria-label="Twitter">
            <i class="fa fa-twitter" style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;line-height:<?php echo $this->button_width ?>px;font-size:<?php echo $fontsize ?>em;" aria-hidden="true" title="Twitter"></i>

            <span>Twitter</span>
        </a>
    </div>
    </div>
    <?php endif; ?>
    
    <?php if( !empty($this->renderGoogle) ): ?>
    <div style="margin-left: 5px;margin-right:5px;float: left; text-align: center;">
    <div style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;" class="social-btn btn-google social-btn-icon <?php echo $shapeClass ?> <?php echo empty($this->showShadow)?'':'google-seboxshadow seboxshadow';?> ">
        <a  href="<?php echo $this->baseUrl() . '/sitelogin/auth/google' ?>" aria-label="google plus">
            <i class="fa fa-google" style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;line-height:<?php echo $this->button_width ?>px;font-size:<?php echo $fontsize ?>em;" aria-hidden="true" title="Google Plus"></i>

            <span>Google</span>
        </a>
    </div>
    </div>
    <?php endif; ?>
    
    <?php if( !empty($this->renderInstagram) ): ?>
    <div style="margin-left: 5px;margin-right:5px;float: left; text-align: center;">
    <div style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;" class="social-btn btn-instagram social-btn-icon <?php echo $shapeClass ?> <?php echo empty($this->showShadow)?'':'instagram-seboxshadow seboxshadow';?> ">
        <a href="<?php echo $this->baseUrl() . '/sitelogin/auth/instagram' ?>" aria-label="instagram">
            <i class="fa fa-instagram" style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;line-height:<?php echo $this->button_width ?>px;font-size:<?php echo $fontsize ?>em;" aria-hidden="true" title="Instagram"></i>

            <span>Instagram</span>
        </a>
    </div>
    </div>
    <?php endif; ?>
    
    <?php if( !empty($this->renderLinkedin) ): ?>
    <div style="margin-left: 5px;margin-right:5px;float: left; text-align: center;">
    <div style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;" class="social-btn btn-linkedin social-btn-icon <?php echo $shapeClass ?> <?php echo empty($this->showShadow)?'':'linkedin-seboxshadow seboxshadow';?> ">
        <a href="<?php echo $this->baseUrl() . '/sitelogin/auth/linkedin' ?>" aria-label="Linkedin">
            <i class="fa fa-linkedin" style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;line-height:<?php echo $this->button_width ?>px;font-size:<?php echo $fontsize ?>em;" aria-hidden="true" title="Linkedin"></i>

            <span>LinkedIn</span>
        </a>
    </div>
    </div>
    <?php endif; ?>
    
    
    
    <?php if( !empty($this->renderPinterest) ): ?>
    <div style="margin-left: 5px;margin-right:5px;float: left; text-align: center;">
    <div style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;" class="social-btn btn-pinterest social-btn-icon <?php echo $shapeClass ?> <?php echo empty($this->showShadow)?'':'pinterest-seboxshadow seboxshadow';?> ">
        <a href="<?php echo $this->baseUrl() . '/sitelogin/auth/pinterest' ?>" aria-label="pinterest">
            <i class="fa fa-pinterest-p" style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;line-height:<?php echo $this->button_width ?>px;font-size:<?php echo $fontsize ?>em;" aria-hidden="true" title="Pinterest"></i>
            <span>Pinterest</span>
        </a>
    </div>
    </div>
    <?php endif; ?>
    
    <?php if( !empty($this->renderYahoo) ): ?>
    <div style="margin-left: 5px;margin-right:5px;float: left; text-align: center;">
     <div style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;" class="social-btn btn-yahoo social-btn-icon <?php echo $shapeClass ?> <?php echo empty($this->showShadow)?'':'yahoo-seboxshadow seboxshadow';?>">
          <a href="<?php echo $this->baseUrl() . '/sitelogin/auth/yahoo' ?>" aria-label="yahoo">
              <i class="fa fa-yahoo" style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;line-height:<?php echo $this->button_width ?>px;font-size:<?php echo $fontsize ?>em;" aria-hidden="true" title="Yahoo"></i>
              <span>Yahoo</span>
          </a>
      </div>
    </div>
    <?php endif; ?>
    
    <?php if( !empty($this->renderOutlook) ): ?>
    <div style="margin-left: 5px;margin-right:5px;float: left; text-align: center;">
     <div style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;" class="social-btn btn-hotmail social-btn-icon <?php echo $shapeClass ?> <?php echo empty($this->showShadow)?'':'hotmail-seboxshadow seboxshadow';?> ">
        <a href="<?php echo $this->baseUrl() . '/sitelogin/auth/outlook' ?>" aria-label="Outlook">
            <i class="fa fa-envelope" style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;line-height:<?php echo $this->button_width ?>px;font-size:<?php echo $fontsize ?>em;" aria-hidden="true" title="Outlook"></i>

            <span>Outlook</span>
        </a>
    </div>
    </div>
    <?php endif; ?>
    
    <?php if( !empty($this->renderFlickr) ): ?>
    <div style="margin-left: 5px;margin-right:5px;float: left; text-align: center;">
    <div style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;" class="social-btn btn-flickr social-btn-icon <?php echo $shapeClass ?> <?php echo empty($this->showShadow)?'':'flickr-seboxshadow seboxshadow';?> ">
        <a href="<?php echo $this->baseUrl() . '/sitelogin/auth/flickr' ?>" aria-label="Flickr">

            <i class="fa fa-flickr" style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;line-height:<?php echo $this->button_width ?>px;font-size:<?php echo $fontsize ?>em;" aria-hidden="true" title="Flickr"></i>

            <span>Flickr</span>
        </a>
    </div>
    </div>
    <?php endif; ?>
    
    <?php if( !empty($this->renderVk) ): ?>
    <div style="margin-left: 5px;margin-right:5px;float: left; text-align: center;">
    <div style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;" class="social-btn btn-vk social-btn-icon <?php echo $shapeClass ?> <?php echo empty($this->showShadow)?'':'vk-seboxshadow seboxshadow';?> ">
        <a href="<?php echo $this->baseUrl() . '/sitelogin/auth/vk' ?>" aria-label="Vkontakte">

            <i class="fa fa-vk" style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;line-height:<?php echo $this->button_width ?>px;font-size:<?php echo $fontsize ?>em;" aria-hidden="true" title="Vkontakte"></i>

            <span>Vkontakte</span>
        </a>
    </div>
    </div>
    <?php endif; ?>
    
</div>
<?php endif; ?>