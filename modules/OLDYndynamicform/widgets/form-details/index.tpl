<div class="yndform_form_detail_info">
    <div class="yndform_form_detail_info_title">
        <h3 class="h3"><?php echo $this -> form -> getTitle() ?></h3>
    </div>
    <div class="yndform_form_category_entries">
        <div class="yndform_form_category_parent">
            <span class="ynicon yn-folder"></span>
            <?php $category = $this -> form -> getCategory() ?>
            <?php foreach ($category -> getBreadCrumNode() as $node): ?>
                <?php if ($node -> level != 0): ?>
                    <?php echo $this->htmlLink(
                        $node->getHref(),
                        $this->string()->truncate($this->translate($node->getTitle()), 50),
                        array('title' => $this->translate($node->getTitle()))); ?>
                    <span class="yndform_slash">&#47;</span><span class="yndform_backslash">&#92;</span>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php
            if (count($category -> getBreadCrumNode()) > 0):
            echo $this->htmlLink(
                $category->getHref(),
                $this->string()->truncate($this->translate($category->getTitle()), 50),
                array('title' => $this->translate($category->getTitle())));
            else:
                echo $this -> translate("Uncategoried");
            endif; ?>
        </div>
        <i class="yn_dots">.</i>
        <div class="yndform_form_detail_info_creation_date">
            <?php echo $this->translate('Created on %1$s', $this -> timestamp($this -> form->creation_date)) ?>
        </div>
    </div>
    <div class="yndform_form_detail_info_description">
        <?php echo $this->viewMore(nl2br($this -> form -> description), 1024, 1024 * 100); ?>
    </div>
    <div class="yndform_form_detail_info_parent clearfix">
        <div class="yndform_form_detail_info_stast clearfix">
            <div class="yndform_form_detail_info_stast_items">
                <div class="yndform_detail_count_item yndform_no_padding">
                    <?php $totalEntries = $this -> form -> total_entries;?>
                    <span class="yndform_detail_count_number">
                        <?php echo $this -> partial('_number.tpl', 'yndynamicform', array('number'=>$totalEntries)); ?>
                    </span>
                    <span class="yndform_detail_count_label">
                        <?php echo $this -> translate(array(' entry', ' entries', $totalEntries));?>
                    </span>
                </div>
                <div class="yndform_detail_count_item">
                    <?php $likeCount = $this -> form -> like_count;?>
                    <span class="yndform_detail_count_number">
                        <?php echo $this -> partial('_number.tpl', 'yndynamicform', array('number'=>$likeCount)); ?>
                    </span>
                    <span class="yndform_detail_count_label">
                        <?php echo $this -> translate(array(' like', ' likes', $likeCount));?>
                    </span>
                </div>
                <div class="yndform_detail_count_item">
                    <?php $commentCount = $this -> form -> comment_count; ?>
                    <span class="yndform_detail_count_number">
                        <?php echo $this -> partial('_number.tpl', 'yndynamicform', array('number'=>$commentCount)); ?>
                    </span>
                    <span class="yndform_detail_count_label">
                        <?php echo $this -> translate(array(' comment', ' comments', $commentCount));?>
                    </span>
                </div>
                <div class="yndform_detail_count_item yndform_no_border_right">
                    <?php $viewCount = $this -> form -> view_count;?>
                    <span class="yndform_detail_count_number">
                        <?php echo $this -> partial('_number.tpl', 'yndynamicform', array('number'=>$viewCount)); ?>
                    </span>
                    <span class="yndform_detail_count_label">
                        <?php echo $this -> translate(array(' view', ' views', $viewCount));?>
                    </span>
                </div>
            </div>
        </div>
        <div class="yndform_form_detail_info_button">
            <?php if ($this -> viewer -> getIdentity()): ?>
                <?php if ($this -> isModerator || $this -> viewer -> isAdmin()): ?>
                    <?php echo $this -> htmlLink(array(
                        'module'=>'yndynamicform',
                        'action'=>'list',
                        'form_id'=> $this -> form -> getIdentity(),
                        'route'=>'yndynamicform_entry_general',
                    ), '<span class="ynicon yn-bars"></span>'.$this -> translate("View Entries"), array()); ?>
                <?php endif; ?>

                <?php echo $this -> htmlLink(array(
                    'module'=>'activity',
                    'controller'=>'index',
                    'action'=>'share',
                    'route'=>'default',
                    'type'=>'yndynamicform_form',
                    'id' => $this -> form -> getIdentity(),
                    'format' => 'smoothbox'
                ), '<span class="ynicon yn-share"></span>'.$this -> translate("Share"), array('class' => 'yndform_share_button smoothbox')); ?>

                <?php $isLiked = $this -> form -> likes() -> isLike($this -> viewer()) ? 1 : 0; ?>
                <a id="yndform_like_button" class="yndform_like_button <?php echo $isLiked ? 'yndform_liked':'' ?>" href="javascript:void(0);" onclick="onlike('<?php echo $this -> form -> getType() ?>', '<?php echo $this -> form -> getIdentity() ?>', <?php echo $isLiked ?>);">
                    <?php if( $isLiked ): ?>
                        <?php echo '<span class="ynicon yn-thumb-up"></span>'.$this -> translate("Liked");?>
                    <?php else: ?>
                        <?php echo '<span class="ynicon yn-thumb-o-up"></span>'.$this -> translate("Like");?>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="yndform_addthis">
        <?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.buttons', '<!-- Go to www.addthis.com/dashboard to customize your tools --> <div class="addthis_sharing_toolbox"></div>'); ?> 
        <!-- Go to www.addthis.com/dashboard to customize your tools -->  
        <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.pub', 'younet');?>"></script>
    </div>
</div>

<script type="text/javascript">
    function onlike(itemType, itemId, isLiked) {
        if (isLiked) {
            unlike(itemType, itemId);
        } else {
            like(itemType, itemId);
        }
    }

    function like(itemType, itemId)
    {
        new Request.JSON({
            url: en4.core.baseUrl + 'core/comment/like',
            method: 'post',
            data : {
                format: 'json',
                type : itemType,
                id : itemId,
                comment_id : 0
            },
            onSuccess: function(responseJSON, responseText) {
                if (responseJSON.status == true)
                {
                    var html = '<a id="yndform_like_button" class="yndform_like_button yndform_liked" href="javascript:void(0);" onclick="unlike(\'<?php echo $this -> form ->getType()?>\', \'<?php echo $this -> form ->getIdentity() ?>\')"><span class="ynicon yn-thumb-up"></span><?php echo $this -> translate('Liked'); ?></a>';
                    $("yndform_like_button").outerHTML = html;
                }
            },
            onComplete: function(responseJSON, responseText) {
            }
        }).send();
    }

    function unlike(itemType, itemId)
    {
        new Request.JSON({
            url: en4.core.baseUrl + 'core/comment/unlike',
            method: 'post',
            data : {
                format: 'json',
                type : itemType,
                id : itemId,
                comment_id : 0
            },
            onSuccess: function(responseJSON, responseText) {
                if (responseJSON.status == true)
                {
                    var html = '<a id="yndform_like_button" class="yndform_like_button" href="javascript:void(0);" onclick="like(\'<?php echo $this -> form ->getType()?>\', \'<?php echo $this -> form ->getIdentity() ?>\')"><span class="ynicon yn-thumb-up"></span><?php echo $this -> translate('Like'); ?></a>';
                    $("yndform_like_button").outerHTML = html;
                }
            }
        }).send();
    }
</script>

