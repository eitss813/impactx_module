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

<?php if ($this->contentModule == 'sitepage'): ?>
    <h2>
        <?php echo $this->translate('Directory / Pages Plugin'); ?>
    </h2>
<?php elseif ($this->contentModule == 'sitebusiness'): ?>
    <h2>
        <?php echo $this->translate('Directory / Businesses Plugin'); ?>
    </h2>
<?php elseif ($this->contentModule == 'sitegroup'): ?>
    <h2>
        <?php echo $this->translate('Groups / Communities Plugin'); ?>
    </h2>
<?php elseif ($this->contentModule == 'sitestore'): ?>
    <h2>
        <?php echo $this->translate('Stores / Marketplace Plugin'); ?>
    </h2>
<?php elseif ($this->contentModule == 'siteevent'): ?>
    <h2>
        <?php echo $this->translate('Advanced Events Plugin'); ?>
    </h2>
<?php elseif (Engine_Api::_()->hasModuleBootstrap('sitereview') && Engine_Api::_()->hasModuleBootstrap('sitereviewlistingtype') && $this->contentModule == 'sitereview'): ?>
    <h2>
        <?php echo $this->translate('Multiple Listing Types - Listing Type Creation Extension'); ?>
    </h2>
<?php elseif (Engine_Api::_()->hasModuleBootstrap('sitereview') && $this->contentModule == 'sitereview'): ?>
    <h2>
        <?php echo $this->translate('Multiple Listing Types Plugin Core (Reviews & Ratings Plugin)'); ?>
    </h2>
<?php elseif ($this->contentModule == 'sitecrowdfunding'): ?>
    <h2>
        <?php echo $this->translate('Crowdfunding / Fundraising / Donations Plugin'); ?>
    </h2>
<?php else: ?>
    <h2><?php echo $this->translate("Advanced Videos / Channels / Playlists Plugin") ?></h2>
<?php endif; ?>



<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
    </div>
<?php endif; ?>

<h3><?php echo $this->translate("Manage Videos") ?></h3>
<p>
    <?php echo $this->translate("This page lists all the videos your users have created. You can use this page to monitor these videos and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific video entries. Leaving the filter fields blank will show all the video entries on your social network. ") ?>
</p>
<br />

<script type="text/javascript">
    var currentOrder = '<?php echo $this->order ?>';
    var currentOrderDirection = '<?php echo $this->order_direction ?>';
    var changeOrder = function (order, default_direction) {
        // Just change direction
        if (order == currentOrder) {
            $('order_direction').value = (currentOrderDirection == 'ASC' ? 'DESC' : 'ASC');
        } else {
            $('order').value = order;
            $('order_direction').value = default_direction;
        }

        $('filter_form').submit();
    };

    function multiDelete()
    {
        return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected videos ?")) ?>');
    }

    function selectAll()
    {
        var i;
        var multidelete_form = $('multidelete_form');
        var inputs = multidelete_form.elements;

        for (i = 1; i < inputs.length - 1; i++) {
            if (!inputs[i].disabled) {
                inputs[i].checked = inputs[0].checked;
            }
        }
    }
</script>

<div class="admin_search sitevideo_admin_video_search">
    <div class="search">
        <form method="post" class="global_form_box" action="" width="100%">

            <div>
                <label>
                    <?php echo $this->translate("Title") ?>
                </label>
                <?php if (empty($this->title)): ?>
                    <input type="text" name="title" /> 
                <?php else: ?>
                    <input type="text" name="title" value="<?php echo $this->translate($this->title) ?>"/>
                <?php endif; ?>
            </div>

            <div>
                <label>
                    <?php echo $this->translate("Owner") ?>
                </label>	
                <?php if (empty($this->owner)): ?>
                    <input type="text" name="owner" /> 
                <?php else: ?> 
                    <input type="text" name="owner" value="<?php echo $this->translate($this->owner) ?>" />
                <?php endif; ?>
            </div>        

            <?php $categories = Engine_Api::_()->getDbtable('videoCategories', 'sitevideo')->getCategories(array('fetchColumns' => array('category_id', 'category_name'), 'sponsored' => 0, 'cat_depandancy' => 1)); ?>              
            <div class="form-wrapper" id="category_id-wrapper">
                <div class="form-label" id="category_id-label">
                    <label class="optional" for="category_id"><?php echo $this->translate('Category'); ?></label>
                </div>
                <div class="form-element" id="category_id-element">
                    <select id="category_id" name="category_id" onchange='addOptions(this.value, "cat_dependency", "subcategory_id", 0);'>
                        <option value=""></option>
                        <?php if (count($categories) != 0): ?>
                            <?php
                            $categories_prepared[0] = "";
                            foreach ($categories as $category) {
                                $categories_prepared[$category->category_id] = $category->category_name;
                                ?>
                                <option value="<?php echo $category->category_id; ?>" <?php if ($this->category_id == $category->category_id) echo "selected"; ?>><?php echo $this->translate($category->category_name); ?></option>
                            <?php } ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <div class="form-wrapper" id="subcategory_id-wrapper" style='display:none;'>
                <div class="form-label" id="subcategory_id-label">
                    <label class="optional" for="subcategory_id"><?php echo $this->translate('Sub-Category'); ?></label>
                </div>
                <div class="form-element" id="subcategory_id-element">
                    <select id="subcategory_id" name="subcategory_id" onchange='addOptions(this.value, "subcat_dependency", "subsubcategory_id", 0);'></select>
                </div>
            </div>

            <div class="form-wrapper" id="subsubcategory_id-wrapper" style='display:none;'>
                <div class="form-label" id="subsubcategory_id-label">
                    <label class="optional" for="subsubcategory_id"><?php echo $this->translate('3rd Level Category', "<sup>rd</sup>") ?></label>
                </div>
                <div class="form-element" id="subsubcategory_id-element">
                    <select id="subsubcategory_id" name="subsubcategory_id"  ></select>
                </div>
            </div>
            <div>
                <label>
                    <?php echo $this->translate("Browse By") ?>	
                </label>
                <select id="" name="videobrowse">
                    <option value="0" ><?php echo $this->translate("Select") ?></option>
                    <option value="1" <?php if ($this->videobrowse == 1) echo "selected"; ?> ><?php echo $this->translate("Most Recent") ?></option>
                    <option value="2" <?php if ($this->videobrowse == 2) echo "selected"; ?> ><?php echo $this->translate("Most Viewed") ?></option>
                    <option value="3" <?php if ($this->videobrowse == 3) echo "selected"; ?> ><?php echo $this->translate("Most Liked") ?></option>
                    <option value="4" <?php if ($this->videobrowse == 4) echo "selected"; ?> ><?php echo $this->translate("Most Commented") ?></option>
                    <option value="5" <?php if ($this->videobrowse == 5) echo "selected"; ?> ><?php echo $this->translate("Most Rated") ?></option>
                </select>
            </div>

            <?php if ($this->contentModule != 'sitereview'): ?>
                <?php
                $contentTypes = Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1));

                $contentTypeArray = array();
                if (!empty($contentTypes)) {
                    $contentTypeArray[] = 'All';
                    $moduleTitle = '';
                    foreach ($contentTypes as $contentType) {
                        if ($contentType['item_title']) {
                            $contentTypeArray['user'] = 'Member Videos';
                            $contentTypeArray[$contentType['item_type']] = $contentType['item_title'];
                        }
                    }
                }
                ?>

                <?php if (!empty($contentTypeArray)): ?>
                    <div>
                        <label>
                            <?php echo $this->translate("Video Type") ?>	
                        </label>
                        <select name="contentType" id="contentType">
                            <?php foreach ($contentTypeArray as $key => $contentType) : ?>
                                <option value="<?php echo $key; ?>" <?php if ($this->contentType == $key) echo "selected"; ?> label="<?php echo $contentType; ?>"><?php echo $contentType; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
            <?php elseif ($this->contentModule == 'sitereview'): ?>
                <?php
                $contentTypes = Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1));
                $contentTypeArray = array();
                $moduleTitle = '';
                if (!empty($contentTypes)) {
                    foreach ($contentTypes as $contentType) {

                        if (Engine_Api::_()->hasModuleBootstrap('sitereview') && Engine_Api::_()->hasModuleBootstrap('sitereviewlistingtype')) {
                            $moduleTitle = 'Reviews & Ratings - Multiple Listing Types';
                        } elseif (Engine_Api::_()->hasModuleBootstrap('sitereview')) {
                            $moduleTitle = 'Reviews & Ratings';
                        }
                        $explodedResourceType = explode('_', $contentType['item_type']);
                        if (isset($explodedResourceType[2]) && $moduleTitle) {
                            $listingtypesTitle = Engine_Api::_()->getDbtable('listingtypes', 'sitereview')->getListingRow($explodedResourceType[2])->title_plural;
                            $listingtypesTitle = $listingtypesTitle . ' ( ' . $moduleTitle . ' ) ';
                            $contentTypeArray[$contentType['item_type']] = $listingtypesTitle . ' Videos';
                        } else {
                            $contentTypeArray[$contentType['item_type']] = Engine_Api::_()->getDbtable('modules', 'sitevideo')->getModuleTitle($contentType['item_module']) . ' Videos';
                        }
                    }
                }
                ?>

                <?php if (!empty($contentTypeArray)): ?>
                    <div>
                        <label>
                            <?php echo $this->translate("Video Type") ?>	
                        </label>
                        <select name="contentType" id="contentType">
                            <?php foreach ($contentTypeArray as $key => $contentType) : ?>
                                <option value="<?php echo $key; ?>" <?php if ($this->contentType == $key) echo "selected"; ?> label="<?php echo $contentType; ?>"><?php echo $contentType; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
            <?php endif; ?>            

            <div>
                <label>
                    <?php echo $this->translate("Type") ?>	
                </label>
                <select id="" name="videotype">
                    <option value="0" ><?php echo $this->translate("Select") ?></option>
                    <option value="youtube" <?php if ($this->videotype == 'youtube') echo "selected"; ?> ><?php echo $this->translate("YouTube") ?></option>
                    <option value="vimeo" <?php if ($this->videotype == 'vimeo') echo "selected"; ?> ><?php echo $this->translate("Vimeo") ?></option>
                    <option value="dailymotion" <?php if ($this->videotype == 'dailymotion') echo "selected"; ?> ><?php echo $this->translate("Dailymotion") ?></option>
                    <option value="embedcode" <?php if ($this->videotype == 'embedcode') echo "selected"; ?> ><?php echo $this->translate("Embedded Videos") ?></option>
                    <option value="upload" <?php if ($this->videotype == 'upload') echo "selected"; ?> ><?php echo $this->translate("My Computer") ?></option>
                    <option value="iframely" <?php if ($this->videotype == 'iframely') echo "selected"; ?> ><?php echo $this->translate("External Sites") ?></option>
                    <?php if(Engine_Api::_()->sitevideo()->enableStreamVideo()): ?>
                    <option value="stream" <?php if ($this->videotype == 'stream') echo "selected"; ?> ><?php echo $this->translate("Stream") ?></option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="clear mtop10">
                <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>
            </div>
        </form>
    </div>
</div>
<br />

<div class='admin_search'>
    <?php echo $this->formFilter->render($this) ?>
</div>
<br />

<?php if (count($this->paginator) > 0): ?>
    <div class='admin_members_results'>
        <div>
            <?php $count = $this->paginator->getTotalItemCount() ?>
            <?php echo $this->translate(array("%s video found.", "%s videos found.", $count), $this->locale()->toNumber($count))
            ?>
        </div>
    <?php else: ?>
        <div class="tip"><span>
                <?php echo $this->translate("No videos were found.") ?></span>
        </div>
    <?php endif; ?>
    <div style="margin-top:5px">
        <?php echo $this->paginationControl($this->paginator); ?>
    </div>
</div>

<?php if ($this->paginator->getTotalItemCount() > 0): ?>
    <form id='multidelete_form' method="post" action="<?php echo $this->url(array('action' => 'multi-delete')); ?>" onSubmit="return multiDelete();">
        <table class='admin_table seaocore_admin_table' width="100%">
            <thead>
                <tr>
                    <th><input onclick="selectAll();" type='checkbox' class='checkbox'></th>

                    <?php $class = ( $this->order == 'video_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('video_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>

                    <?php $class = ( $this->order == 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>"  align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate("Title") ?></a></th>

                    <?php $class = ( $this->order == 'displayname' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('displayname', 'ASC');"><?php echo $this->translate("Owner") ?></a></th>

                    <?php $class = ( $this->order == 'channel_title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('channel_title', 'ASC');"><?php echo $this->translate("Channel") ?></a></th>

                    <?php $class = ( $this->order == 'category_name' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('category_name', 'ASC');"><?php echo $this->translate("Category") ?></a></th>

                    <?php $class = ( $this->order == 'type' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('type', 'ASC');"><?php echo $this->translate("Type") ?></a></th>

                    <?php $class = ( $this->order == 'status' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('status', 'ASC');"><?php echo $this->translate("Status") ?></a></th>

                    <?php $class = ( $this->order == 'rating' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('rating', 'ASC');"><?php echo $this->translate('Overall Rating'); ?></a></th>

                    <?php $class = ( $this->order == 'view_count' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?> center" class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('view_count', 'ASC');" title="<?php echo $this->translate('Views'); ?>" ><?php echo $this->translate('Views'); ?></a></th>

                    <?php $class = ( $this->order == 'like_count' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?> center"  class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('like_count', 'ASC');" title="<?php echo $this->translate('Likes'); ?>" ><?php echo $this->translate('L'); ?></a></th>

                    <?php $class = ( $this->order == 'favourite_count' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?> center"  class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('favourite_count', 'ASC');" title="<?php echo $this->translate('Favourites'); ?>" ><?php echo $this->translate('F'); ?></a></th>

                    <?php $class = ( $this->order == 'comment_count' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?> center" class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('comment_count', 'ASC');" title="<?php echo $this->translate('Comments'); ?>" ><?php echo $this->translate('C'); ?></a></th>
                    <?php $class = ( $this->order == 'featured' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?> admin_table_centered"  title="<?php echo $this->translate('Featured'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('featured', 'ASC');"><?php echo $this->translate('F'); ?></a></th>

                    <?php $class = ( $this->order == 'sponsored' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?> center"  class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('sponsored', 'ASC');" title="<?php echo $this->translate('Sponsored'); ?>" ><?php echo $this->translate('S'); ?></a></th>

                    <?php $class = ( $this->order == 'creation_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate("Creation Date") ?></a></th>
                    <th class="<?php echo $class ?>" class='admin_table_centered'><?php echo $this->translate("Option") ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($this->paginator)): ?>
                    <?php
                    foreach ($this->paginator as $item): 
                        ?>
                        <tr>
                            <td><input name='delete_<?php echo $item->video_id; ?>' type='checkbox' class='checkbox' value="<?php echo $item->video_id ?>"/></td>
                            <td><?php echo $item->video_id ?></td>
                            <td class='admin_table_bold'>
                                <?php
                                echo $this->htmlLink($item->getHref(), $this->string()->truncate($item->getTitle(), 10), array('target' => '_blank'))
                                ?>
                            </td>
                            <td class='admin_table_user'>
                                <?php echo $this->htmlLink($item->getOwner()->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getOwner()->getTitle(), 10), array('target' => '_blank')) ?>
                            </td>
                            <td class='admin_table_user'>
                                <?php 
                                if($item->main_channel_id) : ?>
                                <?php $channel = Engine_Api::_()->getItem('sitevideo_channel', $item->main_channel_id);?>
                                <?php if($channel) :?>
                                    <?php echo $this->htmlLink($channel->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($channel->getTitle(), 20), array('target' => '_blank')) ?>
                                <?php else : ?>
                                ---
                                <?php endif; ?>
                                <?php else : ?>
                                ---
                                <?php endif; ?>
                            </td>
                            <td class='admin_table_email'>
                                <?php
                                if ($item->category_id):
                                    $categoryName = Engine_Api::_()->getDbtable('videoCategories', 'sitevideo')->getCategoryName($item->category_id);
                                    ?>
                                    <?php if ($categoryName) : ?>
                                        <a href="<?php echo $this->url(array('category_id' => $item->category_id, 'categoryname' => Engine_Api::_()->getItem('sitevideo_video_category', $item->category_id)->getCategorySlug()), 'sitevideo_video_general_category', true) ?>" target="_blank">
                                            <span><?php echo $categoryName; ?></span> 
                                        </a>
                                    <?php endif; ?>
                                    <?php
                                else :
                                    echo "---";
                                endif;
                                ?>
                            </td>
                            <td class='admin_table_email'>
                                <?php
                                switch ($item->type) {
                                    case 'youtube' : echo 'YouTube';
                                        break;
                                    case 'vimeo' : echo 'Vimeo';
                                        break;
                                    case 'upload' : echo 'My Computer';
                                        break;
                                    case 'dailymotion' : echo 'Dailymotion';
                                        break;
                                    case 'iframely' : echo 'External Sites';
                                        break;
                                    case 'stream' : echo 'Stream';
                                        break;
                                    default :
                                        echo '---';
                                }
                                ?>
                            </td>
                            <td class='admin_table_email'>
                                <?php
                                switch ($item->status) {
                                    case 0 : echo 'Queued';
                                        break;
                                    case 1 : echo 'Ready';
                                        break;
                                    case 2 : echo 'Processing';
                                        break;
                                    case 3 :
                                    case 4 :
                                    case 5 :
                                    case 6 :
                                    case 7 : echo 'Failed';
                                    default :
                                        echo '---';
                                }
                                ?>
                            </td>
                            <td>
                                <div>
                                    <span title="<?php echo $item->rating . $this->translate('rating '); ?>">
                                        <?php if (($item->rating > 0)): ?>
                                            <?php for ($x = 1; $x <= $item->rating; $x++): ?>
                                                <span class="rating_star_generic rating_star"></span>
                                                <?php
                                            endfor;
                                            $roundrating = round($item->rating)
                                            ?>
                                            <?php if (($roundrating - $item->rating) > 0): ?>
                                                <span class="rating_star_generic rating_star_half"></span>
                                            <?php endif; ?>
                                            <?php
                                            $roundrating++;
                                            for ($x = $roundrating; $x <= 5; $x++) {
                                                ?>
                                                <span class="rating_star_generic rating_star_disabled"></span>
                                            <?php } ?>

                                        <?php else : ?>
                                            <span class="rating_star_generic rating_star_disabled"></span>
                                            <span class="rating_star_generic rating_star_disabled"></span>
                                            <span class="rating_star_generic rating_star_disabled"></span>
                                            <span class="rating_star_generic rating_star_disabled"></span>
                                            <span class="rating_star_generic rating_star_disabled"></span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </td>
                            <td class="center"><?php echo $this->locale()->toNumber($item->view_count) ?></td>
                            <td class="center"><?php echo $this->locale()->toNumber($item->like_count) ?></td>
                            <td class="center"><?php echo $this->locale()->toNumber($item->favourite_count) ?></td>
                            <td class="center"><?php echo $this->locale()->toNumber($item->comment_count) ?></td>
                            <?php if ($item->featured == 1): ?> 
                                <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitevideo', 'controller' => 'video', 'action' => 'remove-featured', 'id' => $item->video_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/featured.gif', '', array('title' => $this->translate('Make Un-featured'))), array('class' => 'smoothbox buttonlink')) ?></td>
                            <?php else: ?>
                                <td align="center" class="admin_table_centered"><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitevideo', 'controller' => 'video', 'action' => 'add-featured', 'id' => $item->video_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/unfeatured.gif', '', array('title' => $this->translate('Make Featured'))), array('class' => 'smoothbox buttonlink')) ?></td>
                            <?php endif; ?>
                            <?php if ($item->sponsored == 1): ?>
                                <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitevideo', 'controller' => 'manage-video', 'action' => 'sponsored', 'id' => $item->video_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/sponsored.png', '', array('title' => $this->translate('Make Unsponsored')))); ?></td>
                            <?php else: ?>
                                <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitevideo', 'controller' => 'manage-video', 'action' => 'sponsored', 'id' => $item->video_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/unsponsored.png', '', array('title' => $this->translate('Make Sponsored')))); ?>
                                <?php endif; ?> 
                            <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>             

                            <td class='admin_table_options'>
                                <?php echo $this->htmlLink($item->getHref(), "View"); ?>    
                                <?php
                                echo $this->htmlLink(array(
                                    'route' => 'default',
                                    'module' => 'sitevideo',
                                    'controller' => 'video',
                                    'action' => 'edit',
                                    'video_id' => $item->video_id,
                                    'admin' => 'true'
                                        ), $this->translate('Edit'), array(
                                    'target' => '_blank'
                                ));
                                ?>
                                <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitevideo', 'controller' => 'admin-manage-video', 'action' => 'Delete', 'id' => $item->video_id), $this->translate("Delete"), array('class' => 'smoothbox')); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div class='buttons'>
            <button type='submit'><?php echo $this->translate('Delete Selected'); ?></button>
        </div>
    </form>
<?php endif; ?>

<script type="text/javascript">
    function addOptions(element_value, element_type, element_updated, domready) {

        var element = $(element_updated);
        if (domready == 0) {
            switch (element_type) {
                case 'cat_dependency':
                    $('subcategory_id' + '-wrapper').style.display = 'none';
                    clear($('subcategory_id'));
                    $('subcategory_id').value = 0;

                case 'subcat_dependency':
                    $('subsubcategory_id' + '-wrapper').style.display = 'none';
                    clear($('subsubcategory_id'));
                    $('subsubcategory_id').value = 0;
            }
        }

        if (element_value <= 0)
            return;

        var url = '<?php echo $this->url(array('module' => 'sitevideo', 'controller' => 'video', 'action' => 'categories'), "admin_default", true); ?>';
        en4.core.request.send(new Request.JSON({
            url: url,
            data: {
                format: 'json',
                element_value: element_value,
                element_type: element_type
            },
            onSuccess: function (responseJSON) {
                var categories = responseJSON.categories;
                var option = document.createElement("OPTION");
                option.text = "";
                option.value = 0;
                element.options.add(option);
                for (i = 0; i < categories.length; i++) {
                    var option = document.createElement("OPTION");
                    option.text = categories[i]['category_name'];
                    option.value = categories[i]['category_id'];
                    element.options.add(option);
                }

                if (categories.length > 0)
                    $(element_updated + '-wrapper').style.display = 'block';
                else
                    $(element_updated + '-wrapper').style.display = 'none';

                if (domready == 1) {
                    var value = 0;
                    if (element_updated == 'category_id') {
                        value = search_category_id;
                    } else if (element_updated == 'subcategory_id') {
                        value = search_subcategory_id;
                    } else {
                        value = search_subsubcategory_id;
                    }
                    $(element_updated).value = value;
                }
            }

        }), {'force': true});
    }

    function clear(element)
    {
        for (var i = (element.options.length - 1); i >= 0; i--) {
            element.options[ i ] = null;
        }
    }

    var search_category_id, search_subcategory_id, search_subsubcategory_id;
    window.addEvent('domready', function () {

        search_category_id = '<?php echo $this->category_id ? $this->category_id : 0 ?>';

        if (search_category_id != 0) {
            search_subcategory_id = '<?php echo $this->subcategory_id ? $this->subcategory_id : 0 ?>';

            addOptions(search_category_id, 'cat_dependency', 'subcategory_id', 1);

            if (search_subcategory_id != 0) {
                search_subsubcategory_id = '<?php echo $this->subsubcategory_id ? $this->subsubcategory_id : 0 ?>';
                addOptions(search_subcategory_id, 'subcat_dependency', 'subsubcategory_id', 1);
            }
        }
    });
</script>
