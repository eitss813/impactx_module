<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitelogin/externals/styles/sitelogin_style.css');
?>
<?php 
$isEnableSocialAccount=$this->renderTwitter||$this->renderFacebook||$this->renderLinkedin||$this->renderGoogle||$this->renderInstagram||$this->renderPinterest||$this->renderYahoo||$this->renderOutlook||$this->renderVk;
    if($this->layout==7){
        $shapeClass='social-botton-rounded';
    }elseif($this->layout==8){
        $shapeClass='social-btn-rounded-lg';
    }else{
        $shapeClass='social-btn-square';
    }
    
    
?>

<?php if( $isEnableSocialAccount ): ?>
<div class="social-btn-large-box">
    <?php if( !empty($this->renderFacebook) ): ?>
    <div style="width:<?php echo $this->button_width?>%;float: left;">
    <div class="social-btn btn-facebook social-btn-lg <?php echo $shapeClass ?> <?php echo empty($this->showShadow)?'':'facebook-seboxshadow seboxshadow';?>">
        <a href="<?php echo $this->baseUrl() . '/sitelogin/auth/facebook' ?>" aria-label="Facebook">
            <i class="fa fa-facebook" aria-hidden="true" title="Facebook"></i>
            <span>Facebook</span>
        </a>
    </div>
    </div>
    <?php endif; ?>
    <?php if( !empty($this->renderTwitter) ): ?>
    <div style="width:<?php echo $this->button_width?>%;float: left;">
      <div class="social-btn btn-twitter social-btn-lg <?php echo $shapeClass ?> <?php echo empty($this->showShadow)?'':'twitter-seboxshadow seboxshadow';?>">
        <a href="<?php echo $this->baseUrl() . '/sitelogin/auth/twitter' ?>" aria-label="Twitter">
            <i class="fa fa-twitter" aria-hidden="true" title="Twitter"></i>
            <span>Twitter</span>
        </a>
      </div>
    </div>
    <?php endif; ?>
    <?php if( !empty($this->renderGoogle) ): ?>
    <div style="width:<?php echo $this->button_width?>%;float: left;">
    <div class="social-btn btn-google social-btn-lg <?php echo $shapeClass ?> <?php echo empty($this->showShadow)?'':'google-seboxshadow seboxshadow';?>">
        <a href="<?php echo $this->baseUrl() . '/sitelogin/auth/google' ?>" aria-label="google plus">
            <i class="fa fa-google" aria-hidden="true" title="Google Plus"></i>
            <span>Google</span>
        </a>
    </div>
    </div>
    <?php endif; ?>
    <?php if( !empty($this->renderInstagram) ): ?>
    <div style="width:<?php echo $this->button_width?>%;float: left;">
      <div class="social-btn btn-instagram social-btn-lg <?php echo $shapeClass ?> <?php echo empty($this->showShadow)?'':'instagram-seboxshadow seboxshadow';?>">
        <a href="<?php echo $this->baseUrl() . '/sitelogin/auth/instagram' ?>" aria-label="instagram">
            <i class="fa fa-instagram" aria-hidden="true" title="Instagram"></i>
            <span>Instagram</span>
        </a>
      </div>
    </div>
    <?php endif; ?>
    <?php if( !empty($this->renderLinkedin) ): ?>
    <div style="width:<?php echo $this->button_width?>%;float: left;">
      <div class="social-btn btn-linkedin social-btn-lg <?php echo $shapeClass ?> <?php echo empty($this->showShadow)?'':'linkedin-seboxshadow seboxshadow';?>">
        <a href="<?php echo $this->baseUrl() . '/sitelogin/auth/linkedin' ?>" aria-label="Linkedin">
            <i class="fa fa-linkedin" aria-hidden="true" title="Linkedin"></i>
            <span>LinkedIn</span>
        </a>
      </div>
    </div>
    <?php endif; ?>
    
    <?php if( !empty($this->renderPinterest) ): ?>
    <div style="width:<?php echo $this->button_width?>%;float: left;">
      <div class="social-btn btn-pinterest social-btn-lg <?php echo $shapeClass ?> <?php echo empty($this->showShadow)?'':'pinterest-seboxshadow seboxshadow';?>">
        <a href="<?php echo $this->baseUrl() . '/sitelogin/auth/pinterest' ?>" aria-label="pinterest">
            <i class="fa fa-pinterest-p" aria-hidden="true" title="Pinterest"></i>
            <span>Pinterest</span>
        </a>
      </div>
    </div>
    <?php endif; ?>
    
    <?php if( !empty($this->renderYahoo) ): ?>
    <div style="width:<?php echo $this->button_width?>%;float: left;">
      <div class="social-btn btn-yahoo social-btn-lg <?php echo $shapeClass ?> <?php echo empty($this->showShadow)?'':'yahoo-seboxshadow seboxshadow';?>">
        <a href="<?php echo $this->baseUrl() . '/sitelogin/auth/yahoo' ?>" aria-label="yahoo">
            <i class="fa fa-yahoo" aria-hidden="true" title="Yahoo"></i>
            <span>Yahoo</span>
        </a>
      </div>
    </div>
   <?php endif; ?>
    
   <?php if( !empty($this->renderOutlook) ): ?>
    <div style="width:<?php echo $this->button_width?>%;float: left;">
      <div class="social-btn btn-hotmail social-btn-lg <?php echo $shapeClass ?> <?php echo empty($this->showShadow)?'':'hotmail-seboxshadow seboxshadow';?>">
        <a href="<?php echo $this->baseUrl() . '/sitelogin/auth/outlook' ?>" aria-label="Outlook">
            <i class="fa fa-envelope" aria-hidden="true" title="Outlook"></i>
            <span>Outlook</span>
        </a>
      </div>
    </div>
    <?php endif; ?>
    
    <?php if( !empty($this->renderFlickr) ): ?>
    <div style="width:<?php echo $this->button_width?>%;float: left;">
      <div class="social-btn btn-flickr social-btn-lg <?php echo $shapeClass ?> <?php echo empty($this->showShadow)?'':'flickr-seboxshadow seboxshadow';?>">
        <a href="<?php echo $this->baseUrl() . '/sitelogin/auth/flickr' ?>" aria-label="Flickr">
            <i class="fa fa-flickr" aria-hidden="true" title="Flickr"></i>
            <span>Flickr</span>
        </a>
    </div>
    </div>
    <?php endif; ?>
    
    <?php if( !empty($this->renderVk) ): ?>
    <div style="width:<?php echo $this->button_width?>%;float: left;">
      <div class="social-btn btn-vk social-btn-lg <?php echo $shapeClass ?> <?php echo empty($this->showShadow)?'':'vk-seboxshadow seboxshadow';?>">
        <a href="<?php echo $this->baseUrl() . '/sitelogin/auth/vk' ?>" aria-label="Vkontakte">
            <i class="fa fa-vk" aria-hidden="true" title="Vkontakte"></i>
            <span>Vkontakte</span>
        </a>
      </div>
    </div>
    <?php endif; ?>
   
  </div>

<?php if($this->button_width>50): ?>
<style>
    .social-btn-large-box > div {
  float: none !important;
  margin: auto;

}
</style>
<?php endif; ?>
<?php endif; ?>