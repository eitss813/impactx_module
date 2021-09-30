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

<div>
    <h2 class="scalingx_block_title">Scaling With ImpactX: The Community Independence Initiative Story </h2>

    <div class="impact_customers">

        <div class="impact_customer" style="border: 1px solid #ccc;background-color: #cccccc30;border-radius: 5px;padding: 20px 5px;">
            <div class="impact_customer_info_details" style="float: left;width: 17%;">
                <div class="impact_customer_image" style="text-align: center;">
                    <?php
                        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
                        $baseUrl = $view->baseUrl();
                        $imgpath = $baseUrl . "/application/modules/Sitepage/externals/images/Mauricio.png";
                    ?>
                    <img src="<?php echo $imgpath; ?>" style="width: 128px;text-align: center;"/>
                </div>
                <div class="impact_customer_desc intro_desc" id="intro_desc" style="font-weight: 500; font-size: 18px !important;text-align: center;">
                    Mauricio Miller, Founder & Director, Community Independence Initiative
                </div>
            </div>
            <div class="impact_customer_logo" style="float: left;width: 15%;">
                <div class="wow" style="height: 200px; visibility: visible;">
                    <div class="sr_card_view_image">
                        <a target='_blank' href="/organization/cii"  title="Community Independence Initiative">
                            <span style="background-size: contain; background-image: url(<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitecoretheme/externals/images/custom/intro_log_3.jpg' ?>); "></span>
                        </a>
                        <span class="sr_card_view_image_hover">
                            <i title="Featured" class="sr_icon seaocore_icon_featured"></i>
                        </span>
                    </div>
                    <div class="sr_card_view_info">
                        <div class="sr_title">
                            <!-- https://www.CIIAlternative.org -->
                            <a  target='_blank' href="/organization/cii"  title="Community Independence Initiative">Community Independence Initiative</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="impact_customer_content">
                <div class="widgets_title_description intro_desc">
                    “In 2020 CII made the decision to move from Excel spreadsheets to online tools for tracking our initiatives.  We also wanted our donors to be able to review and fund initiatives directly online. Working with Ashoka, we choose ImpactX to be our tech backbone.  The suite of cloud-based tools is enabling us stay on top of our many initiatives and connected with stakeholders and donors. Now we have the infrastructure to expand operations to 7 countries. ImpactX has been a great fit for us.”
                </div>
            </div>
        </div>

        <br>
        <br>

        <div class="impact_customer" style="border: 1px solid #ccc;background-color: #cccccc30;border-radius: 5px;padding: 20px 5px;">
            <div class="impact_customer_info_details" style="float: left;width: 17%;">
                <div class="impact_customer_image" style="text-align: center;">
                    <?php
                        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
                        $baseUrl = $view->baseUrl();
                        $imgpath = $baseUrl . "/application/modules/Sitepage/externals/images/Bob.png";
                    ?>
                    <img src="<?php echo $imgpath; ?>"/>
                </div>
                <div class="impact_customer_desc intro_desc" id="intro_desc" style="font-weight: 500; font-size: 18px !important;text-align: center;">
                    Bob Spoer, Chief Entrepreneur for People, Ashoka
                </div>
            </div>
            <div class="impact_customer_logo" style="float: left;width: 15%;">
                <div class="wow" style="height: 200px; visibility: visible;">
                    <div class="sr_card_view_image">
                        <a href="https://www.ashoka.org/en/about-ashoka" title="Ashoka">
                            <span style="background-size: contain; background-image: url(<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitecoretheme/externals/images/custom/intro_log_1.jpg' ?>); "></span>
                        </a>
                        <span class="sr_card_view_image_hover">
                            <i title="Featured" class="sr_icon seaocore_icon_featured"></i>
                        </span>
                    </div>
                    <div class="sr_card_view_info">
                        <div class="sr_title">
                            <a href="https://www.ashoka.org/en/about-ashoka" title="Ashoka">Ashoka</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="impact_customer_content">
                <div class="widgets_title_description intro_desc">
                    “CII is creating important systems change through its Mutuality initiatives, which depend upon peer-driven change.  CII was able to build a branded Mutuality Platform within the ImpactX Platform, which encompasses its ecosystem of partners and projects.  ImpactX is allowing the Mutuality model to scale.”
                </div>
            </div>
        </div>

    </div>



    <br><br>
    <h3 class="sitecore_title">Our Data Commitment</h3>
    <div class="widgets_title_description services_desc">
        Data placed on ImpactX will be protected and will not be given or sold to third parties without the contributor’s permission.  ImpactX reserves the right to anonymize and aggregate data strictly for purposes of providing impact insights to the community.  Our data policies are reviewed by a disinterested panel of impact leaders and available upon request.
    </div>

    <!--
    <div class="widgets_title_border">
        <span></span>
        <i></i>
        <span></span>
    </div>

    <div class="widgets_title_description intro_desc">
        ImpactX proudly hosts The Mutuality Platform
        in partnership with the Community Independence
        Initiative and Ashoka.<br/><br/> The Mutuality Platform is a
        customized portion of ImpactX that
        supports the journey of impact projects in sync with
        the principles of Mutuality and are
        initiated by very poor people and groups,
        and micro-businesses.
    </div>
    -->
    <br>
    <!--<div class="spec_btnsblock">
        <a href="organizations/initiatives/landing-page/page_id/7/initiative_id/2"   target="_blank">Browse Mutuality</a>
    </div>-->
</div>


<style>
    .scalingx_block_title {
        border: 1px solid #ccc;
        background-color: #cccccc30;
        border-radius: 5px;
        padding: 12px;
        text-transform: capitalize;
        font-size: 28px;
        margin-bottom: 20px;
        text-align: center;
        position: relative;
        line-height: normal;
        margin-top: 5px;
    }
    .sr_card_view {
        overflow: hidden;
        text-align: center;
        margin-bottom: 15px;
        clear: both;
    }

    @media only screen and (min-width: 1200px) {
        .sr_card_view ul > li {
            width: 350px;
        }
    }
    @media only screen and (max-width: 1199px){
        .sr_card_view ul > li {
            width: 31.5%;
        }
    }
    @media only screen and (max-width: 767px) {
        .sr_card_view ul > li {
            width: 47.9%;
        }
        #intro_desc {
            line-height: 30px !important;
        }
    }
    @media only screen and (max-width: 600px) {
        .sr_card_view ul > li {
            width: 98%;
        }
    }

    .sr_card_view ul > li {
        display: inline-block;
        box-sizing: border-box;
        position: relative;
        vertical-align: top;
        overflow: hidden;
        margin: 5px 5px 8px 5px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
    }

    .sr_card_view ul > li {
        border-bottom: 2px solid transparent;
        background-color: #fff;
        border: 0 solid transparent;
        border-radius: 0;
    }

    .slideInUp {
        -webkit-animation-name: slideInUp;
        animation-name: slideInUp;
    }

    .sr_card_view_image {
        overflow: hidden;
        border-bottom: 1px solid rgba(0, 0, 0, .04);
    }

    .sr_card_view_info {
        padding: 15px;
        position: relative;
    }

    .sr_card_view .listing_readmore {
        position: absolute;
        bottom: 15px;
        width: 100%;
        text-align: center;
    }

    .sr_card_view_image_hover {
        position: absolute;
        right: 10px;
        top: -10px;
        opacity: 0;
        transition: all 0.35s ease-in-out 0s;
    }

    .sr_card_view ul > li a > span {
        background-size: cover;
        background-position: center 50%;
        background-repeat: no-repeat;
        display: block;
        height: 210px;
        margin: 0 auto;
    }

    .sr_card_view_image_hover i, .sr_card_view_image_hover a {
        margin: 1px;
        border-radius: 2px;
        vertical-align: middle;
        border: 1px solid transparent;
        transition: background-color 0.2s ease-in-out 0s;
    }

    .sr_card_view .sr_title {
        font-size: 16px;
        font-weight: bold;
        text-align: center;
    }

    .sr_card_view_info > div {
        margin-bottom: 6px;
        text-align: center;
    }

    .sr_card_view .listing_description {
        height: 62px;
        overflow: hidden;
    }
    .sr_card_view ul > li:hover {
        box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
    }
    .sr_card_view ul>li:hover {
        box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
        border-bottom: 2px solid #44AEC1;
        transition: box-shadow .3s linear;
    }

    .block_title{
        border-bottom: 0;
        padding-bottom: 10px;
        text-transform: capitalize;
        background: transparent;
        font-size: 28px;
        margin-bottom: 20px;
        text-align: center;
        position: relative;
        line-height: normal;
    }

    .block_title::before {
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

    .block_title{
        font-size: 30px;
    }
    .intro_desc{

        line-height: 30px;
    }

    .impact_customer_content .widgets_title_description{
        margin-bottom: 14px;
        border-radius: 5px;
        padding: 11px;
        font-size: 16.5px !important;
        width: 100% !important;
        text-align: left;
    }
    .impact_customer_content {
        margin: 20px;
    }

    /*
    .impact_customer_logo{
        overflow: hidden;
        text-align: center;
        margin-bottom: 15px;
        clear: both;
    }
    .impact_customer_logo .wow {
        border-bottom: 2px solid transparent;
        background-color: #fff;
        border: 0 solid transparent;
        border-radius: 0;
    }*/

    .impact_customer_logo .wow {
        display: inline-block;
        box-sizing: border-box;
        position: relative;
        vertical-align: top;
        overflow: hidden;
        margin: 5px 5px 8px 5px;
    }
    @media only screen and (min-width: 1200px){
        .impact_customer_logo .wow {
            width: 235px;
        }
    }
    .impact_customer_logo .wow a > span {
        background-size: cover;
        background-position: center 50%;
        background-repeat: no-repeat;
        display: block;
        height: 150px;
        margin: 0 auto;
    }

    .impact_customer_info_details{
        width: 45%;
        display: flex;
        justify-content: center;
        flex-direction: column;
        align-items: center;
        object-fit: cover;
    }
    .impact_customer_image {
        width: 150px;
        height: 150px;
    }
    .impact_customer_image img{
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .impact_customer{
        display: flex;
        width: 100%;
    }
    @media(max-width:800px){
        .impact_customer{
            display: flex;
            flex-direction:column !important;
            width: 100%;
        }
        .impact_customer_info_details ,.impact_customer_logo{
            width:100% !important;
            display:flex;
            flex-direction:column !important;
            align-items:center !important;
            justify-content:center !important;
        }
    }

    @media(max-width:800px){
        .sr_card_view_info{
            padding-top: 0px;
        }
        .impact_customer_logo .wow .animated{
            width:120px !important;
        }
        .impact_customer_logo{
            width: 100% !important;
            display: flex;
            justify-content: center;
        }
    }

    .sr_card_view_info a{
        font-weight: 500;
        font-size: 18px !important;
        text-align: center;
    }
    @media(max-width:800px){
        .impact_customer_logo .wow{
            width:120px !important;
        }
        .impact_customer_content{
            margin-top: 0px !important;
        }
        .impact_customer_content .widgets_title_description{
            padding-top: 0px;
        }
    }
    .impact_customer_logo .wow{
        min-height:200px !important;
        height:auto !important;
    }

    @media (max-width: 800px)
        .impact_customer_logo .wow {
            width: 100% !important;
        }
        .sr_card_view_info {
            padding-top: 0px;
            padding-left: 0;
            padding-right: 0;
        }
    }
    .sr_title{
        margin-top:10px;
    }

    .sr_card_view_info > div{
        margin-top: 0px;
    }
    .sr_card_view_info a{
        color: #444;
    }

    .sr_card_view_image {
        border: unset !important;
    }
    .impact_customer_logo .wow a > span {
        background-position: center 32% !important;
        background-size: contain !important;
        width: 128px !important;
    }

    @media(max-width:800px) {
        .sr_card_view_info > div {
            margin-top: 10px;
        }
    }

</style>