<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitelogin/externals/styles/sitelogin_style.css');
?>
<?php
$textsize=$this->button_width*(16/80);
$fontsize=$this->button_width*(16/500);
$isEnableSocialAccount=$this->renderTwitter||$this->renderFacebook||$this->renderLinkedin||$this->renderGoogle||$this->renderInstagram||$this->renderPinterest||$this->renderYahoo||$this->renderOutlook||$this->renderVk;
if($this->layout==1){
$shapeClass='social-btn-circle';
}elseif($this->layout==2){
$shapeClass='social-btn-rounded-lg';
}else{
$shapeClass='social-btn-square';
}
?>
<?php if( $isEnableSocialAccount ): ?>

<div class="social-btn-circle-box">
    <?php if( !empty($this->renderFacebook) ): ?>
    <div style="margin-left: 5px;margin-right:5px;float: left; height:<?php echo ($this->button_width+20) ?>px;" class="circle-box-height">
        <div style="display: inline-block;" class="btn-facebook <?php echo $shapeClass ?> social-btn-icon-with-text <?php echo empty($this->showShadow)?'':'facebook-seboxshadow';?>">
            <a href="<?php echo $this->baseUrl() . '/sitelogin/auth/facebook' ?>" aria-label="Facebook">
                <i class="fa fa-facebook" style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;line-height:<?php echo $this->button_width ?>px;font-size:<?php echo $fontsize ?>em;" aria-hidden="true" title="Facebook"></i>
                <span class="social-btn-text" style="font-size:<?php echo $textsize ?>px">Facebook</span>
            </a>
        </div>
    </div>
    <?php endif; ?>
    <?php if( !empty($this->renderTwitter) ): ?>
    <div style="margin-left: 5px;margin-right:5px;float: left; height:<?php echo ($this->button_width+20) ?>px;" class="circle-box-height">
        <div style="display: inline-block;" class="btn-twitter <?php echo $shapeClass ?> social-btn-icon-with-text <?php echo empty($this->showShadow)?'':'twitter-seboxshadow';?>">
            <a href="<?php echo $this->baseUrl() . '/sitelogin/auth/twitter' ?>" aria-label="Twitter">
                <i class="fa fa-twitter" style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;line-height:<?php echo $this->button_width ?>px;font-size:<?php echo $fontsize ?>em;" aria-hidden="true" title="Twitter"></i>
                <span class="social-btn-text" style="font-size:<?php echo $textsize ?>px">Twitter</span>
            </a>
        </div>
    </div>
    <?php endif; ?>
    <?php if( !empty($this->renderGoogle) ): ?>
    <div style="margin-left: 5px;margin-right:5px;float: left; height:<?php echo ($this->button_width+20) ?>px;" class="circle-box-height">
        <div style="display: inline-block;" class="btn-google <?php echo $shapeClass ?> social-btn-icon-with-text <?php echo empty($this->showShadow)?'':'google-seboxshadow';?>">
            <a href="<?php echo $this->baseUrl() . '/sitelogin/auth/google' ?>" aria-label="google plus">
                <i class="fa fa-google" style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;line-height:<?php echo $this->button_width ?>px;font-size:<?php echo $fontsize ?>em;" aria-hidden="true" title="Google Plus"></i>
                <span class="social-btn-text" style="font-size:<?php echo $textsize ?>px">Google</span>
            </a>
        </div>
    </div>
    <?php endif; ?>
    <?php if( !empty($this->renderInstagram) ): ?>
    <div style="margin-left: 5px;margin-right:5px;float: left; height:<?php echo ($this->button_width+20) ?>px;" class="circle-box-height">
        <div style="display: inline-block;" class="btn-instagram <?php echo $shapeClass ?> social-btn-icon-with-text <?php echo empty($this->showShadow)?'':'instagram-seboxshadow';?>">
            <a href="<?php echo $this->baseUrl() . '/sitelogin/auth/instagram' ?>" aria-label="instagram">
                <i class="fa fa-instagram" style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;line-height:<?php echo $this->button_width ?>px;font-size:<?php echo $fontsize ?>em;" aria-hidden="true" title="Instagram"></i>
                <span class="social-btn-text" style="font-size:<?php echo $textsize ?>px">Instagram</span>
            </a>
        </div>
    </div>
    <?php endif; ?>
    <?php if( !empty($this->renderLinkedin) ): ?>
    <div style="margin-left: 5px;margin-right:5px;float: left; height:<?php echo ($this->button_width+20) ?>px;" class="circle-box-height">
        <div style="display: inline-block;" class="btn-linkedin <?php echo $shapeClass ?> social-btn-icon-with-text <?php echo empty($this->showShadow)?'':'linkedin-seboxshadow';?>">
            <a href="<?php echo $this->baseUrl() . '/sitelogin/auth/linkedin' ?>" aria-label="Linkedin">
                <i class="fa fa-linkedin" style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;line-height:<?php echo $this->button_width ?>px;font-size:<?php echo $fontsize ?>em;" aria-hidden="true" title="Linkedin"></i>
                <span class="social-btn-text" style="font-size:<?php echo $textsize ?>px">LinkedIn</span>
            </a>
        </div>
    </div>
    <?php endif; ?>

    <?php if( !empty($this->renderPinterest) ): ?>
    <div style="margin-left: 5px;margin-right:5px;float: left; height:<?php echo ($this->button_width+20) ?>px;" class="circle-box-height">
        <div style="display: inline-block;" class="btn-pinterest <?php echo $shapeClass ?> social-btn-icon-with-text <?php echo empty($this->showShadow)?'':'pinterest-seboxshadow';?>">
            <a href="<?php echo $this->baseUrl() . '/sitelogin/auth/pinterest' ?>" aria-label="pinterest">
                <i class="fa fa-pinterest-p" style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;line-height:<?php echo $this->button_width ?>px;font-size:<?php echo $fontsize ?>em;" aria-hidden="true" title="Pinterest"></i>
                <span class="social-btn-text" style="font-size:<?php echo $textsize ?>px">Pinterest</span>
            </a>
        </div>
    </div>
    <?php endif; ?>
    <?php if( !empty($this->renderYahoo) ): ?>
    <div style="margin-left: 5px;margin-right:5px;float: left; height:<?php echo ($this->button_width+20) ?>px;" class="circle-box-height">
        <div style="display: inline-block;" class="btn-yahoo <?php echo $shapeClass ?> social-btn-icon-with-text <?php echo empty($this->showShadow)?'':'yahoo-seboxshadow';?>">
            <a href="<?php echo $this->baseUrl() . '/sitelogin/auth/yahoo' ?>" aria-label="yahoo">
                <i class="fa fa-yahoo" style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;line-height:<?php echo $this->button_width ?>px;font-size:<?php echo $fontsize ?>em;" aria-hidden="true" title="Yahoo"></i>
                <span class="social-btn-text" style="font-size:<?php echo $textsize ?>px">Yahoo</span>
            </a>
        </div>
    </div>
    <?php endif; ?>
    <?php if( !empty($this->renderOutlook) ): ?>
    <div style="margin-left: 5px;margin-right:5px;float: left; height:<?php echo ($this->button_width+20) ?>px;" class="circle-box-height">
        <div style="display: inline-block;" class="btn-hotmail <?php echo $shapeClass ?> social-btn-icon-with-text <?php echo empty($this->showShadow)?'':'hotmail-seboxshadow';?>">
            <a href="<?php echo $this->baseUrl() . '/sitelogin/auth/outlook' ?>" aria-label="Outlook">
                <i class="fa fa-envelope" style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;line-height:<?php echo $this->button_width ?>px;font-size:<?php echo $fontsize ?>em;" aria-hidden="true" title="Outlook"></i>
                <span class="social-btn-text" style="font-size:<?php echo $textsize ?>px">Outlook</span>
            </a>
        </div>
    </div>
    <?php endif; ?>
    <?php if( !empty($this->renderFlickr) ): ?>
    <div style="margin-left: 5px;margin-right:5px;float: left; height:<?php echo ($this->button_width+20) ?>px;" class="circle-box-height">
        <div style="display: inline-block;" class="btn-flickr <?php echo $shapeClass ?> social-btn-icon-with-text <?php echo empty($this->showShadow)?'':'flickr-seboxshadow';?>">
            <a href="<?php echo $this->baseUrl() . '/sitelogin/auth/flickr' ?>" aria-label="Flickr">
                <i class="fa fa-flickr" style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;line-height:<?php echo $this->button_width ?>px;font-size:<?php echo $fontsize ?>em;" aria-hidden="true" title="Flickr"></i>
                <span class="social-btn-text" style="font-size:<?php echo $textsize ?>px">Flickr</span>
            </a>
        </div>
    </div>
    <?php endif; ?>
    <?php if( !empty($this->renderVk) ): ?>
    <div style="margin-left: 5px;margin-right:5px;float: left; height:<?php echo ($this->button_width+20) ?>px;" class="circle-box-height">
        <div style="display: inline-block;" class="btn-vk <?php echo $shapeClass ?> social-btn-icon-with-text <?php echo empty($this->showShadow)?'':'vk-seboxshadow';?>">
            <a href="<?php echo $this->baseUrl() . '/sitelogin/auth/vk' ?>" aria-label="Vkontakte">
                <i class="fa fa-vk" style="width:<?php echo $this->button_width ?>px;height:<?php echo $this->button_width ?>px;line-height:<?php echo $this->button_width ?>px;font-size:<?php echo $fontsize ?>em;" aria-hidden="true" title="Vkontakte"></i>

                <span class="social-btn-text" style="font-size:<?php echo $textsize ?>px">Vkontakte</span>
            </a>
        </div>
    </div>
    <?php endif; ?>


</div>
<?php endif; ?>