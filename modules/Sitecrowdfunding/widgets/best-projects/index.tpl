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
<?php
$id = $this->identity;
$class = "slide_box_" . $id;
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headScript()->appendFile($baseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js');
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css');
$this->headScript()->appendFile($baseUrl . 'application/modules/Seaocore/externals/scripts/_class.noobSlide.packed.js');
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/favourite.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>
<?php $defaultCategoryUrl = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/category_images/icons/noicon_category.png" ?>
<?php if(empty($this->is_ajax)): ?>
    <?php if($this->categoryAtTop): ?>
        <ul class="best_projects nav_cat_<?php echo $id; ?>">
            <?php foreach($this->categories as $category): ?>
                <li>
                    <a href="javascript:void(0);" onclick="filterBestProjects<?php echo $id; ?>(event)" data-id="<?php echo $category->getIdentity(); ?>"> 
                        <?php if ($category->file_id): ?>
                            <?php $url = Engine_Api::_()->storage()->get($category->file_id)->getPhotoUrl(); ?>
                             <img src="<?php echo $url ?>" style="width: 42px; height: 42px;" alt="<?php echo $category->getTitle(); ?>">
                        <?php elseif($category->font_icon): ?>
                              <i class="fa <?php echo $category->font_icon; ?>" alt="<?php echo $category->getTitle(); ?>"></i>
                        <?php else: ?>
                             <?php $src = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/category_images/icons/noicon_category.png" ?> 
                             <img src="<?php echo $src ?>" style="width: 42px; height: 42px;" alt="<?php echo $category->getTitle(); ?>">
                        <?php endif; ?>  
                    </a>
                    <span><?php echo $category->getTitle(); ?></span>     
                </li>
            <?php endforeach; ?>    
        </ul>
    <?php endif; ?>
<?php endif; ?>
<div class='categories_manage sitecrowdfunding_horizontal_carausel sitecrowdfunding_horizontal_best_projects' id='sitecrowdfunding_best_projects_<?php echo $id; ?>' style="height: <?php echo $this->gridViewHeight; ?>px;" >
    <?php if($this->totalCount > 0) : ?>
    <div id="featured_slideshow_wrapper_<?php echo $id; ?>" class="featured_slideshow_wrapper">
        <div id="featured_slideshow_mask_<?php echo $id; ?>" class="featured_slideshow_mask" style="height:<?php echo $this->gridViewHeight; ?>px;">
            <div id="sitecrowdfunding_featured_project_im_te_advanced_box_<?php echo $id; ?>" class="featured_slideshow_advanced_box">
                <?php $i = 1; ?>
                <?php $limit = $this->rowLimit; ?>
                <?php $rowLimit = $limit; ?>
                <?php foreach ($this->paginator as $item) : ?>

                    <?php if ($i == 1): ?>
                        <div class='featured_slidebox sitecrowdfunding_featured_horizontal_slidebox <?php echo $class; ?>' style="height:<?php echo $this->gridViewHeight; ?>px;">
                            <div class='featured_slidshow_content sitecrowdfunding_featured_slidebox_content'>
                                <h3 class="ul"></h3>
                                <ul id="sitecrowdfunding_featured_slidebox_block_wrap_<?php echo $this->identity; ?>" class="projects_manage sitecrowdfunding_projects_grid_view">
                                <?php endif; ?>
                                <li class="effect2 sitecrowdfunding_featured_slidebox_block">
                                    <div class="sitecrowdfunding_featured_block sitecrowdfunding_featured_block_other sitecrowdfunding_thumb_wrapper sitecrowdfunding_thumb_viewer" style="width:<?php echo $this->gridViewWidth; ?>px; height:<?php echo $this->gridViewHeight; ?>px;">
                                        <div class="sitecrowdfunding_grid_thumb">
                                            <?php $fsContent = ""; ?>
                                            <?php if ($item->featured && in_array('featured', $this->projectOption)): ?>
                                                <?php $fsContent .= '<div class="sitecrowdfunding_featured" style="background: ' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.featuredcolor', '#f72828') . '">' . $this->translate("Featured") . '</div>'; ?>
                                            <?php endif; ?>
                                            <?php if ($item->sponsored && in_array('sponsored', $this->projectOption)): ?>
                                                <?php $fsContent .= '<div class="sitecrowdfunding_sponsored"  style="background: ' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.sponsoredcolor', '#FC0505') . '">' . $this->translate("Sponsored") . '</div>'; ?>
                                            <?php endif; ?>
                                            <?php
                                            if ($item->photo_id) {
                                                echo $this->htmlLink($item->getHref(), $fsContent . $this->itemBackgroundPhoto($item, null, null, array('tag' => 'i')), array('class' => 'sitecrowdfunding_thumb'));
                                            } else {
                                                $url = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/nophoto_project_thumb_profile.png";
                                                echo $this->htmlLink($item->getHref(), $fsContent . "<i style='background-image:url(" . $url . ")'></i>", array('class' => 'sitecrowdfunding_thumb'));
                                            }
                                            ?>

                                            <div class='sitecrowdfunding_hover_info'>
                                                <div class="sitecrowdfunding_stats sitecrowdfunding_grid_stats"> 
                                                    <?php echo $this->sitecrowdfundingShareLinks($item, $this->projectOption); ?>  
                                                    <span class="sitecrowdfunding_likes_comment_wrapper">
                                                        <?php if (in_array('like', $this->projectOption)) : ?>
                                                            <?php $count = $this->locale()->toNumber($item->likes()->getLikeCount()); ?>
                                                            <?php $countText = $this->translate(array('%s like', '%s likes', $item->like_count), $count); ?>
                                                            <span class="seaocore_icon_like" title="<?php echo $countText; ?>"><?php echo $this->translate($count); ?></span>
                                                        <?php endif; ?>

                                                        <?php if (in_array('comment', $this->projectOption)) : ?>
                                                            <?php $count = $item->comment_count; ?>
                                                            <span class="seaocore_icon_comment" title="<?php echo $this->translate(array('%s comment', '%s comments', $count), $this->locale()->toNumber($count)); ?>"><?php echo $this->translate($count); ?></span>
                                                        <?php endif; ?> 
                                                    </span>                      
                                                </div>
                                                <?php if($item->isFundingApproved()): ?>
                                                <div class="sitecrowdfunding_backers">
                                                    <?php if (in_array('backer', $this->projectOption)) : ?>
                                                        <?php $count = $item->backer_count; ?>
                                                        <?php $countText = $this->translate(array('%s backer', '%s backers', $count), $this->locale()->toNumber($count)) ?>
                                                        <span class="backers" title="<?php echo $countText ?>">
                                                            <?php echo $countText; ?> 
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="sitecrowdfunding_info_wrapper">
                                            <div class="sitecrowdfunding_info">
                                                <div class="sitecrowdfunding_bottom_info sitecrowdfunding_grid_bott_info">

                                                    <?php if (in_array('title', $this->projectOption)) : ?>
                                                        <h3><?php echo $this->htmlLink($item->getHref(), $this->string()->truncate($this->string()->stripTags($item->getTitle()), $this->titleTruncation), array('title' => $item->getTitle())) ?></h3>
                                                    <?php endif; ?>

                                                    <div class="sitecrowdfunding_grid_bottom_info">
                                                        <?php $title = ""; ?>
                                                        <?php if (in_array('owner', $this->projectOption)) : ?>
                                                            <?php
                                                            $title = $this->translate('by %s', $this->htmlLink($item->getOwner()->getHref(), $this->string()->truncate($this->string()->stripTags($item->getOwner()->getTitle()), 17), array('title' => $item->getOwner()->getTitle())));
                                                            echo $title;
                                                            ?> 
    <?php endif; ?>

                                                        <div class="sitecrowdfunding_desc" title="<?php echo $this->string()->truncate($this->string()->stripTags($item->description), 250) ?>">
    <?php echo $this->string()->truncate($this->string()->stripTags($item->description), $this->descriptionTruncation) ?>
                                                        </div>

                                                        <div class="sitecrowdfunding_bottom_info_category">
                                                            <?php $category = Engine_Api::_()->getItem('sitecrowdfunding_category', $item->category_id); ?>
                                                            <?php if ($category->file_id): ?>
                                                              <?php $url = Engine_Api::_()->storage()->get($category->file_id)->getPhotoUrl(); ?>
                                                               <img src="<?php echo $url ?>" style="width: 16px; height: 16px;" alt="">
                                                              <?php elseif($category->font_icon): ?>
                                                                <i class="fa <?php echo $category->font_icon; ?>"></i>
                                                              <?php else: ?>
                                                               <?php $src = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/category_images/icons/noicon_category.png" ?> 
                                                               <img src="<?php echo $src ?>" style="width: 16px; height: 16px;" alt="">
                                                            <?php endif; ?> 
                                                            <?php echo $this->htmlLink($category->getHref(), $category->getTitle()) ?>
                                                        </div>

                                                            <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1) && in_array('location', $this->projectOption) && $item->location) : ?>
                                                            <div class="sitecrowdfunding_bottom_info_location" title="<?php echo $item->location ?>">
                                                            <i class="seao_icon_location"></i>
                                                            <?php echo $this->string()->truncate($this->string()->stripTags($item->location), $this->truncationLocation); ?> 
                                                            </div>
    <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                             if($item->isFundingApproved()): ?>
                                            <?php
                                            $fundedAmount = $item->getFundedAmount();
                                            $fundedRatio = $item->getFundedRatio();
                                            $fundedAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($fundedAmount);
                                            ?>
                                            <?php
                                            // Add Progressvive bar
                                            echo $this->fundingProgressiveBar($fundedRatio);
                                            ?>
                                            <div class="sitecrowdfunding_funding_pledged_days_wrapper">

                                                <div class="sitecrowdfunding_funding_pledged_days">
                                                    <span>
                                                        <?php echo $fundedRatio; ?><br />
    <?php echo $this->translate("Funded "); ?>
                                                    </span>                                
                                                    <span>
                                                        <?php echo $fundedAmount; ?><br />
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
                                </li>
                                <?php if ($i == $rowLimit): ?>
                                    <?php $rowLimit = $limit; ?>
                                    <?php echo "</li>"; ?>
        <?php $i = 0; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?> 
                    <?php $i++; ?>
                <?php endforeach; ?>
                <?php if ($i > 1): ?>
                    <?php if ($i <= $rowLimit) : ?>
                        <?php echo "</ul></div></div>"; ?>
                    <?php endif; ?>
<?php endif; ?>
            </div>
        </div>
        <div class="bestprojects_controls">
            <div id="sitecrowdfunding_featured_project_prev8_div_<?php echo $id; ?>" style="display:<?php echo $this->showPagination ? 'block' : 'none' ?> ;" >
                <span id="sitecrowdfunding_featured_project_prev8_<?php echo $id; ?>" class="featured_slideshow_controllers-prev featured_slideshow_controllers prev" title=<?php echo $this->translate("Previous") ?> ></span>
            </div>
            <div id="sitecrowdfunding_featured_project_next8_div_<?php echo $id; ?>" style="display:<?php echo $this->showPagination ? 'block' : 'none' ?>;" >
                <span id="sitecrowdfunding_featured_project_next8_<?php echo $id; ?>" class="featured_slideshow_controllers-next featured_slideshow_controllers" title=<?php echo $this->translate("Next") ?> ></span>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="p5">        
        <?php echo $this->translate("No Project found in the Selected Category"); ?>
    </div>
<?php endif ?>
    <div class="sitecrowdfunding_best_projects_loding_image p5" style="display: none; text-align: center;">
        <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/loading.gif'/>
    </div>
</div>
<div id="bestprojects_hidden_ajax_div"></div>
<script type="text/javascript">

    <?php if(empty($this->is_ajax)):?>
        en4.core.runonce.add(bestProjectSlideshow<?php echo $id;?> ());
    <?php endif; ?>
    function bestProjectSlideshow<?php echo $id;?> () {
        <?php if($this->totalCount == 0): ?>
            return false;
        <?php endif; ?>

        <?php if ($this->showPagination) : ?>
            $("sitecrowdfunding_featured_project_next8_<?php echo "$id"; ?>").style.display = "block";
            $("sitecrowdfunding_featured_project_prev8_<?php echo "$id"; ?>").style.display = "block";
        <?php else : ?>
            $("sitecrowdfunding_featured_project_next8_<?php echo "$id"; ?>").style.display = "none";
            $("sitecrowdfunding_featured_project_prev8_<?php echo "$id"; ?>").style.display = "none";
        <?php endif; ?>

        if (document.getElementsByClassName == undefined) {
            document.getElementsByClassName = function (className)
            {
                var hasClassName = new RegExp("(?:^|\\s)" + className + "(?:$|\\s)");
                var allElements = document.getElementsByTagName("*");
                var results = [];

                var element;
                for (var i = 0; (element = allElements[i]) != null; i++) {
                    var elementClass = element.className;
                    if (elementClass && elementClass.indexOf(className) != -1 && hasClassName.test(elementClass))
                        results.push(element);
                }

                return results;
            }
        }
        SlideShow = function ()
        {
            this.width = 0;
            this.slideElements = [];
            this.noOfSlideShow = 0;
            this.id = 0;
            this.handles8_more = '';
            this.handles8 = '';
            this.interval = 0;
            this.autoPlay = 0;
            this.slideBox = '';
            this.set = function (arg)
            {
                this.noOfSlideShow = arg.noOfSlideShow;
                this.id = arg.id;
                this.interval = arg.interval;
                this.slideBox = arg.slideBox;
                var globalContentElement = en4.seaocore.getDomElements('content');
                this.width = $(globalContentElement).getElement("#featured_slideshow_wrapper_" + this.id).clientWidth;
                $(globalContentElement).getElement("#featured_slideshow_mask_" + this.id).style.width = this.width + "px";
                $(globalContentElement).getElement("#sitecrowdfunding_featured_slidebox_block_wrap_" + this.id).style.width = this.width + "px";
                this.slideElements = document.getElementsByClassName(this.slideBox);
                for (var i = 0; i < this.slideElements.length; i++)
                    this.slideElements[i].style.width = this.width + "px";
                this.handles8_more = $$('#handles8_more_' + this.id + ' span');
                this.handles8 = $$('#handles8_' + this.id + ' span');
                this.autoPlay = arg.autoPlay;

            }
            this.walk = function ()
            {
                var uid = this.id;
                var noOfSlideShow = this.noOfSlideShow;
                var handles8 = this.handles8;
                var nS8 = new noobSlide({
                    box: $('sitecrowdfunding_featured_project_im_te_advanced_box_' + this.id),
                    items: $$('#sitecrowdfunding_featured_project_im_te_advanced_box_' + this.id + ' h3.ul'),
                    size: this.width,
                    handles: this.handles8,
                    addButtons: {previous: $('sitecrowdfunding_featured_project_prev8_' + this.id), next: $('sitecrowdfunding_featured_project_next8_' + this.id)},
                    interval: this.interval,
                    fxOptions: {
                        duration: 500,
                        transition: '',
                        wait: false,
                    },
                    autoPlay: this.autoPlay,
                    mode: 'horizontal',
                    onWalk: function (currentItem, currentHandle) {
                      if(this.items.length == 1) {
                          $('sitecrowdfunding_featured_project_prev8_<?php echo $id; ?>').addClass('nocontent_slider_arrow');
                          $('sitecrowdfunding_featured_project_next8_<?php echo $id; ?>').addClass('nocontent_slider_arrow');
                      } else {
                          if(this.currentIndex == 0) {
                              $('sitecrowdfunding_featured_project_prev8_<?php echo $id; ?>').addClass('nocontent_slider_arrow');
                              $('sitecrowdfunding_featured_project_next8_<?php echo $id; ?>').removeClass('nocontent_slider_arrow'); 
                          } else if(this.currentIndex == this.items.length - 1) { 
                              $('sitecrowdfunding_featured_project_next8_<?php echo $id; ?>').addClass('nocontent_slider_arrow');
                              $('sitecrowdfunding_featured_project_prev8_<?php echo $id; ?>').removeClass('nocontent_slider_arrow');
                          } else { 
                              $('sitecrowdfunding_featured_project_prev8_<?php echo $id; ?>').removeClass('nocontent_slider_arrow');
                              $('sitecrowdfunding_featured_project_next8_<?php echo $id; ?>').removeClass('nocontent_slider_arrow');
                          }
                      }
                    }
                });
                //more handle buttons
                nS8.addHandleButtons(this.handles8_more);
                //walk to item 3 witouth fx
                nS8.walk(0, false, true);
            }
        }

        var slideshow = new SlideShow();
        slideshow.set({
            id: '<?php echo $id; ?>',
            noOfSlideShow: 0,
            interval: 3500,
            autoPlay: 0,
            slideBox: '<?php echo $class; ?>'
        });
        slideshow.walk();
    }
</script>

<?php if(empty($this->is_ajax) && $this->categoryAtTop): ?>
    <script type="text/javascript">
        window.addEvent('domready',function () {
            link = $$('.nav_cat_<?php echo $id; ?> li > a')[0];
            if(link) {
                link.parentNode.addClass('active');
                link.addClass('active'); 
            }
        })
        function filterBestProjects<?php echo $id; ?> (event) {
            if(event.target.get('tag') == 'a')
                link = event.target;
            else
                link = event.target.parentElement;
            var category_id = link.get('data-id');
            $$('#sitecrowdfunding_best_projects_<?php echo $id; ?> >div').setStyle('display','none');
            $$('.sitecrowdfunding_best_projects_loding_image').setStyle('display','block');

            var request = new Request.HTML({
                url: en4.core.baseUrl + 'widget',
                data: $merge(<?php echo json_encode($this->params); ?>, {
                    format: 'html',
                    subject: en4.core.subject.guid,
                    is_ajax_load: true,
                    'content_id': '<?php echo $id; ?>',
                    'category_id': category_id,
                    'is_ajax' : 1
                }),
                onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                    $('bestprojects_hidden_ajax_div').set('html',responseHTML);
                    contentHtml = $('bestprojects_hidden_ajax_div').getElement('#sitecrowdfunding_best_projects_<?php echo $id; ?>').get('html');
                    $('sitecrowdfunding_best_projects_<?php echo $id; ?>').set('html',contentHtml);
                    $('bestprojects_hidden_ajax_div').set('html','');
                    bestProjectSlideshow<?php echo $id;?>();
                    $$('.sitecrowdfunding_best_projects_loding_image').setStyle('display','none');
                }
            });
            request.send();
            $$('.nav_cat_<?php echo $id; ?> li').removeClass('active');
            $$('.nav_cat_<?php echo $id; ?> li a').removeClass('active');
            link.parentNode.addClass('active');
            link.addClass('active');
        }
    </script>
<?php endif; ?>
