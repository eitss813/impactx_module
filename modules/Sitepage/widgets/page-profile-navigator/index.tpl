<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl .'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js'); ?>
<ul class="custom-nav-list">

    <li data-id="layout_activity_feed">
        What's New
    </li>

    <li data-id="layout_sitepage_overview_sitepage">
        Overview
    </li>

    <?php if($this->initiativesCount > 0): ?>
        <li data-id="layout_sitepage_page_profile_initiatives">
            Initiatives
        </li>
    <?php endif; ?>

    <li data-id="layout_sitepage_sitepage_map">
        Map
    </li>

    <?php if($this->projectsCount > 0): ?>
    <li data-id="layout_sitepage_page_projects">
        Projects
    </li>
    <?php endif; ?>

    <li data-id="layout_sitepage_page_peoples">
        People
    </li>

    <li data-id="layout_sitepage_info_sitepage">
        Information
    </li>

    <?php if($this->partnerOrganisationCount > 0): ?>
        <li data-id="layout_sitepage_page_profile_partners">
            <?php echo $this->translate('Partner Pages'); ?>
        </li>
    <?php endif; ?>

    <?php if($this->albumcount > 0): ?>
        <li data-id="layout_sitepage_photos_sitepage">
            Photos
        </li>
    <?php endif; ?>

    <?php if($this->videoCount > 0): ?>
        <li data-id="layout_sitepage_page_videos">
            Videos
        </li>
    <?php endif; ?>

</ul>

<script>
    var $j = jQuery.noConflict();
    $j(document).ready(function() {
        $j('.layout_main > .layout_middle').children(':not(.layout_activity_feed)').show();
        $j('.custom-nav-list li:nth-child(2)').addClass('active');
        $j('.custom-nav-list').on('click', 'li', function() {
            $j('.custom-nav-list li.active').removeClass('active');
            $j(this).addClass('active');
            var className = $j(this).data("id");
            if(className ==='layout_activity_feed'){
                $j('.layout_main > .layout_middle').children('.layout_activity_feed').show();
                $j('.layout_main > .layout_middle').children(':not(.layout_activity_feed)').hide();
                $j('.layout_main > .layout_middle').children('.layout_sitepage_page_cover_information_sitepage').show();
                $j('.layout_main > .layout_middle').children('.layout_sitepage_page_profile_navigator').show();
            }else{
                $j('.layout_main > .layout_middle').children('.layout_activity_feed').hide();
                $j('.layout_main > .layout_middle').children(':not(.layout_activity_feed)').show();
                $j('.layout_main > .layout_middle').children('.layout_sitepage_page_cover_information_sitepage').show();
                $j('.layout_main > .layout_middle').children('.layout_sitepage_page_profile_navigator').show();
            }
            $j('html, body').animate({
                scrollTop: $j(`.${className}`).offset().top - 70
            }, 1000);
            // Zoom in the map
            if(map){
                map.fitBounds(bounds);
            }
        });
    })
</script>
<style type="text/css">
    ul.custom-nav-list {
        display: flex;
        justify-content: center;
    }
    .custom-nav-list > li{
        border-bottom: none !important;
    }
    @media only screen and (max-width: 400px){
        #global_wrapper {
            width: 97% !important;
        }
        .generic_layout_container.layout_main {
            width: 102% !important;
        }
        .layout_sitepage_pages_slideshow .channelInfo {
            width: 267px !important;
        }
        ul#activity-feed {
            display: block !important;
        }
        .generic_layout_container.layout_sitepage_page_profile_navigator {
            display: none !important;
        }
        div#fail_msg {
            display: block !important;
        }
    }
    @media only screen  and (max-width: 767px){
        div#fail_msg {
            display: block !important;
        }
        ul#activity-feed {
            display: block !important;
        }
        .generic_layout_container.layout_sitepage_page_profile_navigator {
            display: none !important;
        }
        * {
            border-color: #f2f0f0 !important;
        }
        ul.custom-nav-list {
           flex-direction: column !important;
        }
        .layout_left{
            display: block !important;
            width: 100% !important;
            padding: 0;
        }
        .sp_coverinfo_status {
            position: relative !important;
            top: 83px !important;
            width: 100%;
        }
        sp_coverinfo_status{
             padding: 10px 30px 100px !important;
        }
        .sitepage_cover_information {
            padding: 10px 0px 120px !important;
        }
        .sp_coverinfo_status{
            position: relative !important;
            top: 55px !important;
        }
        .sp_coverinfo_buttons {
            left: 12px !important;
            position: relative;
        }
        .sitepage_cover_photo img {
            top: 0px !important;
        }
    }

    .layout_activity_feed{
        min-height: 0 !important;
    }
    .custom-nav-list > li{
        border-bottom: 1px solid #eee;
        font-size: 18px;
        padding: 10px;
        border-radius: 3px;
        font-family: 'fontawesome', Roboto, sans-serif;
    }
    .custom-nav-list > .active{
        background: #44AEC1;
        color: #fff;
    }
    .custom-nav-list li:hover{
        cursor: pointer;
    }
    .generic_layout_container > h3 {
         position: relative;
         text-align: center;
         font-size: 18px;
        font-weight: 500;
        border-bottom: unset !important;
    }
    .generic_layout_container > h3::before{
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
</style>