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
	.layout_sitecoretheme_landing_page_header + .layout_core_content, .layout_sitecoretheme_landing_page_header + .layout_sitecoretheme_form_banner + .layout_core_content {
		margin-top: 70px;
	}
  body {
    <?php /*if ($this->imgPath): ?>
      background-image: url(<?php echo $this->imgPath ?>);
    <?php else: ?>
      background-image: url(application/modules/Sitecoretheme/externals/images/login-signup-bg.png);
    <?php endif;*/ ?>
    background-size: cover; 
    background-position: center; 
	  background-repeat: no-repeat;
	  background-attachment: fixed;
    position: relative;
  }
	#global_wrapper, #global_content_simple{
		padding-top: 40px;
	}
	#global_wrapper:before, #global_content_simple:befre {
		background: rgba(0, 0, 0, .3);
		content: " "; 
		top: 0; 
		bottom: 0; left: 0; 
		right: 0;
		position: absolute;
	}
	#global_content_simple {display: block;}
	#global_content{width: 100%;}
  div.layout_page_footer {background-image: none;}
	<?php if (!empty($this->gradientColor1) && !empty($this->gradientColor2)): ?>
		/* banner gradient */
		#global_wrapper, #global_content_simple {
			background: <?php echo $this->gradientColor2 ?>;  /* fallback for old browsers */
			background: -webkit-linear-gradient(to right,<?php echo $this->gradientColor1 ?>, <?php echo $this->gradientColor2 ?>);  /* Chrome 10-25, Safari 5.1-6 */
			background: linear-gradient(to right, <?php echo $this->gradientColor1 ?>, <?php echo $this->gradientColor2 ?>); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
		}
	<?php endif; ?>
</style>


<div id="form_logo_wrapper">
	<?php
	$siteTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title', $this->translate('_SITE_TITLE'));
	$siteTitle = $this->translate($siteTitle);
	$logo = $this->logo;
	$route = $this->viewer()->getIdentity() ? array('route' => 'user_general', 'action' => 'home') : array('route' => 'default');
	?>
	<?php if (!empty($logo)) : ?>
		<?php echo $this->htmlLink($route, $this->htmlImage($logo, array('alt' => $siteTitle))); ?>
	<?php else : ?>
		<h1>
			<?php echo $this->htmlLink($route, $siteTitle); ?>
		</h1>
	<?php endif; ?>
</div>
<?php
$signinHref = $this->url(array('module' => 'user', 'action' => 'login', 'return_url' => $this->return_url ), 'user_login', true);
$signupBottomText = $this->translate("Already a member? <a href='%s'>Sign In</a>", $signinHref);
?>
<?php
$redirect_url = $_GET['redirect_url'];
if($redirect_url == '/net/members/home'){
	$signupHref = $this->url(array('module' => 'user', 'action' => 'index', 'return_url' => !empty($this->return_url) ? $this->return_url : '64-' . base64_encode($this->url(array('action'=>'home'),"user_general")) ), 'user_signup', true);
	$signinBottomText = $this->translate("Don't have an account? <a href='%s'>Sign Up</a>", $signupHref);
}else{
	$signupHref = $this->url(array('module' => 'user', 'action' => 'index', 'return_url' => !empty($this->return_url) ? $this->return_url : '64-' . base64_encode($this->url()) ), 'user_signup', true);
	$signinBottomText = $this->translate("Don't have an account? <a href='%s'>Sign Up</a>", $signupHref);
}
?>
<div id="vertical-form-banner-signup" style="display:none">
	<?php echo $signupBottomText; ?>
</div>
<div id="vertical-form-banner-signin" style="display:none">
	<?php echo $signinBottomText; ?>
</div>

<script type="text/javascript">
  var signupBottomText = '';
  var signinBottomText = '';
  en4.core.runonce.add(function () {
    $$('.layout_core_content form #login_via_siteotp_otp-wrapper').each(function (el) {
      if (el.getParent('.layout_core_content')) {
        el.removeClass('form-wrapper').inject(el.getParent('form').getElementById('password-label'), 'after');
      }
    });
    $$('.layout_core_content form #forgot-wrapper').each(function (el) {
      el.inject(el.getParent('form').getElementById('fieldset-buttons'));
    });
    $$('.layout_core_content form').each(function (form) {
      $('form_logo_wrapper').inject(form.getParent('.layout_core_content'), 'top');
    });
    $$('.layout_core_content form#user_form_login').each(function (form) {
      form.getElements('input').each(function (el) {
        if (el.get('id') !== 'password') {
          return;
        }
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
      });
    });

    var bottomText = '';
    var url = window.location.href;
    if (url.indexOf('signup') !== -1) {
      bottomText = $('vertical-form-banner-signup').get('html');
    } else {
      bottomText = $('vertical-form-banner-signin').get('html');
    }
    var bottomTextElement = new Element('p', {
      'class': 'sitecoretheme_signin_signup_switch',
      'html': bottomText,
    });
    bottomTextElement.inject($$('.layout_middle')[0]);

	// Remove the duplicate container
	var contentElements = document.getElementsByClassName('layout_page_core_error_requireuser');
	if(contentElements.length > 1){
	  if(contentElements[1]){
		  contentElements[1].remove();
	  }
	}
	var switchElements = document.getElementsByClassName('sitecoretheme_signin_signup_switch');
	if(switchElements.length > 1){
	  if(switchElements[1]){
		  switchElements[1].remove();
	  }
	}

  });

</script>