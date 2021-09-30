<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl .'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js'); ?>
<h3>
    View More Project Info
</h3>
<ul class="custom-nav-list">
    <li data-id="layout_activity_feed">
        What's New
    </li>
    <!--<li data-id="layout_sitecrowdfunding_project_overview">
        Overview
    </li>-->
    <?php // if($this->goalsCount > 0): ?>
        <li data-id="layout_sitecrowdfunding_development_goals">
            Development Goals
        </li>
    <?php // endif; ?>
    <li data-id="layout_sitecrowdfunding_project_location">
        Project Location
    </li>
   <!--
    <li data-id="layout_sitecrowdfunding_specification_project">
        Contextual Info
    </li>
    -->
    <?php if($this->project->isFundingApproved()):?>
        <li class="layout_sitecrowdfunding_project_backers_button" data-id="layout_sitecrowdfunding_project_backers">
            Project Funders
        </li>
    <?php endif; ?>
    <?php if($this->milestonesCount >0 ):?>
        <li data-id="layout_sitecrowdfunding_project_milestone">
            Milestones
        </li>
    <?php endif; ?>

    <?php /*if( $this->externalorganizationsCount >0 || $this->internalorganizationsCount >0  ):?>
        <li data-id="layout_sitecrowdfunding_project_organizations">
            Organizations
        </li>
    <?php endif;*/ ?>

    <li data-id="layout_sitecrowdfunding_project_peoples">
        Key People Involved
    </li>

    <?php if( $this->photosCount >0  || $this->videoCount >0  ):?>
        <li data-id="layout_sitecrowdfunding_project_photos">
            More Photo/Video
        </li>
    <?php endif; ?>

    <!-- <li data-id="layout_sitevideo_contenttype_videos">
    </li> -->


    <?php if( $this->address || $this->email || $this->phone  ):?>
        <li data-id="layout_sitecrowdfunding_project_contact_details">
            Contact
        </li>
    <?php endif; ?>

    <?php if(count($this->metric_id_array) > 0):?>
        <li data-id="layout_sitecrowdfunding_metrics">
            Metrics
        </li>
    <?php endif; ?>

   <!--
    <li data-id="layout_sitecrowdfunding_project_link">
        Get Link to Project
    </li>
    -->
    <?php if($this->additional_section && $this->additional_section[0]): ?>

        <li data-id="layout_sitecrowdfunding_additional">
            <?php echo $this->additional_section[0]['title']; ?>
        </li>
    <?php endif; ?>


    <?php /*if( $this->outcomesCount >0 || $this->outputCount >0  ):?>
        <li data-id="layout_sitecrowdfunding_project_outcome_output">
            Outputs
        </li>
    <?php endif;*/ ?>

</ul>
<script>
    var $j = jQuery.noConflict();
    $j(document).ready(function() {
        // highlight the tabs by default
        var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
        if (isMobile) {
            // You are using Mobile
            $j('.layout_main > .layout_middle').children(':not(.layout_activity_feed,.layout_sitecrowdfunding_project_initiativeanswers,.layout_sitecrowdfunding_project_backstory,.layout_sitecrowdfunding_project_funding_chart,.layout_sitecrowdfunding_project_location)').hide();
            $j(".layout_activity_feed").insertAfter(".layout_sitecrowdfunding_project_location");
            $j('.custom-nav-list li:first-child').addClass('active');
        } else {
            // You are using Desktop
            $j('.layout_main > .layout_middle').children(':not(.layout_activity_feed,.layout_sitecrowdfunding_project_initiativeanswers,.layout_sitecrowdfunding_project_backstory,.layout_sitecrowdfunding_project_funding_chart)').hide();
            $j('.custom-nav-list li:first-child').addClass('active');
        }
        $j('.custom-nav-list').on('click', 'li', function() {
            $j('.custom-nav-list li.active').removeClass('active');
            $j(this).addClass('active');
            var className = $j(this).data("id");
            if(className ==='layout_activity_feed'){
                $j('.layout_main > .layout_middle').children('.layout_activity_feed').show();
                $j('.layout_main > .layout_middle').children('.layout_sitecrowdfunding_project_backstory').show();
                $j('.layout_main > .layout_middle').children('.layout_sitecrowdfunding_project_initiativeanswers').show();
                $j('.layout_main > .layout_middle').children('.layout_sitecrowdfunding_project_funding_chart').show();
                $j('.layout_main > .layout_middle').children(':not(.layout_activity_feed,.layout_sitecrowdfunding_project_initiativeanswers,.layout_sitecrowdfunding_project_backstory,.layout_sitecrowdfunding_project_funding_chart)').hide();
            } else {
                $j('.layout_main > .layout_middle').children('.layout_activity_feed').hide();
                $j('.layout_main > .layout_middle').children('.layout_sitecrowdfunding_project_backstory').hide();
                $j('.layout_main > .layout_middle').children('.layout_sitecrowdfunding_project_initiativeanswers').hide();
                $j('.layout_main > .layout_middle').children('.layout_sitecrowdfunding_project_funding_chart').hide();
                $j('.layout_main > .layout_middle').children(':not(.layout_activity_feed,.layout_sitecrowdfunding_project_initiativeanswers,.layout_sitecrowdfunding_project_backstory,.layout_sitecrowdfunding_project_funding_chart)').show();
            };

            if (className === 'layout_sitecrowdfunding_project_link') {
                openLink();
            }else{
                $j('html, body').animate({
                    scrollTop: $j(`.${className}`).offset().top - 70
                }, 1000);
                // Zoom in the map
                map.fitBounds(bounds);
            }
        });

    });

    function openLink(){
        var url = "/projects/get-link/subject/sitecrowdfunding_project_<?php echo $this->project->project_id ?>";
        Smoothbox.open(url);
    }
</script>
<style type="text/css">
    @media only screen and (max-width: 767px){
        .layout_left{
            display: block !important;
            width: 100% !important;
            padding: 0;
        }
        .right {
            position: unset !important;
            top:unset !important;
            right: unset !important;
        }
        .middle{
            position: relative;
            left:unset !important;
            width: unset !important;
        }
    }

    .layout_activity_feed{
        min-height: 0 !important;
    }
    .custom-nav-list > li{
        border-bottom: 1px solid #eee;
        font-size: 16px;
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
        color: #44AEC1;
    }
    .custom-nav-list > .active:hover {
        color: #eee !important;
    }
    .custom-nav-list{
        margin-top: 10px;
    }

    .custom_h3{
        padding-top: 5px;
        padding-left: 0px;
        padding-bottom: 5px;
        padding-right: 0px;
        border: none;
    }

    .generic_layout_container > h3 {
        position: relative;
        text-align: center;
        font-size: 18px;
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