<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _recently_popular_random_page.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $enableBouce = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.map.sponsored', 1); ?>
<?php $latitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.map.latitude', 0); ?>
<?php $longitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.map.longitude', 0); ?>
<?php $defaultZoom = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.map.zoom', 1); ?>
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1); ?>
<?php $verifyTableObj = Engine_Api::_()->getDbtable('verifies', 'sitepage'); ?>
<?php $verify_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.verify.limit', 3); ?>
<?php
    //Get Map icons
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $baseUrl = $view->baseUrl();
    $organisationMapIcon = $baseUrl . "/externals/map/organisation.png";
?>
<script type="text/javascript" >
    function owner(thisobj) {
        var Obj_Url = thisobj.href;
        Smoothbox.open(Obj_Url);
    }
</script>
<script>
    var sitepages_likes = function (resource_id, resource_type) {
        var content_type = 'sitepage';

        // SENDING REQUEST TO AJAX
        var request = createLikepage(resource_id, resource_type, content_type);

        // RESPONCE FROM AJAX
        request.addEvent('complete', function (responseJSON) {
            if (responseJSON.error_mess == 0) {
                $(resource_id).style.display = 'block';
                if (responseJSON.like_id)
                {
                    $('backgroundcolor_' + resource_id).className = "sitepage_browse_thumb sitepage_browse_liked";
                    $('sitepage_like_' + resource_id).value = responseJSON.like_id;
                    $('sitepage_most_likes_' + resource_id).style.display = 'none';
                    $('sitepage_unlikes_' + resource_id).style.display = 'block';
                    $('show_like_button_child_' + resource_id).style.display = 'none';
                }
                else
                {
                    $('backgroundcolor_' + resource_id).className = "sitepage_browse_thumb";
                    $('sitepage_like_' + resource_id).value = 0;
                    $('sitepage_most_likes_' + resource_id).style.display = 'block';
                    $('sitepage_unlikes_' + resource_id).style.display = 'none';
                    $('show_like_button_child_' + resource_id).style.display = 'none';
                }

            }
            else {
                en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
                return;
            }
        });
    };

    // FUNCTION FOR CREATING A FEEDBACK
    var createLikepage = function (resource_id, resource_type, content_type) {
        if ($('sitepage_most_likes_' + resource_id).style.display == 'block')
            $('sitepage_most_likes_' + resource_id).style.display = 'none';


        if ($('sitepage_unlikes_' + resource_id).style.display == 'block')
            $('sitepage_unlikes_' + resource_id).style.display = 'none';
        $(resource_id).style.display = 'none';
        $('show_like_button_child_' + resource_id).style.display = 'block';

        if (content_type == 'sitepage') {
            var like_id = $(content_type + '_like_' + resource_id).value
        }
        //	var url = '<?php echo $this->url(array('action' => 'global-likes'), 'sitepage_like', true); ?>';
        var request = new Request.JSON({
            url: '<?php echo $this->url(array('action' => 'global-likes'), 'sitepage_like', true); ?>',
            data: {
                format: 'json',
                'resource_id': resource_id,
                'resource_type': resource_type,
                'like_id': like_id
            }
        });
        request.send();
        return request;
    }

</script>

<?php
$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
$MODULE_NAME = 'sitepage';
$RESOURCE_TYPE = 'sitepage_page';
?>
<?php if ($this->list_view): ?>
    <div id="rgrid_view_page"  style="display: none;">
        <?php if (count($this->sitepagesitepage)): ?>
            <?php
            $counter = '1';
            $limit = $this->active_tab_list;
            ?>
            <ul class="seaocore_browse_list">
                <?php foreach ($this->sitepagesitepage as $item): ?>
                    <?php $sitepage = Engine_Api::_()->getItem('sitepage_page', $item->page_id); ?>
                    <?php
                    if ($counter > $limit):
                        break;
                    endif;
                        $counter++;
                    ?>
                    <li>
                        <div class="sitepage_browse">
                            <div class='seaocore_browse_list_photo'>
                                <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id, $sitepage->getSlug()), $this->itemPhoto($sitepage, 'thumb.normal', '', array('align' => 'left'))); ?>
                            </div>
                            <div class='seaocore_browse_list_info'>

                                <div class='seaocore_browse_list_info_title'>
                                    <div class="seaocore_title">
                                        <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id, $sitepage->getSlug()), Engine_Api::_()->sitepage()->truncation($sitepage->getTitle(), $this->listview_turncation), array('title' => $sitepage->getTitle())) ?>
                                    </div>
                                </div>

                                <?php if (@in_array('reviewCount', $this->statistics) && $this->ratngShow): ?>
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
                                <span class="clr" title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
                                        <?php for ($x = 1; $x <= $sitepage->rating; $x++): ?>
                                            <span class="rating_star_generic rating_star" ></span>
                                    <?php endfor; ?>
                                    <?php if ((round($sitepage->rating) - $sitepage->rating) > 0): ?>
                                            <span class="rating_star_generic rating_star_half" ></span>
                                    <?php endif; ?>
                                    </span>
                                <?php endif; ?>
                                <?php endif; ?>

                                <?php /*
                                <div class='seaocore_browse_list_info_date'>
                                    <?php echo $this->translate('by'); ?>
                                    <?php echo $this->htmlLink($sitepage->getOwner()->getHref(), $sitepage->getOwner()->getTitle()) ?>
                                </div>
                                */?>

                                <div class="site_page_list_desc">
                                    <?php echo $this->string()->truncate($this->string()->stripTags($sitepage->body), 150) ?>
                                </div>

                                <?php if (!empty($this->statistics)) : ?>
                                <div class='seaocore_browse_list_info_date'>
                                    <?php
                                    $statistics = '';

                                    if (@in_array('likeCount', $this->statistics)) {
                                    $statistics .= $this->translate(array('%s like', '%s likes', $sitepage->like_count), $this->locale()->toNumber($sitepage->like_count)) . ', ';
                                    }
                                    if (@in_array('followCount', $this->statistics)) {
                                    $statistics .= $this->translate(array('%s follower', '%s followers', $sitepage->follow_count), $this->locale()->toNumber($sitepage->follow_count)) . ', ';
                                    }

                                    if (@in_array('memberCount', $this->statistics) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
                                    $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('pagemember.member.title', 1);
                                    if ($sitepage->member_title && $memberTitle) {
                                    echo $sitepage->member_count . ' ' . $sitepage->member_title . ', ';
                                    } else {
                                    $statistics .= $this->translate(array('%s member', '%s members', $sitepage->member_count), $this->locale()->toNumber($sitepage->member_count)) . ', ';
                                    }
                                    }

                                    if (!empty($sitepage->review_count) && @in_array('reviewCount', $this->statistics) && !empty($this->ratngShow)) {
                                    $statistics .= $this->translate(array('%s review', '%s reviews', $sitepage->review_count), $this->locale()->toNumber($sitepage->review_count)) . ', ';
                                    }

                                    if (@in_array('commentCount', $this->statistics)) {
                                    $statistics .= $this->translate(array('%s comment', '%s comments', $sitepage->comment_count), $this->locale()->toNumber($sitepage->comment_count)) . ', ';
                                    }

                                    if (@in_array('viewCount', $this->statistics)) {
                                    $statistics .= $this->translate(array('%s view', '%s views', $sitepage->view_count), $this->locale()->toNumber($sitepage->view_count)) . ', ';
                                    }
                                    $statistics = trim($statistics);
                                    $statistics = rtrim($statistics, ',');
                                    ?>
                                    <?php echo $statistics; ?>
                                </div>
                                <?php endif; ?>


                                <?php if (!empty($sitepage->price) && $this->enablePrice): ?>
                                <div class='seaocore_browse_list_info_date'>
                                    <?php
                                    echo $this->translate("Price: ");
                                    echo Engine_Api::_()->sitepage()->getPriceWithCurrency($sitepage->price);
                                    ?>
                                </div>
                                <?php endif; ?>

                                <div class="site_page_category">
                                    <?php $category = Engine_Api::_()->getItem('sitepage_category', $sitepage->category_id); ?>

                                    <?php if ($category->file_id): ?>
                                    <?php $url = Engine_Api::_()->storage()->get($category->file_id)->getPhotoUrl(); ?>
                                    <img src="<?php echo $url ?>" style="width: 16px; height: 16px;" alt="">
                                    <?php else: ?>
                                    <?php $src = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/category_images/icons/noicon_category.png" ?>
                                    <img src="<?php echo $src ?>" style="width: 16px; height: 16px;" alt="">
                                    <?php endif; ?>

                                    <?php echo $this->htmlLink($category->getHref(), $category->getTitle()) ?>
                                </div>

                                <?php if (!empty($sitepage->location) && $this->enableLocation): ?>
                                <div class='seaocore_browse_list_info_date'>
                                    <?php echo $this->translate("Location: ");?> <?php echo $this->translate($sitepage->location); ?>
                                    <?php $location_id = Engine_Api::_()->getDbTable('locations', 'sitepage')->getLocationId($sitepage->page_id, $sitepage->location); ?>
                                    <?php if (!empty($this->showgetdirection)) : ?>&nbsp;
                                    - <b> <?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $sitepage->page_id, 'resouce_type' => 'sitepage_page', 'location_id' => $location_id, 'flag' => 'map'), $this->translate("Get Directions"), array('onclick' => 'owner(this);return false')); ?> </b>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>

                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="tip">
                <span>
                    <?php echo $this->translate('Nobody has created a page with that criteria.') ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ($this->grid_view): ?>
    <div id="rimage_view_page" style="display: none;">
        <?php if (count($this->sitepagesitepage)): ?>

            <?php
                $counter = 1;
                $total_sitepage = count($this->sitepagesitepage);
                $limit = $this->active_tab_image;
            ?>

            <div class="sitepage_img_view o_hidden">
                <div class="sitepage_img_view_sitepage">
                    <?php foreach ($this->sitepagesitepage as $item): ?>
                        <?php $sitepage = Engine_Api::_()->getItem('sitepage_page', $item->page_id); ?>
                        <?php
                         // Code added by Questwalk Start
                         $result = Engine_Api::_()->getDbTable('formmappings', 'impactx')->getMappingInfoByRoleId($item->role_id);
                         $form_id =  $result['form_id'];

                         $entry_id = Engine_Api::_()->getDbTable('entries', 'yndynamicform')->getEntryIDByOwneridAndFormId($form_id,$viewer_id);

                         // End

                        if ($counter > $limit):
                            break;
                        endif;
                            $counter++;
                        ?>
                        <?php
                        $likePage = false;

                        if (!empty($viewer_id)):
                            $likePage = Engine_Api::_()->sitepage()->hasPageLike($sitepage->page_id, $viewer_id);
                        endif;
                        ?>

                        <div class="sitepage_browse_thumb" style="width:<?php echo $this->columnWidth; ?>px;height:<?php echo $this->columnHeight; ?>px;" >

                            <div class="sitepage_browse_thumb_details">

                                <div class="sitepage_grid_thumb">
                                    <?php echo $this->htmlLink($sitepage->getHref(), $this->itemBackgroundPhoto($sitepage, 'thumb.profile', null, array('tag' => 'i')), array('class' => 'sitepage_thumb')); ?>
                                    <div class='sitepage_hover_info'>
                                        <div class="txt_center">
                                            <button onclick="window.location = '<?php echo $sitepage->getHref() ?>'">
                                                <?php echo $this->translate('View'); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="sitepage_browse_thumb_info">

                                    <div class='seaocore_browse_list_info_title'>
                                        <div class="seaocore_title">
                                            <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id, $sitepage->getSlug()), Engine_Api::_()->sitepage()->truncation($sitepage->getTitle(), $this->turncation), array('title' => $sitepage->getTitle())) ?>
                                        </div>
                                    </div>

                                    <?php /*
                                    <div class='seaocore_browse_list_info_date'>
                                        <?php echo $this->translate('by'); ?>
                                        <?php echo $this->htmlLink($sitepage->getOwner()->getHref(), $sitepage->getOwner()->getTitle()) ?>
                                    </div>
                                    */ ?>

                                    <div class="site_page_desc">
                                        <?php echo $this->string()->truncate($this->string()->stripTags($sitepage->body), 150) ?>
                                    </div>

                                    <div class="site_page_category">
                                        <?php $category = Engine_Api::_()->getItem('sitepage_category', $sitepage->category_id); ?>

                                        <?php if ($category->file_id): ?>
                                            <?php $url = Engine_Api::_()->storage()->get($category->file_id)->getPhotoUrl(); ?>
                                            <img src="<?php echo $url ?>" style="width: 16px; height: 16px;" alt="">
                                        <?php else: ?>
                                            <?php $src = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/category_images/icons/noicon_category.png" ?>
                                            <img src="<?php echo $src ?>" style="width: 16px; height: 16px;" alt="">
                                        <?php endif; ?>

                                        <?php echo $this->htmlLink($category->getHref(), $category->getTitle()) ?>
                                    </div>

                                    <?php if (!empty($sitepage->location)) : ?>
                                        <div class="site_page_bottom_info_location">
                                            <i class="seao_icon_location"></i>
                                            <?php echo $this->string()->truncate($this->string()->stripTags($sitepage->location), 100); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($this->showdate)) : ?>
                                        <div class='seaocore_browse_list_info_date'>
                                            <?php echo $this->timestamp(strtotime($sitepage->creation_date)) ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($this->showprice) && !empty($sitepage->price) && $this->enablePrice): ?>
                                        <div class='seaocore_browse_list_info_date'>
                                            <?php
                                            echo $this->translate("Price: ");
                                            echo Engine_Api::_()->sitepage()->getPriceWithCurrency($sitepage->price);
                                            ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (@in_array('memberCount', $this->statistics) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) : ?>
                                    <?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('pagemember.member.title', 1); ?>
                                        <?php if ($sitepage->member_title && $memberTitle):?>
                                            <div class="member_count">
                                            <?php echo $sitepage->member_count . ' ' . ucfirst($sitepage->member_title); ?>
                                        </div>
                                        <?php else : ?>
                                            <div class="member_count">
                                            <?php echo $this->translate(array('%s ' . ucfirst('member'), '%s ' . ucfirst('members'), $sitepage->member_count), $this->locale()->toNumber($sitepage->member_count)) ?>
                                        </div>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if (!empty($this->statistics)) : ?>
                                        <div class='sitepage_browse_thumb_stats seaocore_txt_light'>
                                            <?php
                                                $statistics = '';
                                                if (@in_array('likeCount', $this->statistics)) {
                                            $statistics .= $this->translate(array('%s like', '%s likes', $sitepage->like_count), $this->locale()->toNumber($sitepage->like_count)) . ', ';
                                            }

                                            if (@in_array('followCount', $this->statistics)) {
                                            $statistics .= $this->translate(array('%s follower', '%s followers', $sitepage->follow_count), $this->locale()->toNumber($sitepage->follow_count)) . ', ';
                                            }

                                            if (@in_array('commentCount', $this->statistics)) {
                                            $statistics .= $this->translate(array('%s comment', '%s comments', $sitepage->comment_count), $this->locale()->toNumber($sitepage->comment_count)) . ', ';
                                            }
                                            if (@in_array('viewCount', $this->statistics)) {
                                            $statistics .= $this->translate(array('%s view', '%s views', $sitepage->view_count), $this->locale()->toNumber($sitepage->view_count)) . ', ';
                                            }
                                            $statistics = trim($statistics);
                                            $statistics = rtrim($statistics, ',');
                                            ?>
                                            <?php echo $statistics; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (@in_array('reviewCount', $this->statistics) && $this->ratngShow): ?>
                                        <div class='sitepage_browse_thumb_stats seaocore_txt_light'>

                                            <?php if ($sitepage->review_count) : ?>
                                                <?php echo $this->translate(array('%s review', '%s reviews', $sitepage->review_count), $this->locale()->toNumber($sitepage->review_count)); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                                            <?php endif; ?>

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

                                                <span class="clr" title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
                                                    <?php for ($x = 1; $x <= $sitepage->rating; $x++): ?>
                                                        <span class="rating_star_generic rating_star" ></span>
                                                    <?php endfor; ?>

                                                    <?php if ((round($sitepage->rating) - $sitepage->rating) > 0): ?>
                                                                <span class="rating_star_generic rating_star_half" ></span>
                                                    <?php endif; ?>
                                                </span>

                                            <?php endif; ?>

                                        </div>
                                    <?php endif; ?>

                                    <?php if($this->params['link'] == 'joined_pages'): ?>

                                    <div class="site_page_bottom_info_submission">
                                      <a href="dynamic-form/entry/view/<?php echo $entry_id; ?>/is_popup/1" class="button smoothbox"> View Submissions </a>
                                    </div>

                                    <?php endif ?>

                                </div>

                            </div>
                        </div>

                    <?php endforeach; ?>
                </div>
            </div>

        <?php else: ?>
                <div class="tip">
                    <span>
            <?php echo $this->translate('Nobody has created a page with that criteria.') ?>
                    </span>
                </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div id="rmap_canvas_view_page" style="display: none;">
    <div class="seaocore_map clr" style="overflow:hidden;">
        <div id="rmap_canvas_page"> </div>
        <?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?>
        <?php if (!empty($siteTitle)) : ?>
            <div class="seaocore_map_info"><?php echo "Locations on "; ?><a href="" target="_blank"><?php echo $siteTitle; ?></a></div>
<?php endif; ?>
    </div>	

<?php if ($this->enableLocation && $this->flageSponsored && $this->map_view && $enableBouce): ?>
        <a href="javascript:void(0);" onclick="rtoggleBouncePage()" class="stop_bounce_link"> <?php echo $this->translate('Stop Bounce'); ?></a>
        <br />
<?php endif; ?>
</div>

<?php if ($this->enableLocation && $this->map_view): ?>
    <?php
    $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
    $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
    ?>

    <script type="text/javascript">

        var rgmarkersPage = [];
        var rmap_page = null;
        var bounds = null;

        var organisationIcon = {
            url: "<?php echo $organisationMapIcon; ?>", // url
        };

        // A function to create the marker and set up the event window function
        function rcreateMarkerPage(latlng, name, html, title_page) {
            var contentString = html;
            bounds = new google.maps.LatLngBounds();
            bounds.extend(latlng);

            if (name == 0)
            {
                var marker = new google.maps.Marker({
                    position: latlng,
                    map: rmap_page,
                    title: title_page,
                    icon: organisationIcon,
                    animation: google.maps.Animation.DROP,
                    zIndex: Math.round(latlng.lat() * -100000) << 5
                });
            }
            else {
                var marker = new google.maps.Marker({
                    position: latlng,
                    map: rmap_page,
                    title: title_page,
                    draggable: false,
                    animation: google.maps.Animation.BOUNCE,
                    icon: organisationIcon
                });
            }
            rgmarkersPage.push(marker);

            var infowindow = new google.maps.InfoWindow({
                content: contentString,
                maxWidth: 400,
                maxHeight : 400
            });

            google.maps.event.addListener(marker, 'click', function () {
                infowindow.open(rmap_page, marker);
            });

            // Center the map to fit all markers on the screen
            rmap_page.fitBounds(bounds);

        }

        function setMarkers(){
            <?php if (count($this->locations) > 0) : ?>
                <?php foreach ($this->locations as $location) : ?>

                    var lat = <?php echo $location->latitude ?>;
                    var lng =<?php echo $location->longitude ?>;
                    var point = new google.maps.LatLng(lat, lng);

                    var title = <?php echo '"'.$this->sitepage[$location->page_id]->getTitle().'"' ?>;

                    var contentString = "<?php
                        echo $this->string()->escapeJavascript($this->partial('application/modules/Sitepage/views/scripts/_mapOrganisationInfoWindowContent.tpl', array(
                            'sitepage' =>$this->sitepage[$location->page_id],
                            'location' => $location,
                            'page_type' => 'Organisation'
                        )), false);
                    ?>";

                    var marker = rcreateMarkerPage(point, 0, contentString,title);

                <?php endforeach; ?>
            <?php endif; ?>
        }

        function rinitializePage() {

            var defaultLatlng = new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>);

            // create the map
            var mapOptions = {
                navigationControl: true,
                zoom: <?php echo $defaultZoom; ?>,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                center: defaultLatlng
            }

            rmap_page = new google.maps.Map(document.getElementById("rmap_canvas_page"),mapOptions);
            bounds = new google.maps.LatLngBounds();
            bounds.extend(defaultLatlng);

            // Center the map to fit all markers on the screen
            rmap_page.fitBounds(bounds);

            $$(".tab_icon_map_view").addEvent('click', function () {
                rmap_page.fitBounds(bounds);
            });

            setMarkers();
        }

        function rtoggleBouncePage() {
            for (var i = 0; i < rgmarkersPage.length; i++) {
                if (rgmarkersPage[i].getAnimation() != null) {
                    rgmarkersPage[i].setAnimation(null);
                }
            }
        }

    </script>
<?php endif; ?>
<style>
    .sitepage_grid_thumb{
        height: 166px;
        position: relative;
        overflow: hidden;
    }
    .sitepage_hover_info
    {
        top: 0;
        left: 0px;
        right: 0px;
        position: absolute;
        opacity: 0;
        transition: all 0.4s ease-in;
        text-align: center;
        bottom: 0;
        z-index: 1;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .sitepage_grid_thumb:hover .sitepage_hover_info
    {
        opacity: 1;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .sitepage_browse_thumb_details{
        margin: 10px;
    }
    .site_page_category,.site_page_bottom_info_location{
        display: block;
        margin-top: 8px;
    }
    .site_page_category img{
        vertical-align: middle;
        display: inline;
    }
    .site_page_category a {
        margin-left: 8px;
        vertical-align: middle;
        display: inline;
    }

    .site_page_bottom_info_submission{
        margin-top: 29px;
    }

</style>