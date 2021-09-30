<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<style>
<?php if ($this->insideHeader == 2) : ?>
		/*#global_page_core-index-index #global_header {display: block;}
		#global_page_core-index-index #sitecoretheme_landing_slider_header {display: none;}*/
<?php else: ?>
		/*#global_page_core-index-index #global_header {display: none;}
		#global_page_core-index-index #sitecoretheme_landing_slider_header {display: inline-block;}*/
<?php endif; ?>
	.layout_sitecoretheme_images .sitecoretheme_images_image_content {
		  background: rgba(0, 0, 0, <?php echo $this->settings('sitecoretheme.landing.slider.overlay.opacity', '20') /100 ?>);
	}
	.sitecoretheme_images_middle_content ._middle_form ._top._add_bottom {
	max-height: calc(100% - 166px);
		overflow-y: auto;
		 
}
	.sitecoretheme_images_middle_content ._middle_form .sliderform_tabs + ._top._add_bottom {
		max-height: calc(100% - 217px);
	}
  </style>

	<?php
	$baseURL = $this->baseUrl();
	$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/jquery.min.js');
	$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecoretheme/externals/scripts/typeWriter.js');
	?>
  <script type="text/javascript">
    if (typeof (window.jQuery) != 'undefined') {
      jQuery.noConflict();

<?php if ($this->removePadding): ?>
	      jQuery("#global_wrapper").css('padding-top', '0px');
<?php endif; ?>
      setTimeout(function () {
        if (jQuery(".layout_middle").children().length == 1) {
          jQuery("#global_footer").css('margin-top', '165px');
        }
      }, 100);
    }
    var widgetName = 'layout_sitecoretheme_images';

    window.addEvent('domready', function () {
      var durationOfRotateImage = <?php echo!empty($this->defaultDuration) ? $this->defaultDuration : 500; ?>;
      var slideshowDivObj = $('slide-images');
      var imagesObj = slideshowDivObj.getElements('img');
      var indexOfRotation = 0;
      imagesObj.each(function (img, i) {
        if (i > 0) {
          img.set('opacity', 0);
        }
      });
      var show = function () {
        imagesObj[indexOfRotation].fade('out');
        indexOfRotation = indexOfRotation < imagesObj.length - 1 ? indexOfRotation + 1 : 0;
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
    .layout_sitecoretheme_images .sitecoretheme_images_middle_content,
    .layout_sitecoretheme_images #slide-images{
      height: <?php echo $this->slideHeight . 'px;'; ?>;
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
				<div class="sitecoretheme_images_middle_content">
					<?php if ($this->settings('sitecoretheme.landing.slider.form.type')): ?>
						<div class="_middle_form _middle_<?php echo $this->settings('sitecoretheme.landing.slider.form.style', 'transparent') ?> _<?php echo $this->settings('sitecoretheme.landing.slider.form.position', 'left') ?>">
							<?php if ($this->settings('sitecoretheme.landing.slider.form.type') === 'user_login_signup'): ?>
							<div class='sliderform_tabs'>
								<ul class="_navigation">

									<li class="seaocoretheme_slider_form_tab active" data-target="seaocoretheme_slider_user_auth">
										<?php echo $this->translate('Sign In') ?>
									</li>
									<li class="seaocoretheme_slider_form_tab"  data-target="seaocoretheme_slider_user_signup">
										<?php echo $this->translate('Create Account') ?>
									</li>
								</ul>
							</div>
							<?php endif; ?>
							<div class="_top <?php if ($this->settings('sitecoretheme.landing.slider.form.bottom.item', 'user')): ?> _add_bottom <?php endif; ?> scrollbars">
								<?php if ($this->settings('sitecoretheme.landing.slider.form.type') === 'user_search'): ?>
									<?php if ($this->settings('sitecoretheme.landing.slider.form.heading', 'Welcome to community!')): ?>
										<h4><?php echo $this->translate($this->settings('sitecoretheme.landing.slider.form.heading', 'Welcome to community!')); ?></h4>
									<?php endif; ?>
									<div class="seaocoretheme_slider_form">
										<?php echo $this->content()->renderWidget("sitecoretheme.browse-user-search", array()); ?>
									</div>
								<?php elseif ($this->settings('sitecoretheme.landing.slider.form.type') === 'user_login_signup'): ?>
									<?php if ($this->settings('sitecoretheme.landing.slider.form.heading', 'Welcome to community!')): ?>
										<h4><?php echo $this->translate($this->settings('sitecoretheme.landing.slider.form.heading', 'Welcome to community!')); ?></h4>
									<?php endif; ?>			
									<div class="seaocoretheme_slider_form seaocoretheme_slider_user_auth">
										<?php $loginModule = Engine_Api::_()->hasModuleBootstrap('sitelogin') ? 'sitelogin' : 'user'; ?>
										<?php
										echo $this->action('login', 'auth', $loginModule, array(
											'disableContent' => true,
											'return_url' => '64-' . base64_encode($this->url())
										));
										?>
										<ul class="_navigation _footer_bottom mtop10 mbot15">
											<li class="seaocoretheme_slider_form_tab"  data-target="seaocoretheme_slider_user_signup">
												<?php echo $this->translate('Don\'t have an account? Sign Up') ?>
											</li>         
										</ul>
									</div>
									<div class="seaocoretheme_slider_form seaocoretheme_slider_user_signup _form_cont dnone">
										<?php
										$ifSiteLogin = Engine_Api::_()->hasModuleBootstrap('sitequicksignup');
										$signupModule = $ifSiteLogin ? 'sitequicksignup' : 'user';
										?>  
										<?php echo $this->action('index', 'signup', $signupModule, array('disableContent' => true)); ?>
										<ul class="_navigation _footer_bottom mtop10 mbot15">
											<li class="seaocoretheme_slider_form_tab" data-target="seaocoretheme_slider_user_auth">
												<?php echo $this->translate('Already a member? Sign In') ?>
											</li>          
										</ul>
									</div>
								<?php elseif ($this->settings('sitecoretheme.landing.slider.form.type') === 'user_login'): ?>
									<?php if ($this->settings('sitecoretheme.landing.slider.form.heading', 'Welcome to community!')): ?>
										<h4><?php echo $this->translate($this->settings('sitecoretheme.landing.slider.form.heading', 'Welcome to community!')); ?></h4>
									<?php endif; ?>									

									<div class="seaocoretheme_slider_form seaocoretheme_slider_user_auth">
										<?php $loginModule = Engine_Api::_()->hasModuleBootstrap('sitelogin') ? 'sitelogin' : 'user'; ?>
										<?php
										echo $this->action('login', 'auth', $loginModule, array(
											'disableContent' => true,
											'return_url' => '64-' . base64_encode($this->url())
										));
										?>
										<ul class="_navigation _footer_bottom mtop10 mbot15">
											<li class="seaocoretheme_slider_form_tab">
												<?php echo $this->translate('Don\'t have an account?') ?>
												<a class="user_signup_link" href="<?php echo $this->url(array(), "user_signup", true) ?>"><?php echo $this->translate("Sign Up"); ?></a>
											</li>         
										</ul>
									</div>
								<?php endif; ?>
							</div>
							<?php if ($this->settings('sitecoretheme.landing.slider.form.bottom.item', 'user')): ?>
								<?php								
								$footerContent = $this->content()->renderWidget("sitecoretheme.landing-page-listing", array('itemType' => $this->settings('sitecoretheme.landing.slider.form.bottom.item', 'user'), 'sortBy' => $this->settings('sitecoretheme.landing.slider.form.bottom.sort', 'creation_date'), 'limit' => 9, 'crousalView' => true, 'widget_id' => 'lp_image_footer_listing', 'showInfo' => 0));
								?>
								<?php if (strlen($footerContent) > 50): ?>
									<div class="_bottom">
										<?php if ($this->settings('sitecoretheme.landing.slider.form.bottom.heading', "Latest Registered Members")): ?>
											<h4><?php echo $this->translate($this->settings('sitecoretheme.landing.slider.form.bottom.heading', "Latest Registered Members")); ?></h4>
										<?php endif; ?>
										<?php echo $footerContent; ?>
									</div>
								<?php endif; ?>
							<?php endif; ?>
						</div>          
					<?php endif; ?>
					<?php if ($this->settings('sitecoretheme.landing.slider.frontImage.src')): ?>
						<div class="sitecoretheme_images_middle_front_image  _front_image_<?php echo $this->settings('sitecoretheme.landing.slider.frontImage.position', 'left') ?>">
							<div style="text-align: <?php echo $this->settings('sitecoretheme.landing.slider.frontImage.position', 'left') ?>">
								<img src="<?php echo $this->settings('sitecoretheme.landing.slider.frontImage.src'); ?>"/>
							</div>
						</div>
					<?php endif; ?>
					<div class="sitecoretheme_images_middle_caption">
						<h3><?php echo $this->translate($this->verticalHtmlTitle); ?></h3>
						<?php if (!empty($this->description)) : ?>
							<p class="typewrite" data-period="200"  data-type='' >
							<div id='moving_description_container' style='display: none;'>
								<?php echo json_encode($this->description); ?>
							</div>
							<span class="wrap"></span>
							</p>
						<?php endif; ?>
						<div class="spec_btnsblock">
							<a href='javascript:void(0);' onclick="gotoProject()" ><?php echo $this->translate("Explore Projects"); ?></a>
						</div>
						<?php /*
						<?php if (!empty($this->verticalSignupLoginButton) && !$this->viewer->getIdentity()): ?>
							<div class="spec_btnsblock">
								<a class="user_auth_link" href="<?php echo $this->url(array(), "user_login", true) ?>" ><?php echo $this->translate("Sign In"); ?></a>
								<a class="user_signup_link" href="<?php echo $this->url(array(), "user_signup", true) ?>"><?php echo $this->translate("Sign Up"); ?></a>
							</div>
						<?php endif; ?>
						*/ ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php if ($this->settings('sitecoretheme.landing.slider.form.type')): ?>
<!--	<style type="text/css">
		.sitecoretheme_images_middle_content .scrollbars .scrollbar-content{
			overflow-x: hidden !important;
			padding-right: 0 !important;
			padding-bottom: 0!important;
		}
		.sitecoretheme_images_middle_content .scrollbars .scrollbar-content .scrollbar-content-wrapper {
			margin-right: 0 !important;
			margin-bottom: 0!important;
		}
	</style>-->
		<script type="text/javascript">
	    (function () {
	      var handelerOnFocus = function (event) {
	        $(event.target).getParent('.form-wrapper').addClass('form-wapper-focus');
	      };
	      var handelerOnBlur = function (event) {
	        $(event.target).getParent('.form-wrapper').removeClass('form-wapper-focus');
	      };
				$$('.layout_sitecoretheme_images ._middle_form').each(function (el) {
							var maxHeight = <?php echo $this->slideHeight * 0.80; ?>//form.setSize().y;
							if(el.getElement('._bottom')){
								maxHeight = maxHeight - 147;
							}
							if(el.getElement('.sliderform_tabs')){
								maxHeight = maxHeight - el.getElement('.sliderform_tabs').offsetHeight;
							}

							el.getElement('._top').setStyle('maxHeight', maxHeight+'px');

				});
	      $$('.layout_sitecoretheme_images form').each(function (form) {
	        var isSignupForm = form.get('id') === 'signup_account_form';
	        form.getElements('input').each(function (el) {
	          var type = el.get('type');
	          if (type == 'email') {
	            el.getParent('.form-wrapper').addClass('form-email-wrapper');
	          }
	          if (el.get('type') == 'password') {
	            var showHideEl = new Element('div', {
	              'id': 'show-hide-password-element',
	              'class': 'show-hide-password-form-element fa fa-eye'
	            }).inject(el.getParent('.form-element'));
	            showHideEl.addEvent('click', function () {
	              if (el.get('type') == 'password') {
	                showHideEl.addClass('fa-eye-slash').removeClass('fa-eye');
	                el.set('type', 'text');
	              } else {
	                showHideEl.removeClass('fa-eye-slash').addClass('fa-eye');
	                el.set('type', 'password');
	              }
	            });
	            form.addEvent('submit', function () {
	              el.set('type', 'password');
	              showHideEl.removeClass('fa-eye-slash').addClass('fa-eye');
	            });
	          }
	          if ((type == 'text' || type == 'email' || type == 'password') && el.getParent('.form-wrapper').getElement('label').get('html')) {
	            el.set('placeholder', el.getParent('.form-wrapper').getElement('label').get('html'));
	            el.getParent('.form-wrapper').addClass('seaocoretheme_form_field');
	            el.addEvent('focus', handelerOnFocus);
	            el.addEvent('blur', handelerOnBlur);
	          }
	        });
					 if (isSignupForm) {
	            if (form.getElementById('password-element') && form.getElementById('passconf-element')) {
	              form.getElementById('password-element').getParent('.form-wrapper').addClass('_half_field');
	              form.getElementById('passconf-element').getParent('.form-wrapper').addClass('_half_field');
	            }
	            var canMakeSmallFileds = !!form.getElementById('language-element') && !!form.getElementById('timezone-element');						
	            if (form.getElementById('timezone-element') && !form.getElementById('timezone-option-label')) {
	              var el = form.getElementById('timezone');
	              var options = new Element('option', {
	                'id': 'timezone-option-label',
	                'disabled': 'disabled',
	                'class': 'seaocoretheme_form_field_option_label',
	                'html': el.getParent('.form-wrapper').getElement('label').get('html')

	              });
	              options.inject(el, 'top');
	              el.getParent('.form-wrapper').addClass('seaocoretheme_form_field');
	              if (canMakeSmallFileds) {
	                el.getParent('.form-wrapper').addClass('_half_field');
	              }
	            }
	            if (form.getElementById('language-element') && !form.getElementById('language-option-label')) {
	              var el = form.getElementById('language');
	              var options = new Element('option', {
	                'id': 'language-option-label',
	                'class': 'seaocoretheme_form_field_option_label',
	                'disabled': 'disabled',
	                'html': el.getParent('.form-wrapper').getElement('label').get('html')

	              });
	              options.inject(el, 'top');
	              el.getParent('.form-wrapper').addClass('seaocoretheme_form_field');
	              if (canMakeSmallFileds) {
	                el.getParent('.form-wrapper').addClass('_half_field');
	              }
	            }
	            if (form.getElementById('profile_type') && form.getElementById('profile_type').get('type') != 'hidden' && !form.getElementById('profile_type-option-label')) {
	              var el = form.getElementById('profile_type');
	              var addedFields = false;
	              el.getElements('option').each(function (el) {
	                if (el.get('value') == '') {
	                  el.set('html', el.getParent('.form-wrapper').getElement('label').get('html')).addClass('seaocoretheme_form_field_option_label');
	                  addedFields = true;
	                }
	              });
	              if (!addedFields) {
	                var options = new Element('option', {
	                  'id': 'profile_type-option-label',
	                  'class': 'seaocoretheme_form_field_option_label',
	                  'disabled': 'disabled',
	                  'html': el.getParent('.form-wrapper').getElement('label').get('html')

	                });
	                options.inject(el, 'top');
	              }
	              el.getParent('.form-wrapper').addClass('seaocoretheme_form_field seaocoretheme_profile_type_form_field');
	            }
	          }
	      });
				var scrollContentEl = $$('.layout_sitecoretheme_images ._middle_form .scrollbars')[0];
				scrollContentEl.scrollbars({
						scrollBarSize: 1,
						fade: !("ontouchstart" in document.documentElement),
						barOverContent: true
					});
				 var scrollBar = scrollContentEl.retrieve('scrollbars');
					scrollBar.element.getElement('.scrollbar-content-wrapper').setStyle('float', 'none');
					scrollBar.updateScrollBars();
					en4.core.runonce.add(function() {
						scrollBar.updateScrollBars();
					});
		      $$('.seaocoretheme_slider_form_tab').addEvent('click', function (event) {
						if(!$(this).get("data-target")) {
							return;
						}
		        $$('.seaocoretheme_slider_form').addClass('dnone');
		        $$('.' + $(this).get("data-target")).removeClass('dnone');
		        $$('.seaocoretheme_slider_form_tab').removeClass('active');
		        $$('.seaocoretheme_slider_form_tab[data-target="' + $(this).get("data-target") + '"]').addClass('active');
						scrollBar.updateScrollBars();
		      });
	    })();
		</script>
	<?php endif; ?>

<script type="text/javascript">
	// go to project
	function gotoProject(){
		var $j = jQuery.noConflict();
		var className = "layout_sitecrowdfunding_ajax_based_projects_home";
		$j('html, body').animate({
			scrollTop: $j(`.${className}`).offset().top - 70
		}, 1000);
	};
</script>