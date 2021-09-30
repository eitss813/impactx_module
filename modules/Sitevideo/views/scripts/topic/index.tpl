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

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css'); ?>
<?php
//include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/Adintegration.tpl';
?>
<div class="layout_middle">
<div class="generic_layout_container">
<div class="sitevideo_view_top">
    <?php echo $this->htmlLink($this->sitevideo->getHref(), $this->itemPhoto($this->sitevideo, 'thumb.icon', '', array('align' => 'left'))) ?>
    <p>	
        <?php echo $this->sitevideo->__toString() ?>	
        <?php echo $this->translate('&raquo; '); ?>
        <?php echo $this->htmlLink($this->sitevideo->getHref(array('tab' => $this->tab_selected_id)), $this->translate('Discussions')) ?>
    </p>
</div>


<div class="sitevideo_topic_view mtop10">
    <div class="sitevideo_discussion_thread_options">

        <?php echo $this->htmlLink($this->sitevideo->getHref(), $this->translate("Back to Channels"), array('class' => 'sitevideo_icon_back')) ?>
        <?php
        if ($this->can_post) {
            echo $this->htmlLink(array('route' => "sitevideo_topic_extended", 'controller' => 'topic', 'action' => 'create', 'channel_id' => $this->sitevideo->getIdentity(), 'content_id' => $this->tab_selected_id), $this->translate('Post New Topic'), array('class' => 'seaocore_icon_add'));
        }
        ?>
    </div>

    <?php if ($this->paginator->count() > 1): ?>
        <div>
            <br />
            <?php echo $this->paginationControl($this->paginator) ?>
            <br />
        </div>
    <?php endif; ?>
		
    <div class="sitevideo_sitevideos_sitevideo mtop10">
    <ul class="sitevideo_sitevideos">
        <?php foreach ($this->paginator as $topic): ?>
            <?php
            $lastpost = $topic->getLastPost();
            $lastposter = Engine_Api::_()->getItem('user', $topic->lastposter_id); 
            ?>
            <li>

                <div class="sitevideo_sitevideos_replies seaocore_txt_light">
                    <span>
                        <?php echo $this->locale()->toNumber($topic->post_count - 1) ?>
                    </span>
                    <?php echo $this->translate(array('reply', 'replies', $topic->post_count - 1)) ?>
                </div>

                <div class="sitevideo_sitevideos_lastreply">
                    <?php echo $this->htmlLink($lastposter->getHref(), $this->itemPhoto($lastposter, 'thumb.icon')) ?>
                    <div class="sitevideo_sitevideos_lastreply_info">
                        <?php echo $this->htmlLink($lastpost->getHref(), $this->translate('Last Post')) ?> <?php echo $this->translate('by'); ?> <?php echo $lastposter->__toString() ?>
                        <br />
                        <?php echo $this->timestamp(strtotime($topic->modified_date), array('tag' => 'div', 'class' => 'siteevent_siteevents_lastreply_info_date seaocore_txt_light')) ?>
                    </div>
                </div>

                <div class="sitevideo_sitevideos_info">
                    <p<?php if ( $topic->sticky): ?> class='sitevideo_icon_sticky'<?php endif; ?>>
                        <?php echo $this->htmlLink($topic->getHref(), $topic->getTitle()) ?>
                    </p>
                    <div class="sitevideo_sitevideos_blurb">
                        <?php echo $this->viewMore(strip_tags($topic->getDescription())) ?>
                    </div>
                </div>

            </li>
        <?php endforeach; ?>
    </ul>
    </div>

    <?php if ($this->paginator->count() > 1): ?>
        <div>
            <?php echo $this->paginationControl($this->paginator) ?>
        </div>
    <?php endif; ?>

</div>
</div>
</div>