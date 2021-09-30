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
<script type="text/javascript">
  // todo: 5.2.1 Upgrade => Uncaught TypeError: $(document) is not a function
  document.getElement('body').addClass('global_sitecoretheme_header_body_wapper');
  $$('.responsive_search_toggle').hide();
  $$('.search_title').hide();
</script>
<?php
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitemobile/externals/scripts/core.js')
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js')
?>
<?php
$isSiteadvsearchEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteadvsearch');
$viewerId = $this->viewer()->getIdentity();

$displayWidgets = $this->displayWidgets;
?>
<?php if( in_array('search_box', $displayWidgets) ) : ?>
<?php
  $this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<?php endif; ?>
<?php if( $this->headerStyle !=2 || $this->menuPosition == 2): ?>
<div class="sitecoretheme_top_header sitecoretheme_top_header_one" id="sitecoretheme_top_header_wrapper">
  <?php if(in_array('sociallink', $displayWidgets) ) : ?>
  <div class="sitecoretheme_social-sites">
    <?php echo $this->content()->renderWidget("sitecoretheme.menu-social-sites", array()); ?>
  </div>
  <?php endif; ?>

  <div class="sitecoretheme_top_header_container">
    <?php if( $this->menuPosition == 2 && $this->showMenu && in_array('main_menu', $displayWidgets) && empty($this->menuParams) ): ?>
    <div class="_main_menu_toggle_wapper <?php if( $this->settings('sitecoretheme.header.menu.style', 'slide') == 'slide' && $this->settings('sitecoretheme.header.menu.alwaysOpen', '0') ): ?> _main_menu_toggle_hide <?php endif; ?>" >
      <a class="sitecoretheme_main_menu_toggle _main_menu_toggle" href="javascript:void(0);"><i></i></a>
    </div>
    <?php endif; ?>
    <?php if( in_array('logo', $displayWidgets) ) : ?>
    <div class="sitecoretheme_logo">
      <?php echo $this->content()->renderWidget("core.menu-logo", $this->logoParams); ?>
      <?php echo $this->content()->renderWidget("core.menu-logo", $this->alternateLogoParams); ?>
    </div>
    <?php endif; ?>

    <?php if( $this->menuPosition != 2 && $this->showMenu && in_array('main_menu', $displayWidgets) && $this->headerStyle ==1 ): ?>
    <div class="sitecoretheme_mainmenu">
      <?php echo $this->content()->renderWidget("sitecoretheme.browse-menu-main", array()); ?>
    </div>
    <?php endif; ?>


    <div class="search_menu">

      <?php if( in_array('search_box', $displayWidgets) ) : ?>
      <div class="sitecoretheme_search">

        <?php $route_name= Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName(); ?>
        <?php $actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();?>
        <!-- based on route ans action shown search -->
        <?php if( ($route_name=='default' && $actionName=='index')) : ?>
        <?php elseif( 'sitepage_entry_view' != $route_name && $route_name !='sitepage_initiatives' ) : ?>

        <div>
          <i id="responsive_search_toggle" class="responsive_search_toggle fa fa-search" style="font-size: 18px;"><span class="search_title">Search</span></i>

        </div>
        <?php endif; ?>
        <div id="sitecoretheme_fullsite_search" class="">
          <?php if( !empty($isSiteadvsearchEnable) ) : ?>
          <?php echo $this->content()->renderWidget("siteadvsearch.search-box", array("widgetName" => "advmenu_mini_menu",)); ?>
          <?php else: ?>
          <form id="global_search_form"  action="<?php echo $this->url(array('controller' => 'search'), 'default', true) ?>" method="post" class="search_form" target="_blank">
            <input type="hidden" id="page_no" name="page_no" value="1"/>
            <input type="hidden" id="tab_link" name="tab_link" value="all_tab"/>
            <input type="hidden" id="searched_from_page" name="searched_from_page" value=null />
            <input type="hidden" id="searched_from_page_id" name="searched_from_page_id" value=null />
            <input type="hidden" id="searched_from_initiative_id" name="searched_from_initiative_id" value=null />
            <input type="hidden" id="searched_from_project_id" name="searched_from_project_id" value=null />
            <input type="hidden" id="category_id" name="category_id" value=null />
            <input type="hidden" id="type" name="type" value=null />
            <input type="hidden" id="sdg_goal_id" name="sdg_goal_id" value=null />
            <input type="hidden" id="sdg_target_id" name="sdg_target_id" value=null />
            <input type="hidden" id="search_only_in_project" name="search_only_in_project" value=true />
            <input name='query' id='global_search_field' type="text" placeholder="<?php echo $this->translate("Search here...") ?>"/>
            <button id="responsive_search_toggle_search" class="responsive_search_toggle_search" ><i class="fa fa-search"></i></button>
            <button id="responsive_search_toggle_remove" class="responsive_search_toggle_remove" type="button"><i class="fa fa-remove"></i></button>
          </form>
          <?php endif; ?>
        </div>

      </div>
      <?php endif; ?>

      <?php if( in_array('mini_menu', $displayWidgets) ) : ?>
      <div class="sitecoretheme_minimenu">
        <?php echo $this->content()->renderWidget($this->isSitemenuEnable && $this->settings('sitecoretheme.header.siteminimenu.enable', 1) ? "sitemenu.menu-mini" : "seaocore.menu-mini", $this->miniMenuParams); ?>
      </div>
      <?php endif; ?>

    </div>


    <?php if( $this->menuPosition == 2 && $this->showMenu && in_array('main_menu', $displayWidgets) && empty($this->menuParams) ): ?>
    <div class="sitecoretheme_main_menu_pannel">
      <?php
        echo $this->content()->renderWidget("sitecoretheme.menu-main", array(
      'menuType' => $this->settings('sitecoretheme.header.menu.style', 'slide'),
      'alwaysOpen' => $this->settings('sitecoretheme.header.menu.alwaysOpen', '0'),
      'mobuleNavigations' => $this->settings('sitecoretheme.header.menu.submenu', 1),
      'menuIcons' => $this->settings('sitecoretheme.header.menu.icon', 1),
      'settingNavigations' => $this->settings('sitecoretheme.header.leftmenu.settingNavigations', 1),
      'footerSection' => $this->settings('sitecoretheme.header.leftmenu.footerSection', 1),
      ));
      ?>
    </div>
    <?php endif; ?>
  </div>
</div>


<?php if (in_array('main_menu', $displayWidgets) && !empty($this->menuParams) && (($this->headerStyle == 3 && $this->menuPosition == 1) || $this->menuPosition == 2) && $this->showMenu) : ?>
<div class="sitecoretheme_mainmenu">
  <?php if( $this->menuPosition == 2 ) : ?>
  <?php echo $this->content()->renderWidget("sitemenu.vertical-menu-main", $this->menuParams); ?>
  <?php else: ?>
  <?php echo $this->content()->renderWidget("sitemenu.menu-main", $this->menuParams); ?>
  <?php endif; ?>
</div>
<?php endif; ?>
<?php elseif( $this->headerStyle == 2): ?>
<div class="sitecoretheme_top_header_two" id="sitecoretheme_top_header_wrapper">
  <div class="_static_top_header sitecoretheme_top_header_container">
    <div class="_top">
      <?php if(in_array('sociallink', $displayWidgets) ) : ?>
      <div class="sitecoretheme_social-sites">
        <?php echo $this->content()->renderWidget("sitecoretheme.menu-social-sites", array()); ?>
      </div>
      <?php endif; ?>
      <div class="search_menu">
        <?php if( in_array('search_box', $displayWidgets) ) : ?>
        <div class="sitecoretheme_search">
          <div id="sitecoretheme_fullsite_search">
            <?php if( !empty($isSiteadvsearchEnable) ) : ?>
            <?php echo $this->content()->renderWidget("siteadvsearch.search-box", array("widgetName" => "advmenu_mini_menu",)); ?>
            <?php else: ?>

            <form id="global_search_form" action="<?php echo $this->url(array('controller' => 'search'), 'default', true) ?>" method="post" class="search_form" target="_blank">
              <input type="hidden" name="page_no" value="1" />
              <input type="hidden" name="tab_link" value="all_tab" />
              <input type="hidden" name="searched_from_page" value=null />
              <input type="hidden" name="searched_from_page_id" value=null />
              <input type="hidden" name="searched_from_initiative_id" value=null />
              <input type="hidden" name="searched_from_project_id" value=null />
              <input type="hidden" id="category_id" name="category_id" value=null />
              <input type="hidden" name="type" value=null />
              <input name='query' id='global_search_field' type="text" placeholder="<?php echo $this->translate("Search here...") ?>"/>
              <button id="responsive_search_toggle_search" class="responsive_search_toggle_search" ><i class="fa fa-search"></i></button>
              <button id="responsive_search_toggle_remove" class="responsive_search_toggle_remove" type="button"><i class="fa fa-remove"></i></button>
            </form>
            <?php endif; ?>
          </div>

        </div>
        <?php endif; ?>
        <?php if( in_array('mini_menu', $displayWidgets) ) : ?>
        <div class="sitecoretheme_minimenu">
          <?php echo $this->content()->renderWidget($this->isSitemenuEnable && $this->settings('sitecoretheme.header.siteminimenu.enable', 1) ? "sitemenu.menu-mini" : "seaocore.menu-mini", $this->miniMenuParams); ?>
        </div>
        <?php endif; ?>

      </div>


    </div>
    <?php
        $menusContent = in_array('main_menu', $displayWidgets) && $this->showMenu ? $this->content()->renderWidget("sitecoretheme.browse-menu-main", array(
    'mobuleNavigations' => $this->settings('sitecoretheme.header.menu.submenu', 1),
    'menuIcons' => $this->settings('sitecoretheme.header.menu.icon', 1)
    )) : '';
    ?>
    <div class="_bottom">
      <div class="_bottom_content">
        <?php if( in_array('logo', $displayWidgets) ) : ?>
        <div class="sitecoretheme_logo">
          <?php echo $this->content()->renderWidget("core.menu-logo", $this->logoParams); ?>
          <?php echo $this->content()->renderWidget("core.menu-logo", $this->alternateLogoParams); ?>
        </div>
        <?php endif; ?>
        <div class="_mobile_menu_options">

          <?php if( in_array('search_box', $displayWidgets) ) : ?>
          <?php $route_name= Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName(); ?>
          <?php if( 'sitepage_entry_view' != $route_name && $route_name !='sitepage_initiatives' ) : ?>
          <i id="responsive_search_toggle" class="responsive_search_toggle fa fa-search"></i>
          <?php endif; ?>
          <?php endif; ?>
          <?php if( in_array('main_menu', $displayWidgets) && $this->showMenu ) : ?>
          <a class="_mobile_toggle_icon" href="javascript:void(0);" ><i class="fa fa-bars"></i></a>
          <?php endif; ?>
        </div>
        <?php if( $menusContent ) : ?>
        <div class="sitecoretheme_mainmenu">
          <?php echo $menusContent; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="_fix_top_header">
    <div class="_fix_top_content">
      <?php if( in_array('logo', $displayWidgets) ) : ?>
      <div class="sitecoretheme_logo">
        <?php echo $this->content()->renderWidget("core.menu-logo", $this->logoParams); ?>
        <?php echo $this->content()->renderWidget("core.menu-logo", $this->alternateLogoParams); ?>
      </div>
      <?php endif; ?>
      <div class="_mobile_menu_options">
        <?php if( in_array('main_menu', $displayWidgets) &&  $this->showMenu ) : ?>
        <a class="_mobile_toggle_icon" href="javascript:void(0);" ><i class="fa fa-bars"></i></a>
        <?php endif; ?>
      </div>
      <?php if( in_array('main_menu', $displayWidgets) &&  $this->showMenu ) : ?>
      <div class="sitecoretheme_mainmenu">
        <?php echo $menusContent; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
  <div class="_background_overlay"></div>
  <?php if( in_array('main_menu', $displayWidgets) && $this->showMenu ) : ?>
  <div class="_mobile_main_menu_content">
    <?php echo $menusContent; ?>
  </div>
</div>
<script>
  en4.core.runonce.add(function() {
    $$('.sitecoretheme_top_header_two ._mobile_main_menu_content .layout_sitecoretheme_browse_menu_main .sitecoretheme_main_menu .navigation > li.more_link').each(function(el) {
      el.getElements('.sitecoretheme_submenu > li').inject(el.getParent('ul'));
      el.destroy();
    });
    /* $$('.sitecoretheme_top_header_two .layout_sitecoretheme_browse_menu_main .sitecoretheme_main_menu .navigation > li.more_link .sitecoretheme_submenu') */
    $$('.sitecoretheme_top_header_two ._mobile_toggle_icon').addEvent('click', function() {
      $$('.sitecoretheme_top_header_two').toggleClass('_mobile_active');
    });
  });
</script>
<?php endif; ?>
</div>
<style type="text/css">
  .layout_page_header.<?php echo $this->headerClass ?> {
                          background-color: transparent;
                        }
</style>
<?php endif; ?>
<?php if( !empty($this->signupLoginPopup) ): ?>
<?php echo $this->content()->renderWidget("seaocore.login-or-signup-popup", array(
'popupVisibilty' => $this->popupVisibilty,
'allowClose' => $this->popupClosable,
'autoOpenLogin' => $this->autoShowPopup == 1,
'autoOpenSignup' => $this->autoShowPopup == 2
)); ?>
<?php endif; ?>
<script type="text/javascript">

  var routeName = "<?php echo Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName(); ?>";
  var actionName = "<?php echo Zend_Controller_Front::getInstance()->getRequest()->getActionName();?>";
  var controllerName = "<?php echo Zend_Controller_Front::getInstance()->getRequest()->getControllerName();?>";

  var headerPageId = null;
  var pageName =  "<?php echo $_POST['searched_from_page'];?>";
  if( pageName == 'organisation' || pageName == 'initiative' || pageName == 'project' ){
    headerPageId = "<?php echo $_POST['searched_from_page_id'];?>";
  }

  // todo: 5.2.1 Upgrade => Uncaught TypeError: $(...).getSize() is not a function
  var headerHeight = $$('sitecoretheme_top_header_wrapper').getSize().y;

  // organisation edit - menu action names
  var sitePageDashboardActionNames = ["profile-picture","overview","edit-location","profile-type","manage-member-category","contact","notification-settings"];
  var sitepageInitiativeActionNames = ["create", "edit", "list"];
  var sitepageExtendedActionNames = ["manage-projects","privacy","settings","manage-partner","manage-members","index","get-transactions"];
  var sitepageEditActionNames = ["edit"];
  var sitepageTransactionActionNames = ["get-transactions","project-transactions-details"];
  var sitepageWebReportActionNames = ["index"];
  var sitepagePaymentActionNames = ["set-payment"];
  var sitepageFormControllerNames = ["manageforms"];
  var sitepageMetricsActionNames = ["metrics"];

  // project edit - menu action names
  var projectDashboardActionNames = ["overview","project-settings","meta-detail"];
  var projectExtendedActionNames = ["settings","manage-goals","details","contact-info","manage-milestone","edit-privacy","add-goal","edit-goal","project-transactions","add-milestone"];
  var projectSpecificActionNames = ["edit","editlocation","payment-info"];
  var projectOrgSpecificActionNames = ["editorganizations","create"];
  var projectMileEditStoneActionNames = ["edit-milestone"];
  var projectInitiativeActionNames = ["edit-initiative-answers"];
  var projectFormControllerNames = ["form"];

  // if(routeName == 'sitepage_entry_view') {
  //   $$('.responsive_search_toggle').hide();
  //   $$('.search_title').hide();
  // }

  <?php if( $this->fixedMenu && !(($this->menuPosition == 2 && $this->isSitemenuEnable))): ?>
  en4.core.runonce.add(function () {
    var headerElement = $$('.layout_page_header');
    if (headerElement.length === 0) {
      return;
    }
    headerElement = headerElement[0];

    // Before Scroll occurs
    console.log('before scroll occurs');


    // Organisation edit Page
    if(
            (routeName === 'sitepage_dashboard' && (sitePageDashboardActionNames.indexOf(actionName) !== -1) )  ||
            (routeName === 'sitepage_initiatives' && (sitepageInitiativeActionNames.indexOf(actionName) !== -1) ) ||
            (routeName === "sitepage_extended" && (sitepageExtendedActionNames.indexOf(actionName) !== -1) )||
            (routeName === "sitepage_transaction" && (sitepageTransactionActionNames.indexOf(actionName) !== -1) )||
            (routeName === 'sitepage_edit' && (sitepageEditActionNames.indexOf(actionName) !== -1) ) ||
            (routeName === 'sitepage_webpagereport' && (sitepageWebReportActionNames.indexOf(actionName) !== -1)  ) ||
            (routeName === 'sitepage_projectpayment' && (sitepagePaymentActionNames.indexOf(actionName) !== -1)  ) ||
            (routeName === 'sitepage_extended' && (sitepageFormControllerNames.indexOf(controllerName) !== -1)  ) ||
            (routeName === 'sitepage_extended' && (sitepageMetricsActionNames.indexOf(actionName) !== -1)  )
    ){
      $$('.default_menu_items').hide();
      var allCustomMenusItems = document.getElementsByClassName('custom_menu_items');
      for (var i = 0; i < allCustomMenusItems.length; i++) {
        allCustomMenusItems[i].style.display = 'inline-block';
      }
    }

    // project edit page
    if(
            (routeName === 'sitecrowdfunding_extended' && (projectExtendedActionNames.indexOf(actionName) !== -1) ) ||
            (routeName === 'sitecrowdfunding_initiative' && (projectInitiativeActionNames.indexOf(actionName) !== -1) ) ||
            (routeName === 'sitecrowdfunding_dashboard' && (projectDashboardActionNames.indexOf(actionName) !== -1) ) ||
            (routeName === 'sitecrowdfunding_specific' && (projectSpecificActionNames.indexOf(actionName) !== -1) ) ||
            (routeName === 'sitecrowdfunding_organizationspecific' && (projectOrgSpecificActionNames.indexOf(actionName) !== -1) ) ||
            (routeName === 'sitecrowdfunding_milestoneedit' && (projectMileEditStoneActionNames.indexOf(actionName) !== -1) ) ||
            (routeName === 'sitecrowdfunding_extended' && (projectFormControllerNames.indexOf(controllerName) !== -1) )
    ){
      $$('.default_menu_items').hide();
      var allCustomMenusItems = document.getElementsByClassName('custom_menu_items');
      for (var i = 0; i < allCustomMenusItems.length; i++) {
        allCustomMenusItems[i].style.display = 'inline-block';
      }
    }

    //  Initiatives landing page
    if((routeName =='sitepage_initiatives' && actionName == 'landing-page' )){
      $$('.default_menu_items').hide();
      $$('.custom_menu_items').hide();
      var allCustomDefaultMenusItems = document.getElementsByClassName('custom_default_menu_items');
      for (var i = 0; i < allCustomDefaultMenusItems.length; i++) {
        allCustomDefaultMenusItems[i].style.display = 'inline-block';
      }
      document.getElementById('menu_navigation').classList.add("custom_default_menu_items_header");
    }

    //  Search Page (When Searched from organisation and initiative)
    if((routeName =='default' && actionName == 'index' && controllerName == 'search' )){
      if(headerPageId){
        $$('.default_menu_items').hide();
        $$('.custom_menu_items').hide();
        var allCustomDefaultMenusItems = document.getElementsByClassName('custom_default_menu_items');
        for (var i = 0; i < allCustomDefaultMenusItems.length; i++) {
          allCustomDefaultMenusItems[i].style.display = 'inline-block';
        }
        document.getElementById('menu_navigation').classList.add("custom_default_menu_items_header");
      }
    }

    // Project details page
    if( (routeName == 'sitecrowdfunding_entry_view' && actionName == 'view')){
      $$('.default_menu_items').hide();
      var allCustomDefaultMenusItems = document.getElementsByClassName('custom_default_menu_items');
      for (var i = 0; i < allCustomDefaultMenusItems.length; i++) {
        allCustomDefaultMenusItems[i].style.display = 'inline-block';
      }
      document.getElementById('menu_navigation').classList.add("custom_default_menu_items_header");
    }

    // Organisation details page
    if( (routeName == 'sitepage_entry_view' && actionName == 'view')){
      $$('.default_menu_items').hide();
      var allCustomDefaultMenusItems = document.getElementsByClassName('custom_default_menu_items');
      for (var i = 0; i < allCustomDefaultMenusItems.length; i++) {
        allCustomDefaultMenusItems[i].style.display = 'inline-block';
      }
      document.getElementById('menu_navigation').classList.add("custom_default_menu_items_header");
    }

    function setStickyContainers(){

      var scrollTop = window.getScrollTop();

      // show the items as sticky in project details
      if( (routeName == 'sitecrowdfunding_entry_view' && actionName == 'view')){

        var bodyHeight = document.getElementsByTagName('body')[0].clientHeight;

        // main container height
        var mainContainerElems = document.getElementsByClassName("layout_sitecrowdfunding_main_project_information");
        var mainContainerElemHeight = 0;
        for (var i = 0; i < mainContainerElems.length; i++) {
          mainContainerElemHeight = mainContainerElems[i].offsetHeight;
        }

        // middle container height
        var middleContainerElemHeight = $$(".layout_main > .layout_middle").getSize()[0].x;

        // main footer container height
        var mainFooterContainerElems = document.getElementsByClassName("layout_page_footer");
        var mainFooterContainerElemHeight = 0;
        for (var i = 0; i < mainFooterContainerElems.length; i++) {
          mainFooterContainerElemHeight = mainFooterContainerElems[i].offsetHeight;
        }

        // if scroll goes beyond the main content, then show the sticky
        if(scrollTop > mainContainerElemHeight){

          // check if reached to bottom
          var windowHeight = $(window).getHeight();

          if( (scrollTop + windowHeight) > (bodyHeight - mainFooterContainerElemHeight) ){
            console.log('reached bottom');

            // Append footer css and remove the exist sticky css
            $$(".layout_main > .layout_left").addClass("stickyLeftContainerReachedFooter");
            $$(".layout_sitecrowdfunding_project_funding_chart").addClass("stickyRightContainerReachedFooter");

            $$(".layout_main > .layout_left").removeClass("stickyLeftContainer");
            $$(".layout_sitecrowdfunding_project_funding_chart").removeClass("stickyRightContainer");

            $$(".layout_main > .layout_middle").addClass("stickyContainerMiddle");
            $$(".layout_page_footer").addClass("stickyContainerFooter");
          }else{
            console.log('not reached bottom');

            // Remove footer css and append the exist sticky css
            $$(".layout_main > .layout_left").removeClass("stickyLeftContainerReachedFooter");
            $$(".layout_sitecrowdfunding_project_funding_chart").removeClass("stickyRightContainerReachedFooter");

            $$(".layout_main > .layout_left").addClass("stickyLeftContainer");
            $$(".layout_sitecrowdfunding_project_funding_chart").addClass("stickyRightContainer");

            $$(".layout_main > .layout_middle").addClass("stickyContainerMiddle");
            $$(".layout_page_footer").addClass("stickyContainerFooter");
          }
        }else{

          $$(".layout_main > .layout_left").removeClass("stickyLeftContainerReachedFooter");
          $$(".layout_sitecrowdfunding_project_funding_chart").removeClass("stickyRightContainerReachedFooter");

          $$(".layout_main > .layout_left").removeClass("stickyLeftContainer");
          $$(".layout_sitecrowdfunding_project_funding_chart").removeClass("stickyRightContainer");
          $$(".layout_main > .layout_middle").removeClass("stickyContainerMiddle");
          $$(".layout_page_footer").removeClass("stickyContainerFooter");
        }

      }
    }

    function headerScrolling() {

      /*
      // Defining event listener function
      function displayWindowSize(){
        // Get width and height of the window excluding scrollbars
        var w = document.documentElement.clientWidth;
        var h = document.documentElement.clientHeight;

        if( w > 1200 ) {
          setStickyContainers();
        }else {

          $$(".layout_main > .layout_left").removeClass("stickyLeftContainerReachedFooter");
          $$(".layout_sitecrowdfunding_project_funding_chart").removeClass("stickyRightContainerReachedFooter");

          $$(".layout_main > .layout_left").removeClass("stickyLeftContainer");
          $$(".layout_sitecrowdfunding_project_funding_chart").removeClass("stickyRightContainer");
          $$(".layout_main > .layout_middle").removeClass("stickyContainerMiddle");
          $$(".layout_page_footer").removeClass("stickyContainerFooter");
        }

      }

      // Attaching the event listener function to window's resize event
      window.addEventListener("resize", displayWindowSize);

      // Calling the function for the first time
      displayWindowSize();
      */


      var height = headerElement.getCoordinates().height;
    <?php if($this->menuPosition == 2): ?>
      headerElement.getParent().setStyle('minHeight', height+'px');
      if ( !headerElement.hasClass('<?php echo $this->headerClass ?>')) {
        headerElement.addClass('<?php echo $this->headerClass ?>');
      }
    <?php else: ?>
      var scrollTop = window.getScrollTop();
      if (scrollTop > height*1.2 && !headerElement.hasClass('<?php echo $this->headerClass ?>')) {
        headerElement.getParent().setStyle('minHeight', height+'px');
        headerElement.addClass('<?php echo $this->headerClass ?>');

        console.log('scrolling to bottom');

        // HIDE/SHOW CUSTOM MENU ITEMS - AFTER SCROLL

        // organisation edit
        if(
                (routeName === 'sitepage_dashboard' && (sitePageDashboardActionNames.indexOf(actionName) !== -1) )  ||
                (routeName === 'sitepage_initiatives' && (sitepageInitiativeActionNames.indexOf(actionName) !== -1) ) ||
                (routeName === "sitepage_extended" && (sitepageExtendedActionNames.indexOf(actionName) !== -1) )||
                (routeName === "sitepage_transaction" && (sitepageTransactionActionNames.indexOf(actionName) !== -1) )||
                (routeName === 'sitepage_edit' && (sitepageEditActionNames.indexOf(actionName) !== -1) ) ||
                (routeName === 'sitepage_webpagereport' && (sitepageWebReportActionNames.indexOf(actionName) !== -1)  ) ||
                (routeName === 'sitepage_projectpayment' && (sitepagePaymentActionNames.indexOf(actionName) !== -1)  )
        ){
          $$('.default_menu_items').hide();
          document.getElementsByClassName('_main_menu_parent')[0].classList.remove('_show_sub_nav');
          var allCustomMenusItems = document.getElementsByClassName('custom_menu_items');
          for (var i = 0; i < allCustomMenusItems.length; i++) {
            allCustomMenusItems[i].style.display = 'inline-block';
          }
        }

        // project edit page
        if(
                (routeName === 'sitecrowdfunding_extended' && (projectExtendedActionNames.indexOf(actionName) !== -1) ) ||
                (routeName === 'sitecrowdfunding_dashboard' && (projectDashboardActionNames.indexOf(actionName) !== -1) ) ||
                (routeName === 'sitecrowdfunding_specific' && (projectSpecificActionNames.indexOf(actionName) !== -1) ) ||
                (routeName === 'sitecrowdfunding_organizationspecific' && (projectOrgSpecificActionNames.indexOf(actionName) !== -1) ) ||
                (routeName === 'sitecrowdfunding_milestoneedit' && (projectMileEditStoneActionNames.indexOf(actionName) !== -1) )
        ){
          $$('.default_menu_items').hide();
          var allCustomMenusItems = document.getElementsByClassName('custom_menu_items');
          for (var i = 0; i < allCustomMenusItems.length; i++) {
            allCustomMenusItems[i].style.display = 'inline-block';
          }
        }

        //  Initiatives landing page
        if((routeName =='sitepage_initiatives' && actionName == 'landing-page' )){
          $$('.default_menu_items').hide();
          $$('.custom_default_menu_items').hide();
          var allCustomMenusItems = document.getElementsByClassName('custom_menu_items');
          for (var i = 0; i < allCustomMenusItems.length; i++) {
            allCustomMenusItems[i].style.display = 'inline-block';
          }
          document.getElementById('menu_navigation').classList.remove("custom_default_menu_items_header");
        }

        //  Search Page (When Searched from organisation and initiative)
        if((routeName =='default' && actionName == 'index' && controllerName == 'search' )){
          if(headerPageId){
            $$('.default_menu_items').hide();
            $$('.custom_menu_items').hide();
            var allCustomDefaultMenusItems = document.getElementsByClassName('custom_default_menu_items');
            for (var i = 0; i < allCustomDefaultMenusItems.length; i++) {
              allCustomDefaultMenusItems[i].style.display = 'inline-block';
            }
            document.getElementById('menu_navigation').classList.add("custom_default_menu_items_header");
          }
        }

        // Project details page
        if( (routeName == 'sitecrowdfunding_entry_view' && actionName == 'view')){
          $$('.default_menu_items').hide();
          var allCustomDefaultMenusItems = document.getElementsByClassName('custom_default_menu_items');
          for (var i = 0; i < allCustomDefaultMenusItems.length; i++) {
            allCustomDefaultMenusItems[i].style.display = 'inline-block';
          }
          document.getElementById('menu_navigation').classList.add("custom_default_menu_items_header");
        }

        // Organisation details page
        if( (routeName == 'sitepage_entry_view' && actionName == 'view')){
          $$('.default_menu_items').hide();
          var allCustomDefaultMenusItems = document.getElementsByClassName('custom_default_menu_items');
          for (var i = 0; i < allCustomDefaultMenusItems.length; i++) {
            allCustomDefaultMenusItems[i].style.display = 'inline-block';
          }
          document.getElementById('menu_navigation').classList.add("custom_default_menu_items_header");
        }

      } else if (scrollTop < height && headerElement.hasClass('<?php echo $this->headerClass ?>')) {
        headerElement.removeClass('<?php echo $this->headerClass ?>');
        headerElement.getParent().setStyle('minHeight');

        console.log('scroll to top fixed');

        // HIDE/SHOW CUSTOM MENU ITEMS - BEFORE SCROLL

        // organisation details
        if(
                (routeName === 'sitepage_dashboard' && (sitePageDashboardActionNames.indexOf(actionName) !== -1) )  ||
                (routeName === 'sitepage_initiatives' && (sitepageInitiativeActionNames.indexOf(actionName) !== -1) ) ||
                (routeName === "sitepage_extended" && (sitepageExtendedActionNames.indexOf(actionName) !== -1) )||
                (routeName === "sitepage_transaction" && (sitepageTransactionActionNames.indexOf(actionName) !== -1) )||
                (routeName === 'sitepage_edit' && (sitepageEditActionNames.indexOf(actionName) !== -1) ) ||
                (routeName === 'sitepage_webpagereport' && (sitepageWebReportActionNames.indexOf(actionName) !== -1)  ) ||
                (routeName === 'sitepage_projectpayment' && (sitepagePaymentActionNames.indexOf(actionName) !== -1)  )
        ){
          document.getElementsByClassName('_main_menu_parent')[0].classList.remove('_show_sub_nav');
          $$('.default_menu_items').hide();
          var allCustomMenusItems = document.getElementsByClassName('custom_menu_items');
          for (var i = 0; i < allCustomMenusItems.length; i++) {
            allCustomMenusItems[i].style.display = 'inline-block';
          }
        }

        // project edit page
        if(
                (routeName === 'sitecrowdfunding_extended' && (projectExtendedActionNames.indexOf(actionName) !== -1) ) ||
                (routeName === 'sitecrowdfunding_dashboard' && (projectDashboardActionNames.indexOf(actionName) !== -1) ) ||
                (routeName === 'sitecrowdfunding_specific' && (projectSpecificActionNames.indexOf(actionName) !== -1) ) ||
                (routeName === 'sitecrowdfunding_organizationspecific' && (projectOrgSpecificActionNames.indexOf(actionName) !== -1) ) ||
                (routeName === 'sitecrowdfunding_milestoneedit' && (projectMileEditStoneActionNames.indexOf(actionName) !== -1) )
        ){
          $$('.default_menu_items').hide();
          var allCustomMenusItems = document.getElementsByClassName('custom_menu_items');
          for (var i = 0; i < allCustomMenusItems.length; i++) {
            allCustomMenusItems[i].style.display = 'inline-block';
          }
        }

        //  Initiatives landing page
        if((routeName =='sitepage_initiatives' && actionName == 'landing-page' )){
          $$('.default_menu_items').hide();
          $$('.custom_menu_items').hide();
          var allCustomDefaultMenusItems = document.getElementsByClassName('custom_default_menu_items');
          for (var i = 0; i < allCustomDefaultMenusItems.length; i++) {
            allCustomDefaultMenusItems[i].style.display = 'inline-block';
          }
          document.getElementById('menu_navigation').classList.add("custom_default_menu_items_header");
        }

        //  Search Page (When Searched from organisation and initiative)
        if((routeName =='default' && actionName == 'index' && controllerName == 'search' )){
          if(headerPageId){
            $$('.default_menu_items').hide();
            $$('.custom_menu_items').hide();
            var allCustomDefaultMenusItems = document.getElementsByClassName('custom_default_menu_items');
            for (var i = 0; i < allCustomDefaultMenusItems.length; i++) {
              allCustomDefaultMenusItems[i].style.display = 'inline-block';
            }
            document.getElementById('menu_navigation').classList.add("custom_default_menu_items_header");
          }
        }

        // Project details page
        if( (routeName == 'sitecrowdfunding_entry_view' && actionName == 'view')){
          $$('.default_menu_items').hide();
          var allCustomDefaultMenusItems = document.getElementsByClassName('custom_default_menu_items');
          for (var i = 0; i < allCustomDefaultMenusItems.length; i++) {
            allCustomDefaultMenusItems[i].style.display = 'inline-block';
          }
          document.getElementById('menu_navigation').classList.add("custom_default_menu_items_header");
        }

        // Organisation details page
        if( (routeName == 'sitepage_entry_view' && actionName == 'view')){
          $$('.default_menu_items').hide();
          var allCustomDefaultMenusItems = document.getElementsByClassName('custom_default_menu_items');
          for (var i = 0; i < allCustomDefaultMenusItems.length; i++) {
            allCustomDefaultMenusItems[i].style.display = 'inline-block';
          }
          document.getElementById('menu_navigation').classList.add("custom_default_menu_items_header");
        }

      }
      if (!headerElement.hasClass('<?php echo $this->headerClass ?>')) {
        headerElement.setStyle('top', '-'+scrollTop+'px');
      }else {
        headerElement.setStyle('top');
      }
    <?php endif; ?>
    }
    window.addEvent('scroll', headerScrolling);
  });
  <?php endif; ?>
  <?php if( in_array('search_box', $displayWidgets) ) : ?>

  // show the search btn by default
  $$('.responsive_search_toggle').show();
  $$('.search_title').show();
  // if search is clicked
  $$('.responsive_search_toggle').addEvent('click', function () {

    console.log('testtt click');
    // show search i/p
    $('sitecoretheme_fullsite_search').show();

    // hide the search btn
    $$('.responsive_search_toggle').hide();
    $$('.search_title').hide();
    // hide the minimenu (for web) or logo (for mobile)
    $$('.sitecoretheme_minimenu').hide();

    // set values in search i/p
    // organisation edit
    if(
            (routeName === 'sitepage_dashboard' && (sitePageDashboardActionNames.indexOf(actionName) !== -1) )  ||
            (routeName === 'sitepage_initiatives' && (sitepageInitiativeActionNames.indexOf(actionName) !== -1) ) ||
            (routeName === "sitepage_extended" && (sitepageExtendedActionNames.indexOf(actionName) !== -1) )||
            (routeName === "sitepage_transaction" && (sitepageTransactionActionNames.indexOf(actionName) !== -1) )||
            (routeName === 'sitepage_edit' && (sitepageEditActionNames.indexOf(actionName) !== -1) ) ||
            (routeName === 'sitepage_webpagereport' && (sitepageWebReportActionNames.indexOf(actionName) !== -1)  ) ||
            (routeName === 'sitepage_projectpayment' && (sitepagePaymentActionNames.indexOf(actionName) !== -1)  )
    ){
      if($('header_page_id')){
        var page_id = $('header_page_id').value;
        if(page_id){
          var els=document.getElementsByName("searched_from_page");
          for (var i=0;i<els.length;i++) {
            els[i].value = "organisation";
          }
          var els=document.getElementsByName("type");
          for (var i=0;i<els.length;i++) {
            els[i].value = "everything_in_organization";
          }
          var els=document.getElementsByName("searched_from_page_id");
          for (var i=0;i<els.length;i++) {
            els[i].value = page_id;
          }
          var els=document.getElementsByName("searched_from_initiative_id");
          for (var i=0;i<els.length;i++) {
            els[i].value = null;
          }
          var els=document.getElementsByName("searched_from_project_id");
          for (var i=0;i<els.length;i++) {
            els[i].value = null;
          }
        }
      }
    }

    // project edit page
    if(
            (routeName === 'sitecrowdfunding_extended' && (projectExtendedActionNames.indexOf(actionName) !== -1) ) ||
            (routeName === 'sitecrowdfunding_dashboard' && (projectDashboardActionNames.indexOf(actionName) !== -1) ) ||
            (routeName === 'sitecrowdfunding_specific' && (projectSpecificActionNames.indexOf(actionName) !== -1) ) ||
            (routeName === 'sitecrowdfunding_organizationspecific' && (projectOrgSpecificActionNames.indexOf(actionName) !== -1) ) ||
            (routeName === 'sitecrowdfunding_milestoneedit' && (projectMileEditStoneActionNames.indexOf(actionName) !== -1) )
    ){
      if($('header_page_id')){
        var page_id = $('header_page_id').value;
        if(page_id){
          var els=document.getElementsByName("searched_from_page");
          for (var i=0;i<els.length;i++) {
            els[i].value = "project";
          }
          var els=document.getElementsByName("type");
          for (var i=0;i<els.length;i++) {
            els[i].value = "everything_in_organization";
          }
          var els=document.getElementsByName("searched_from_page_id");
          for (var i=0;i<els.length;i++) {
            els[i].value = page_id;
          }
          var els=document.getElementsByName("searched_from_initiative_id");
          for (var i=0;i<els.length;i++) {
            els[i].value = null;
          }
          var els=document.getElementsByName("searched_from_project_id");
          for (var i=0;i<els.length;i++) {
            els[i].value = null;
          }
        }
      }
    }


  });

    // if search-cancel is clicked
    $$('.responsive_search_toggle_remove').addEvent('click', function () {

    // hide search i/p
    $('sitecoretheme_fullsite_search').hide();

    // show the search btn
    $$('.responsive_search_toggle').show();
    $$('.search_title').show();
    // show the minimenu
    $$('.sitecoretheme_minimenu').show();

  });

  <?php endif; ?>
  <?php if( in_array('search_box', $displayWidgets) ) : ?>
  var requestURL = '<?php echo $this->url(array('module' => 'sitecoretheme', 'controller' => 'general', 'action' => 'get-search-content'), "default", true) ?>';
  if($('global_search_field')) {
    contentAutocomplete = new Autocompleter.Request.JSON('global_search_field', requestURL, {
      'postVar': 'text',
      'cache': false,
      'minLength': 1,
      'selectFirst': false,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'tag-autosuggest adsearch-autosuggest adsearch-stoprequest',
      'maxChoices': 8,
      'indicatorClass': 'vertical-search-loading',
      'customChoices': true,
      'filterSubset': true,
      'multiple': false,
      'injectChoice': function (token) {
        if (typeof token.label != 'undefined') {
          var seeMoreText = '<?php echo $this->string()->escapeJavascript($this->translate('See more results for') . ' '); ?>';
          if (token.type == 'no_resuld_found') {
            var choice = new Element('li', {'class': 'autocompleter-choices', 'id': 'sitecoretheme_search_' + token.type});
            new Element('div', {'html': token.label, 'class': 'autocompleter-choicess'}).inject(choice);
            choice.inject(this.choices);
            choice.store('autocompleteChoice', token);
            return;
          }
          if (token.item_url != 'seeMoreLink') {
            var choice = new Element('li', {'class': 'autocompleter-choices', 'html': token.photo, 'item_url': token.item_url, onclick: 'javascript: showSearchResultPage("' + token.item_url + '")'});
            var divEl = new Element('div', {
              'html': token.type ? this.options.markQueryValueCustom.call(this, (token.label)) : token.label,
              'class': 'autocompleter-choice'
            });

            new Element('div', {
              'html': token.type, //this.markQueryValue(token.type)
              'class': 'seaocore_txt_light f_small'
            }).inject(divEl);

            divEl.inject(choice);
            new Element('input', {
              'type': 'hidden',
              'value': JSON.encode(token)
            }).inject(choice);
            this.addChoiceEvents(choice).inject(this.choices);
            choice.store('autocompleteChoice', token);
          }
          if (token.item_url == 'seeMoreLink') {
            var titleAjax1 = encodeURIComponent($('global_search_field').value);
            var choice = new Element('li', {'class': 'autocompleter-choices', 'html': '', 'id': 'stopevent', 'item_url': ''});
            new Element('div', {'html': seeMoreText + '"' + titleAjax1 + '"', 'class': 'autocompleter-choicess', onclick: 'javascript:seeMoreSearchResults()'}).inject(choice);
            this.addChoiceEvents(choice).inject(this.choices);
            choice.store('autocompleteChoice', token);
          }
        }
      },
      markQueryValueCustom: function (str) {
        return (!this.options.markQuery || !this.queryValue) ? str
                : str.replace(new RegExp('(' + ((this.options.filterSubset) ? '' : '^') + this.queryValue.escapeRegExp() + ')', (this.options.filterCase) ? '' : 'i'), '<b>$1</b>');
      },
    });
  }
  function showSearchResultPage(url) {
    window.location.href = url;
  }
  function seeMoreSearchResults() {

    $('stopevent').removeEvents('click');
    var url = '<?php echo $this->url(array('controller' => 'search'), 'default', true); ?>' + '?query=' + encodeURIComponent($('global_search_field').value) + '&type=' + 'all';
    console.log('url---',url);
    window.location.href = url;

  }
  $('global_search_field').addEvent('keydown', function (event) {
    if (event.key == 'enter') {
      $('sitecoretheme_fullsite_search').submit();
    }
  });
  <?php endif; ?>
</script>
<style>
  .generic_layout_container.layout_sesnewsletter_newsletter {
    display: none;
  }
  #global_page_core-error-requireuser .sitecoretheme_minimenu,
  #global_page_user-signup-index .sitecoretheme_minimenu,
  #global_page_user-auth-login .sitecoretheme_minimenu{
    display: none !important;
  }
  .stickyLeftContainer{
    position: fixed;
    top: 10%;
  }
  .search_menu{
    float: right;
    display: flex !important;
    align-items: center;
  }
  .stickyRightContainer{
    position: fixed;
    top: 10%;
    width: 240px;
  }
  .stickyLeftContainerReachedFooter{
    position: fixed;
    bottom: 40%;
  }
  .stickyRightContainerReachedFooter{
    position: fixed;
    bottom: 40%;
    width: 240px;
  }
  .stickyContainerMiddle{
    left: 19%;
    position: relative;
    width: 62%;
  }
  .stickyContainerFooter{
    z-index: -99999;
  }
  .sitecoretheme_top_header_container .sitecoretheme_search{
    margin-right:10px;
  }
  .search_title{
    font-size: 13px;
    margin-left: 3px;
    color: #21a8c1;
  }
  /** Hide the sticky in mobile view **/
  @media (max-width: 767px){
    .stickyLeftContainer{
      position: unset !important;
      top: unset !important;
    }
    .stickyRightContainer{
      position: unset !important;
      top: unset !important;
      width: unset !important;
    }
    .stickyLeftContainerReachedFooter{
      position: unset !important;
      bottom: unset !important;
    }
    .stickyRightContainerReachedFooter{
      position: unset !important;
      bottom: unset !important;
      width: unset !important;
    }
    .stickyContainerMiddle{
      left: unset !important;
      position: unset !important;
      width: unset !important;
    }
    .stickyContainerFooter{
      z-index: unset !important;
    }
  }

  #sitecoretheme_fullsite_search{
    display: none;
  }
  ul.tag-autosuggest {
    margin-top: 22px;
    max-height: 200px;
    overflow-y: auto !important;
  }
  #responsive_search_toggle{
    font-size: 16px;
    color: #44AEC1;
  }
  ul#im_container {
     
  }

  /* Fix search css in mobile view */
  @media (max-width: 767px){
    .search_form {
      margin: 10px 0px;
      display: flex;
    }
    #responsive_search_toggle_search{
      margin: 0 5px 0 5px;
    }
    i#responsive_search_toggle {
      margin-right: 28px !important;
      display: flex;
      align-items: center;
    }

  }
</style>