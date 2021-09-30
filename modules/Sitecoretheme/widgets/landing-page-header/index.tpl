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
  body #global_header {
    display: none;
  }
  body #sitecoretheme_landing_slider_header {
    display: inline-block;
  }
</style>

<?php
$baseURL = $this->baseUrl();
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/jquery.min.js');
//$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecoretheme/externals/scripts/typeWriter.js');
?>
<script type="text/javascript">
  if (typeof (window.jQuery) != 'undefined') {
    jQuery.noConflict();

<?php if( $this->removePadding ): ?>
      jQuery("#global_wrapper").css('padding-top', '0px');
<?php endif; ?>
    setTimeout(function () {
      if (jQuery(".layout_middle").children().length == 1) {
        jQuery("#global_footer").css('margin-top', '165px');
      }
    }, 100);
  }
  var widgetName = 'sitecoretheme_landing_header';
</script>
<?php if( !empty($this->isSitemenuExist) && (!empty($this->verticalSignupLoginLink) || !empty($this->verticalSignupLoginButton)) ): ?>

  <?php
  $this->headLink()
    ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemenu/externals/styles/style_sitemenu.css');
  ?>
  <?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitemenu/externals/scripts/core.js');
  ?>
<?php endif; ?>
<div class="sitecoretheme_header_wrapper">
  <div class="sitecoretheme_header_top_head" id="sitecoretheme_landing_slider_header">
    <div class="sitecoretheme_header_top_head_left">
      <?php if( !empty($this->showLogo) ): ?>
        <div class="layout_core_menu_logo _website_logo">
          <?php
          $title = $this->settings('core_general_site_title', $this->translate('_SITE_TITLE'));
          $logo = $this->logo;
          $route = $this->viewer()->getIdentity() ? array('route' => 'user_general', 'action' => 'home') : array('route' => 'default');
          echo ($logo) ? $this->htmlLink($route, $this->htmlImage($logo, array('alt' => $title))) : $this->htmlLink($route, $title);
          ?>
        </div>
				<div class="layout_core_menu_logo _alternate_logo">
						<?php
						$title = $this->settings('core_general_site_title', $this->translate('_SITE_TITLE'));
						$logo = $this->alternateLogo;
						$route = $this->viewer()->getIdentity() ? array('route' => 'user_general', 'action' => 'home') : array('route' => 'default');
						echo ($logo) ? $this->htmlLink($route, $this->htmlImage($logo, array('alt' => $title))) : $this->htmlLink($route, $title);
						?>
				</div>
      <?php endif; ?>
    </div>
    <div class="sitecoretheme_header_top_head_right">
      <div>
        <?php if( ($this->headerStyle == "3") && Engine_Api::_()->hasModuleBootstrap('sitemenu')) : ?>
          <?php
          echo $this->content()->renderWidget("sitemenu.menu-main", $this->menuParams);
          ?>
        <?php else: ?>
          <?php
          echo $this->content()->renderWidget("sitecoretheme.browse-menu-main", array(
            'max' => $this->settings('sitecoretheme.landing.header.max', $this->settings("sitecoretheme.landing.header.max", 6))
          ));
          ?>
        <?php endif; ?>
				<?php if($this->settings('sitecoretheme.landing.header.showMiniMenu', 1) && 0):?>
				<div class="sitecoretheme_minimenu">
					<?php //echo $this->content()->renderWidget($this->isSitemenuEnable && $this->settings('sitecoretheme.header.siteminimenu.enable', 1) ? "sitemenu.menu-mini" : "seaocore.menu-mini", $this->miniMenuParams); ?>
				</div>
				<?php endif; ?>
        <?php if( $this->showSearch == 1 ) : ?>
          <div class='header_search_box'>
            <div id='menu_search_icon' onclick="showSearchBox()">
              <i class="fa fa-search"></i>
            </div>
            <?php echo $this->content()->renderWidget("sitecoretheme.search-box", array('sitecoretheme_search_width' => 300)); ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
<?php if( $this->showSearch == 1 ) : ?>
    if ($('menu_search_icon') && $$('.layout_sitecoretheme_search_box')) {
      $$('.layout_sitecoretheme_search_box').each(function (item) {
        item.style.display = 'none';
      });
    }
    function showSearchBox() {
      $$('.layout_sitecoretheme_search_box').each(function (item) {
        item.style.display = 'block';
      });
    }

    function hideSearchBox() {
      $$('.layout_sitecoretheme_search_box').each(function (item) {
        item.style.display = 'none';
      });
    }

<?php endif; ?>
  en4.core.runonce.add(function () {
    var headerFixed = function () {
      if (window.getScrollTop() > 50) {
        if (!$$('.layout_sitecoretheme_landing_page_header').hasClass('sitecoretheme_landing_header_fixed')[0]) {
          $$('.layout_sitecoretheme_landing_page_header').addClass('sitecoretheme_landing_header_fixed');
        }
      } else {
        if ($$('.layout_sitecoretheme_landing_page_header').hasClass('sitecoretheme_landing_header_fixed')[0]) {
          $$('.layout_sitecoretheme_landing_page_header').removeClass('sitecoretheme_landing_header_fixed');
        }
      }
    };
    headerFixed();
    window.addEvent('scroll', headerFixed);
  });
</script>
<?php if( !empty($this->signupLoginPopup) ): ?>
  <?php
  echo $this->content()->renderWidget("seaocore.login-or-signup-popup", array(
    'popupVisibilty' => $this->popupVisibilty,
    'allowClose' => $this->popupClosable,
    'autoOpenLogin' => $this->autoShowPopup == 1,
    'autoOpenSignup' => $this->autoShowPopup == 2
  ));
  ?>
<?php endif; ?>