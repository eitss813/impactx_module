<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if ($this->is_ajax_load):?>
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1);
 ?>

<?php if ($this->paginator->count() > 0): ?>
<?php if(!$this->autoContentLoad) : ?>
  <form id='filter_form_page' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'index'), 'sitepage_general', true) ?>' style='display: none;'>
    <input type="hidden" id="page" name="page"  value=""/>
  </form>

  <?php if (empty($this->isajax)) : ?>
       <?php if(!empty($this->category_title)):?>
        <?php echo $this->translate("Showing Pages in category: ");?>
        <b><?php echo $this->translate($this->category_title);?></b>     
        <br/><br/>
       <?php endif;?>
        
     <?php if(!empty($this->tag_name)):?>
        <h3><?php echo $this->tag_name;?>&nbsp;<a href="#" data-rel="back">(X)</a></h3>        
     <?php endif;?>
    <?php if( $this->list_view && $this->grid_view):?>
    <div class="p_view_op ui-page-content">
      <a href="<?php echo $this->view_selected == 'grid' ? $this->url(array('view_selected' => 'list')) : 'javascript:void(0);'; ?>" class="ui-link-inherit"> <span  class="sm-widget-block"><i class="ui-icon ui-icon-th-list"></i></span></a>
      <a href="<?php echo $this->view_selected == 'list' ? $this->url(array('view_selected' => 'grid')) : 'javascript:void(0);'; ?>" class="ui-link-inherit" ><span  class="sm-widget-block"><i class="ui-icon ui-icon-th-large"></i></span></a>
    </div>
    <?php endif;?>
    <div id="id" class="ui-page-content">
    <?php endif; ?>
<?php endif; ?>
    <?php if ($this->view_selected == "list"): ?>
      <?php if(!$this->autoContentLoad) : ?>
      <div id="list_view" class="sm-content-list">
        <ul data-role="listview" data-inset="false" id='browsepages_ul'>
      <?php endif;?>
          <?php foreach ($this->paginator as $sitepage): ?>
            <li <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>data-icon="angle-right"<?php else : ?>data-icon="arrow-r"<?php endif;?>>
              <a href="<?php echo $sitepage->getHref(); ?>">
                <?php echo $this->itemPhoto($sitepage, 'thumb.icon') ?>
                <h3><?php  echo $this->string()->chunk($this->string()->truncate($sitepage->getTitle(), 45), 10); ?></h3>				
                <p>
                  <?php $contentArray = array(); ?>
                  <?php if (in_array('date', $this->contentDisplayArray)): ?>
                    <?php $contentArray[] = $this->timestamp(strtotime($sitepage->creation_date)) ?> 
                  <?php endif; ?>

                  <?php if (in_array('owner', $this->contentDisplayArray) && $sitepage->show_name): ?>
                    <?php $contentArray[] = $this->translate('posted by ') . '<b>' . $sitepage->getOwner()->getTitle() . '</b>'; ?>
                  <?php endif; ?>
                  <?php
                  if (!empty($contentArray)) {
                    echo join(" - ", $contentArray);
                  }
                  ?> 
                </p>
           
                <p>                        
                    <?php $contentArray = array(); ?>
                    <?php
                     if (in_array('memberCount', $this->contentDisplayArray) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
                      $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('pagemember.member.title', 1);
                      if ($sitepage->member_title && $memberTitle) {
                          $contentArray[] = $sitepage->member_count . ' ' . $sitepage->member_title;
                      } else {
                        $contentArray[] = $this->translate(array('%s member', '%s members', $sitepage->member_count), $this->locale()->toNumber($sitepage->member_count));
                      }
                    }
                    
                    if (in_array('likeCount', $this->contentDisplayArray)) {
                      $contentArray[] = $this->translate(array('%s like', '%s likes', $sitepage->like_count), $this->locale()->toNumber($sitepage->like_count));
                    }
                    if (in_array('followCount', $this->contentDisplayArray)) {
                      $contentArray[] = $this->translate(array('%s follower', '%s followers', $sitepage->follow_count), $this->locale()->toNumber($sitepage->follow_count));
                    }

                    if (in_array('reviewCount', $this->contentDisplayArray) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview') && !empty($this->ratngShow)) {
                      $contentArray[] = $this->translate(array('%s review', '%s reviews', $sitepage->review_count), $this->locale()->toNumber($sitepage->review_count));
                    }

                    if (in_array('commentCount', $this->contentDisplayArray)) {
                      $contentArray[] = $this->translate(array('%s comment', '%s comments', $sitepage->comment_count), $this->locale()->toNumber($sitepage->comment_count));
                    }

                    if (in_array('viewCount', $this->contentDisplayArray)) {
                      $contentArray[] = $this->translate(array('%s view', '%s views', $sitepage->view_count), $this->locale()->toNumber($sitepage->view_count));
                    }
                    ?>
                    <?php
                    if (!empty($contentArray)) {
                      echo join(" - ", $contentArray);
                    }
                    ?>  
                </p>
                
                <p> 
                  <?php if (in_array('price', $this->contentDisplayArray) && !empty($sitepage->price) && $this->enablePrice): ?>            
                  <?php echo  $this->translate("Price: ") . Engine_Api::_()->sitepage()->getPriceWithCurrency($sitepage->price); ?>
                  <?php endif; ?>
                </p>
                <p>
                  <?php if (in_array('location', $this->contentDisplayArray) && !empty($sitepage->location) && $this->enableLocation): ?>
                    <?php
                    $locationId = Engine_Api::_()->getDbTable('locations', 'sitepage')->getLocationId($sitepage->page_id, $sitepage->location);
                    echo $this->translate("Location: ") . $this->translate($sitepage->location);
                    ?>     
                  <?php endif; ?>                 
                </p>

                <p>
                  <?php if (in_array('ratings', $this->contentDisplayArray) && $this->ratngShow): ?>
                    <?php if (($sitepage->rating > 0)): ?>
                      <?php
                      $currentRatingValue = $sitepage->rating;
                      $difference = $currentRatingValue - (int) $currentRatingValue;
                      if ($difference < .5) {
                        $finalRatingValue = (int) $currentRatingValue;
                      } else {
                        $finalRatingValue = (int) $currentRatingValue + .5;
                      }
                      ?>
                      <span class="list_rating_star" title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
                        <?php for ($x = 1; $x <= $sitepage->rating; $x++): ?>
                          <span class="rating_star_generic rating_star" ></span>
                        <?php endfor; ?>
                      <?php if ((round($sitepage->rating) - $sitepage->rating) > 0): ?>
                          <span class="rating_star_generic rating_star_half" ></span>
                      <?php endif; ?>
                      </span>		
                    <?php endif; ?>
                  <?php endif; ?>


                  <?php if (in_array('closed', $this->contentDisplayArray) && $sitepage->closed): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/close.png', '', array('class' => 'icon', 'title' => $this->translate('Closed'))) ?>

                  <?php endif; ?> 

                  <?php if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.fs.markers', 1)) : ?>
                    <?php if (in_array('featured', $this->contentDisplayArray) && ($sitepage->sponsored == 1)): ?>
                      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
                    <?php endif; ?>
                    <?php if (in_array('sponsored', $this->contentDisplayArray) && ($sitepage->featured == 1)): ?>
          <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
        <?php endif; ?>
      <?php endif; ?>

                </p>
              </a>
            </li>
      <?php endforeach; ?>
      <?php if(!$this->autoContentLoad) : ?>
        </ul>
      </div>
      <?php endif;?>
        <?php endif; ?>
        <?php if ($this->view_selected == "grid"): ?> 
      
      <?php if(!$this->autoContentLoad) : ?>
      <div id="grid_view">
        <ul class="p_list_grid" id='browsepages_ul'>
      <?php endif;?>
          <?php foreach ($this->paginator as $sitepage): ?>
            <li style="height:<?php echo $this->columnHeight ?>px;">
              <a href="<?php echo $sitepage->getHref(); ?>" class="ui-link-inherit">
                <div class="p_list_grid_top_sec">
                  <div class="p_list_grid_img">
                    <?php $url = $this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/nophoto_page_thumb_profile.png';
                    $temp_url = $sitepage->getPhotoUrl('thumb.profile');
                    if (!empty($temp_url)): $url = $sitepage->getPhotoUrl('thumb.profile');
                      endif; ?>
                    <span style="background-image: url(<?php echo $url; ?>);"> </span>
                  </div>
                  <div class="p_list_grid_title">
                    <span><?php echo $this->string()->chunk($this->string()->truncate($sitepage->getTitle(), 45), 10); ?></span>
                  </div>
                  <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
                    <div class="list-label-wrap">
                    <?php if (in_array('sponsored', $this->contentDisplayArray)&& ($sitepage->sponsored == 1)): ?>
                      <span class="list-label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponsored.color', '#fc0505'); ?>;'>
                        <?php echo $this->translate('SPONSORED'); ?>     				
                      </span>
                    <?php endif; ?>
                    <?php if (in_array('featured', $this->contentDisplayArray)  && ($sitepage->featured == 1)): ?>
                      <span class="list-label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.featured.color', '#0cf523'); ?>;'><?php echo $this->translate('FEATURED')?></span>
                    <?php endif; ?>
                    </div>
                  <?php endif; ?>
                </div>
              </a>
              
              <?php if (!Engine_Api::_()->sitemobile()->isApp()): ?>
                <?php if (in_array('closed', $this->contentDisplayArray) && $sitepage->closed): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/close.png', '', array('class' => 'icon', 'title' => $this->translate('Closed'))) ?>
                <?php endif; ?>
                <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.fs.markers', 1)) : ?>
                  <?php if (in_array('sponsored', $this->contentDisplayArray)&& ($sitepage->sponsored == 1)): ?>
                    <div class="sm-sl" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponsored.color', '#fc0505'); ?>;'>
                      <?php echo $this->translate('SPONSORED'); ?>     				
                    </div>
                  <?php endif; ?>
                  <?php if (in_array('featured', $this->contentDisplayArray)  && ($sitepage->featured == 1)): ?>
                    <i title="<?php echo $this->translate('Featured')?>" class="sm-fl"></i>
                  <?php endif; ?>
                <?php endif; ?>
              <?php endif; ?>

              <div class="p_list_grid_info">	

                <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
                  <span class="p_list_grid_stats">
                    <span class="fleft">
                      <?php if (in_array('date', $this->contentDisplayArray)): ?>
                        <?php echo $this->timestamp(strtotime($sitepage->creation_date)) ?> 
                      <?php endif; ?>
                    </span>
                    <span class="fright">
                      <?php if (in_array('owner', $this->contentDisplayArray)): ?>
                       <?php echo $this->translate('by ') . '<b>' .$this->htmlLink($sitepage->getOwner()->getHref(), $this->string()->truncate($sitepage->getOwner()->getTitle(), 16)) . '</b>'; ?>
                      <?php endif; ?>
                    </span>
                  </span>
                <?php endif; ?>

                <span class="p_list_grid_stats">
                    <?php if (in_array('ratings', $this->contentDisplayArray) && $this->ratngShow): ?>
                      <?php if (($sitepage->rating > 0)): ?>
                          <?php
                          $currentRatingValue = $sitepage->rating;
                          $difference = $currentRatingValue - (int) $currentRatingValue;
                          if ($difference < .5) {
                            $finalRatingValue = (int) $currentRatingValue;
                          } else {
                            $finalRatingValue = (int) $currentRatingValue + .5;
                          }
                          ?>
                        <span class="list_rating_star" title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
        <?php for ($x = 1; $x <= $sitepage->rating; $x++): ?>
                            <span class="rating_star_generic rating_star" ></span>
                        <?php endfor; ?>
                        <?php if ((round($sitepage->rating) - $sitepage->rating) > 0): ?>
                            <span class="rating_star_generic rating_star_half" ></span>
                        <?php endif; ?>
                        </span>		
                      <?php endif; ?>
                    <?php endif; ?>
                  </span>
                <?php if (!Engine_Api::_()->sitemobile()->isApp()): ?>
                  <span class="p_list_grid_stats">
                      <?php if (in_array('date', $this->contentDisplayArray)): ?>
                        <?php echo $this->timestamp(strtotime($sitepage->creation_date)) ?> 
                      <?php endif; ?>
                    </span>
                    <span class="p_list_grid_stats">
                      <?php if (in_array('owner', $this->contentDisplayArray) && $sitepage->show_name): ?>
                      <?php echo $this->translate('posted by ') . '<b>' .$this->htmlLink($sitepage->getOwner()->getHref(), $sitepage->getOwner()->getTitle()) . '</b>'; ?>
                      <?php endif; ?>
                    </span>
                <?php endif; ?>
                  <span class="p_list_grid_stats">                                            
                  <?php $contentArray = array(); ?>
                  <?php
                    if (in_array('memberCount', $this->contentDisplayArray) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
                    $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('pagemember.member.title', 1);
                    if ($sitepage->member_title && $memberTitle) {
                        $contentArray[] = $sitepage->member_count . ' ' . $sitepage->member_title;
                    } else {
                      $contentArray[] = $this->translate(array('%s member', '%s members', $sitepage->member_count), $this->locale()->toNumber($sitepage->member_count));
                    }
                  }

                  if (in_array('likeCount', $this->contentDisplayArray)) {
                    $contentArray[] = $this->translate(array('%s like', '%s likes', $sitepage->like_count), $this->locale()->toNumber($sitepage->like_count));
                  }
                  if (in_array('followCount', $this->contentDisplayArray)) {
                    $contentArray[] = $this->translate(array('%s follower', '%s followers', $sitepage->follow_count), $this->locale()->toNumber($sitepage->follow_count));
                  }

                  if (in_array('reviewCount', $this->contentDisplayArray) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview') && !empty($this->ratngShow)) {
                    $contentArray[] = $this->translate(array('%s review', '%s reviews', $sitepage->review_count), $this->locale()->toNumber($sitepage->review_count));
                  }

                  if (in_array('commentCount', $this->contentDisplayArray)) {
                    $contentArray[] = $this->translate(array('%s comment', '%s comments', $sitepage->comment_count), $this->locale()->toNumber($sitepage->comment_count));
                  }

                  if (in_array('viewCount', $this->contentDisplayArray)) {
                    $contentArray[] = $this->translate(array('%s view', '%s views', $sitepage->view_count), $this->locale()->toNumber($sitepage->view_count));
                  }
                  ?>
                  <?php
                  if (!empty($contentArray)) {
                    echo join(" - ", $contentArray);
                  }
                  ?>  
                  </span>
                  <?php if (in_array('price', $this->contentDisplayArray) && !empty($sitepage->price) && $this->enablePrice): ?>
                    <span class="p_list_grid_stats">
                      <?php echo $this->translate("Price: ") . Engine_Api::_()->sitepage()->getPriceWithCurrency($sitepage->price); ?>
                    </span>
                  <?php endif; ?>
                  <?php if (in_array('location', $this->contentDisplayArray) && !empty($sitepage->location) && $this->enableLocation): ?>      
                    <span class="p_list_grid_stats">
                      <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
                        <i class="ui-icon-map-marker"></i>
                         <?php echo $this->htmlLink('https://maps.google.com/?q='.urlencode($sitepage->location), $sitepage->location, array('target' => 'blank')) ?>
                      <?php else: ?>
                      <?php
                        $locationId = Engine_Api::_()->getDbTable('locations', 'sitepage')->getLocationId($sitepage->page_id, $sitepage->location);
                        echo $this->translate("Location: ") . $this->translate($sitepage->location);
                      ?>  
                      <?php endif; ?>
                    </span>
                  <?php endif; ?>
              </div>
            </li>
      <?php endforeach; ?>
      <?php if(!$this->autoContentLoad) : ?>
        </ul>
      </div>
      <?php endif; ?>

  <?php endif; ?>  
  <?php if (empty($this->isajax)) : ?>
    <?php if ($this->paginator->count() > 1 && !Engine_Api::_()->sitemobile()->isApp()): ?>
      <?php
      echo $this->paginationControl($this->paginator, null, null, array(
          'query' => $this->formValues,
      ));
      ?>
      <?php endif; ?> 
    </div> 
    <?php endif; ?>



<?php elseif ($this->search): ?>
  <div class="tip">
  <?php
  if (Engine_Api::_()->sitepage()->hasPackageEnable()):
    $createUrl = $this->url(array('action' => 'index'), 'sitepage_packages');
  else:
    $createUrl = $this->url(array('action' => 'create'), 'sitepage_general');
  endif;
  ?>
    <span> <?php echo $this->translate('Nobody has created a page with that criteria.'); ?>
    </span> 
  </div>
<?php else: ?>
  <div class="tip"> <span> <?php echo $this->translate('No Pages have been created yet.'); ?>
    </span>
  </div>
<?php endif; ?>
<script type='text/javascript'>        
<?php if (Engine_Api::_()->sitemobile()->isApp()) { ?>

          
  var browsePageWidgetUrl = sm4.core.baseUrl + 'widget/index/mod/sitepage/name/sitemobile-pages-sitepage';
         sm4.core.runonce.add(function() {    
              var activepage_id = sm4.activity.activityUpdateHandler.getIndexId();
              sm4.core.Module.core.activeParams[activepage_id] = {'currentPage' : '<?php echo sprintf('%d', $this->page) ?>', 'totalPages' : '<?php echo sprintf('%d', $this->totalPages) ?>', 'formValues' : <?php echo json_encode($this->formValues);?>, 'contentUrl' : browsePageWidgetUrl, 'activeRequest' : false, 'container' : 'browsepages_ul' };
             
          });
          
  <?php } ?>           
</script>
<?php else:?>
<div id="layout_sitepage_sitemobile_pages_sitepage_<?php echo $this->identity; ?>">
</div>
<script type="text/javascript">
    var requestParams = $.extend(<?php echo json_encode($this->paramsLocation);?>, {'content_id': '<?php echo $this->identity;?>'});
    var params = {
      'detactLocation': <?php echo $this->detactLocation; ?>,
      'responseContainer': 'layout_sitepage_sitemobile_pages_sitepage_<?php echo $this->identity; ?>',
      'locationmiles': <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 1000); ?>,
      requestParams: requestParams
    };
    sm4.core.runonce.add(function() {
          setTimeout((function() {
            $.mobile.loading().loader("show");
    }), 100);

          sm4.core.locationBased.startReq(params);
    });
</script>  
<?php endif; ?>



