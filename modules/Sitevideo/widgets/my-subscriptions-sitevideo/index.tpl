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
<?php echo $this->form->render($this) ?>
<?php if ($this->paginator->getTotalItemCount() > 0) : ?>
    <ul class='videos_manage' id='videos_manage'>
        <?php foreach ($this->paginator as $subscription): ?>
            <?php
            $item = $subscription->getChannelModel();
            if (!$item)
                continue;
            ?>
            <li>
                <div class="video_thumb_wrapper">
                    <?php
                    //CHECKING FOR CHANNEL THUMBNAIL
                    if ($item->file_id) {
                        //FIND THE THUMBNAIL 
                        echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal'));
                    } else {
                        echo '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/images/video_default.png">';
                    }
                    ?>
                </div>
                <div class='video_options'>
                    <?php
                    echo $this->htmlLink(array(
                        'module' => 'sitevideo',
                        'controller' => 'subscription',
                        'action' => 'notification-settings',
                        'route' => 'default',
                        'channel_id' => $item->channel_id,
                        'format' => 'smoothbox'
                            ), $this->translate("Notification Settings"), array(
                        'class' => 'smoothbox'
                            //'class' => 'buttonlink smoothbox icon_report'
                    ));
                    ?>
                    <?php
                    echo $this->htmlLink(array(
                        'module' => 'sitevideo',
                        'controller' => 'subscription',
                        'action' => 'unsubscribe',
                        'route' => 'default',
                        'channel_id' => $item->channel_id,
                        'format' => 'smoothbox'
                            ), $this->translate("Unsubscribe"), array(
                        'class' => 'smoothbox'
                            //'class' => 'buttonlink smoothbox icon_report'
                    ));
                    ?>
                </div>
                <div class="video_info">
                    <h3>
                        <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
                    </h3>
                    <div class="video_desc">
                        <?php echo $this->string()->truncate($this->string()->stripTags($item->description), 300) ?>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
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
<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate('You do not have any subscribers.'); ?>
        </span>
    </div>

<?php endif; ?>
<?php if (empty($this->is_ajax)) : ?>
    <script type="text/javascript">
        function viewMoreChannels()
        {
            $('seaocore_view_more').style.display = 'none';
            $('loding_image').style.display = '';
            var params = {
                requestParams:<?php echo json_encode($this->params) ?>
            };
            en4.core.request.send(new Request.HTML({
                method: 'get',
                'url': en4.core.baseUrl + 'widget/index/mod/sitevideo/name/my-subscriptions-sitevideo',
                data: $merge(params.requestParams, {
                    format: 'html',
                    subject: en4.core.subject.guid,
                    page: getNextPage(),
                    isajax: 1,
                    loaded_by_ajax: true
                }),
                evalScripts: true,
                onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                    $('hideResponse_div').innerHTML = responseHTML;
                    var videocontainer = $('hideResponse_div').getElement('.videos_manage').innerHTML;
                    $('videos_manage').innerHTML = $('videos_manage').innerHTML + videocontainer;
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
        window.location.href = en4.core.baseUrl + 'sitevideo/subscription/manage/page/' + page;
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
                        viewMoreChannels();
                }
            }
            window.onscroll = doOnScrollLoadChannel;

        } else if (showContent == 2) {
            var view_more_content = $('seaocore_view_more');
            view_more_content.setStyle('display', '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');
            view_more_content.removeEvents('click');
            view_more_content.addEvent('click', function () {
                viewMoreChannels();
            });
        }
    }
</script>
