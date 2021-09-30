<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Activity/externals/styles/custom-activity.css');?>
<?php
  $this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'externals/mdetect/mdetect' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js')
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/composer.js');
?>

<div class="activity_container">
    <?php if(!empty($this->user_id)):?>
        <div class="activity-update-form">
            <form name="whats-new-form" method="post" id="whats-new-form" class="activity-form">
                <div id="whats-new-avatar">
                    <a class="activity-post-avatar" href="<?php echo $this->user->getHref();?>">
                        <?php echo $this->htmlLink($this->user->getHref(), $this->itemPhoto($this->user, 'thumb.icon',
                        $this->user->getTitle()), array('title'=>$this->user->getTitle())) ?>
                        <span class="user-name">
                        <?php echo $this->htmlLink($this->user->getHref(), $this->user->getTitle(), array('target' => '_blank')) ?>
                    </span>
                    </a>
                </div>
                <div id="whats-new-content">
                    <div id="whats-new-textarea">
                    <textarea
                            id="whats-new-body"
                            cols="1"
                            rows="10"
                            name="body"
                            placeholder="<?php echo $this->escape($this->translate('Post Something...')) ?>">
                    </textarea>
                    </div>
                </div>
                <div id="activity-form-submit-wrapper">
                    <div id="whats-new-options">
                        <div id="compose-menu" class="compose-menu">
                            <div class="compose-right-content">
                                <script type="text/javascript">
                                    var composeInstance;
                                    en4.core.runonce.add(function() {
                                        composeInstance = new Composer('whats-new-body', {
                                            menuElement : 'compose-menu',
                                            hashtagEnabled : '<?php echo $this->hashtagEnabled ?>',
                                            baseHref : '<?php echo $this->baseUrl() ?>',
                                            lang : {
                                                'Post Something...' : '<?php echo $this->string()->escapeJavascript($this->translate('Post Something...')) ?>'
                                            },
                                            submitCallBack : en4.activity.post
                                        });
                                    });
                                </script>
                                <?php foreach( $this->composePartials as $partial ): ?>
                                <?php if (false !== strpos($partial[0], '_composeTag') && !in_array('userTags', $this->composerOptions)) {
                                continue;
                                }?>
                                <?php echo $this->partial($partial[0], $partial[1], array(
                                'composerType' => 'activity'
                                )) ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div id="whats-new-submit">
                        <input type="submit"
                               id="compose-submit"
                               class="button"
                               value="Post">
                    </div>
                </div>
            </form>
        </div>
    <?php endif ?>
    <div class="action_list">
        <?php echo $this->customActivityLoop($this->activity, array(
        'action_id' => $this->action_id,
        'viewAllComments' => $this->viewAllComments,
        'viewAllLikes' => $this->viewAllLikes,
        'similarActivities' => $this->similarActivities,
        'getUpdate' => $this->getUpdate,
        'viewMaxPhoto' => $this->viewMaxPhoto,
        'hashtag' => $this->hashtag
        )); ?>
    </div>
    <div class="feed_viewmore" id="feed_viewmore" style="display: none;">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
        'id' => 'feed_viewmore_link',
        'class' => 'buttonlink icon_viewmore'
        )) ?>
    </div>

    <div class="feed_viewmore" id="feed_loading" style="display: none;">
        <i class="fa-spinner fa-spin fa"></i>
        <?php echo $this->translate("Loading ...") ?>
    </div>
</div>


<div id="hidden_activity_ajax_data" style="display: none;"></div>

<script type="text/javascript">
    en4.core.runonce.add(function() {

        var activity_count = <?php echo sprintf('%d', $this->activityCount) ?>;
        var next_id = <?php echo sprintf('%d', $this->nextid) ?>;
        var subject_guid = '<?php echo $this->subjectGuid ?>';
        var endOfFeed = <?php echo ( $this->endOfFeed ? 'true' : 'false' ) ?>;

        var activityViewMore = window.activityViewMore = function(next_id, subject_guid) {
            if( en4.core.request.isRequestActive() ) return;

            var url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'content_id' => $this->identity), 'default', true) ?>';
            $('feed_viewmore').style.display = 'none';
            $('feed_loading').style.display = '';

            var request = new Request.HTML({
                url : url,
                data : {
                    format : 'html',
                    'maxid' : next_id,
                    'feedOnly' : true,
                    'nolayout' : true,
                    'subject' : subject_guid,
                    'search' : '<?php echo $this->search ?>',
                    'isHashtagPage' : '<?php echo $this->isHashtagPage ?>',
                },
                evalScripts : true,
                onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
                    $('hidden_activity_ajax_data').innerHTML = responseHTML;
                    var refreshedHtml =  $('hidden_activity_ajax_data').getElement('#activity-feed').innerHTML;
                    Elements.from(refreshedHtml).inject($('activity-feed'));
                    $('hidden_activity_ajax_data').innerHTML = '';
                    en4.core.runonce.trigger();
                    Smoothbox.bind($('activity-feed'));
                }
            });
            request.send();
        }

        if( next_id > 0 && !endOfFeed ) {
            $('feed_viewmore').style.display = '';
            $('feed_loading').style.display = 'none';
            $('feed_viewmore_link').removeEvents('click').addEvent('click', function(event){
                event.stop();
                activityViewMore(next_id, subject_guid);
            });
        } else {
            $('feed_viewmore').style.display = 'none';
            $('feed_loading').style.display = 'none';
        }

    });
</script>