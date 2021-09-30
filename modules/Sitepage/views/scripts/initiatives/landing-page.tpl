<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<script type="text/javascript">
    seaocore_content_type = '<?php echo $this->resource_type; ?>';
</script>
<?php
  $this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<?php
$latitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.map.latitude', 0);
$longitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.map.longitude', 0);
$locationEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1);

$this->headScript()->appendFile($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/scripts/infobubble.js");
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/style_sitepage_profile.css');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css');
$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/follow.js');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/core.js');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/core.js');
$defaultLogo = $this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/nophoto_page_thumb_profile.png';

$noOfProjectsLogo = $this->layout()->staticBaseUrl.'application/modules/Sitecoretheme/externals/images/stats/stats_1.png';

$isFollow = $this->subject->follows()->isFollow($this->viewer);

$initiativesURL = $this->url(array('action' => 'landing-page','page_id' => $this->page_id, 'initiative_id' => $this->initiative['initiative_id']), "sitepage_initiatives");
$projectBrowseURL = $this->url(array('action' => 'browse'), 'sitecrowdfunding_project_general', true);
$projectLocationsURL = $this->url(array('action' => 'map'), 'sitecrowdfunding_project_general', true);

$tab_link= $this->params['tab_link'];

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$baseUrl = $view->baseUrl();

$markerClusterFilePath = $baseUrl . "/externals/map/markerclusterer.js";
$markerclusterer1Icon = $baseUrl . "/externals/map/markerclusterer1.png";

$this->headScript()->appendFile($markerClusterFilePath);

?>

<div>

    <!-- Breadcump -->
    <?php /*
    <div class="sitepage_profile_breadcrumb initiative_container">
    <?php $temp_general_url = $this->url(array(),'sitepage_general', false ); ?>
    <a href="<?php echo $temp_general_url;?>" target="_blank">
        <?php echo $this->translate("Pages Home");?>
    </a>
    <span class="brd-sep seaocore_txt_light">&raquo;</span>
    <a href="<?php echo $this->sitepage->getHref(); ?>" target="_blank"><?php echo $this->sitepage->getTitle(); ?></a>
    <span class="brd-sep seaocore_txt_light">&raquo;</span>
    <a href="<?php echo $initiativesURL?>" target="_blank"><?php echo $this->initiative['title']; ?></a>
</div>
*/?>

<!-- Image -->
<div class="initiative_container">
    <div class="sitepage_cover_information_wrapper">

        <div class='sitepage_cover_wrapper' id="sitepage_cover_photo" style='min-height:110px; height:300px;'  >
        </div>

        <div class="sitepage_cover_information b_medium">

            <div class="sp_coverinfo_profile_photo_wrapper">
                <div class="sp_coverinfo_profile_photo b_dark">
                    <div class='sitepage_photo <?php if ($this->can_edit) : ?>sitepage_photo_edit_wrapper<?php endif; ?>'>
                        <table>
                            <tr valign="middle">
                                <td>
                                    <?php echo $this->htmlLink($this->sitepage, $this->itemPhoto($this->sitepage, 'thumb.profile', '', array('align' => 'left')), array('target' => '_blank','class' => 'thumbs_photo', 'title' => $this->translate($this->sitepage->getTitle()))); ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="sp_coverinfo_buttons">
                <div class="sitecoretheme_search">
                    <div id="sitecoretheme_fullsite_search_initiative">
                        <form id="organisation_search_form" action="<?php echo $this->url(array('controller' => 'search'), 'default', true) ?>" method="post" class="search_form" target="_blank">
                            <input type="hidden" name="page_no" value="1"/>
                            <input type="hidden" name="tab_link" value="all_tab"/>
                            <input type="hidden" name="searched_from_page" value="initiative"/>
                            <input type="hidden" name="searched_from_page_id" value="<?php echo $this->page_id?>" />
                            <input type="hidden" name="searched_from_initiative_id" value="<?php echo $this->initiative_id?>" />
                            <input type="hidden" name="searched_from_project_id" value=null />
                            <input type="hidden" id="category_id" name="category_id" value=null />
                            <input name='query' id='global_search_initiative_field' type="text" autocomplete="off" style="height: 28px !important;width: 147px;" placeholder="<?php echo $this->translate("Search here...") ;?> "/>
                            <input type="hidden" name="type" value="everything_in_organization"/>
                            <input type="hidden" id="sdg_goal_id" name="sdg_goal_id" value=null />
                            <input type="hidden" id="sdg_target_id" name="sdg_target_id" value=null />
                            <input type="hidden" id="search_only_in_project" name="search_only_in_project" value=false />
                            <button style="height: 30px !important;" id="responsive_search_toggle_search" class="responsive_search_toggle_search" >
                                <i style="display: flex;justify-content: center" class="fa fa-search"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div>

                    <!-- Get Link-->

                    <div class="seaocore_follow_button_wrap fleft initiative_edit_container">
                        <a class="create_btn seaocore_follow_button button create_project_btn" href='javascript:void(0);' onclick='javascript:showSmoothBox("shorturl/get-link/subject/sitepage_initiative_<?php echo $this->page_id;?>_<?php echo $this->initiative_id;?>")'>
                            <i class="fa fa-link" style="color: #333333"></i>
                            <span ><?php echo $this->translate('Get Link') ?></span>
                        </a>
                    </div>


                    <?php if(empty($this->viewer_id)):?>

                    <div class="seaocore_follow_button_wrap fleft initiative_edit_container">
                        <a class="edit_btn seaocore_follow_button button user_auth_link" href="javascript:void(0);">
                            <i class="seaocore_icon_edit"></i>
                            <span><?php echo $this->translate('Edit') ?></span>
                        </a>
                    </div>

                    <div class="seaocore_follow_button_wrap fleft initiative_edit_container" id="create_project_web">
                        <a class="create_btn seaocore_follow_button button user_auth_link" href="javascript:void(0);">
                            <i class="seaocore_icon_edit"></i>
                            <span><?php echo $this->translate('Create Project') ?></span>
                        </a>
                    </div>

                    <?php else: ?>

                    <!-- Edit Button -->
                    <div class="seaocore_follow_button_wrap fleft initiative_edit_container" id="edit_intiative">
                        <a class="create_btn seaocore_follow_button button user_auth_link" target="_blank" href='<?php echo $this->url(array('controller' => 'initiatives','action' => 'edit','initiative_id' => $this->initiative_id,'page_id' => $this->page_id), 'sitepage_initiatives', true) ?>'>
                        <i class="seaocore_icon_edit"></i>
                        <span><?php echo $this->translate('Edit') ?></span>
                        </a>
                    </div>

                    <!-- create project btn-->
                    <div class="seaocore_follow_button_wrap fleft initiative_edit_container" id="create_project_web">
                        <a class="create_btn seaocore_follow_button button user_auth_link" target="_blank" href='<?php echo $this->url(array('controller' => 'project-create', 'action' => 'step-one','initiative_id' => $this->initiative_id,'page_id' => $this->page_id), 'sitecrowdfunding_create_with_page_and_initiative', true) ?>'>
                        <i class="seaocore_icon_edit"></i>
                        <span><?php echo $this->translate('Create Project') ?></span>
                        </a>
                    </div>

                    <?php endif; ?>

                </div>
            </div>

            <div class="sp_coverinfo_status">
                <h2><a target="_blank" href="<?php echo $this->sitepage->getHref(); ?>"> <?php echo $this->sitepage->getTitle() ?> </a> </h2>
                <div class="sp_coverinfo_stats seaocore_txt_light">

                    <?php $pagesTable = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding'); ?>
                    <?php $projects = $pagesTable->getPageProjects($this->sitepage->page_id); ?>
                    <!-- <?php $projectsCount = count($projects); ?> -->
                    <?php
                      $allPartnerPages = Engine_Api::_()->getDbtable('pages', 'sitepage')->getPageDetailsWithProjectsCustomCountForPageIds($this->sitepage->page_id);
                    $projectsCount = count($allPartnerPages) > 0 ? $allPartnerPages[0]->projects_count : 0;
                    ?>
                    <?php $admin = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->getManageAdminUserLocation($this->sitepage->page_id); ?>
                    <?php $adminCount = count($projects); ?>

                    <?php $partnerCount = Engine_Api::_()->getDbtable('partners', 'sitepage')->getPartnerPagesCount($this->sitepage->page_id); ?>

                    <a href="javascript:void(0);">
                        <?php
                           $resource_type ='sitepage_page';
                           $followerscount = Engine_Api::_()->getDbtable('follows', 'seaocore')->numberOfFollows($resource_type, $this->sitepage->page_id);
                        echo $this->translate(array('%s followers', '%s followers',$followerscount),$this->locale()->toNumber($followerscount)); ?>
                    </a>
                    &middot;
                    <a href="javascript:void(0);">
                        <?php
                         $memberCount = Engine_Api::_()->getDbtable('follows', 'seaocore')->numberOfMembers($this->sitepage->page_id);
                        echo $this->translate(array('%s members', '%s members',$memberCount),$this->locale()->toNumber($memberCount)); ?>
                    </a>
                    &middot;
                    <a href="javascript:void(0);">
                        <?php echo $this->translate(array('%s projects', '%s projects', $projectsCount),$this->locale()->toNumber($projectsCount)); ?>
                    </a>
                </div>

                <div class="sitepage_follow_edit_btns" >
                    <div class="sp_coverinfo_buttons" style="display: none">
                        <div>
                            <?php if(empty($this->viewer_id)):?>

                            <div class="seaocore_follow_button_wrap fleft initiative_edit_container" id="create_project_mobile">
                                <a class="seaocore_follow_button button user_auth_link" href="javascript:void(0);">
                                    <i style="color: white !important;" class="seaocore_icon_edit"></i>
                                    <span style="color: white !important;"><?php echo $this->translate('Create Project') ?></span>
                                </a>
                            </div>

                            <?php else: ?>

                            <div class="seaocore_follow_button_wrap fleft initiative_edit_container" id="create_project_mobile">
                                <a class="seaocore_follow_button button user_auth_link" target="_blank" href='<?php echo $this->url(array('controller' => 'project-create', 'action' => 'step-one'), 'sitecrowdfunding_create', true) ?>'>
                                <i style="color: white !important;" class="seaocore_icon_edit"></i>
                                <span style="color: white !important;"><?php echo $this->translate('Create Project') ?></span>
                                </a>
                            </div>

                            <?php endif; ?>

                        </div>
                    </div>
                    <?php if(empty($this->viewer_id)):?>

                    <div class="seaocore_follow_button_wrap fleft" id="<?php echo $this->resource_type ?>_most_follows_<?php echo $this->resource_id;?>" style ='display:<?php echo empty($isFollow) ?"inline-block":"none"?>'>
                        <a class="seaocore_follow_button button user_auth_link" href="javascript:void(0);">
                            <i style="color: white !important;" class="follow"></i>
                            <span style="color: white !important;" ><?php echo $this->translate('Follow') ?></span>
                        </a>
                    </div>

                    <?php else: ?>

                    <!-- Follow Button -->
                    <div class="seaocore_follow_button_wrap fleft button seaocore_follow_button_active" id="<?php echo $this->resource_type ?>_unfollows_<?php echo $this->resource_id;?>" style =' display:<?php echo $isFollow ?"inline-block":"none"?>' >
                        <a class="seaocore_follow_button button seaocore_follow_button_following" href="javascript:void(0);">
                            <i style="color: white !important;" class="following"></i>
                            <span style="color: white !important;"><?php echo $this->translate('Following') ?></span>
                        </a>
                        <a class="seaocore_follow_button button seaocore_follow_button_unfollow" href="javascript:void(0);" onclick = "seaocore_content_type_follows('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');">
                            <i style="color: white !important;" class="unfollow"></i>
                            <span style="color: white !important;"><?php echo $this->translate('Unfollow') ?></span>
                        </a>
                    </div>

                    <div class="seaocore_follow_button_wrap fleft" id="<?php echo $this->resource_type ?>_most_follows_<?php echo $this->resource_id;?>" style ='display:<?php echo empty($isFollow) ?"inline-block":"none"?>'>
                        <a  class="seaocore_follow_button button" href="javascript:void(0);" onclick = "seaocore_content_type_follows('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');">
                            <i class="follow"></i>
                            <span style="color:white !important;"><?php echo $this->translate('Follow') ?></span>
                        </a>
                    </div>

                    <input type ="hidden" id = "<?php echo $this->resource_type; ?>_follow_<?php echo $this->resource_id;?>" value = '<?php echo $isFollow ? $isFollow :0; ?>' />



                    <?php endif; ?>

                </div>

            </div>
        </div>
    </div>
</div>


<!-- Metrics-->
<div id="initiative_metric">
    <?php $initiativeMetrics = Engine_Api::_()->getItemTable('sitepage_initiativemetric')->getAllInitiativesMetricByIdPaginator($this->page_id, $this->initiative_id); ?>
    <?php $items_per_page = 4;?>
    <?php $initiativeMetrics->setItemCountPerPage($items_per_page);?>

    <?php if($initiativeMetrics->getTotalItemCount() > 0 ): ?>
    <div class="initiative_container">
        <h3 class="initiative_about_header">Metrics</h3>
        <?php
                if($this->metric_page_no) {
        $this->metric_page_no = $this->metric_page_no;
        }else {
        $this->metric_page_no = 1;
        }
        $initiativeMetrics->setCurrentPageNumber($this->metric_page_no);
        ?>
        <div class="sitecoretheme_counter_container" id ='sitecoretheme_counter_container'>
            <div class="sitecoretheme_container">
                <div class="sitecoretheme_counter_statistic">

                    <!-- Prev Icon Page -->
                    <?php if($initiativeMetrics->getTotalItemCount() > $items_per_page && $this->metric_page_no != 1 ):?>
                    <div id="prev_spinner" class="arrow-button prev-button" style="z-index: 999;">
                        <i onclick="slidePrev('<?php echo $this->page_id; ?>','<?php echo $this->initiative_id; ?>','<?php echo $this->metric_page_no; ?>')" style="font-size: 19px; display: flex;justify-content: center; color: white;margin-bottom: 4px;" class="fa fa-angle-left" aria-hidden="true">
                        </i>
                    </div>
                    <?php endif; ?>

                    <div id="metric_list" class="metric_list" style="display: flex;justify-content: center;width: 100%">
                        <?php foreach($initiativeMetrics as $initiativeMetric): ?>
                        <?php if( !empty($initiativeMetric['initiativemetric_value']) ) :  ?>
                        <?php $metric_id = $initiativeMetric['metric_id'];?>
                         <?php if( $metric_id): ?>
                            <div class="sitecoretheme_counter_statistic_3" style="cursor: pointer" onclick='redirectMetricPage("<?php echo $this->url(array( 'module' => 'sitepage' ,'controller' => 'metrics', 'action' => 'index', 'metric_id' => $metric_id), 'default', true) ?>")' >
                         <?php else: ?>
                            <div class="sitecoretheme_counter_statistic_3"  onclick='redirectMetricPage("<?php echo $this->url(array( 'module' => 'sitepage' ,'controller' => 'metrics', 'action' => 'index', 'metric_id' => $metric_id), 'default', true) ?>")' >
                         <?php endif; ?>
                            <div class="sitecoretheme_counter_wrapper">
                                <div style="display: none" class="stats_icon">
                                    <img src="<?php echo $noOfProjectsLogo; ?>">
                                </div>
                                <div class="stats_info">
                                    <?php $total_aggregate_value = 0; ?>
                                    <?php
                                        // get field_id
                                        $field_ids = array();
                                        foreach (Engine_Api::_()->fields()->getFieldsMeta('yndynamicform_entry') as $field) {
                                            if ($field->type == 'metrics') {
                                                $fieldMeta = Engine_Api::_()->fields()->getField($field->field_id, 'yndynamicform_entry');
                                                    if ($fieldMeta->config['selected_metric_id'] == $initiativeMetric['metric_id']) {
                                                        $field_ids[] = $field->field_id;
                                                    }
                                            }
                                        }
                                    ?>

                                    <?php if (count($field_ids)):?>
                                        <?php
                                            $entryTable = Engine_Api::_()->getDbTable('entries', 'yndynamicform');
                                            $entryTableName = $entryTable->info('name');

                                            $valuesTableName = 'engine4_yndynamicform_entry_fields_values';

                                            // get total aggregate value
                                            $project_aggregate_value = $entryTable->select()
                                            ->setIntegrityCheck(false)
                                            ->from($entryTableName, array("SUM($valuesTableName.value) as project_aggregate"))
                                            ->join($valuesTableName, "$entryTableName.entry_id = $valuesTableName.item_id")
                                            ->where("$valuesTableName.field_id in (?)", $field_ids)
                                            ->where("$entryTableName.project_id IS NOT NULL")
                                            ->where("$entryTableName.user_id IS NULL")
                                            ->query()
                                            ->fetchColumn();

                                            $user_aggregate_value = $entryTable->select()
                                            ->setIntegrityCheck(false)
                                            ->from($entryTableName, array("SUM($valuesTableName.value) as user_aggregate"))
                                            ->join($valuesTableName, "$entryTableName.entry_id = $valuesTableName.item_id")
                                            ->where("$valuesTableName.field_id in (?)", $field_ids)
                                            ->where("$entryTableName.project_id IS NULL")
                                            ->where("$entryTableName.user_id IS NOT NULL")
                                            ->query()
                                            ->fetchColumn();

                                            $project_aggregate_value = $project_aggregate_value;
                                            $user_aggregate_value = $user_aggregate_value;
                                            $totalAggregateValue = (int)$project_aggregate_value + (int)$user_aggregate_value;

                                        ?>

                                        <h4><?php echo $initiativeMetric['initiativemetric_unit'].' '.$totalAggregateValue; ?></h4>
                                    <?php else:?>
                                        <h4><?php echo $initiativeMetric['initiativemetric_unit'].' '.$initiativeMetric['initiativemetric_value']; ?></h4>
                                    <?php endif;?>
                                    <p><?php echo $initiativeMetric['initiativemetric_name']; ?></p>

                                </div>
                            </div>
                        </div>
                        <?php endif;  ?>
                        <?php endforeach;?>
                    </div>

                    <!-- Next Icon Page -->
                    <?php if($initiativeMetrics->getTotalItemCount() > $items_per_page && $this->metric_page_no != $initiativeMetrics->getPages()->pageCount ):?>
                    <div  id="next_spinner" class="arrow-button next-button" style="">
                        <i onclick="slideNext('<?php echo $this->page_id; ?>','<?php echo $this->initiative_id; ?>','<?php echo $this->metric_page_no; ?>')"      style="font-size: 19px; display: flex;justify-content: center; color: white;margin-bottom: 4px;"
                           class="fa fa-angle-right" aria-hidden="true">
                        </i>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- About -->
<?php if(!empty($this->initiative['about'])):?>
<div class="initiative_container">
    <h3 class="initiative_about_header">About</h3>
    <div class="initiative_about_header_desc">
        <?php echo $this->initiative['about']; ?>
    </div>
</div>
<?php endif; ?>

<!-- Projects -->
<div class="initiative_container">

    <!--Menus-->
    <div class="initiative_menu headline sitecrowdfunding_inner_menu">
        <div class='tabs sitecrowdfunding_nav'>
            <ul class='initiative_menu_nav navigation'>
                <?php if(count($this->project_galleries) != 0):?>
                <li>
                    <a id="project_galleries" href="javascript:void(0);" onclick="selected_ui('project_galleries')" >
                        <?php echo $this->translate('Project Galleries'); ?>
                    </a>
                </li>
                <?php endif; ?>
                <li>
                    <a id="browse_projects" href="javascript:void(0);" onclick="selected_ui('browse_projects')" >
                        <?php echo $this->translate('Browse Projects'); ?>
                    </a>
                </li>
                <li>
                    <a id="project_locations" href="javascript:void(0);" onclick="selected_ui('project_locations')" >
                        <?php
                             $item = Engine_Api::_()->getItem('sitepage_initiative', $this->initiative_id); $count = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectsCountByPageIdAndInitiativesIds($item['page_id'],$item['initiative_id']);
                        if($count > 0) {
                        echo $this->translate('Locations');
                        }

                        ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div id="landing_page_projects">

        <?php $showViewMore = false; ?>

        <?php if ( $tab_link == 'project_galleries') : ?>
        <div id="project_galleries" class="landing_page_projects_container project_galleries_container">
            <div id="project_galleries_inner">
                <?php $i = 1;?>

                <!--  set limit count for projects -->
                <?php $totalProjectGalleries = count($this->project_galleries);?>

                <?php if ( $totalProjectGalleries == 1) : ?>
                <?php $projectLimitCount = 8; ?>
                <?php elseif ($totalProjectGalleries == 2): ?>
                <?php $projectLimitCount = 4; ?>
                <?php elseif ($totalProjectGalleries == 3): ?>
                <?php $projectLimitCount = 2; ?>
                <?php elseif ($totalProjectGalleries == 4): ?>
                <?php $projectLimitCount = 2; ?>
                <?php endif; ?>


                <?php foreach($this->project_galleries as $project_galleries): ?>
                <div id='projects_section<?php echo "_" . $i; ?>' class="projects_section_containers">

                    <h3 class="projects_section_name">
                        <a href="javascript:void(0);" onclick="viewMore('<?php echo $project_galleries; ?>');"' title="<?php echo $project_galleries ?>">
                        <?php echo $project_galleries; ?>
                        </a>
                    </h3>

                    <div class="projects_section_projects_container">

                        <!-- get projects -->
                        <?php $projects = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectsByPageIdAndTag($this->page_id,$project_galleries,$projectLimitCount); ?>

                        <!-- get projects count -->
                        <?php $totalProjectsCount = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectsCountByPageIdAndTag($this->page_id,$project_galleries); ?>

                        <ul class='projects_manage sitecrowdfunding_projects_grid_view sitecrowdfunding_projects_galleries'  id='<?php if(count($this->project_galleries) == 2) echo "projects_section_test2";  elseif(count($this->project_galleries) == 3) echo "projects_section_test3"; elseif(count($this->project_galleries) == 4) echo "projects_section_test4";elseif(count($this->project_galleries) == 1) echo "projects_section_test1";?>' >
                            <?php foreach ($projects as $project): ?>
                            <li class="effect2">
                                <?php $item = Engine_Api::_()->getItem('sitecrowdfunding_project', $project['project_id']); ?>
                                <div class="sitecrowdfunding_thumb_wrapper sitecrowdfunding_thumb_viewer" id='<?php if(count($this->project_galleries) == 2) echo "sitecrowdfunding_thumb_viewer2";  elseif(count($this->project_galleries) == 3) echo "sitecrowdfunding_thumb_viewer3"; elseif(count($this->project_galleries) == 4) echo "sitecrowdfunding_thumb_viewer4"; elseif(count($this->project_galleries) == 1) echo "sitecrowdfunding_thumb_viewer1";?>' >
                                    <div class="sitecrowdfunding_grid_thumb">
                                        <?php $fsContent = ""; ?>
                                        <?php
                                                            if ($item->photo_id) {
                                        echo $this->htmlLink($item->getHref(), $fsContent . $this->itemBackgroundPhoto($item,'thumb.cover', null, null, array('tag' => 'i')), array('class' => 'sitecrowdfunding_thumb','target' => '_blank'));
                                        } else {
                                        $url = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/nophoto_project_thumb_profile.png";
                                        echo $this->htmlLink($item->getHref(), $fsContent . "<i style='background-image:url(" . $url . ")'></i>", array('class' => 'sitecrowdfunding_thumb','target' => '_blank'));
                                        }
                                        ?>
                                        <div class='sitecrowdfunding_hover_info' style="display: flex;align-items: center;justify-content: center;">
                                            <div class="txt_center">
                                                <button onclick="window.open('<?php echo $item->getHref() ?>','_blank')">
                                                    <?php echo $this->translate('View'); ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="sitecrowdfunding_info_wrapper">
                                        <div class="sitecrowdfunding_info">
                                            <div class="sitecrowdfunding_bottom_info sitecrowdfunding_grid_bott_info">
                                                <!-- <h3><?php echo $this->htmlLink($item->getHref(), $this->string()->truncate($this->string()->stripTags($item->getTitle()), $this->titleTruncationGridView), array('title' => $item->getTitle())) ?></h3> -->
                                                <h3 class="project_titles">   <?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('target' => '_blank')) ?> </h3>

                                                <div class="sitecrowdfunding_grid_bottom_info">

                                                    <!-- <?php $title = $this->translate('by %s', $this->htmlLink($item->getOwner()->getHref(), $this->string()->truncate($this->string()->stripTags($item->getOwner()->getTitle()), 17), array('title' => $item->getOwner()->getTitle()))); ?>
                                                    <?php echo $title; ?> -->

                                                    <div class="sitecrowdfunding_desc" title="<?php echo $this->string()->truncate($this->string()->stripTags($item->desire_desc), 250) ?>">
                                                        <!-- <?php echo $this->string()->truncate($this->string()->stripTags($item->desire_desc), $this->descriptionTruncation ? $this->descriptionTruncation : 175) ?> -->
                                                        <?php echo $this->string()->stripTags($item->desire_desc); ?>
                                                    </div>

                                                    <?php /*
                                                    <!-- <div class="sitecrowdfunding_bottom_info_category">
                                                         <?php $category = Engine_Api::_()->getItem('sitecrowdfunding_category', $item->category_id); ?>

                                                         <?php if ($category->file_id): ?>
                                                         <?php $url = Engine_Api::_()->storage()->get($category->file_id)->getPhotoUrl(); ?>
                                                         <img src="<?php echo $url ?>" style="width: 16px; height: 16px;" alt="">
                                                         <?php elseif ($category->font_icon): ?>
                                                         <i class="fa <?php echo $category->font_icon; ?>"></i>
                                                         <?php else: ?>
                                                         <?php $src = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/category_images/icons/noicon_category.png" ?>
                                                         <img src="<?php echo $src ?>" style="width: 16px; height: 16px;" alt="">
                                                         <?php endif; ?>

                                                         <?php echo $this->htmlLink($category->getHref(), $category->getTitle()) ?>
                                                     </div> -->
                                                    */?>

                                                    <?php
                                                                  $pro_location = Engine_Api::_()->getDbtable('locations', 'sitecrowdfunding')->getLocation(array('id' => $item->project_id));
                                                    ?>
                                                    <?php if ($pro_location->location) : ?>
                                                    <div class="sitecrowdfunding_bottom_info_location" title="<?php echo $item->location ?>">
                                                        <i class="seao_icon_location"></i>
                                                        <?php echo $this->string()->truncate($this->string()->stripTags($pro_location->location), $this->truncationLocation); ?>
                                                    </div>
                                                    <?php endif; ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="project_funding_progressive_bar">
                                            <?php if($item->isFundingApproved()): ?>
                                            <?php
                                                                        $fundedAmount = $item->getFundedAmount();
                                            $fundedRatio = $item->getFundedRatio();
                                            $fundedAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($fundedAmount);
                                            ?>
                                            <?php echo $this->fundingProgressiveBar($fundedRatio);?>
                                            <div class="sitecrowdfunding_funding_pledged_days_wrapper" style="position: absolute;bottom: 0px;left:0px;width:100%">
                                                <div class="sitecrowdfunding_funding_pledged_days">
                                                                            <span>
                                                                                <?php echo $this->translate("$fundedRatio %"); ?><br />
                                                                                <?php echo $this->translate("Funded "); ?>
                                                                            </span>
                                                    <span>
                                                                                <?php echo $this->translate("%s", $fundedAmount); ?><br />
                                                        <?php echo $this->translate("Backed"); ?>
                                                                            </span>
                                                    <?php if (in_array('endDate', $this->projectOption)) : ?>
                                                    <span><?php echo $item->getRemainingDays(); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>

                        <!-- Show common view more based on project count -->
                        <?php if( $totalProjectsCount > count($projects) && $showViewMore == false ):?>
                        <?php $showViewMore = true;?>
                        <?php endif; ?>

                    </div>
                </div>

                <?php $i = $i + 1; ?>
                <?php endforeach;?>


            </div>
            <!-- common view more button-->
            <?php if( $showViewMore == true ):?>
            <div class="seaocore_view_more mtop10" id="seaocore_view_more">
                <a href="javascript:void(0);" class="buttonlink icon_viewmore">
                    <?php echo $this->translate("View More") ?>
                </a>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- View More -->
        <?php if ( $tab_link == 'browse_projects') : ?>
        <div id="browse_projects" class="landing_page_projects_container browse_projects_container">
            <div class="generic_layout_container layout_main">

                <div class="generic_layout_container layout_middle" id="projects_list">
                    <?php
                       $routeName = Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName();
                    $item = Engine_Api::_()->getItem('sitepage_initiative', $this->initiative_id);
                    $projects = Engine_Api::_()->getDbTable('pages', 'sitecrowdfunding')->getProjectsCountByPageIdAndInitiativesIds($item['page_id'], $item['initiative_id']);
                    if($projects > 0) {
                    include_once APPLICATION_PATH .'/application/modules/Sitecrowdfunding/views/scripts/_project_search.tpl' ;
                    include_once APPLICATION_PATH .'/application/modules/Sitecrowdfunding/views/scripts/_project_browse.tpl' ;
                    }
                    else { ?>
                    <div class="tip">
                            <span>
                                <?php echo $this->translate('No Projects Found.');?>
                            </span>
                    </div>
                    <?php }
                    ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ( $tab_link == 'project_locations') : ?>
        <div id="project_locations" class="landing_page_projects_container project_locations_container">
            <?php
            $item = Engine_Api::_()->getItem('sitepage_initiative', $this->initiative_id); $count = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectsCountByPageIdAndInitiativesIds($item['page_id'],$item['initiative_id']);
            if($count > 0) {
            include_once APPLICATION_PATH .'/application/modules/Sitecrowdfunding/views/scripts/_project_locations.tpl' ;
            }
            else { ?>
            <div class="tip">
                            <span>
                                <?php echo $this->translate('No Projects Found.');?>
                            </span>
            </div>
            <?php }
                    ?>

        </div>
        <?php endif; ?>

    </div>

    <div id="hidden_ajax_data" style="display: none;"></div>

</div>

<!-- Backstory -->
<?php if(!empty($this->initiative['back_story'])):?>
<div class="initiative_container">
    <h3 class="initiative_back_story_header">Backstory</h3>
    <div class="initiative_back_story_desc">
        <?php echo $this->initiative['back_story']; ?>
    </div>
</div>
<?php endif; ?>

<!-- Goals -->
<div class="initiative_container">
    <h3 class="initiative_about_header">Sustainable Development Goals</h3>
    <div class="initiative_about_header_desc">
        <?php echo $this->content()->renderWidget("sitepage.development-goals", array( 'page_layout' => "initiative_landing_page" , 'initiative_id' => $this->initiative_id ));?>
    </div>
</div>


</div>
<div id="hidden_ajax_page_projects_data" style="display: none;"></div>
<script type="text/javascript">
    // if search is clicked
    // bind search i/p
    window.addEvent('domready', function() {

        var requestURL = '<?php echo $this->url(array('module' => 'sitecoretheme', 'controller' => 'general', 'action' => 'get-search-content'), "default", true) ?>'+'?page_id= '+'<?php echo $this->page_id?>'+'&initiative_id='+'<?php echo $this->initiative_id?>';
        contentAutocomplete = new Autocompleter.Request.JSON('global_search_initiative_field', requestURL, {
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
                        var titleAjax1 = encodeURIComponent($('global_search_initiative_field').value);
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

        $('global_search_initiative_field').addEvent('keydown', function (event) {
            if (event.key == 'enter') {
                $('sitecoretheme_fullsite_search_initiative').submit();
            }
        });
    });
    function  redirectMetricPage(url) {
        let protocol = "<?php echo ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != 'off') ? 'https' : 'http'; ?>";
        let host= "<?php echo $_SERVER['HTTP_HOST']; ?>";
        var full_url = protocol+"://"+host+url;
        window.location.href = full_url;
    }
    function showSearchResultPage(url) {
        window.location.href = url;
    }
    function seeMoreSearchResults() {

        $('stopevent').removeEvents('click');
        var url = '<?php echo $this->url(array('controller' => 'search'), 'default', true); ?>' + '?query=' + encodeURIComponent($('global_search_field').value) + '&type=' + 'all';
        window.location.href = url;

    }
    var $j = jQuery.noConflict();

    $j(".responsive_search_toggle_search").click(function() {
        $j('#sdg_target_id').val(null);
        $j('#sdg_goal_id').val(null);
    });
    function slidePrev(page_id,initiative_id,page_no) {
        var container_name = 'initiative_metric';
        var spinner_name = 'metric_list';
        var page_nos = parseInt(page_no) - 1;
        var url = en4.core.baseUrl + 'organizations/initiatives/landing-page/';

        $(spinner_name).innerHTML = '<div class="seaocore_content_loader" style="display: flex;justify-content: center"></div>';
        var request = new Request.HTML({
            url: url,
            data:{
                format: 'html',
                page_id: page_id,
                initiative_id: initiative_id,
                metric_page_no: page_nos
            },
            evalScripts: true,
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $('hidden_ajax_page_projects_data').innerHTML = responseHTML;
                $(container_name).innerHTML = $('hidden_ajax_page_projects_data').getElement('#'+container_name).innerHTML;
                $('hidden_ajax_page_projects_data').innerHTML = '';
                fundingProgressiveBarAnimation();
                Smoothbox.bind($(spinner_name));
                en4.core.runonce.trigger();
            }
        });
        request.send();
    }
    function slideNext(page_id,initiative_id,page_no) {
        var container_name = 'initiative_metric';
        var spinner_name = 'metric_list';
        var page_nos = parseInt(page_no) + 1;
        var url = en4.core.baseUrl + 'organizations/initiatives/landing-page/';

        $(spinner_name).innerHTML = '<div class="clr"></div><div class="seaocore_content_loader"></div>';
        var request = new Request.HTML({
            url: url,
            data:{
                format: 'html',
                page_id: page_id,
                initiative_id: initiative_id,
                metric_page_no: page_nos
            },
            evalScripts: true,
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $('hidden_ajax_page_projects_data').innerHTML = responseHTML;
                $(container_name).innerHTML = $('hidden_ajax_page_projects_data').getElement('#'+container_name).innerHTML;
                $('hidden_ajax_page_projects_data').innerHTML = '';
                fundingProgressiveBarAnimation();
                Smoothbox.bind($(spinner_name));
                en4.core.runonce.trigger();
            }
        });
        request.send();
    }


</script>
<style type="text/css">
    #prev_spinner{
        background-color: #44AEC1;
        border-radius: 26px;
        width: 15px;
        padding: 4px 8px;
        cursor: pointer;
        position: unset !important;
    }
    #next_spinner{
        background-color: #44AEC1;
        float: right;
        position: relative;
        bottom: 138px;
        border-radius: 26px;
        width: 15px;
        padding: 4px 8px;
        cursor: pointer;
        position: unset !important;
    }
    .sitecoretheme_counter_statistic_3 {
        background: #fff;
        margin: 2%;
        padding: 35px 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0,0,0,0.24);
        border-radius: 3px;
        width:25%;
        height: 179px;
        text-align: center;
        align-items: center;
        justify-content: center;
        display: flex;
    }
    .sitecoretheme_counter_statistic {
        width: 100%;
        float: left;
        font-size: 0;
        text-align: center;
        position: relative;
        /* top: -91px; */
        bottom: 39px;


        display: flex;
        justify-content: center;
        align-items: center;
        position: unset !important;
    }

    @media(min-width:1024px){
        .sitecoretheme_container{
            padding:35px !important;
        }
    }
    .arrow-button.prev-button {
        background-color: #44AEC1;
        border-radius: 26px;
        width: 15px;
        padding: 4px 8px;
        /* margin-bottom: -127px; */
        position: relative;
        top: 114px !important;
    }
    .metric_name{
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
    }
    .stats_info {
        width: 100%;
    }
    #category_id, #category_id-label{
        display:none;
    }
    .stats_info p {
        text-align: center;
    }
    .sitecoretheme_counter_wrapper p{
        display: flex;
        justify-content: center;
    }
    .sitecoretheme_counter_wrapper .stats_info {
        padding-left: unset !important;
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        vertical-align: top;
        width: calc(100% - 0px);
    }
    a.seaocore_icon_edit.initiative_edit_btn {
        border: 1px solid #CECECE;
        border-radius: 3px 3px 3px 3px;
        clear: both;
        float: left;
        outline: medium none;
        padding: 5px 7px;
        cursor: pointer;
        line-height: normal;
    }
    div#edit_intiative {
        cursor: pointer;
        z-index: 999;
    }
    #sitecrowdfunding_thumb_viewer2,#sitecrowdfunding_thumb_viewer1{
        width:100%
    }
    a.seaocore_follow_button.seaocore_icon_edit.initiative_edit_btn {
        cursor: pointer;
    }
    ul.projects_manage.sitecrowdfunding_projects_galleries {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .sitepage_profile_breadcrumb {
        font-size: 11px;
        margin-bottom: 10px;
    }
    .sitepage_profile_breadcrumb .brd-sep {
        margin: 0 3px;
    }
    div#mobile_view {
        display: none;
    }
    .initiative_container {
        background-color: #ffffff;
        padding: 15px;
        margin-bottom: 15px;
        -webkit-border-radius: 6px;
        border-radius: 6px;
        -moz-box-shadow: 0 1px 8px 0 rgba(0, 0, 0, .05);
        -webkit-box-shadow: 0 1px 8px 0 rgba(0, 0, 0, .05);
        box-shadow: 0 1px 8px 0 rgba(0, 0, 0, .05);
    }
    .initiative_about_header, .initiative_back_story_header  {
        font-size: 30px;
        border-bottom: 0;
        padding-bottom: 10px;
        text-transform: capitalize;
        background: transparent;
        margin-bottom: 20px;
        text-align: center;
        position: relative;
        line-height: normal;
    }
    .initiative_about_header::before, .initiative_back_story_header::before {
        left: 0;
        margin: 0 auto;
        right: 0;
        text-align: center;
        width: 85px;
        background: #44AEC1;
        top: 100%;
        content: "";
        display: block;
        min-height: 2px;
        position: absolute;
    }
    .projects_section_name{
        font-size: 20px;
        border-bottom: 0;
        padding-bottom: 10px;
        text-transform: capitalize;
        background: transparent;
        margin-bottom: 20px;
        text-align: center;
        position: relative;
        line-height: normal;
    }
    .projects_section_name::before {
        left: 0;
        margin: 0 auto;
        right: 0;
        text-align: center;
        width: 85px;
        background: #44AEC1;
        top: 100%;
        content: "";
        display: block;
        min-height: 2px;
        position: absolute;
    }
    .initiative_about_header_desc , .initiative_back_story_desc{
        font-size: 18px !important;
        line-height: 30px;
    }
    .projects_section_containers{
        display: inline-block;
        vertical-align: top;
    }
    .initiative_header_name{
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0, 0, 0, 0.3) !important;
        padding: 10px !important;
        border-radius: 5px;
        color: white;
        font-size: 40px;
        font-weight: bold !important;
        line-height: 45px;
        letter-spacing: 2px;
        border: none;
        text-shadow: 0px 1px 1px rgba(0, 0, 0, 0.7);
    }

    #sitecrowdfunding_thumb_viewer2
    {
        height: 510px !important;
    }
    #sitecrowdfunding_thumb_viewer1{
        height: 520px !important;
    }
    #sitecrowdfunding_thumb_viewer3,
    #sitecrowdfunding_thumb_viewer4  {
        width: 314px;
        height: 520px !important;
    }
    .sitecrowdfunding_funding_bar {
        position: absolute ;
        bottom: 77px !important;
        left: 0px;
        width: 99% !important;
    }
    ul#projects_section_test1 {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
    }
    .sitecrowdfunding_thumb_wrapper a i, .featured_slidshow_img a i {
        background-position: unset !important;
    }
    img#before_reposition_image {
        object-fit: cover !important;
    }
    #create_project_mobile {
        display: none;
    }
    .sitecrowdfunding_projects_grid_view li {
        width: 294px;
    }
    #projects_section_test1 li {
        width: 23%;
    }
    div#project_browse_content {
        display: unset !important;
    }
    @media(max-width:767px){
        #metric_list {
            justify-content: unset !important;
            width: 80%;
            overflow: auto;
        }
        #metric_list::-webkit-scrollbar {
            width: 4px;
        }
        /* Track */
        #metric_list::-webkit-scrollbar-track {
            box-shadow: inset 0 0 5px grey;
            border-radius: 10px;
        }
        /* Handle */
        #metric_list::-webkit-scrollbar-thumb {
            background: #44AEC1;
            border-radius: 10px;
        }
        /* Handle on hover */
        #metric_list::-webkit-scrollbar-thumb:hover {
            background: #44AEC1;
        }
        .sitecoretheme_counter_statistic_3 {

            min-width: 300px !important;
        }
        .sp_coverinfo_buttons {
            margin-top: 30%;
            display: flex;
            flex-wrap: wrap;
            margin-left: 0px;
        }
        div#project_browse_content {
            display: flex !important;
            flex-direction: column;
        }
        ul#projects_manage_ {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: unset !important;
            overflow-x: auto;
            justify-content: unset !important;
        }

        .sitecrowdfunding_projects_grid_view li .sitecrowdfunding_thumb_wrapper {
            height: 510px !important;
            width: 272px !important;
        }
        #projects_section_test2 li{
            width:unset !important;
        }
        #project_galleries_inner {
            flex-direction: column !important;
        }
        div#project_galleries{
            flex-direction: column !important;
        }
        #projects_section_1, #projects_section_2{
            width:100% !important;
        }
        .sitepage_cover_information {
            padding: 10px 26px 182px !important;
        }
        .seaocore_follow_button_active .seaocore_follow_button_following {
            display: block !important;
        }
        .initiative_edit_container {
            margin-left: 10px;
        }
        a.seaocore_follow_button.seaocore_follow_button_unfollow {
            margin-top: 10px;
            width: 75px;
        }
        .initiative_title > h2 {
            width: 89% !important;
            top: 42% !important;
        }
        .sp_coverinfo_status {
            overflow: unset !important;
            margin-top: 10%;
        }
        .sp_coverinfo_profile_photo {
            bottom: -65px !important;
        }
        div#sitepage_cover_photo {
            min-height: 110px !important;
            height: 369px !important;
            width: 100% !important;
        }
        .sp_coverinfo_status a {
            position: relative;
            top: 40px;
            float: left; !important;
        }
        .sp_coverinfo_buttons {
            position: relative;
            top: 0px !important;
            float: left !important;
        }
        .initiative_container {
            display: flex;
            flex-direction: column !important;
        }
        #projects_section_1 ,
        #projects_section_2 ,
        #projects_section_3 ,
        #projects_section_4 {
            width: 100% !important;
        }
        #sitecrowdfunding_thumb_viewer1, #sitecrowdfunding_thumb_viewer3, #sitecrowdfunding_thumb_viewer4 {
            width: 292px !important;
        }
        #sitecrowdfunding_thumb_viewer2
        {
            width: 296px !important;
        }
        ul#projects_section_test1 ,
        ul#projects_section_test2 ,
        ul#projects_section_test3 ,
        ul#projects_section_test4{
            display: flex;
            flex-direction: row;
            flex-wrap: unset !important;
            overflow-x: auto;
            justify-content: unset !important;
        }
        ul.projects_manage > li {
            margin: 2% !important;
        }
        li.effect2 {
            width: 292px !important;
        }
        #create_project_mobile > a {
            width: 103px;
        }
        .sitepage_follow_edit_btns {
            display: flex;
            justify-content: space-evenly;
            width: 80vw;
            position: relative;
            top: 27px;
            right: 28px;
        }
        div#mobile_view {
            width: 100%;
            display: flex !important;
            align-items: baseline;
            justify-content: space-evenly;
            position: relative;
            top: 50px;
            width: 91vw;
            right: 19%;
        }
        div#web_view {
            display: none;
        }
        #create_project_mobile {
            display: block !important;
            position: relative;
            right: 10px;
        }
        #create_project_web {
            display: none !important;
        }
        ul#projects_section_test1 ,
        ul#projects_section_test2 ,
        ul#projects_section_test3 ,
        ul#projects_section_test4{
            display: flex;
            flex-direction: row;
            flex-wrap: unset !important;
            overflow-x: auto;
        }
        ul.projects_manage > li {
            margin: 2% !important;
        }
        li.effect2 {
            width: 292px !important;
        }
        .projects_section_projects_container{
            width: 100% !important;
        }
        #projects_section_test2{
            left: 0% !important;
        }
        img.cover_photo.thumb_cover.item_photo_sitepage_photo {
            top: 0px !important;
        }
    }
    .initiative_edit_container{
        margin-left: 10px;
    }
    .initiative_edit_container > a {
        padding: 4px 5px !important;
        height: 17px !important;
    }
    .initiative_edit_container > a:hover {
        color: black !important;
    }
    .headline .tabs > ul > li > a.active {
        border-color: #44AEC1;
        color: #44AEC1;
    }
    .initiative_menu{
        border-bottom: 1px solid #f2f0f0;
    }
    div#sitepage_cover_photo {
        min-height: 110px !important;
        height: 300px !important;
        width: 100% !important;
    }
    .sitepage_cover_photo img {
    }
</style>

<script type="text/javascript">

    var tab_link = null;
    var map=null;
    var alreadyStarted = false;

    // set width container for project galleries
    function setWidthForProjectGalleriesContainer(){
        var projectGalleries = <?php echo count($this->project_galleries) ?>;
        if(projectGalleries != 0){
            if(projectGalleries == 4){

                if(document.getElementById("projects_section_1")){
                    document.getElementById("projects_section_1").style.width = "24%";
                }
                if(document.getElementById("projects_section_2")){
                    document.getElementById("projects_section_2").style.width = "24%";
                }
                if(document.getElementById("projects_section_3")){
                    document.getElementById("projects_section_3").style.width = "24%";
                }
                if(document.getElementById("projects_section_4")){
                    document.getElementById("projects_section_4").style.width = "24%";
                }

            }else if(projectGalleries == 3){
                if(document.getElementById("projects_section_1")) {
                    document.getElementById("projects_section_1").style.width = "33%";
                }
                if(document.getElementById("projects_section_2")) {
                    document.getElementById("projects_section_2").style.width = "33%";
                }
                if(document.getElementById("projects_section_3")) {
                    document.getElementById("projects_section_3").style.width = "33%";
                }
            }else if(projectGalleries == 2){
                if(document.getElementById("projects_section_1")) {
                    document.getElementById("projects_section_1").style.width = "45%";
                }
                if(document.getElementById("projects_section_2")) {
                    document.getElementById("projects_section_2").style.width = "45%";
                }
            }else{
                if(document.getElementById("projects_section_1")) {
                    document.getElementById("projects_section_1").style.width = "100%";
                }
            }
        }
    }

    // go to menu container
    function goToMenuContainer(){
        var containerName = "initiative_menu";
        var $j = jQuery.noConflict();
        $j('html, body').animate({
            scrollTop: $j(`.${containerName}`).offset().top - 70
        }, 1000);
    }

    // set click event for view more button
    function setClickForViewMore(){
        var seaocore_view_more = document.getElementById('seaocore_view_more');
        if(seaocore_view_more){
            seaocore_view_more.onclick = function() {
                viewMore(null);
            };
        }
    }

    function loadProjectLocationUI(){
        initialize();
        setButton();
        locationAutoSuggest('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.countrycities'); ?>', 'location', 'project_city');
        // Zoom in the map
        if(map && bounds){
            map.fitBounds(bounds);
        }
    }

    function loadProjectBrowseUI(){

        // Hide the initiative fields in form
        if($('page_id-wrapper')){
            $('page_id-wrapper').style.display = 'none';
        }

        if($('initiative-wrapper')){
            $('initiative-wrapper').style.display = 'none';
        }


        var initiative_id = <?php echo $this->initiative_id?>;
        var page_id = <?php echo $this->page_id?>;

        // show initiative_galleries if initiative has initiative_galleries
        var projectGalleries = <?php echo count($this->project_galleries) ?>;
        if(projectGalleries != 0){
            var initiative_galleries = "<?php echo $this->params['initiative_galleries']; ?>";
            initiativeOptions(page_id,'initiative_names',initiative_id);
            if($('initiative')){
                $('initiative').value = initiative_id;
            }
            if($('page_id')){
                $('page_id').value = page_id;
            }

            // false => show the "initiative" wrapper
            if(initiative_galleries !== null && initiative_galleries !== ''){
                $("initiative_galleries").value = initiative_galleries;
                initiativeOptions(initiative_id,'initiative_project_galleries',initiative_galleries,false);
            }else{
                initiativeOptions(initiative_id,'initiative_project_galleries',null,false);
            }
        }
    }

    function loadProjectGalleriesUI(){

        // set width container
        setWidthForProjectGalleriesContainer();
        // set click for view more
        setClickForViewMore();
    }

    // view more btn in project galleries
    function viewMore(type){

        // remove active for other classes
        if($('project_galleries')){
            $('project_galleries').removeClass('active');
        }
        $('browse_projects').removeClass('active');
        $('project_locations').removeClass('active');

        // Add class
        $("browse_projects").addClass('active');

        $('landing_page_projects').innerHTML = '<div class="clr"></div><div class="seaocore_content_loader"></div>';

        var params = {
            requestParams:<?php echo json_encode($this->params) ?>
    };

        params.requestParams = resetParamValues("browse_projects",params.requestParams);

        goToMenuContainer();
        var request = new Request.HTML({
            url: en4.core.baseUrl + 'sitepage/initiatives/landing-page',
            data: $merge(params.requestParams, {
                format: 'html',
                subject: en4.core.subject.guid,
                page_id: <?php echo $this->page_id?>,
        initiative_id: <?php echo $this->initiative_id?>,
        initiative: <?php echo $this->initiative_id?>,
        tab_link: "browse_projects",
            initiative_galleries:type
    }),
        evalScripts: true,
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
            $('hidden_ajax_data').innerHTML = responseHTML;
            if($('hidden_ajax_data').getElement('#landing_page_projects')) {
                $('landing_page_projects').innerHTML = $('hidden_ajax_data').getElement('#landing_page_projects').innerHTML;
            }

            $('hidden_ajax_data').innerHTML = '';
            fundingProgressiveBarAnimation();
            Smoothbox.bind($('landing_page_projects'));
            en4.core.runonce.trigger();
            loadProjectBrowseUI();
        }
    });

        request.send();

    }

    // counter effect
    /*window.addEventListener("scroll", function() {
    var el = $('sitecoretheme_counter_container');
    if(el){
        var rect = el.getBoundingClientRect();
        if (!alreadyStarted && (rect.top - el.offsetTop) <= 500) {
            alreadyStarted = true;
            $$('.sitecoretheme_counter_wrapper').each(function (el) {
                //startCounter(el.getElement('h4'), 0, parseInt(el.getElement('h4').get('text').replace('+', '')), 1000);
                startCounter(el.getElement('h4'), 0, parseInt(el.getElement('h4').get('text').replace('', '')), 1000);
            });
        }
    }
});
function startCounter(el, start, end, duration) {
    var current = start;
    var interval = Math.abs(Math.ceil(end / duration));
    var timer = setInterval(function() {
        current += interval;
        if (current >= end) {
            //el.innerHTML = end + '+';
            el.innerHTML = end + '';
            clearInterval(timer);
        }
        //el.innerHTML = current + '+';
        el.innerHTML = current + '';
    }, 1);
}*/

    // called when page is loaded
    window.addEvent('domready', function () {

        // set menu highlight
        tab_link = "<?php echo $tab_link; ?>";
        $(tab_link).addClass('active');

        // load the ui based on tab
        if(tab_link === 'browse_projects'){
            loadProjectBrowseUI();
        }

        if(tab_link === 'project_locations'){
            loadProjectLocationUI();
        }

        if(tab_link === 'project_galleries'){
            loadProjectGalleriesUI();
        }

        // set cover photo
        document.seaoCoverPhoto= new SitepageCoverPhoto({
            block :$('sitepage_cover_photo'),
            photoUrl:'<?php echo $this->url(array('action' => 'get-cover-photo', 'page_id' => $this->page_id , 'initiative_id' => $this->initiative_id ), 'sitepage_initiatives', true); ?>',
            buttons:'seao_cover_options',
            positionUrl :'<?php echo $this->url(array('action' => 'reset-position-cover-photo', 'page_id' => $this->page_id , 'initiative_id' => $this->initiative_id), 'sitepage_initiatives', true); ?>',
            position :<?php  echo $this->cover_params ? json_encode($this->cover_params): json_encode(array('top' => 0, 'left' => 0)); ?>
    });

    });

    // function used to reset params of project_location / browse_projects
    function resetParamValues(tabLink, params){
        if(tabLink === 'project_galleries'){

            // reset browse_projects params
            delete params.view_view;
            delete params.users;
            delete params.Latitude;
            delete params.Longitude;
            delete params.categoryname;
            delete params.city;
            delete params.locationmiles;
            delete params.project_city;
            delete params.project_country;
            delete params.project_state;
            delete params.project_street;
            delete params.rewrite;
            delete params.separator1;
            delete params.separator2;
            delete params.subcategoryname;
            delete params.subsubcategoryname;
            delete params.tag;
            delete params.advancedSearch;
            delete params.category_id;
            delete params.defaultLocationDistance;
            delete params.defaultViewType;
            delete params.detactLocation;
            delete params.location;
            delete params.locationDetection;
            delete params.orderby;
            delete params.page;
            delete params.profile_type;
            delete params.projectOption;
            delete params.projectType;
            delete params.search;
            delete params.selectProjects;
            delete params.showAllCategories;
            delete params.subcategory_id;
            delete params.subsubcategory_id;
            delete params.tag_id;
            delete params.whatWhereWithinmile;

            // reset project_locations params
            delete params.whatWhereWithinmile;
            delete params.advancedSearch;
            delete params.showAllCategories;
            delete params.locationDetection;
            delete params.orderby;
            delete params.category_id;
            delete params.subcategory_id;
            delete params.subsubcategory_id;
            delete params.profile_type;
            delete params.projectType;
            delete params.selectProjects;
            delete params.viewType;
            delete params.defaultViewType;
            delete params.viewFormat;
            delete params.orderby;
            delete params.detactLocation;
            delete params.page;
            delete params.defaultLocationDistance;
            delete params.latitude;
            delete params.longitude;
            delete params.search;
            delete params.category_id;
            delete params.subcategory_id;
            delete params.location;
            delete params.tag_id;

        }else if(tabLink === 'browse_projects'){
            // reset project_galleries params
            delete params.initiative_galleries;

            // reset project_locations params
            delete params.whatWhereWithinmile;
            delete params.advancedSearch;
            delete params.showAllCategories;
            delete params.locationDetection;
            delete params.orderby;
            delete params.category_id;
            delete params.subcategory_id;
            delete params.subsubcategory_id;
            delete params.profile_type;
            delete params.projectType;
            delete params.selectProjects;
            delete params.viewType;
            delete params.defaultViewType;
            delete params.viewFormat;
            delete params.orderby;
            delete params.detactLocation;
            delete params.page;
            delete params.defaultLocationDistance;
            delete params.latitude;
            delete params.longitude;
            delete params.search;
            delete params.category_id;
            delete params.subcategory_id;
            delete params.location;
            delete params.tag_id;

        }else if(tabLink === 'project_locations'){
            if(params) {
                // reset project_galleries params
                delete params.initiative_galleries;

                // reset browse_projects params
                delete params.view_view;
                delete params.users;
                delete params.Latitude;
                delete params.Longitude;
                delete params.categoryname;
                delete params.city;
                delete params.locationmiles;
                delete params.project_city;
                delete params.project_country;
                delete params.project_state;
                delete params.project_street;
                delete params.rewrite;
                delete params.separator1;
                delete params.separator2;
                delete params.subcategoryname;
                delete params.subsubcategoryname;
                delete params.tag;
                delete params.advancedSearch;
                delete params.category_id;
                delete params.defaultLocationDistance;
                delete params.defaultViewType;
                delete params.detactLocation;
                delete params.location;
                delete params.locationDetection;
                delete params.orderby;
                delete params.page;
                delete params.profile_type;
                delete params.projectOption;
                delete params.projectType;
                delete params.search;
                delete params.selectProjects;
                delete params.showAllCategories;
                delete params.subcategory_id;
                delete params.subsubcategory_id;
                delete params.tag_id;
                delete params.whatWhereWithinmile;
            }

        }

        return params;
    }

    // menu select function
    function selected_ui(tabLink){
        let count =  "<?php  $item = Engine_Api::_()->getItem('sitepage_initiative', $this->initiative_id); $count = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectsCountByPageIdAndInitiativesIds($item['page_id'],$item['initiative_id']); echo $count; ?>";
        if(tabLink == 'project_galleries') {

            $(tabLink).addClass('active');
            $('browse_projects').removeClass('active');
            $('project_locations').removeClass('active');
        }
        else if(tabLink == 'browse_projects') {

            $(tabLink).addClass('active');
            $('browse_projects').removeClass('active');
            $('project_locations').removeClass('active');
        }
        else if(tabLink == 'project_locations') {

            $(tabLink).addClass('active');
            $('browse_projects').removeClass('active');
            if($('project_galleries')) {
                $('project_galleries').removeClass('active');
            }
        }
        if(count <= 0) {
            document.getElementById('project_locations').style.display = "none";
            document.getElementById('browse_projects').style.width = "101%";
        }
        if(count > 0) {


            // remove active for other classes
            if($('project_galleries')){
                $('project_galleries').removeClass('active');
            }
            $('browse_projects').removeClass('active');
            $('project_locations').removeClass('active');

            // Add class
            $(tabLink).addClass('active');

            $('landing_page_projects').innerHTML = '<div class="clr"></div><div class="seaocore_content_loader"></div>';

            var params = {
                requestParams:<?php echo json_encode($this->params) ?>
        };
            if(count > 0) {
                params.requestParams = resetParamValues(tabLink, params.requestParams);
            }
            var request = new Request.HTML({
                url: en4.core.baseUrl + 'sitepage/initiatives/landing-page',
                data: $merge(params.requestParams, {
                    format: 'html',
                    subject: en4.core.subject.guid,
                    page_id: <?php echo $this->page_id?>,
            initiative_id: <?php echo $this->initiative_id?>,
            tab_link: tabLink
        }),
            evalScripts: true,
                onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $('hidden_ajax_data').innerHTML = responseHTML;
                if($('hidden_ajax_data').getElement('#landing_page_projects')) {
                    $('landing_page_projects').innerHTML = $('hidden_ajax_data').getElement('#landing_page_projects').innerHTML;
                }


                $('hidden_ajax_data').innerHTML = '';
                fundingProgressiveBarAnimation();
                Smoothbox.bind($('landing_page_projects'));
                en4.core.runonce.trigger();
                if(tabLink == 'project_galleries'){
                    loadProjectGalleriesUI();
                    $(tabLink).addClass('active');
                    $('browse_projects').removeClass('active');
                    $('project_locations').removeClass('active');
                }
                if(tabLink =='project_locations'){
                    loadProjectLocationUI();
                    $(tabLink).addClass('active');
                    $('browse_projects').removeClass('active');
                    $('project_galleries').removeClass('active');
                }
                if(tabLink =='browse_projects'){
                    loadProjectBrowseUI();
                    $(tabLink).addClass('active');
                    $('project_locations').removeClass('active');
                    $('project_galleries').removeClass('active');
                }


            }
        });

            request.send();
        }


    }

</script>
<style>
    div#projectViewFormat {
        display: flex;
        justify-content: center;
        font-weight: 600;
    }
    .sitecrowdfunding_projects_grid_view, .sitecrowdfunding_projects_list_view {
        width: 105% !important;
        position: relative;
        left: -30px;
    }
    .sitecrowdfunding_projects_galleries {
        width: 100% !important;
        position: relative;
        left: 0px;
    }
    .sitecrowdfunding_desc {
        display: block;
        font-size: 13px !important;
        margin: 5px 0;
        display: -webkit-box;
        -webkit-line-clamp: 8;
        -webkit-box-orient: vertical;
        /* height: 200px; */
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .sitecoretheme_counter_wrapper .stats_info:before{
        background: unset !important;
    }
    .sitecrowdfunding_thumb_wrapper.sitecrowdfunding_thumb_viewer {
        width: 100% !important;
    }
    .initiative_menu {
        border-bottom: 1px solid #f2f0f0;
        text-align: center;
    }
    .initiative_menu_nav > li > a{
        font-size: 18px !important;
    }
    #project_galleries_inner{
        display: flex !important;
        justify-content: center !important;
    }
    #projects_section_1,#projects_section_2{
        width:33%;
    }
    #projects_section_test2{
        position: relative !important;
        left: 0% !important;
        display: flex;
        flex-direction: row;
        justify-content: center;
        flex-wrap: wrap;
    }
    #projects_section_test2 li{
        width:47%;
    }
    .seaocore_searchform_criteria {
        display: flex;
        justify-content: center;
        margin-bottom: 7px !important;
    }
    .browsesitecrowdfundings_horizonatl_criteria ul > li {
        margin-right: 15px;
    }
    div#initiative_galleries-wrapper {
        margin-right: 15px;
    }
    .project_titles{
        font-weight: 500;
    }

    @media (min-width:851px) and (max-width: 1199px)
    {
        #sitecrowdfunding_thumb_viewer1,
        #sitecrowdfunding_thumb_viewer2 {
            height: 510px !important;
        }
        .sitecrowdfunding_desc {
            -webkit-line-clamp: 5 !important;
        }
        ul#projects_section_test1 {
            display: flex;
            justify-content: center;
        }
        #sitecrowdfunding_thumb_viewer4{
            width: 300px !important;
        }
        #sitecrowdfunding_thumb_viewer3{
            width: 250px !important;
        }
        ul#projects_section_test3 .effect2:after {
            right: 45px !important;
        }
        #sitecrowdfunding_thumb_viewer1,
        #sitecrowdfunding_thumb_viewer2 {
            height: 510px !important;
        }
        /*#projects_section_test2 li {*/
        /*    width: 47% !important;*/
        /*}*/
    }
    @media (min-width:1280px) and (max-width: 1399px)
    {

        #projects_section_test2{
            display: flex !important;
            flex-wrap: wrap !important;
            flex-direction: unset !important;
            align-items: unset !important;
        }
        /*ul#projects_section_test2 #sitecrowdfunding_thumb_viewer2{*/
        /*    width: 245px !important;*/
        /*}*/
        /*#projects_section_test2 li {*/
        /*    width:260px !important;*/
        /*}
        #projects_section_test2 .effect2:after{
            left: 190px !important;
        }*/
        #projects_section_test2 .effect2:before{
            left: 50px !important;
        }
        #sitecrowdfunding_thumb_viewer1,
        #sitecrowdfunding_thumb_viewer2 {
            height: 510px !important;
        }
    }
    @media (min-width:768px) and (max-width: 850px)
    {
        #sitecrowdfunding_thumb_viewer3{
            width: 220px !important;
        }
        #sitecrowdfunding_thumb_viewer1,
        #sitecrowdfunding_thumb_viewer2 {
            height: 510px !important;
        }
        #sitecrowdfunding_thumb_viewer4{
            width: 300px !important;
        }
        ul#projects_section_test3 .effect2:after {
            right: 75px !important;
        }
        .sitecrowdfunding_desc {
            -webkit-line-clamp: 5 !important;
        }
    }
    @media (max-width: 767px) {

        .sitecrowdfunding_projects_grid_view, .sitecrowdfunding_projects_list_view {
            left: 0px !important;
        }
    }
    a.seaocore_follow_button.button {
        padding: 4px 5px !important;
        height: 28px !important;
        font-weight: unset !important;
    }
    ul.tag-autosuggest.seaocore-autosuggest {
        z-index: 99;
        opacity: 1;
        width: 162px;
        overflow-y: hidden;
        overflow: hidden;
        background-color: rgb(255, 255, 255);
        color: rgb(153, 153, 153);
        border: 1px solid rgb(206, 206, 206);
        padding: 5px;
        font-size: 10pt;
        margin-top: 6px;
    }
    ul.tag-autosuggest > li.autocompleter-choices .autocompleter-choice {
        line-height: 16px;
        font-size: 10pt !important;
        color: #999;
        padding: 5px;
    }
    .browsesitecrowdfundings_criteria ul.seaocore-autosuggest {
        width: 280px !important;
        padding: 0px !important;
    }
    ul.tag-autosuggest > li.autocompleter-choices .autocompleter-choice:hover{
        background: #1a89d7;
        color: #ffff !important;
        border: 1px solid black !important;
    }
    ul.tag-autosuggest > li.autocompleter-choices {
        font-size: .8em;
        padding: 0px !important;
        white-space: pre-wrap;
    }
    ul.tag-autosuggest > li span.autocompleter-queried {
        font-weight: unset !important;
    }
    li.autocompleter-choices .autocompleter-queried {
        font-weight: unset !importants;
    }
    .browsesitecrowdfundings_horizonatl_criteria ul > li:last-child {
        margin-top: unset !important;
    }
    li#stopevent {
        font-weight: 500;
    }
    .edit_btn,.create_btn {
        background-color: #f9f9f9 !important;
        border: 1px solid #0b0b0b !important;
        color: black !important;
    }
    .edit_btn > .seaocore_icon_edit,
    .create_btn > .seaocore_icon_edit{
        color: black !important;
    }
    .edit_btn.button:hover,
    .create_btn.button:hover{
        background-color: unset !important;
    }
    .edit_btn > a.button {
        background-color: #f9f9f9 !important;
    }
    .sp_coverinfo_buttons {
        float: right;
        margin-left: 10px;
        display: flex;
        align-items: center;
    }
</style>