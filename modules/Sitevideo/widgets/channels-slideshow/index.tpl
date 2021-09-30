<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/favourite.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/scripts/core.js'); ?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css');
if (empty($this->is_ajax)) :
    $this->headScript()->appendFile($baseUrl . 'application/modules/Seaocore/externals/scripts/_class.noobSlide.packed.js');
endif;
?>
<?php
$linkOption = array();
$sideLinkOption = array();
if (in_array('facebook', $this->channelOption))
    $linkOption[] = 'facebook';
if (in_array('twitter', $this->channelOption))
    $linkOption[] = 'twitter';
if (in_array('linkedin', $this->channelOption))
    $linkOption[] = 'linkedin';
if (in_array('googleplus', $this->channelOption))
    $linkOption[] = 'googleplus';
if (in_array('like', $this->channelOption))
    $sideLinkOption[] = 'like';
if (in_array('favourite', $this->channelOption))
    $sideLinkOption[] = 'favourite';
if (in_array('subscribe', $this->channelOption))
    $sideLinkOption[] = 'subscribe';
?>
<?php if ($this->totalCount > 0) : ?>
    <div class='categories_manage' id='categories_manage' >
        <?php foreach ($this->paginator as $category) : ?>
            <?php $id = $category->getIdentity(); ?>
            <?php $channelFilter = array('category_id' => $id, 'orderby' => $this->channelOrderby, 'selectLimit' => $this->channelCount); ?>
            <?php $channelModels = Engine_Api::_()->getDbTable('channels', 'sitevideo')->getChannel($channelFilter); ?>
            <?php
            $channelCount = count($channelModels);
            if ($channelCount == 0) :
                continue;
            endif;
            $categoryTitle = $this->htmlLink($category->getHref(), $category->getTitle());
            if($this->showLink) :
                $totalChannel = Engine_Api::_()->getDbTable('channels', 'sitevideo')->getChannelsCount(array('columnName'=>'category_id','category_id'=>$id));
                $url = $this->url(array('action' => 'browse','category_id' => $category->getIdentity(), 'categoryname' => $category->getCategorySlug()), "sitevideo_general");
                $categoryTitle .=" ($totalChannel)".$this->htmlLink($url, ' + '.$this->translate('See all channels'));
            endif;
            $this->categorieIds[$id] = $channelCount;
            ?>
            <div class="sitevideo_channel_slideshow">
                <h3 class="channel_slide_cat_title">
                    <?php echo $categoryTitle; ?>
                </h3>
                <div id="featured_slideshow_wrapper_<?php echo $id; ?>" class="featured_slideshow_wrapper">
                    <div class="sitevideo_slide_thumbs" id="handles8_<?php echo $id; ?>">
                        <?php
                        $content = "";
                        $image_count = 1;
                        ?>
                        <?php foreach ($channelModels as $type => $channel) : ?>
                            <?php $span = ""; ?>
                            <?php if ($channel->file_id): ?>
                                <?php $span .="<span class='inactive' title='" . $channel->getTitle() . "'>" . $this->itemPhoto($channel, 'thumb.icon', array()) . "</span>"; ?>
                            <?php else: ?>
                                <?php $span .="<span class='inactive'></span>"; ?>
                            <?php endif; ?>
                            <?php echo $span; ?>
                        <?php endforeach; ?>

                    </div>
                    <div id="featured_slideshow_mask_<?php echo $id; ?>" class="featured_slideshow_mask sitevideo_slideshow_container">
                        <div id="sitevideo_featured_channel_im_te_advanced_box_<?php echo $id; ?>" class="featured_slideshow_advanced_box">
                            <?php foreach ($channelModels as $type => $channel) : ?>
                                <?php
                                $videoFilter = array('channel_id' => $channel->channel_id, 'orderby' => $this->videoOrderby, 'selectLimit' => $this->videoCount);
                                $videoModels = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getVideos($videoFilter);
                                ?>
                                <div class='featured_slidebox featured_slidebox_box'>
                                    <div class='featured_slidshow_img'>

                                        <?php if ($channel->file_id): ?>
                                            <?php echo $this->htmlLink($channel->getHref(), $this->itemBackgroundPhoto($channel, 'thumb.profile', null, array('tag' => 'i'))); ?>
                                        <?php else : ?>
                                            <?php echo $this->htmlLink($channel->getHref()); ?>
                                        <?php endif; ?>

                                        <span class="sitevideo_bottom_info_videoscount">
                                            <?php if (in_array('numberOfVideos', $this->channelOption)) : ?>
                                                <?php echo $this->translate(array('%s video', '%s videos', $channel->videos_count), $this->locale()->toNumber($channel->videos_count)); ?>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    <div class='featured_slidshow_content sitevideo_featured_slide_content'>
                                        <!--don't remove this h3 tag. This makes an item list. Used in Slider Js-->
                                        <h3></h3>
                                        <div class='channelInfo'>
                                            <?php if (in_array('title', $this->channelOption)): ?>
                                                <div class="sitevideo_slide_title">
                                                    <h4>
                                                        <?php echo $this->htmlLink($channel->getHref(), $this->string()->truncate($this->string()->stripTags($channel->getTitle()), $this->titleTruncation)); ?>
                                                    </h4>
                                                </div>
                                            <?php endif; ?>
                                            <div class="sitevideo_stats">
                                                <?php if (in_array('owner', $this->channelOption)) : ?>
                                                    <?php
                                                    $owner = $channel->getOwner();
                                                    ?>
                                                    <?php echo $this->translate('Created by'); ?>
                                                    <span class='site_video_author_name'>
                                                        <?php echo $this->translate('%s', $this->htmlLink($owner->getHref(), $owner->getTitle())); ?>
                                                    </span>
                                                <?php endif; ?>
                                                <?php if (in_array('subscribe', $this->channelOption)) : ?>
                                                    <?php $count = $this->locale()->toNumber($channel->subscribe_count); ?>
                                                    <?php $countText = $this->translate(array('%s subscriber', '%s subscribers', $channel->subscribe_count), $count); ?>
                                                    <span class="sitevideo_bottom_info_subscribers" title="<?php echo $countText; ?>">
                                                        <?php echo $count; ?> 
                                                    </span>
                                                <?php endif; ?>
                                                <?php if (in_array('like', $this->channelOption)) : ?>
                                                    <?php $count = $this->locale()->toNumber($channel->likes()->getLikeCount()); ?>
                                                    <?php $countText = $this->translate(array('%s like', '%s likes', $channel->like_count), $count); ?>
                                                    <span class="sitevideo_bottom_info_likes" title="<?php echo $countText; ?>">
                                                        <?php echo $count; ?>    
                                                    </span>
                                                <?php endif; ?>
                                                <?php if (in_array('comment', $this->channelOption)) : ?>
                                                    <?php $count = $this->locale()->toNumber($channel->comments()->getCommentCount()); ?>
                                                    <?php $countText = $this->translate(array('%s comment', '%s comments', $channel->comment_count), $count); ?>
                                                    <span class="sitevideo_bottom_info_comment" title="<?php echo $countText; ?>">
                                                        <?php echo $count; ?>   
                                                    </span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="sitevideo_desc sitevideo_channel_slide_social">
                                                <?php if (count($linkOption) > 0) : ?>
                                                    <div class="sitevideo_channel_social_1">
                                                        <?php $this->shareLinks($channel, $linkOption); ?>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="sitevideo_channel_social_2">
                                                    <?php $this->shareLinks($channel, $sideLinkOption); ?>
                                                </div>

                                            </div>

                                            <div class="sitevideo_channel_desc sitevideo_list_desc">
                                                <?php echo $this->string()->truncate($this->string()->stripTags($channel->description), $this->descriptionTruncation) ?>
                                            </div>
                                            <div class='sitevideo_channel_slide_videos'>
                                                <h5 class="slideshow_preview">Preview</h5>
                                                <div class="channel_slideshow_videos_container">
                                                    <?php if (count($videoModels) > 0) : ?>
                                                        <?php foreach ($videoModels as $video) : ?>

                                                            <span class="sitevideo_channel_slide_videos_thumb">
                                                                <?php
                                                                if ($video->photo_id) {
                                                                    echo $this->htmlLink($video->getHref(), $this->itemPhoto($video, 'thumb.normal'));
                                                                } else {
                                                                    echo $this->htmlLink($video->getHref(), '');
                                                                }
                                                                ?>
                                                            </span>
                                                        <?php endforeach; ?>
                                                    <?php else : ?>
                                                        <div class="tip">
                                                            <span>
                                                                <?php echo $this->translate('You do not have any video on this channel.'); ?>
                                                            </span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="featured_slideshow_option_bar sitevideo_channel_carausel_navigations">
                        <div>
                            <p class="buttons">
                                <span id="sitevideo_featured_channel_prev8_<?php echo $id; ?>" class="featured_slideshow_controllers-prev featured_slideshow_controllers prev" title=<?php echo $this->translate("Previous") ?> ></span>
                                <span id="sitevideo_featured_channel_next8_<?php echo $id; ?>" class="featured_slideshow_controllers-next featured_slideshow_controllers" title=<?php echo $this->translate("Next") ?> ></span>
                            </p>
                        </div>

                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if (empty($this->is_ajax)) : ?>

        <div class = "seaocore_view_more mtop10" id="seaocore_view_more">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => '', 'class' => 'buttonlink icon_viewmore')); ?>
        </div>
        <div class="seaocore_view_more" id="loding_image" style="display: none;">
            <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' style='margin-right: 5px;' />
            <?php echo $this->translate("Loading ...") ?>
        </div>
        <div id="hideResponse_div"> </div>
    <?php endif; ?>
<?php else : ?>
    <div class="tip">
        <span>
            <?php $url = $this->url(array('action' => 'create'), "sitevideo_general", true); ?>
            <?php if ($this->categoryId) : ?>
                <?php echo $this->translate('You do not have any channels created in this category. '); ?>
            <?php else : ?>
                <?php echo $this->translate('You do not have any channels created in any category. '); ?>
            <?php endif; ?>
            <?php if ($this->can_create) : ?>
                <?php echo $this->translate('Please %1$sclick here%2$s to create new channel.', '<a href="' . $url . '">', '</a>'); ?>
            <?php endif; ?>
        </span>
    </div>
<?php endif; ?>
<script type="text/javascript">


    en4.core.runonce.add(function () {

<?php if (empty($this->is_ajax)) : ?>
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
                this.set = function (arg)
                {
                    this.noOfSlideShow = arg.noOfSlideShow;
                    this.id = arg.id;
                    var globalContentElement = en4.seaocore.getDomElements('content');
                    this.width = $(globalContentElement).getElement("#featured_slideshow_wrapper_" + this.id).clientWidth;
                    $(globalContentElement).getElement("#featured_slideshow_mask_" + this.id).style.width = (this.width) + "px";
                    this.slideElements = document.getElementsByClassName('featured_slidebox_box');
                    for (var i = 0; i < this.slideElements.length; i++)
                        this.slideElements[i].style.width = (this.width) + "px";
                    this.handles8_more = $$('#handles8_more_' + this.id + ' span');
                    this.handles8 = $$('#handles8_' + this.id + ' span');
                }
                this.walk = function ()
                {
                    var uid = this.id;
                    var noOfSlideShow = this.noOfSlideShow;
                    var handles8 = this.handles8;
                    var nS8 = new noobSlide({
                        box: $('sitevideo_featured_channel_im_te_advanced_box_' + this.id),
                        items: $$('#sitevideo_featured_channel_im_te_advanced_box_' + this.id + ' h3'),
                        size: (this.width),
                        handles: this.handles8,
                        addButtons: {previous: $('sitevideo_featured_channel_prev8_' + this.id), next: $('sitevideo_featured_channel_next8_' + this.id)},
                        interval: 5000,
                        fxOptions: {
                            duration: 500,
                            transition: '',
                            wait: false,
                        },
                        autoPlay: false,
                        mode: 'horizontal',
                        onWalk: function (currentItem, currentHandle) {
                            $$(this.handles, handles8).removeClass('active');
                            $$(currentHandle, handles8[this.currentIndex]).addClass('active');
                            if ((this.currentIndex + 1) == (this.items.length))
                                $('sitevideo_featured_channel_next8_' + uid).hide();
                            else
                                $('sitevideo_featured_channel_next8_' + uid).show();

                            if (this.currentIndex > 0)
                                $('sitevideo_featured_channel_prev8_' + uid).show();
                            else
                                $('sitevideo_featured_channel_prev8_' + uid).hide();
                        }
                    });
                    //more handle buttons
                    nS8.addHandleButtons(this.handles8_more);
                    //walk to item 3 witouth fx
                    nS8.walk(0, false, true);
                }
            }

<?php endif; ?>
<?php foreach ($this->categorieIds as $key => $value) : ?>
            var slideshow<?php echo $key; ?> = new SlideShow();
            slideshow<?php echo $key; ?>.set({
                id: '<?php echo $key ?>',
                noOfSlideShow: <?php echo $value; ?>
            });
            slideshow<?php echo $key; ?>.walk();
<?php endforeach; ?>
    });

</script>
<?php if (empty($this->is_ajax)) : ?>
    <script type="text/javascript">
        function viewMorePlaylist(viewFormat)
        {
            $('seaocore_view_more').style.display = 'none';
            $('loding_image').style.display = '';
            var params = {
                requestParams:<?php echo json_encode($this->params) ?>
            };
            en4.core.request.send(new Request.HTML({
                method: 'get',
                'url': en4.core.baseUrl + 'widget/index/mod/sitevideo/name/channels-slideshow',
                data: $merge(params.requestParams, {
                    format: 'html',
                    subject: en4.core.subject.guid,
                    page: getNextPage(),
                    is_ajax: 1,
                    loaded_by_ajax: true,
                    categorieIds: getCategories()
                }),
                evalScripts: true,
                onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                    $('hideResponse_div').innerHTML = responseHTML;
                    var categorycontainer = $('hideResponse_div').getElement('.categories_manage').innerHTML;
                    $('categories_manage').innerHTML = $('categories_manage').innerHTML + categorycontainer;
                    $('loding_image').style.display = 'none';
                    $('hideResponse_div').innerHTML = "";


                }
            }));
            return false;
        }
    </script>
<?php endif; ?>

<?php if ($this->showContent == 3): ?>
    <script type="text/javascript">
        en4.core.runonce.add(function () {
            hideViewMoreLink('<?php echo $this->showContent; ?>');
        });
    </script>
<?php elseif ($this->showContent == 2): ?>
    <script type="text/javascript">
        en4.core.runonce.add(function () {
            hideViewMoreLink('<?php echo $this->showContent; ?>');
        });
    </script>
<?php else: ?>
    <script type="text/javascript">
        en4.core.runonce.add(function () {
            $('seaocore_view_more').style.display = 'none';
        });
    </script>
    <?php
    echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitevideo"), array("orderby" => $this->orderby));
    ?>
<?php endif; ?>

<script type="text/javascript">

    var pageAction = function (page) {
        window.location.href = en4.core.baseUrl + 'sitevideo/index/index/page/' + page;
    }
    var getCategories = function ()
    {
        return '<?php echo Zend_Json::encode($this->categorieIds); ?>';
    }

    function getNextPage() {
        return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
    }

    function hideViewMoreLink(showContent) {

        if (showContent == 3) {
            $('seaocore_view_more').style.display = 'none';
            var totalCount = '<?php echo $this->paginator->count(); ?>';
            var currentPageNumber = '<?php echo $this->paginator->getCurrentPageNumber(); ?>';

            function doOnScrollLoadChannel()
            {
                if (typeof ($('seaocore_view_more').offsetParent) != 'undefined') {
                    var elementPostionY = $('seaocore_view_more').offsetTop;
                } else {
                    var elementPostionY = $('seaocore_view_more').y;
                }
                if (elementPostionY <= window.getScrollTop() + (window.getSize().y - 40)) {

                    if ((totalCount != currentPageNumber) && (totalCount != 0))
                        viewMorePlaylist();
                }
            }
            window.onscroll = doOnScrollLoadChannel;

        } else if (showContent == 2) {

            var view_more_content = $('seaocore_view_more');
            view_more_content.setStyle('display', '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');
            view_more_content.removeEvents('click');
            view_more_content.addEvent('click', function () {
                viewMorePlaylist();
            });
        }
    }
</script>
