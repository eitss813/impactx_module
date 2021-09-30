<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _imageContent.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<script type="text/javascript">
    window.addEvent('domready', function () {
    var durationOfRotateImage = <?php echo!empty($this->defaultDuration) ? $this->defaultDuration : 500; ?>;
    var slideshowDivObj = $('slide-images');
    var imagesObj = slideshowDivObj.getElements('img');
    var indexOfRotation = 0;
    imagesObj.each(function(img, i){
      if(i > 0) {
        img.set('opacity',0);
      }
    }); 
    var show = function() {
      imagesObj[indexOfRotation].fade('out');
      indexOfRotation = indexOfRotation < imagesObj.length - 1 ? indexOfRotation+1 : 0;
      imagesObj[indexOfRotation].fade('in');
    };
    show.periodical(durationOfRotateImage);

});
</script>

<style type="text/css">
		.layout_sitecoretheme_images #slide-images{
			width: <?php echo!empty($this->slideWidth) ? $this->slideWidth . 'px;' : '100%'; ?>;
		}
		.layout_sitecoretheme_images .slideblok_image img{
			height: <?php echo $this->slideHeight . 'px;'; ?>;
		}
		@media (min-width: 980px) {
			.layout_sitecoretheme_images #slide-images{
				height: <?php echo $this->slideHeight . 'px;'; ?>;
			}
		}
</style>

<div class="wrapper-image slideblock" >
    <div class="" id="slide-images">
        <?php
        foreach ($this->list as $imagePath):
        if (!is_array($imagePath)):
        $iconSrc = "application/modules/Sitecoretheme/externals/images/" . $imagePath;
        else:
        $iconSrc = Engine_Api::_()->sitecoretheme()->displayPhoto($imagePath['file_id'], 'thumb.icon');
        endif;
        if (!empty($iconSrc)):
        ?>
        <div class="slideblok_image">
            <img src="<?php echo $iconSrc; ?>" />
        </div>
        <?php
        endif;
        endforeach;
        ?>
        <div class="slideoverlay"></div>
    </div>
    <div class="sitecoretheme_images_image_content">
        <div class="sitecoretheme_images_page_container">
           <div class="sitecoretheme_header_wrapper">
			<div class="sitecoretheme_images_top_head" id="sitecoretheme_landing_slider_header">
                <div class="sitecoretheme_images_top_head_left">
                    <?php if (!empty($this->showLogo)): ?>
                    <div class="layout_core_menu_logo">
                        <?php
                        $title = $this->coreSettings->getSetting('core_general_site_title', $this->translate('_SITE_TITLE'));
                        $logo = $this->logo;
                        $route = $this->viewer()->getIdentity() ? array('route' => 'sitecrowdfunding_general') : array('route' => 'default');
                        echo ($logo) ? $this->htmlLink($route, $this->htmlImage($logo, array('alt' => $title))) : $this->htmlLink($route, $title);
                        ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="sitecoretheme_images_top_head_right">
                    <div>
                        <?php if( $this->insideHeader == 1 ) : ?>
                          <?php if (Engine_Api::_()->hasModuleBootstrap('sitemenu')) : ?>
                            <?php
                            echo $this->content()->renderWidget("sitemenu.menu-main", $this->menuParams);
                            ?>
                          <?php else: ?>
                            <?php
                            // echo $this->content()->renderWidget("core.menu-main", array());
                             echo $this->content()->renderWidget("sitecoretheme.browse-menu-main", array(
                               'max' => $this->settings('sitecoretheme.landing.slider.max', 6)
                             ));
                            ?>
                          <?php endif; ?>
                        <?php endif; ?>
                        <?php if( $this->showSearch == 1 ) : ?>
                          <div id='menu_search_icon' class='' onclick="showSearchBox()">
                            <i class="fa fa-search"></i>
                          </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            </div>
            <div class="sitecoretheme_images_middle_content">
              <div class="sitecoretheme_images_middle_caption">
                  <h3><?php echo $this->translate($this->verticalHtmlTitle); ?></h3>
                  <?php if(!empty($this->description)) :?>
                    <p class="typewrite" data-period="200"  data-type='' >
                    <div id='moving_description_container' style='display: none;'>
                       <?php echo json_encode($this->description); ?>
                    </div>
                      <span class="wrap"></span>
                    </p>
                  <?php endif;?>
                  <?php if (!empty($this->verticalSignupLoginButton) && !$this->viewer->getIdentity()): ?>
                  <div class="spec_btnsblock">
                      <?php if (!empty($this->isSitemenuExist) && !empty($this->signupLoginPopup)): ?>
                        <a href="<?php echo $this->url(array(), "user_login", true) ?>" onClick="advancedMenuUserLoginOrSignUp('login', '', '', <?php echo $this->popupClosable; ?>);
                           return false;"><?php echo $this->translate("Sign In"); ?></a>
                         <a href="<?php echo $this->url(array(), "user_signup", true) ?>" onClick="advancedMenuUserLoginOrSignUp('signup', '', '', <?php echo $this->popupClosable; ?>);
                           return false;"><?php echo $this->translate("Sign Up"); ?></a>   
                      <?php else: ?>
                        <?php if (!empty($this->signupLoginPopup)): ?>
                          <a  class="popuplink user_auth_link" ><?php echo $this->translate("Sign In"); ?></a>
                          <a  class="popuplink user_signup_link" ><?php echo $this->translate("Sign Up"); ?></a>
                          <?php echo $this->content()->renderWidget("user.login-or-signup-popup");?>
                        <?php else: ?>
                          <a href="<?php echo $this->url(array(), "user_login", true) ?>" ><?php echo $this->translate("Sign In"); ?></a>
                          <a href="<?php echo $this->url(array(), "user_signup", true) ?>"><?php echo $this->translate("Sign Up"); ?></a>
                        <?php endif; ?>
                      <?php endif; ?>
                  </div>
                  <?php endif; ?>
              </div>  
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
//  function openCoreLoginSignUpPopups(popupType, url) {
//    if(popupType == 'signin') {
//      if($('user_auth_popup')) {
//        Smoothbox.open($('user_auth_popup'));
//      } else {
//        window.location.href = url;
//      }
//    } else if(popupType == 'signup') {
//      if($('user_signup_popup')) {
//        Smoothbox.open($('user_signup_popup'));
//      } else {
//        window.location.href = url;
//      }
//    }
//  }
</script>

<style type="text/css">
  .popuplink {
    cursor: pointer;
  }
</style>