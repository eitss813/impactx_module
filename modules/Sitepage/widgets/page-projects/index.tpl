<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js'); ?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css');
?>

<?php

    $window_width = $_COOKIE["screen_width"];
    $window_width = (int)$window_width;

    // if screen can be seen for 1 data
    if($window_width <= 370){
        $items_per_page = 1;
    }elseif($window_width > 371 && $window_width <= 740){
        $items_per_page = 2;
    }elseif($window_width > 741 && $window_width <= 857){
        $items_per_page = 3;
    }else{
        $items_per_page = 4;
    }
?>

<?php
$currentLink = 'all';
if(isset($this->params['link']) && !empty($this->params['link'])){
    $currentLink = $this->params['link'];
}
?>
<div id="scroll_link_project"> </div>
<div class="layout_core_container_tabs">
    <div class="sitecrowdfunding_browse_lists_view_options txt_right" id='projectViewFormat'>

        <?php if($this->isPartnersPresentYN == true):?>
       <!-- <?php if(count($this->allPartnerPages) > 0):?> -->
            <div class="sitepage_page_top_links b_medium">
                <div class="sitepage_page_projects_top_filter_links txt_center sitepage_page_projects_top_filter_links">

                    <a href="javascript:void(0);" id='all' onclick="filter_projects_rsvp('all')">
                        <?php echo $this->translate('All'); ?> (<?php echo $this->allTabCount; ?>)
                    </a>

                    <?php foreach($this->allPartnerPages as $allPartnerPage): ?>
                        <a href="javascript:void(0);" id='<?php echo $allPartnerPage->page_id ?>'
                            onclick="filter_projects_rsvp('<?php echo $allPartnerPage->page_id ?>')">
                            <?php echo $this->translate($allPartnerPage->title); ?> (<?php echo $allPartnerPage->projects_count; ?>)
                        </a>
                    <?php endforeach;?>

                    <?php foreach($this->pages_ids_noproject as $pgid): ?>
                    <?php $sitepage = Engine_Api::_()->getItem('sitepage_page',$pgid); ?>
                    <a href="javascript:void(0);" id='<?php echo $pgid; ?>'
                       onclick="filter_projects_rsvp('<?php echo $pgid; ?>')">
                        <?php echo $this->translate($sitepage->title); ?> (0)
                    </a>
                    <?php endforeach;?>
                </div>
            </div>
        <!--   <?php endif; ?> -->
        <?php endif; ?>

        <br/><br/>

        <div id='sitepage_page_projects_content'>

                <?php static $scrollCount=0; ?>

                <!--Projects with Initiative -->
                <?php foreach($this->initiatives as $initiative): ?>
                    <div id="initiative_container_<?php echo $initiative['initiative_id'];?>">
                    <?php $item = Engine_Api::_()->getItem('sitepage_initiative', $initiative['initiative_id']);?>
                    <?php $initiativesURL = $this->url(array('action' => 'landing-page','page_id' => $initiative['page_id'], 'initiative_id' => $initiative['initiative_id']), "sitepage_initiatives");?>
                    <?php $projects = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectPaginatorByPageIdAndInitiativesId($initiative['page_id'],$initiative['initiative_id'],null); ?>

                    <?php
                        $projects->setItemCountPerPage($items_per_page);
                        if($this->is_paginated == false){
                            $projects->setCurrentPageNumber(1);
                            $page_no = 1;
                        }else{
                            if($this->paginated_initiative_id == $initiative['initiative_id'] ){
                                $projects->setCurrentPageNumber($this->paginated_page_no);
                                $page_no = $this->paginated_page_no;
                            }else{
                                $projects->setCurrentPageNumber(1);
                                $page_no = 1;
                            }
                        }
                    ?>

                    <?php if($projects->getTotalItemCount() > 0 ): ?>
                        <a class="projects_section_name_header" href="<?php echo $initiativesURL?>" title="<?php echo $initiative['title']?>">
                            <h3 class="projects_section_name">
                                <?php echo $initiative['title']." - Projects (".$projects->getTotalItemCount().")"; ?>
                            </h3>
                        </a>

                        <div id="wrapper" style="position: relative;">
                            <?php $scrollCount= $scrollCount+1; ?>

                            <?php /*
                            <!-- Prev Icon Page -->
                            <?php if($projects->getTotalItemCount() > $items_per_page && $page_no != 1 ):?>
                                <div id="prev_spinner_<?php echo $initiative['initiative_id'];?>" class="arrow-button prev-button">
                                    <i onclick="slidePrev('<?php echo $initiative["initiative_id"]; ?>','<?php echo $page_no; ?>')" class="fa fa-angle-left" aria-hidden="true"></i>
                                </div>
                            <?php endif; ?>

                            <!-- Prev Without Page -->
                            <?php if($page_no == 1 ):?>
                                <div class="arrow-button prev-button">
                                </div>
                            <?php endif; ?>
                            */ ?>

                            <div class="scrollbar projects_container" id="<?php echo 'test-'.$scrollCount; ?>" >
                                <div class="force-overflow">
                                    <ul style="display: flex;">
                                        <?php foreach ($projects as $project): ?>
                                            <li class="effect2_new">
                                                <?php $item = Engine_Api::_()->getItem('sitecrowdfunding_project', $project['project_id']); ?>
                                                <?php $i = 1;?>
                                                <div class="sitecrowdfunding_thumb_wrapper sitecrowdfunding_thumb_viewer" id='<?php echo "sitecrowdfunding_thumb_viewer1"; ?>' >
                                                    <div class="sitecrowdfunding_grid_thumb">
                                                        <?php $fsContent = ""; ?>
                                                        <?php
                                                            if ($item->photo_id) {
                                                                    echo $this->htmlLink($item->getHref(), $fsContent . $this->itemBackgroundPhoto($item, 'thumb.cover' , null, null, array('tag' => 'i')), array('class' => 'sitecrowdfunding_thumb'));
                                                            } else {
                                                                $url = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/nophoto_project_thumb_profile.png";
                                                                echo $this->htmlLink($item->getHref(), $fsContent . "<i style='background-image:url(" . $url . ")'></i>", array('class' => 'sitecrowdfunding_thumb'));
                                                            }
                                                        ?>
                                                        <div class='sitecrowdfunding_hover_info' style="display: flex;align-items: center;justify-content: center;">
                                                            <div class="txt_center">
                                                                <button onclick="window.location = '<?php echo $item->getHref() ?>'">
                                                                    <?php echo $this->translate('View'); ?>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="sitecrowdfunding_info_wrapper">
                                                        <div class="sitecrowdfunding_info">
                                                            <div class="sitecrowdfunding_bottom_info sitecrowdfunding_grid_bott_info">
                                                                <h3 class="project_title"><?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?> </h3>

                                                                <div class="sitecrowdfunding_grid_bottom_info">

                                                                    <div class="sitecrowdfunding_desc" title="<?php echo $this->string()->truncate($this->string()->stripTags($item->desire_desc), 250) ?>">
                                                                        <?php echo $this->string()->stripTags($item->desire_desc); ?>
                                                                    </div>

                                                                    <?php $pro_location = Engine_Api::_()->getDbtable('locations', 'sitecrowdfunding')->getLocation(array('id' => $item->project_id)); ?>

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
                                </div>
                            </div>

                            <?php /*
                            <!-- Next Icon Page -->
                            <?php if($projects->getTotalItemCount() > $items_per_page && $page_no != $projects->getPages()->pageCount ):?>
                                <div id="next_spinner_<?php echo $initiative['initiative_id'];?>" class="arrow-button next-button">
                                    <i onclick="slideNext('<?php echo $initiative["initiative_id"]; ?>','<?php echo $page_no; ?>')" class="fa fa-angle-right" aria-hidden="true"></i>
                                </div>
                            <?php endif; ?>

                            <!-- Next Without Page -->
                            <?php if($page_no == $projects->getPages()->pageCount ):?>
                                <div class="arrow-button next-button">
                                </div>
                            <?php endif; ?>
                            */?>

                            <?php if($projects->getTotalItemCount() > $items_per_page && $page_no != 1 ):?>
                                <div id="prev_spinner_<?php echo $initiative['initiative_id'];?>" class="arrow_buttons prev_arrow">
                                    <i onclick="slidePrev('<?php echo $initiative["initiative_id"]; ?>','<?php echo $page_no; ?>')" class="fa fa-chevron-left fa-2x prev-button" aria-hidden="true"></i>
                                </div>
                            <?php endif; ?>


                            <?php if($projects->getTotalItemCount() > $items_per_page && $page_no != $projects->getPages()->pageCount ):?>
                                <div id="next_spinner_<?php echo $initiative['initiative_id'];?>"  class="arrow_buttons next_arrow">
                                    <i onclick="slideNext('<?php echo $initiative["initiative_id"]; ?>','<?php echo $page_no; ?>')" class="fa fa-chevron-right fa-2x next-button" aria-hidden="true"></i>
                                </div>
                            <?php endif; ?>

                        </div>

                        <br><br>
                    <?php endif; ?>
                    </div>

                <?php endforeach;?>

                <br><br>

                <!--No Initiative projects -->
                <?php $noItiativeProjects = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getNonInitiativeProjectsPaginatorByPageIds($this->pages_ids); ?>

                <?php
                    $noItiativeProjects->setItemCountPerPage($items_per_page);
                    if($this->is_paginated == false){
                        $noItiativeProjects->setCurrentPageNumber(1);
                        $non_initiative_page_no = 1;
                    }else{
                        if($this->paginated_initiative_id == "others" ){
                            $noItiativeProjects->setCurrentPageNumber($this->paginated_page_no);
                            $non_initiative_page_no = $this->paginated_page_no;
                        }else{
                            $noItiativeProjects->setCurrentPageNumber(1);
                            $non_initiative_page_no = 1;
                        }
                    }
                ?>

                <div id="initiative_container_others">
                <?php if($noItiativeProjects->getTotalItemCount() > 0 ):?>

                    <div class="projects_section_name_header">
                        <h3 class="projects_section_name"><?php echo 'Other Projects ('.$noItiativeProjects->getTotalItemCount().')'; ?></h3>
                    </div>

                    <div id="wrapper" style="position: relative;">
                        <?php $scrollCount= $scrollCount+1; ?>

                        <?php /*
                        <!-- Prev Icon Page -->
                        <?php if($noItiativeProjects->getTotalItemCount() > $items_per_page && $non_initiative_page_no != 1 ):?>
                            <div id="prev_spinner_others" class="arrow-button" style="margin-right: 15px;display: flex;align-items: center;">
                                <i onclick="slidePrev('others','<?php echo $non_initiative_page_no; ?>')" class="fa fa-angle-left" aria-hidden="true"></i>
                            </div>
                        <?php endif; ?>

                        <!-- Prev Without Page -->
                        <?php if($non_initiative_page_no == 1 ):?>
                            <div class="arrow-button" style="display: flex;align-items: center; width: 55px !important;">
                            </div>
                        <?php endif; ?>
                        */ ?>

                        <div class="scrollbar" id="<?php echo 'test-'.$scrollCount; ?>" >
                            <div class="force-overflow">
                                <ul style="display: flex;">
                                    <?php foreach ($noItiativeProjects as $project): ?>
                                        <li class="effect2_new">
                                            <?php $item = Engine_Api::_()->getItem('sitecrowdfunding_project', $project['project_id']); ?>
                                            <?php $i = 1;?>
                                            <div class="sitecrowdfunding_thumb_wrapper sitecrowdfunding_thumb_viewer" id='<?php echo "sitecrowdfunding_thumb_viewer1"; ?>' >

                                                <div class="sitecrowdfunding_grid_thumb">
                                                    <?php $fsContent = ""; ?>
                                                    <?php
                                                     if ($item->photo_id) {
                                                        echo $this->htmlLink($item->getHref(), $fsContent . $this->itemBackgroundPhoto($item,'thumb.cover', null, null, array('tag' => 'i')), array('class' => 'sitecrowdfunding_thumb'));
                                                    } else {
                                                        $url = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/nophoto_project_thumb_profile.png";
                                                        echo $this->htmlLink($item->getHref(), $fsContent . "<i style='background-image:url(" . $url . ")'></i>", array('class' => 'sitecrowdfunding_thumb'));
                                                    }
                                                    ?>
                                                    <div class='sitecrowdfunding_hover_info' style="display: flex;align-items: center;justify-content: center;">
                                                        <div class="txt_center">
                                                            <button onclick="window.location = '<?php echo $item->getHref() ?>'">
                                                                <?php echo $this->translate('View'); ?>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="sitecrowdfunding_info_wrapper">
                                                    <div class="sitecrowdfunding_info">
                                                        <div class="sitecrowdfunding_bottom_info sitecrowdfunding_grid_bott_info">
                                                            <h3 class="project_title"> <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?> </h3>
                                                            <div class="sitecrowdfunding_grid_bottom_info">
                                                                <div class="sitecrowdfunding_desc" title="<?php echo $this->string()->truncate($this->string()->stripTags($item->desire_desc), 250) ?>">
                                                                    <?php echo $this->string()->stripTags($item->desire_desc); ?>
                                                                </div>
                                                                <?php $pro_location = Engine_Api::_()->getDbtable('locations', 'sitecrowdfunding')->getLocation(array('id' => $item->project_id)); ?>
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
                                                                        <?php echo $this->translate("%s", $fundedAmount); ?><br /><?php echo $this->translate("Backed"); ?>
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
                            </div>
                        </div>

                        <?php /*
                        <!-- Next Icon Page -->
                        <?php if($noItiativeProjects->getTotalItemCount() > $items_per_page && $non_initiative_page_no != $noItiativeProjects->getPages()->pageCount ):?>
                            <div id="next_spinner_others" class="arrow-button" style="margin-left: 15px;display: flex;align-items: center;">
                                <i onclick="slideNext('others','<?php echo $non_initiative_page_no; ?>')" class="fa fa-angle-right" aria-hidden="true"></i>
                            </div>
                        <?php endif; ?>

                        <!-- Next Without Page -->
                        <?php if($non_initiative_page_no == $noItiativeProjects->getPages()->pageCount ):?>
                            <div class="arrow-button" style="display: flex;align-items: center; width: 55px !important;">
                            </div>
                        <?php endif; ?>
                        */ ?>

                        <?php if($noItiativeProjects->getTotalItemCount() > $items_per_page && $non_initiative_page_no != 1 ):?>
                            <div id="prev_spinner_others" class="arrow_buttons prev_arrow">
                                <i onclick="slidePrev('others','<?php echo $non_initiative_page_no; ?>')" class="fa fa-chevron-left fa-2x prev-button" aria-hidden="true"></i>
                            </div>
                        <?php endif; ?>

                        <?php if($noItiativeProjects->getTotalItemCount() > $items_per_page && $non_initiative_page_no != $noItiativeProjects->getPages()->pageCount ):?>
                            <div id="next_spinner_others"  class="arrow_buttons next_arrow">
                                <i onclick="slideNext('others','<?php echo $non_initiative_page_no; ?>')" class="fa fa-chevron-right fa-2x next-button" aria-hidden="true"></i>
                            </div>
                        <?php endif; ?>


                    </div>

                <?php endif; ?>
                </div>
        </div>

    </div>
</div>

<div id="hidden_ajax_page_projects_data" style="display: none;"></div>

<script type="text/javascript">
    var $j = jQuery.noConflict();
    $j(document).ready(function() {
        var widthValue = window.innerWidth;
        createCookie("screen_width", widthValue, "10");
    });

    // active the option
    var currentLink = "<?php echo $currentLink; ?>";
    var allLinks = $$('div.sitapage_page_projects_top_filter_links > a');
    allLinks.removeClass('active');
    $(currentLink).addClass('active');

    function addProjectsBoldClass(reqType) {
        $$('div.sitepage_page_projects_top_filter_links > a').each(function (el) {
            el.removeClass('active');
        });
        $(reqType).addClass('active');
    }

    function filter_projects_rsvp(req_type) {
        addProjectsBoldClass(req_type);
        var url = null;
        switch (req_type) {
            case 'all':
                url = en4.core.baseUrl + 'widget/index/mod/sitepage/name/page-projects/link/all';
                break;
            default:
                url = en4.core.baseUrl + 'widget/index/mod/sitepage/name/page-projects/link/' + req_type;
                break;
        }
        $('sitepage_page_projects_content').innerHTML = '<div class="clr"></div><div class="seaocore_content_loader"></div>';

        var params = {
            requestParams: <?php echo json_encode($this->params) ?>
        }
        var request = new Request.HTML({
            url: url,
            data: $merge(params.requestParams, {
                format: 'html',
                subject: en4.core.subject.guid,
                is_ajax: 0,
                pagination: 0,
                page: 0,
                is_paginated: false,
                initiative_id: null,
                page_no: null
            }),
            evalScripts: true,
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $('hidden_ajax_page_projects_data').innerHTML = responseHTML;
                $('sitepage_page_projects_content').innerHTML = ($('hidden_ajax_page_projects_data').getElement('#sitepage_page_projects_content')) ? $('hidden_ajax_page_projects_data').getElement('#sitepage_page_projects_content').innerHTML:' <div id="sitepage_page_projects_content"><br><br><div class="tip"><span>No Projects Found. </span>  </div></div>';
                $('hidden_ajax_page_projects_data').innerHTML = '';
                fundingProgressiveBarAnimation();
                Smoothbox.bind($('sitepage_page_projects_content'));
                en4.core.runonce.trigger();
            }
        });
        request.send();
    }

    // slider
    function slidePrev(initiative_id,page_no) {
        var container_name = 'initiative_container_'+initiative_id;
        var spinner_name = 'prev_spinner_'+initiative_id;
        var page_no = parseInt(page_no) - 1;
        // var url = en4.core.baseUrl + 'widget/index/mod/sitepage/name/page-projects/';
        var url = en4.core.baseUrl + 'projects/backer/success';
        var params = {
            requestParams: <?php echo json_encode($this->params) ?>
        }
        $(spinner_name).innerHTML = '<div class="seaocore_content_loader"></div>';
        var request = new Request.HTML({
            url: url,
            data: $merge(params.requestParams, {
                format: 'html',
                subject: en4.core.subject.guid,
                is_paginated: true,
                initiative_id: initiative_id,
                page_no: page_no
            }),
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

    function slideNext(initiative_id,page_no) {
        var container_name = 'initiative_container_'+initiative_id;
        var spinner_name = 'next_spinner_'+initiative_id;
        var page_no = parseInt(page_no) + 1;
        // var url = en4.core.baseUrl + 'widget/index/mod/sitepage/name/page-projects/';
        var url = en4.core.baseUrl + 'projects/backer/success';
        var params = {
            requestParams: <?php echo json_encode($this->params) ?>
        }
        $(spinner_name).innerHTML = '<div class="seaocore_content_loader"></div>';
        var request = new Request.HTML({
            url: url,
            data: $merge(params.requestParams, {
                format: 'html',
                subject: en4.core.subject.guid,
                is_paginated: true,
                initiative_id: initiative_id,
                page_no: page_no
            }),
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

    function createCookie(name, value, days) {
        var expires;
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toGMTString();
        }
        else {
            expires = "";
        }
        document.cookie = escape(name) + "=" + escape(value) + expires + "; path=/";
    }
</script>

<style>
    /* Arrow Buttons */
    .arrow_buttons{
        display: flex;
        justify-content: space-between;
        height: 30px;
    }

    .arrow_buttons i{
        background-color: rgba(28,29,33, .75);
        color: #EEEFF7;
        cursor: pointer;
        height: 26px;
        padding: 6px;
        transition: background-color .5s, color .5s;
    }

    .arrow_buttons i:hover{
        background-color: rgba(28,29,33, .75);
        color: #EEEFF7;
    }
    .prev_arrow {
        position: absolute;
        left: -15px;
        top: 32%;
    }
    .next_arrow {
        position: absolute;
        right: -15px;
        top: 32%;
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

    div#sitecrowdfunding_thumb_viewer1 {
        height: 518px !important;
        width: 274px;
        margin: 10px;
        border: 10px solid #fff;
        background: #f9f9f9;
    }

    ul.projects_manage.sitecrowdfunding_projects_galleries {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    li.effect2 {
        border: medium none;
        float: left;
        overflow: visible;
        clear: none;
        padding: 0 0 30px 0 !important;
    }

    .effect2:before, .effect2:after {
        bottom: 44px !important;
        width: 32% !important;
        top: 91% !important;
    }

    .projects_section_name_header > h3::before {
        left: 0 !important;
        margin: 0 auto !important;
        right: 0 !important;
        text-align: center !important;
        width: 85px !important;
        background: #44AEC1 !important;
        top: 100% !important;
        content: "" !important;
        display: block !important;
        min-height: 2px !important;
        position: absolute !important;
        border-bottom: unset !important;
    }

    .scrollbar {

        width: 100%;
        overflow: auto !important;
        overflow-x: scroll;
        overflow-y: hidden;

    }

    .force-overflow {
        min-height: 450px;
    }
    .project_title{
        font-weight: 500;
    }

    .sitecrowdfunding_funding_bar {
        position: absolute !important;
        bottom: 77px !important;
        left: 0px;
        width: 99% !important;
    }

    /*
    *  STYLE 5
    */
    .scrollbar::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
        background-color: #F5F5F5;
    }

    .scrollbar::-webkit-scrollbar {
        width: 10px;
        background-color: #F5F5F5;
    }

    .scrollbar::-webkit-scrollbar-thumb {
        background-color: #0ae;

        background-image: -webkit-gradient(linear, 0 0, 0 100%,
        color-stop(.5, rgba(255, 255, 255, .2)),
        color-stop(.5, transparent), to(transparent));
    }

    .projects_section_name {
        font-size: 18px;
        font-weight: 500;
        border-bottom: 0;
        padding-bottom: 10px;
        text-transform: capitalize;
        background: transparent;
        text-align: center;
        position: relative;
    }

    .projects_section_name > h3::before {
        left: 0 !important;
        margin: 0 auto !important;
        right: 0 !important;
        text-align: center !important;
        width: 85px !important;
        background: #44AEC1 !important;
        top: 100% !important;
        content: "" !important;
        display: block !important;
        min-height: 2px !important;
        position: absolute !important;
        border-bottom: unset !important;
    }

    i.fa.fa-angle-left, i.fa.fa-angle-right {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 35px;
        height: 35px;
        font-size: 20px;
        font-weight: bold;
        background-color: #44AEC1;
        color: #ffffff;
        border: 2px solid #44AEC1;
        cursor: pointer;
        outline: none;
        position: relative;
        overflow: hidden;
        -webkit-transition: all 500ms ease 0s;
        -moz-transition: all 500ms ease 0s;
        -o-transition: all 500ms ease 0s;
        transition: all 500ms ease 0s;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
        border-radius: 100%;
        -webkit-box-sizing: border-box;
        -mox-box-sizing: border-box;
        box-sizing: border-box;
    }

    /*hided project title*/
    .layout_sitepage_page_projects > h3 {
        display: none;
    }

    @media (max-width: 767px) {
        /*.arrow-button {
            display: none !important;
        }*/

        div#sitecrowdfunding_thumb_viewer1 {
            width: 255px !important;
        }

        .sitecrowdfunding_browse_lists_view_options {
            padding: unset !important;

        }
    }

    @media (min-width: 768px) and (max-width: 1024px) {
        .arrow-button {
            display: none !important;
        }
    }

    .sitepage_page_projects_top_filter_links .active {
        color: #44AEC1;
    }

    .sitepage_mypages_top_links a:last-child,
    .sitepage_page_top_links a:last-child {
        border-right: none;
    }
</style>