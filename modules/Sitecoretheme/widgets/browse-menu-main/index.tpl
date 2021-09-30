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
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js');?>
<script type="text/javascript">
    function toggleNavigation(obj) {

        navigation = obj.getNext('ul');
        if (navigation.style.display == 'block') {
            navigation.style.display = 'none';
            obj.removeClass('menu_icon_active');
        } else {
            navigation.style.display = 'block';
            obj.addClass('menu_icon_active');
        }
        return false;
    }
</script>
<div class="sitecoretheme_main_menu">
    <?php $key = 0; ?>
    <a class="menu_icon" href="javascript:void(0);" onclick="return toggleNavigation(this)"><i class="fa fa-navicon"></i></a>
    <i class="fa fa-caret-up"></i>
    <ul class='navigation' id="menu_navigation">
        <?php foreach( $this->browsenavigation as $nav ): ?>
        <?php if( isset($nav->show_to_guest) && empty($nav->show_to_guest) && !$this->viewer()->getIdentity() ): ?>
        <?php continue; ?>
        <?php endif; ?>
        <?php if( $key >= $this->max ): ?>
        <?php break; ?>
        <?php endif; ?>
        <?php $key++ ?>
        <?php $class = array(); ?>
        <?php if( $nav->active || ($nav->module === 'sitecrowdfunding' && $nav->module == $this->moduleName) ): $class[] ='active'; endif;?>
        <?php if( $nav->hasChildren() ): $class[] ='_main_menu_parent'; endif;?>
        <li class="default_menu_items <?php echo join(' ', $class)?>">
            <?php if( $nav->action ): ?>
            <a class= "<?php echo $nav->class ?>" href='<?php echo empty($nav->uri) ? $this->url(array('action' => $nav->action), $nav->route, true) : $nav->uri ?>' <?php if(!empty($nav->target)):?>target="_blank" <?php endif; ?>><span>
            <?php if($this->menuIcons && $nav->icon): ?>
             <i <?php echo (Zend_Uri::check($nav->icon)) ? 'style="background-image:url(' . $nav->icon . ')"' : 'class="fa ' . $nav->icon . '"' ?>></i>
                <?php endif; ?>
                <?php echo $this->translate($nav->label); ?></span></a>
            <?php else : ?>
            <a class= "<?php echo $nav->class ?>" href='<?php echo $nav->getHref() ?>' <?php if(!empty($nav->target)):?>target="_blank" <?php endif; ?>><span>
              <?php if($this->menuIcons && $nav->icon): ?>
               <i <?php echo (Zend_Uri::check($nav->icon)) ? 'style="background-image:url(' . $nav->icon . ')"' : 'class="fa ' . $nav->icon . '"' ?>></i>
                <?php endif; ?>
                <?php echo $this->translate($nav->label); ?></span></a>
            <?php endif; ?>
            <?php if( $nav->hasChildren() ): ?>
            <i class="fa fa-chevron-down sub_navigation_toggle" onclick="toogle_sub_main_menus(this)"></i>
            <?php
            echo $this->navigation()
            ->menu()
            ->renderMenu($nav, array('ulClass' => 'sub_navigation'));
            ?>
            <?php endif; ?>
        </li>
        <?php endforeach; ?>

        <!----------------- CUSTOM MENU ITEMS ---------------->
        <?php $routeName =  Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName(); ?>
        <?php $actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();?>
        <?php $request = Zend_Controller_Front::getInstance()->getRequest(); ?>
        <?php $controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName(); ?>

        <!-- project menu action names -->
        <?php $projectExtendedActionNames = array("settings","manage-goals","details","contact-info","manage-milestone","edit-privacy","add-goal","edit-goal","project-transactions","add-milestone");?>
        <?php $projectDashboardActionNames = array("overview","project-settings","meta-detail");?>
        <?php $projectSpecificActionNames = array("edit","editlocation","payment-info");?>
        <?php $projectOrgSpecificActionNames = array("editorganizations","create");?>
        <?php $projectMilestoneEditActionNames = array("edit-milestone"); ?>
        <?php $projectInitiativeActionNames = array("edit-initiative-answers"); ?>
        <?php $projectFormControllerNames = array("form"); ?>

        <!-- Organisation menu action names -->
        <?php $sitePageDashboardActionNames = array("profile-picture","overview","edit-location","profile-type","manage-member-category","contact","notification-settings");?>
        <?php $sitepageInitiativeActionNames = array("create", "edit", "list");?>
        <?php $sitepageExtendedActionNames = array("manage-projects","privacy","settings","manage-partner","manage-members","index","get-transactions");?>
        <?php $sitepageEditActionNames = array("edit");?>
        <?php $sitepageTransactionActionNames = array("get-transactions","project-transactions-details");?>
        <?php $sitepageWebReportActionNames = array("index");?>
        <?php $sitepagePaymentActionNames = array("set-payment");?>
        <?php $sitepageFormControllerNames = array("manageforms");?>
        <?php $sitepageMetricsActionNames = array("metrics");?>

        <!-- Organisation Edit Page-->
        <?php if(
            ($routeName == 'sitepage_dashboard' && in_array($actionName, $sitePageDashboardActionNames ) ) ||
            ($routeName == 'sitepage_initiatives' && in_array($actionName,$sitepageInitiativeActionNames ) ) ||
            ($routeName == 'sitepage_edit' && in_array($actionName, $sitepageEditActionNames ) ) ||
            ($routeName == 'sitepage_transaction' && in_array($actionName, $sitepageTransactionActionNames ) ) ||
            ($routeName == 'sitepage_extended' && in_array($actionName, $sitepageExtendedActionNames ) ) ||
            ($routeName == 'sitepage_webpagereport' && in_array($actionName, $sitepageWebReportActionNames) ) ||
            ($routeName == 'sitepage_projectpayment' && in_array($actionName, $sitepagePaymentActionNames ) ) ||
            ($routeName == 'sitepage_extended' && in_array($controllerName, $sitepageFormControllerNames ) ) ||
            ($routeName == 'sitepage_extended' && in_array($actionName, $sitepageMetricsActionNames ) )
            ) :?>

            <!-- get ids-->
            <?php $page_id = $request->getParam('page_id'); ?>
            <?php if(!empty($page_id)):?>

                <!-- fetched these value in search form,so it is made as hidden-->
                <input type="hidden" name="header_page_id" id="header_page_id" value="<?php echo $page_id;?>" />

                <?php $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id); ?>
            <?php endif; ?>

            <?php if(!empty($sitepage->page_id)):?>

                <!-- After/Before Scroll-->
                <li class="custom_menu_items">
                    <a class="menu_core_main" href="<?php echo $sitepage->getHref() ?>"><span><?php echo $sitepage->getTitle(); ?></span></a>
                </li>

                <?php $initiatives = Engine_Api::_()->getDbtable('initiatives', 'sitepage')->getAllInitiativesByPageId($sitepage->page_id); ?>

                <?php if(count($initiatives) != 0):?>
                    <li class="custom_menu_items custom_main_menu_parent _main_menu_parent intiative_menudropdown" style="display: none">
                        <a class="menu_core_main" href="javascript:void(0);"><span>Initiatives</span></a>
                        <i class="fa fa-chevron-down sub_navigation_toggle initiatives_drop_down_icon" onclick="toogle_sub_main_menus(this)"></i>
                        <ul class="sub_navigation custom_navigation _show_sub_nav">
                            <?php foreach($initiatives as $initiative): ?>
                                <?php $item = Engine_Api::_()->getItem('sitepage_initiative', $initiative['initiative_id']); ?>
                                <?php $initiativesURL = $this->url(array('action' => 'landing-page','page_id' => $sitepage->page_id, 'initiative_id' => $initiative['initiative_id']), "sitepage_initiatives");?>
                                <li class="custom_navigation_li_items sub_navigation">
                                    <a class="menu_core_main" href="<?php echo $initiativesURL;?>"><span><?php echo $initiative['title'] ?></span></a>
                                </li>
                            <?php endforeach;?>
                        </ul>
                    </li>
                <?php endif; ?>

            <?php endif; ?>
        <?php endif; ?>

        <!-- Project Edit Page-->
        <?php if(
            ($routeName == 'sitecrowdfunding_extended' && in_array($actionName, $projectExtendedActionNames ) ) ||
            ($routeName == 'sitecrowdfunding_initiative' && in_array($actionName, $projectInitiativeActionNames ) ) ||
            ($routeName == 'sitecrowdfunding_dashboard' && in_array($actionName,$projectDashboardActionNames ) ) ||
            ($routeName == 'sitecrowdfunding_specific' && in_array($actionName, $projectSpecificActionNames ) ) ||
            ($routeName == 'sitecrowdfunding_organizationspecific' && in_array($actionName, $projectOrgSpecificActionNames ) ) ||
            ($routeName == 'sitecrowdfunding_milestoneedit' && in_array($actionName, $projectMilestoneEditActionNames ) ) ||
            ($routeName == 'sitecrowdfunding_extended' && in_array($controllerName, $projectFormControllerNames ) )
            ) :?>
            <?php $project_id = $request->getParam('project_id'); ?>
            <?php $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id); ?>
            <?php $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id); ?>

            <!-- After/Before Scroll-->
            <?php if(!empty($parentOrganization['page_id']) ): ?>

                <!-- fetched these value in search form,so it is made as hidden-->
                <input type="hidden" name="header_page_id" id="header_page_id" value="<?php echo $parentOrganization['page_id'];?>" />
                <input type="hidden" name="header_project_id" id="header_project_id" value="<?php echo $project_id;?>" />

                <li class="custom_menu_items" style="display: none">
                    <a class="menu_core_main" href="<?php echo !empty($parentOrganization['link']) ? $parentOrganization['link'] : 'javascript:void(0);'  ?>"><span><?php echo $parentOrganization['title'] ?></span></a>
                </li>

                <?php
                    //prepare tags
                    $tagString =  array();
                    if(!empty($project->tags())){
                        $projectTags = $project->tags()->getTagMaps();
                        foreach ($projectTags as $tagmap) {
                            $tagString[]= $tagmap->getTag()->getTitle();
                        }
                    }
                ?>

                <?php if(!empty($project->initiative_id)):?>
                    <?php $initiative = Engine_Api::_()->getItem('sitepage_initiative', $project->initiative_id); ?>
                    <?php $initiativesURL = $this->url(array('action' => 'landing-page','page_id' => $parentOrganization['page_id'], 'initiative_id' => $project->initiative_id), "sitepage_initiatives");?>

                    <!-- fetched these value in search form,so it is made as hidden-->
                    <input type="hidden" name="header_initiative_id" id="header_initiative_id" value="<?php echo $project->initiative_id;?>" />

                    <?php if(!empty($initiativesURL) ): ?>
                        <li class="custom_menu_items">
                            <a class="menu_core_main" href="<?php echo $initiativesURL;?>"><span><?php echo $initiative['title'] ?></span></a>
                        </li>
                    <?php endif; ?>
                <?php else : ?>
                    <?php if(count($tagString) >0): ?>
                        <?php $initiatives = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectInitiatives($parentOrganization['page_id'],$tagString); ?>
                            <?php if(count($initiatives) != 0):?>
                                <?php $initiativesURL = $this->url(array('action' => 'landing-page','page_id' => $parentOrganization['page_id'], 'initiative_id' => $initiatives[0]['initiative_id']), "sitepage_initiatives");?>

                                <!-- fetched these value in search form,so it is made as hidden-->
                                <input type="hidden" name="header_initiative_id" id="header_initiative_id" value="<?php echo $initiatives[0]['initiative_id'];?>" />

                                <?php if(!empty($initiativesURL) ): ?>
                                    <li class="custom_menu_items">
                                        <a class="menu_core_main" href="<?php echo $initiativesURL;?>"><span><?php echo $initiatives[0]['title'] ?></span></a>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>

            <?php endif; ?>

        <?php endif; ?>

        <!-- Organisation Details -->
        <?php if($routeName == 'sitepage_entry_view' && $actionName =='view'):?>
            <?php $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page'); ?>
            <?php if(!empty($sitepage->page_id)):?>
                <!-- After/Before Scroll -->
                <li class="custom_default_menu_items" style="display: none">
                    <a class="menu_core_main" href="<?php echo $sitepage->getHref() ?>">
                        <span>
                            <?php if(!empty($sitepage->page_header_name)):?>
                                <?php echo $sitepage->page_header_name;?>
                            <?php else : ?>
                                <?php echo $sitepage->title;?>
                            <?php endif; ?>
                        </span>
                    </a>
                </li>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Search Page (When Searched from organisation and initiative)  -->
        <?php if($routeName == 'default' && $actionName =='index' && $controllerName == 'search'):?>
            <?php if ($_POST['searched_from_page'] == 'organisation' || $_POST['searched_from_page'] == 'initiative' || $_POST['searched_from_page'] == 'project' ): ?>
                <?php if ($_POST['searched_from_page_id']): ?>
                    <?php $page_id = $_POST['searched_from_page_id']; ?>
                    <?php if(!empty($page_id)):?>
                        <?php $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id); ?>
                        <?php if(!empty($sitepage->page_id)):?>
                            <!-- After/Before Scroll -->
                            <li class="custom_default_menu_items" style="display: none">
                                <a class="menu_core_main" href="<?php echo $sitepage->getHref() ?>">
                                    <span>
                                        <?php if(!empty($sitepage->page_header_name)):?>
                                            <?php echo $sitepage->page_header_name;?>
                                        <?php else : ?>
                                            <?php echo $sitepage->title;?>
                                        <?php endif; ?>
                                    </span>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Projects Details -->
        <?php if($routeName == 'sitecrowdfunding_entry_view' && $actionName =='view'):?>
            <?php $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project'); ?>
            <?php $project_id = $project->getIdentity(); ?>

            <?php $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id); ?>
            <?php if(empty($parentOrganization)): ?>
                <?php $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id); ?>
            <?php endif; ?>

            <?php if(!empty($parentOrganization['page_id']) ): ?>

                <?php $sitepage = Engine_Api::_()->getItem('sitepage_page', $parentOrganization['page_id']);?>

                <!-- After/Before Scroll -->
                <!-- get initiative  if present  -->
                <?php
                    //prepare tags
                    $tagString =  array();
                    if(!empty($project->tags())){
                        $projectTags = $project->tags()->getTagMaps();
                        foreach ($projectTags as $tagmap) {
                            $tagString[]= $tagmap->getTag()->getTitle();
                        }
                    }
                ?>

                <?php if(!empty($project->initiative_id)):?>
                    <?php $initiative = Engine_Api::_()->getItem('sitepage_initiative', $project->initiative_id); ?>
                    <?php $initiativesURL = $this->url(array('action' => 'landing-page','page_id' => $parentOrganization['page_id'], 'initiative_id' => $project->initiative_id), "sitepage_initiatives");?>

                    <?php if(!empty($initiativesURL) ): ?>
                        <li class="custom_default_menu_items" style="display: none">
                            <a class="menu_core_main" href="<?php echo $initiativesURL; ?>">
                                <span>
                                    <?php if(!empty($sitepage->page_header_name)):?>
                                        <?php echo $sitepage->page_header_name;?>
                                    <?php else : ?>
                                        <?php echo $sitepage->title;?>
                                    <?php endif; ?>
                                </span>
                            </a>
                        </li>
                    <?php endif; ?>

                <?php else : ?>

                    <?php if(count($tagString) >0): ?>

                        <?php $initiatives = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectInitiatives($parentOrganization['page_id'],$tagString); ?>

                        <?php if(count($initiatives) != 0):?>
                            <?php $initiativesURL = $this->url(array('action' => 'landing-page','page_id' => $parentOrganization['page_id'], 'initiative_id' => $initiatives[0]['initiative_id']), "sitepage_initiatives");?>

                            <li class="custom_default_menu_items" style="display: none">
                                <a class="menu_core_main" href="<?php echo $initiativesURL; ?>">
                                    <span>
                                        <?php if(!empty($sitepage->page_header_name)):?>
                                            <?php echo $sitepage->page_header_name;?>
                                        <?php else : ?>
                                            <?php echo $sitepage->title;?>
                                        <?php endif; ?>
                                    </span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if(count($initiatives) == 0):?>
                            <li class="custom_default_menu_items" style="display: none">
                                <a class="menu_core_main" href="<?php echo !empty($parentOrganization['link']) ? $parentOrganization['link'] : 'javascript:void(0);'  ?>">
                                    <span>
                                        <?php if(!empty($sitepage->page_header_name)):?>
                                            <?php echo $sitepage->page_header_name;?>
                                        <?php else : ?>
                                            <?php echo $sitepage->title;?>
                                        <?php endif; ?>
                                    </span>
                                </a>
                            </li>
                        <?php endif; ?>

                    <?php else:?>
                        <li class="custom_default_menu_items" style="display: none">
                            <a class="menu_core_main" href="<?php echo !empty($parentOrganization['link']) ? $parentOrganization['link'] : 'javascript:void(0);'  ?>">
                                <span>
                                    <?php if(!empty($sitepage->page_header_name)):?>
                                    <?php echo $sitepage->page_header_name;?>
                                    <?php else : ?>
                                    <?php echo $sitepage->title;?>
                                    <?php endif; ?>
                                </span>
                            </a>
                        </li>
                    <?php endif; ?>

                <?php endif; ?>

            <?php endif; ?>
        <?php endif; ?>

        <!-- Initiatives Landing Page -->
        <?php if($routeName == 'sitepage_initiatives' && $actionName =='landing-page'):?>
            <?php $page_id = $request->getParam('page_id');?>
            <?php $initiative_id = $request->getParam('initiative_id'); ?>
            <?php $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id); ?>
            <?php $initiative = Engine_Api::_()->getItem('sitepage_initiative', $initiative_id); ?>
            <?php $initiativesURL = $this->url(array('action' => 'landing-page','page_id' => $page_id, 'initiative_id' => $initiative_id), "sitepage_initiatives"); ?>

            <?php if(!empty($page_id) && !empty($initiative_id) ):?>

                <!--After Scroll-->
                <li class="custom_menu_items" style="display: none">
                    <a class="menu_core_main" href="<?php echo $sitepage->getHref() ?>"><span><?php echo $sitepage->getTitle(); ?></span></a>
                </li>
                <li class="custom_menu_items" style="display: none">
                    <a class="menu_core_main" href="<?php echo $initiativesURL ?>"><span><?php echo $initiative['title'] ?></span></a>
                </li>

                <!--Before Scroll-->
                <li class="custom_default_menu_items" style="display: none">
                    <a class="menu_core_main" href="<?php echo $sitepage->getHref() ?>">
                        <span>
                            <?php if(!empty($sitepage->page_header_name)):?>
                                <?php echo $sitepage->page_header_name;?>
                            <?php else : ?>
                                <?php echo $sitepage->title;?>
                            <?php endif; ?>
                        </span>
                    </a>
                </li>

            <?php endif; ?>
        <?php endif; ?>

        <?php if( count($this->browsenavigation) > $this->max ): ?>
        <li class="more_link sitecoretheme_more_link"  onclick="navigation_more_toggle(this)">
            <span></span>
            <span></span>
            <span></span>
            <a href="javascript:void(0)" class="sitecoretheme_more_link_text"><span><?php echo $this->translate('More +'); ?></span></a>
            <i class="fa fa-caret-up"></i>
            <ul class="sitecoretheme_submenu">
                <?php $key = 0; ?>
                <?php foreach( $this->browsenavigation as $nav ): ?>
                <?php if( isset($nav->show_to_guest) && empty($nav->show_to_guest) && !$this->viewer()->getIdentity() ): ?>
                <?php continue; ?>
                <?php endif; ?>
                <?php if( $key >= $this->max ): ?>
                <?php $class = array(); ?>
                <?php if( $nav->active ): $class[] ='active'; endif;?>
                <?php if( $nav->hasChildren() ): $class[] ='_main_menu_parent'; endif;?>
                <li class="<?php echo join(' ', $class)?>">
                    <?php if( $nav->action ): ?>
                    <a class= "<?php echo $nav->class ?>" href='<?php echo empty($nav->uri) ? $this->url(array('action' => $nav->action), $nav->route, true) : $nav->uri ?>' <?php if(!empty($nav->target)):?>target="_blank" <?php endif; ?>>
                    <span>
                      <?php if($this->menuIcons && $nav->icon): ?>
                        <i <?php echo (Zend_Uri::check($nav->icon)) ? 'style="background-image:url(' . $nav->icon . ')"' : 'class="fa ' . $nav->icon . '"' ?>></i>
                        <?php endif; ?>
                        <?php echo $this->translate($nav->label); ?></span>
                    </a>
                    <?php else : ?>
                    <a class= "<?php echo $nav->class ?>" href='<?php echo $nav->getHref() ?>' <?php if(!empty($nav->target)):?>target="_blank" <?php endif; ?>>
                    <span>
                      <?php if($this->menuIcons && $nav->icon): ?>
                        <i <?php echo (Zend_Uri::check($nav->icon)) ? 'style="background-image:url(' . $nav->icon . ')"' : 'class="fa ' . $nav->icon . '"' ?>></i>
                        <?php endif; ?>
                        <?php echo $this->translate($nav->label); ?></span></a>
                    <?php endif; ?>
                    <?php if( $nav->hasChildren() ): ?>
                    <i class="fa fa-chevron-down sub_navigation_toggle" onclick="toogle_sub_main_menus(this)"></i>
                    <?php
                  echo $this->navigation()
                    ->menu()
                    ->renderMenu($nav, array('ulClass' => 'sub_navigation'));
                    ?>
                    <?php endif; ?>
                </li>
                <?php endif; ?>
                <?php $key++ ?>
                <?php endforeach; ?>
            </ul>
        </li>
        <?php endif; ?>
        <?php foreach($this->mobileNavigation as $mobileMenu): ?>
        <li class="show_in_mobile_view">
            <a class="menu_core_main <?php echo $mobileMenu->getHref() === '/logout' ? 'smoothbox':'' ?> " href="<?php echo $mobileMenu->getHref() == 'javascript:void(0);this.blur();' ? '/activity/notifications' : $mobileMenu->getHref() ?>">
                <span><?php echo $mobileMenu->getHref() == 'javascript:void(0);this.blur();' ? $this->translate("Notifications") : $this->translate($mobileMenu->getLabel()) ?> </span>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
</div>

<style type="text/css">
    i.fa.fa-chevron-down.sub_navigation_toggle.initiatives_drop_down_icon {
        top: 17px !important;
        right: 21px !important;
        color: black !important;
    }
    .custom_navigation_li_items a:hover > span {
        color: white !important;
    }
    li.custom_navigation_li_items.sub_navigation:hover {
        background-color: black !important;
    }

    .layout_sitecoretheme_browse_menu_main > h3  {
        display:none;
    }
    .show_in_mobile_view > a > span > img{
        display: none;
    }

    @media (max-width: 767px) {
        i.fa.fa-chevron-down.sub_navigation_toggle.initiatives_drop_down_icon {
            top: -3px !important;
            right: 136px !important;
            color: white !important;
        }
        .custom_navigation_li_items a:hover > span {
            color: white !important;
        }
        .show_in_mobile_view{
            display: inline-block;
        }
        .layout_core_menu_logo a img{
            position: relative;
            right: 20px;
        }
        .sitecoretheme_minimenu{
            display: none !important;
        }
    }
    @media (min-width: 768px) {
        .show_in_mobile_view{
            display:none !important;
        }
    }
    @media (max-width: 920px) {
        .sitecoretheme_main_menu a.menu_icon{
            top: 10px !important
        }
    }
    @media (min-width: 921px) and (max-width: 1400px) {
        .sitecoretheme_main_menu > ul.navigation{
            display: inline-block !important;
        }
    }
    /*.sitecoretheme_top_header_two .layout_sitecoretheme_browse_menu_main .sitecoretheme_main_menu .navigation > li.more_link .sitecoretheme_submenu._more_lines > li {
      float: left;
    }*/
</style>
<script type="text/javascript">

    jQuery(document).ready(function () {
        jQuery('.intiative_menudropdown').hover(function () {
                document.getElementsByClassName('_main_menu_parent')[0].classList.add('_show_sub_nav')
            },
            function () {
                document.getElementsByClassName('_main_menu_parent')[0].classList.add('_show_sub_nav')
            });

        var mobileElem,mobileStyle,mobileDisplayStyle;
        mobileElem = document.querySelector('.show_in_mobile_view');
        if(mobileElem){
            mobileStyle = getComputedStyle(mobileElem);
            if(mobileStyle){
                if(mobileStyle.hasOwnProperty('display')){
                    mobileDisplayStyle = mobileStyle.display
                }
            }
        }
        // if true, then it is mobile view
        if(mobileDisplayStyle === "none"){
            //console.log('web');
            window.onclick = function(event) {
                if(document.getElementsByClassName('_main_menu_parent')){
                    if(document.getElementsByClassName('_main_menu_parent').length > 0 ){
                        if(document.getElementsByClassName('_main_menu_parent')[0]){
                            document.getElementsByClassName('_main_menu_parent')[0].classList.remove('_show_sub_nav');
                        }
                    }
                }
            }
        }else{
            console.log('mobile');
           // console.log(document.getElementsByClassName('_main_menu_parent')[0].hasClass('_show_sub_nav'));
           // document.getElementsByClassName('_main_menu_parent')[0].classList.add('_show_sub_nav');
        }

        jQuery(".intiative_menudropdown").click(function(event) {
            if(document.getElementsByClassName('_main_menu_parent')){
                if(document.getElementsByClassName('_main_menu_parent').length > 0 ){
                    if(document.getElementsByClassName('_main_menu_parent')[0]){
                        document.getElementsByClassName('_main_menu_parent')[0].classList.add('_show_sub_nav_check');
                    }
                }
            }
        });

    });

    var navigation_more_toogle_set_width = function() {
        $$('.sitecoretheme_top_header_two .layout_sitecoretheme_browse_menu_main .sitecoretheme_main_menu .navigation > li.more_link .sitecoretheme_submenu').each(function(el) {
            var coords = el.getCoordinates();
            var diff = window.getSize().x - coords.right;
            if (el.getCoordinates().width == 0) {
                return;
            }
            if((el.getCoordinates().width + diff*2) > window.getSize().x) {
                el.setStyle('width', (window.getSize().x - (diff*2))+'px');
                el.addClass('_more_lines');
            }
        });
    };
    $$('.sitecoretheme_main_menu .navigation .sitecoretheme_more_link').addEvent('mouseover', navigation_more_toogle_set_width);
    var navigation_more_toggle = function (el) {
        $$('.sitecoretheme_main_menu .navigation .sitecoretheme_more_link').toggleClass('sitecoretheme_submenu_active');
        navigation_more_toogle_set_width();
    };
    var toogle_sub_main_menus = function(el) {
        if( el.getParent('li').hasClass('_show_sub_nav_check') == false) {
            el.getParent('li').addClass('_show_sub_nav')
        }
        else {
            !el.getParent('li').hasClass('_show_sub_nav') ? el.getParent('li').addClass('_show_sub_nav') : el.getParent('li').removeClass('_show_sub_nav');
        }

    }
</script>
<style>
    .custom_default_menu_items_header{
        position: absolute;
        top: 0;
        /*bottom: 0;*/
        left: 35%;
        right: 35%;
        text-align: center;
    }
    .custom_default_menu_items > a > span{
        font-size: 24px;
    }
    @media (max-width: 919px) {
        .custom_default_menu_items{
            display:none;
        }
    }
    /*changed mobile view css for menu*/
    @media (max-width: 767px) {
        .custom_default_menu_items > a > span {
            font-size: unset !important;
        }
        .custom_default_menu_items_header {
             top: unset !important;
             bottom: unset !important;
        }
    }
</style>