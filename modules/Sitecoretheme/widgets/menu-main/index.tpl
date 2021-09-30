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
$menu = $this->navigation()
  ->mainMenuSitecoretheme()
  ->setContainer($this->navigation)
  ->setRenderParentClass(true)
  ->setParentClass('sitecoretheme_main_menu_parent')
  //->setPartial(array('_navFontIcons.tpl', 'sitecoretheme'))
  ->render();
?>
<script type="text/javascript">
  (function () {
    var pannelElement = $(document).getElement('body');
    pannelElement.addClass('global_sitecoretheme_left_panel global_sitecoretheme_left_panel_<?php echo $this->menuPannelType ?>');
<?php if( $this->alwaysOpen ): ?>
      if (matchMedia('only screen and (min-width: 768px)').matches) {
        pannelElement.addClass('panel-open');
      }
<?php endif; ?>
  })();
</script>

<div class="sitecoretheme_menu_pannel <?php if( $this->alwaysOpen ): ?> sitecoretheme_menu_pannel_open<?php endif; ?> <?php if( !$this->menuIcons ): ?> _hide_menu_icons<?php endif; ?>">
  <div class="main_menu_navigation_fixed">
    <?php if( $this->viewer->getIdentity() ): ?>
      <?php $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('user_home'); ?>
      <div class="_main_menu-links-user" <?php if( $this->coverUserPhoto ): ?> style="background-image: url(<?php echo $this->coverUserPhoto->getPhotoUrl('thumb.cover') ?>)"<?php endif; ?>>
        <div class="_user_image">
          <a href="<?php echo $this->viewer()->getHref() ?>" >        
            <?php echo $this->itemPhoto($this->viewer(), 'thumb.icon') ?>
          </a>
        </div>
        <div class="_user_info">
          <div class="_user_name">
            <a href="<?php echo $this->viewer()->getHref() ?>" >
              <?php echo $this->viewer()->getTitle(); ?>
            </a>
          </div>
          <?php if( count($navigation) > 0 ): ?>
            <a class="_quicklink_arrow" id="sitecoretheme_user_quicklinks_action" href="javascript:void(0);"><i class="fa fa-angle-down"></i></a>
          <?php endif; ?>
        </div>
      </div>
      <?php if( count($navigation) > 0 ): ?>
        <div class="_user_quicklinks">
          <div class="_user_quicklinks_caret">
            <span class="_up_side"></span>
          </div>
          <div class="_quicklink_dropdown">
            <?php
            echo $this->navigation()
              ->menu()
              ->setContainer($navigation)
              ->setPartial(array('_navIcons.tpl', 'core'))
              ->render()
            ?>
          </div>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
  <div class="main_menu_left_panel_scroll scrollbars">
    <div class="main_menu_left_panel_section main_menu_navigation">
      <?php echo $menu; ?>
    </div> 
    <?php if( $this->userSettingsNavigation ): ?>
      <div class="clear main_menu_left_panel_section menu_left_panel_settings">
        <div class="main_menu_left_panel_section_header">
          <a href="javascript:;" class="menu_left_panel_section_link">
            <i></i>
            <span><?php echo $this->translate("Account Settings") ?></span>
          </a>
        </div>
        <div class="main_menu_left_panel_section_body">
          <?php
          // Render the menu
          echo $this->navigation()
            ->menu()
            ->setContainer($this->userSettingsNavigation)
            ->render();
          ?>
        </div>
      </div>
    <?php endif; ?>
    <?php if( $this->footerSection ): ?>
      <div class="clear main_menu_left_panel_section menu_left_panel_footer">
        <div class="main_menu_left_panel_section_body">
          <?php echo $this->content()->renderWidget("sitecoretheme.main-menu-footer", array(
            'logoPath' => $this->settings('sitecoretheme.header.logo.image', ''),
          )); ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>
<!--<div class="sitecoretheme_main_menu_toggle panel-toggle"></div>-->
<script type="text/javascript">
  var verticalThemePannelMainMenu = function (type) {
    var setMenuStorage = function (hasOpen) {
      if (typeof (Storage) !== "undefined") {
        hasOpen = hasOpen ? (new Date()) : 0;
        localStorage.setItem("verticalMenuMainOpen", hasOpen);
      }
    };
    var hasMenuOpened = function () {
      if (typeof (Storage) !== "undefined" && matchMedia('only screen and (min-width: 768px)').matches) {
        var openOn = localStorage.getItem("verticalMenuMainOpen", 0);
        if (openOn && openOn != 0) {
          var todayDate = new Date();
          var openDate = new Date(openOn);
          var timeDiff = Math.abs(todayDate.getTime() - openDate.getTime());
          return timeDiff >= 600;
        }
      }
      return false;
    };
    var pannelElement = $(document).getElement('body');
    var scrollContentEl = pannelElement.getElement('.layout_sitecoretheme_menu_main .main_menu_left_panel_scroll');
    var fixedEl = pannelElement.getElement('.layout_sitecoretheme_menu_main .main_menu_navigation_fixed');
    var diffHeaderHeight = 0;
    var setPannelTop = function () {
      if (pannelElement.getElement('.layout_page_header .layout_main')) {
        diffHeaderHeight = pannelElement.getElement('.layout_page_header').getCoordinates().height;
        if (diffHeaderHeight < 66) {
          diffHeaderHeight = 66;
        }
        pannelElement.getElement('.layout_sitecoretheme_menu_main .sitecoretheme_menu_pannel').setStyles({
          'top': diffHeaderHeight + 'px'
        });
      }
    };
    var openPanel = function (event) {
      if (!event) {
        pannelElement.addClass('panel-open');
      } else {
        pannelElement.toggleClass('panel-open');
      }
      setScrollBarContent();
      setMenuStorage(pannelElement.hasClass('panel-open'));
    };
    var setScrollBarContent = function () {
      setPannelTop();
      var contentHeight = (window.getSize().y - diffHeaderHeight) - fixedEl.getCoordinates().height;
      if (contentHeight != scrollContentEl.getCoordinates().height) {
        scrollContentEl.setStyle('height', contentHeight + 'px');
        scrollBar && scrollBar.updateScrollBars();
      }
    };
    var scrollBar;
    if (!pannelElement.getElement('.layout_page_header .layout_main .generic_layout_container .sitecoretheme_main_menu_toggle')) {
      new Element('div', {
        'class': 'sitecoretheme_main_menu_toggle fa header-panel-toggle'
      }).inject(pannelElement.getElement('.layout_page_header .layout_main .generic_layout_container'), 'top');
    }
    pannelElement.getElements('.sitecoretheme_main_menu_toggle').addEvent('click', openPanel);
    scrollContentEl.getElements('.sitecoretheme_main_menu_parent .collapse_icon').addEvent('click', function (event) {
      var hasOpen = $(event.target).getParent('.sitecoretheme_main_menu_parent').hasClass('submenu_expand');
      scrollContentEl.getElements('.sitecoretheme_main_menu_parent').removeClass('submenu_expand');
      if (!hasOpen) {
        $(event.target).getParent('.sitecoretheme_main_menu_parent').addClass('submenu_expand');
      }
    });
    scrollContentEl.getElements('.menu_left_panel_section_link').addEvent('click', function (event) {
      if ($(event.target).getParent('.main_menu_left_panel_section').hasClass('main_menu_left_panel_section_collapsed')){
      $(event.target).getParent('.main_menu_left_panel_section').removeClass('main_menu_left_panel_section_collapsed');
      } else {
        $(event.target).getParent('.main_menu_left_panel_section').addClass('main_menu_left_panel_section_collapsed');
      }
    });
    <?php if( $this->alwaysOpen ): ?>
      if (matchMedia('only screen and (min-width: 768px)').matches) {
        openPanel();
      }
      pannelElement.getElement('.layout_sitecoretheme_menu_main .sitecoretheme_menu_pannel').removeClass('sitecoretheme_menu_pannel_open');
<?php else: ?>
      $(document).getElement('body').addEvent('click', function (event) {
        $el = $(event.target);
        if (!$el.getParent('.layout_sitecoretheme_menu_main') && !$el.getParent('.sitecoretheme_main_menu_toggle') && !$el.hasClass('sitecoretheme_main_menu_toggle')) {
          pannelElement.removeClass('panel-open');
          setMenuStorage(0);
        }
      });
      setPannelTop();
      if (hasMenuOpened()) {
        openPanel();
      }
      pannelElement.addClass('global_sitecoretheme_panel_animation');
<?php endif; ?>
    window.addEvent('resize', setScrollBarContent);
    setScrollBarContent();
    setTimeout(setScrollBarContent, 500);
    scrollContentEl.scrollbars({
      scrollBarSize: 10,
      fade: !("ontouchstart" in document.documentElement),
      barOverContent: true
    });
    scrollBar = scrollContentEl.retrieve('scrollbars');
    scrollBar.element.getElement('.scrollbar-content-wrapper').setStyle('float', 'none');
    scrollBar.updateScrollBars();
    $('sitecoretheme_user_quicklinks_action') && $('sitecoretheme_user_quicklinks_action').addEvent('click', function () {
      fixedEl.getElement('._user_quicklinks').toggleClass('_user_quicklinks_show');
//      setTimeout(setScrollBarContent, 500);
    });
    en4.core.runonce.add(setScrollBarContent);
  };
  verticalThemePannelMainMenu('<?php echo $this->menuPannelType ?>');
</script>