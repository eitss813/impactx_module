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
$this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/jquery.min.js');
$this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/wow.js');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/animate.css');
?>

<script>
  new WOW().init();
</script>

<div class="page_header">
  <h3 class="sitecore_title">
    Take Part In Taking Action
  </h3>
  <div class="widgets_title_description services_desc">
    We are at the doorstep of the Changemaker decade.  The growth of social impact activity is changing our very systems for making change.   With more Impactors, projects, and investment comes the need for new ways to <br>support and measure impact.  <br><br> <b>ImpactX is the social and knowledge network for Impactors worldwide, and the resource for tools and frameworks to better manage and measure impact.</b>
  </div>
  <div class="spec_btnsblock">
    <a href="<?php echo $this->layout()->staticBaseUrl.'public/others/Impact_Nexus.pdf'?>"  ><?php echo $this->translate("LEARN MORE"); ?></a>
  </div>
 <!--
  <h3 class="sitecore_title">
    Welcome To ImpactX!
  </h3>
  <div class="widgets_title_description services_desc">
    ImpactX is the cloud-based platform that supports projects and financial transactions in the global social impact space so you can take part in taking action.
    <br/><br/>
    ImpactX offers support for the whole impact project journey. It welcomes projects by individuals, groups, and organizations  Find new ways to connect, share, act and measure results together…with purpose.
  </div> -->
</div>
<br/><br/>

<div class="sitecoretheme_icons_container">
  <h3 class="sitecore_title">
    Join The Social Impact Community
  </h3>
  <div class="widgets_title_description services_desc">
    Join our global broad-based community of Impactors: individuals and organizations, public and private, philanthropic and for-profit.  All come together at ImpactX to share knowledge, incubate innovation, evolve  practices, and measure and amplify results.
    <br><br>
    <div class="spec_btnsblock">
      <a href="<?php echo $this->layout()->staticBaseUrl.'signup'?>" target="_blank" ><?php echo $this->translate("JOIN NOW"); ?></a>
    </div>
  </div>

  <div class="resource_box" style="border: 1px solid #ccc;border-radius: 12px;padding: 20px;background-color: #add8e670;">
    <h3 class="sitecore_title">
      Explore The Resources
    </h3>
    <div class="services_desc" style="font-stretch: expanded !important;">
      Find invaluable tools for Impactors, whether doers, sponsors, investors, or followers. The IX Project Space provides a cloud-based home for impact projects and offers integrated tools designed to streamline core functions, such as progress tracking, measuring outcomes, and capturing and aggregating data, as well as facilitating visibility, donations, crowd funding, and stakeholder engagement.
    </div>

    <br>

    <div class="sitecoretheme_icons_wrapper <?php echo $this->viewType?>">
      <?php $count = 0; ?>
      <?php foreach( $this->services as $key=>$service ): ?>
      <?php
      $iconUrl = $defaultIcon = $this->layout()->staticBaseUrl . 'application/modules/Sitecoretheme/externals/images/services/service_' . $service->service_id . '.png';
      if( $service->file_id ) {
      $icon = Engine_Api::_()->storage()->get($service->file_id);
      $iconUrl = ( $icon ) ? $icon->getPhotoUrl() : $defaultIcon;
      }
      ?>
      <?php if( !($count % 3) || $count == 0 ): ?>
      <div class="sitecoretheme_icons_inner">
        <?php endif; ?>

        <div class="sitecoretheme_icons_content_4 wow animated fadeInUp" >
          <div class="sitecoretheme_icons_content_4_inner service_<?php echo $key;?> " >
            <div class="_image_icon">
              <span>
                <img src="<?php echo $iconUrl; ?>">
              </span>
            </div>
            <!--<h4><a href="#"><?php echo $this->translate($service->title) ?></a></h4>-->
            <h4 class="services_desc"><a href="#"><?php echo $this->translate($service->description) ?></a></h4>
          </div>
        </div>

        <?php $count++; ?>
        <?php if( !($count % 3) || $count === count($this->services) ) : ?>
      </div>
      <?php endif; ?>

      <?php endforeach; ?>
    </div>

  </div>
  <br>
  <!--
  <h3 class="sitecore_title">
    ImpactX Features
  </h3>
  <div class="widgets_title_description services_desc">
    Like your social network <br/>
    LOVE YOUR IMPACT<br/>
    ... and make it happen at ImpactX
  </div>
  -->

  <br/>
  <!--<div class="widgets_title_description services_desc">
    Free, simple to use, and rich with resources for impact professionals and newcomers
  </div>-->
</div>
<style type="text/css">
  .widgets_title_description + h3 {
     display:block !important;
   }
  .sitecoretheme_icons_wrapper._cards .sitecoretheme_icons_content_4_inner {
    width: 100%;
    border: 1px solid lightgray;
  }
    div.layout_sitecoretheme_our_services{
      background-color: white !important;
    }
    .layout_sitecoretheme_our_services > h3 , .sitecore_title {
      border-bottom: 0;
      padding-bottom: 10px;
      text-transform: capitalize;
      background: transparent;
      font-size: 30px;
      margin-bottom: 20px;
      text-align: center;
      position: relative;
      line-height: normal;
    }

    .layout_sitecoretheme_our_services > h3::before ,
    .sitecore_title::before {
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
    .services_desc{
      font-size: 18px !important;
      line-height: 30px;
    }
  .service_0{
    border: 2px solid #d6b4fc !important;
  }
  .service_1{
    border: 2px solid #fdde6c !important;
  }
  .service_2{
    border: 2px solid #ddadaf !important;
  }
  .service_3{
    border: 2px solid #b1916e !important;
  }
  .service_4{
    border: 2px solid #BFC88A !important;
  }
  .service_5{
    border: 2px solid #b790d4 !important;
  }
</style>