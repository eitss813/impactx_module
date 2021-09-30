<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitemember/views/scripts/infotooltip.tpl'; ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/scripts/core.js'); ?>
<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/styles/style_sitemember.css')
?>

    <?php
    if ($this->viewType == 'gridview'):
        $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/styles/style_sitemember.css');
    endif;
    ?>

        <?php if ($this->viewType == 'listview'): ?>
        <ul class="seaocore_sidebar_list <?php if(!empty($this->circularImage)):?> sitemember_circular_container <?php endif;?>">
            <?php $i = 0;
            foreach ($this->members as $sitemember): ?>
             <?php if($this->itemCount <= $i) break; ?>  
                        <?php if ($this->viewedBy=="viewed_by_me" && ($this->viewer_id == $sitemember->user_id || $this->subject_id == $sitemember->user_id)): continue; ?>
                        <?php endif; ?>
           
                <li>
                            <?php echo $this->htmlLink($sitemember->getHref(array()), $this->itemPhoto($sitemember, 'thumb.icon', array('title' => $sitemember->getTitle()))) ?>
                    <div class='seaocore_sidebar_list_info'>
                        <div class='seaocore_sidebar_list_title'>
                            
                                <?php echo $this->htmlLink($sitemember->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitemember->getTitle(), $this->truncation), array('title' => $sitemember->getTitle())); ?>
                         </div>
                     <?php
                            $this->addHelperPath(APPLICATION_PATH . '/application/modules/Sitemember/View/Helper', 'Sitemember_View_Helper');
                            echo $this->memberInfo($sitemember, $this->statistics, array('customParams' => $this->customParams, 'custom_field_title' => $this->custom_field_title, 'custom_field_heading' => $this->custom_field_heading));
                            ?>
                            <div class="clr sitemember_action_link_options sitemember_action_links">
                         <?php $uaseFRIENFLINK = $this->userFriendshipAjax($this->user($sitemember->user_id)); ?>
                            <?php if (!empty($uaseFRIENFLINK)) : ?>
                    <?php echo $uaseFRIENFLINK; ?>
                    <?php endif; ?></div>
                    </div>
                          
                </li>
            <?php $i++; ?>
        <?php endforeach; ?>
        </ul>
        <?php elseif($this->viewType == 'gridview'): ?>
            <?php $isLarge = ($this->columnWidth > 170); ?>
        <ul class="seaocore_sidebar_list sitemember_grid_view_sidebar o_hidden <?php if(!empty($this->circularImage)):?> sitemember_circular_container <?php endif;?>">
            <?php $i = 0; ?>
            <?php foreach ($this->members as $sitemember): ?>
            <?php if($this->itemCount <= $i) break; ?>
                            <?php if ($this->viewedBy=="viewed_by_me" && ($this->viewer_id == $sitemember->user_id || $this->subject_id == $sitemember->user_id)): continue; ?>
                            <?php endif; ?>
                        
                <li class="sitemember_grid_view" <?php if(empty($this->circularImage)):?> style="width: <?php echo $this->columnWidth; ?>px;height:<?php echo $this->columnHeight; ?>px;" <?php else:?> style="width: <?php echo $this->columnWidth; ?>px;height:<?php echo $this->columnHeight; ?>px;" <?php endif;?>>
                    <div class="sitemember_grid_thumb">
           
                            <?php $rel = 'user' . ' ' . $sitemember->user_id; ?>

                        <a href="<?php echo $sitemember->getHref() ?>" class ="sitemember_thumb sea_add_tooltip_link" rel="<?php echo $rel ?>" >
                            <?php
                            $url = $sitemember->getPhotoUrl($isLarge ? 'thumb.profile' : 'thumb.profile');
                            if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/images/nophoto_user_thumb_normal.png';
                            endif;
                            ?>

                            <?php if(!$this->circularImage):?>
                                <span style="background-image: url(<?php echo $url; ?>); <?php if ($isLarge): ?> height:160px; <?php else: ?> height:<?php echo $this->columnHeight; ?>px; <?php endif; ?>  "></span>
                           <?php else:?>
                                <span style="background-image: url(<?php echo $url; ?>); height:<?php echo $this->circularImageHeight; ?>px;"></span>
                            <?php endif;?>
                        </a>

                            <?php if (!empty($this->titlePosition)) : ?>
                            <div class="sitemember_grid_title">
                                
                                    <?php echo $this->htmlLink($sitemember->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitemember->getTitle(), $this->truncation), array('title' => $sitemember->getTitle())); ?>
                               
                            </div>
                        <?php endif; ?>
                    </div>
                </li>
            <?php $i++;
        endforeach; ?>
        </ul>
    <?php else: ?>
    <div id="layout_sitemember_recently_viewed_<?php echo $this->identity; ?>">
    <?php $isLarge = ($this->columnWidth > 170); ?>
        <ul class="o_hidden sitemember_circular_container members_icon_view">
            <?php $i = 0; ?>
            <?php foreach ($this->members as $sitemember): ?>
            <?php if($this->itemCount <= $i) break; ?>
                            <?php if ($this->viewedBy=="viewed_by_me" && ($this->viewer_id == $sitemember->user_id || $this->subject_id == $sitemember->user_id)): continue; ?>
                            <?php endif; ?>
                        
                <li class="sitemember_grid_view"  style="width: <?php echo $this->columnWidth; ?>px;height:<?php echo $this->columnHeight; ?>px;"  >
                    <div class="sitemember_grid_thumb">
           
                            <?php $rel = 'user' . ' ' . $sitemember->user_id; ?>

                        <a href="<?php echo $sitemember->getHref() ?>" class ="sitemember_thumb sea_add_tooltip_link" rel="<?php echo $rel ?>" >
                            <?php
                            $url = $sitemember->getPhotoUrl($isLarge ? 'thumb.profile' : 'thumb.profile');
                            if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/images/nophoto_user_thumb_normal.png';
                            endif;
                            ?>

                            
                                <span style="background-image: url(<?php echo $url; ?>); height:<?php echo ($this->circularImageHeight) ? $this->circularImageHeight: '77'  ?>px;"></span>
                            
                        </a>
 
                    </div>

                     

                             
          
                </li>
            <?php $i++;
        endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

<?php if ($this->viewtitletype == 'horizontal'): ?>
    <style type="text/css">
        /*Horizontal view*/
        .layout_sitemember_recently_viewed li.sitemember_grid_view{clear:none; border-radius:0;margin:3px !important;}
    </style>
<?php endif; ?>

<?php if($this->circularImage):?>
<script type="text/javascript">

var list=$$('.layout_sitemember_recently_viewed').getElements('.sitemember_grid_view');
if(list) {
    
    list.each(function(el, i)
    {
        if(el) {
     el.getElement('.sitemember_grid_info').each(function(els, i)
    {
        if(els) {
            var sitememberHtml = els.innerHTML;
            if(sitememberHtml.trim() != '') {
                  els.getParent().style.cssText = 'height:<?php echo $this->columnHeight;?>px !important;width:<?php echo $this->columnWidth;?>px';
            }
        }
    }); 
    
    }
    });     
    
    
    
    
}

</script>
<?php endif;?>
<script type="text/javascript">
 en4.core.runonce.add(function(){ 
    <?php if($this->viewType == 'iconview' && $this->siteusercoverphoto ): ?>  
    if($("user_recently_viewed"))    { 
            var clone = document.getElementById("layout_sitemember_recently_viewed_<?php echo $this->identity; ?>");
            $("user_recently_viewed").innerHTML = "<span id='recently_viewed_text'> Recently Viewed By </span>"+document.getElementById("layout_sitemember_recently_viewed_<?php echo $this->identity; ?>").innerHTML;
            clone.getParent().setStyle('display','none');
            <?php if($this->fullWidth || empty($this->viewer_id)): ?>
                $("user_recently_viewed").addClass("fullwidthrecent_member");
            <?php elseif(!$this->insideTab): ?>
                $("user_recently_viewed").addClass("outsiderecent_member");
            <?php endif;  ?>
    }   
    <?php endif; ?>
});
                        
</script>
