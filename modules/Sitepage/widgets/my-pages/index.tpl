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
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js'); ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/core.js'); ?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/style_sitepage.css'); ?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/sitepage-tooltip.css');?>
<?php
    if($this->identity)
$this->params['identity'] = $this->identity;
else
$this->identity = $this->params['identity'];
?>

<?php
    $currentLink = 'created_pages';
    if(isset($this->params['link']) && !empty($this->params['link']))
$currentLink = $this->params['link'];
$viewType = isset($this->userPageNavigationLink[0]) ? ($this->userPageNavigationLink[0]) : 0;
?>

<?php if (empty($this->is_ajax)) : ?>
<?php if($this->routeName=== "sitepage_general" && $this->actionName === "manage") :?>
    <div class="sitepage_mypages_top o_hidden b_medium">
        <div class="fright mypages_top_links_right">
            <span class="sitecrowdfunding_link_wrap">
                <i class="sitecrowdfunding_icon item_icon_sitecrowdfunding_project"></i>
                <a href="<?php echo $this->url(array('action' => 'create'), 'sitepage_general', true); ?>" class="bold upload_new_project seaocore_icon_add" id="list_page_link">
                    <?php echo $this->translate('Create a Organisation');?>
                </a>;
            </span>&nbsp;&nbsp;
        </div>
    </div>
<?php endif; ?>
<div class="sitepage_mypages_top_links b_medium">

    <div class="sitepage_mypages_top_filter_links txt_center sitepage_mypages_top_filter_links_<?php echo $this->identity; ?>" style="display:<?php echo (count($this->userPageNavigationLink) > 0) ? 'block' : 'none'; ?>" >

        <?php if (in_array('created_pages', $this->userPageNavigationLink)) : ?>
            <a href="javascript:void(0);" id='created_pages'  onclick="page_filter_rsvp('created_pages')" ><?php echo $this->translate('My Pages'); ?></a>
        <?php endif; ?>

        <?php if (in_array('followed_pages', $this->userPageNavigationLink)) : ?>
            <a href="javascript:void(0);" id='followed_pages'  onclick="page_filter_rsvp('followed_pages')" ><?php echo $this->translate('Followed Pages'); ?></a>
        <?php endif; ?>

        <?php if (in_array('joined_pages', $this->userPageNavigationLink)) : ?>
            <a href="javascript:void(0);" id='joined_pages'  onclick="page_filter_rsvp('joined_pages')" ><?php echo $this->translate('Joined Pages'); ?></a>
        <?php endif; ?>
        <a href="javascript:void(0);" id='admin_pages'  onclick="page_filter_rsvp('admin_pages')" ><?php echo $this->translate('Organizations I Admin'); ?></a>
    </div>

    <div class="sitepage_mypages_top_filter_views txt_right fright" id='pagesViewFormat'>

        <?php if ($this->show_grid_view): ?>
            <span class="seaocore_tab_select_wrapper fright">
                <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Grid View"); ?></div>
                <span class="seaocore_tab_icon tab_icon_grid_view" onclick="changeView('GridView')" id="seaocore_tab_icon_grid_view" ></span>
            </span>
        <?php endif; ?>

        <?php if ($this->show_list_view): ?>
            <span class="seaocore_tab_select_wrapper fright">
                <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("List View"); ?></div>
                <span class="seaocore_tab_icon tab_icon_list_view" onclick="changeView('ListView')" id="seaocore_tab_icon_list_view" ></span>
            </span>
        <?php endif; ?>

        <?php if ($this->show_map_view): ?>
            <span class="seaocore_tab_select_wrapper fright">
                <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Map View"); ?></div>
                <span class="seaocore_tab_icon tab_icon_map_view" onclick="changeView('MapView')" id="seaocore_tab_icon_map_view" ></span>
            </span>
        <?php endif; ?>

    </div>
</div>
<?php endif; ?>

<div id='sitepage_my_pages' style="display:<?php echo (count($this->userPageNavigationLink) > 0) ? 'block' : 'none'; ?>">

    <?php if(count($this->paginator) > 0): ?>

        <?php if ( $currentLink == 'followed_pages' && in_array('followed_pages', $this->userPageNavigationLink)) : ?>
            <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/_recently_popular_random_page.tpl'; ?>
        <?php endif; ?>

        <?php if ($currentLink == 'joined_pages' && in_array('joined_pages', $this->userPageNavigationLink)) : ?>
            <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/_recently_popular_random_page.tpl'; ?>
        <?php endif; ?>

        <?php if ($currentLink == 'created_pages' && in_array('created_pages', $this->userPageNavigationLink)) : ?>
            <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/_recently_popular_random_page.tpl'; ?>
        <?php endif; ?>

        <?php if ($currentLink == 'admin_pages') : ?>
           <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/_recently_popular_random_page.tpl'; ?>
        <?php endif; ?>

    <?php else: ?>

        <?php if ( $currentLink == 'followed_pages' && in_array('followed_pages', $this->userPageNavigationLink)) : ?>
            <div class="tip">
                <span>
                    <?php echo $this->translate('Not following any pages'); ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if ($currentLink == 'joined_pages' && in_array('joined_pages', $this->userPageNavigationLink)) : ?>
            <div class="tip">
                <span>
                    <?php echo $this->translate('Not joined in any pages'); ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if ($currentLink == 'created_pages' && in_array('created_pages', $this->userPageNavigationLink)) : ?>
            <div class="tip">
                <span>
                    <?php echo $this->translate('No pages was created'); ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if ($currentLink == 'admin_pages') : ?>
            <div class="tip">
                <span>
                    <?php echo $this->translate('You have not any Organizations as Admin.'); ?>
                </span>
            </div>
        <?php endif; ?>

    <?php endif; ?>

</div>

<div id="hidden_ajax_my_page_data" style="display: none;"></div>

<script>
    viewType = '<?php echo $viewType; ?>';
    viewFormatG = '<?php echo $this->viewFormat ?>';
    page_addBoldClass = function (reqType)
    {
        $$('div.sitepage_mypages_top_filter_links_<?php echo $this->identity; ?> > a').each(function (el) {
            el.removeClass('active');
        });
        $$('.seaocore_tab_icon_<?php echo $this->identity ?>').each(function (el) {
            el.removeClass('active');
        });
        $(reqType).addClass('active');
    }

    page_filter_rsvp = function (req_type) {
        if (req_type == '0')
            return false;
        page_addBoldClass(req_type);
        viewType = req_type;
        switch (req_type)
        {
            case 'joined_pages':
                var url = en4.core.baseUrl + 'widget/index/mod/sitepage/name/my-pages/link/joined_pages';
                break;
            case 'followed_pages':
                var url = en4.core.baseUrl + 'widget/index/mod/sitepage/name/my-pages/link/followed_pages';
                break;
            case 'created_pages':
                var url = en4.core.baseUrl + 'widget/index/mod/sitepage/name/my-pages/link/created_pages';
                break;
            case 'admin_pages':
                var url = en4.core.baseUrl + 'widget/index/mod/sitepage/name/my-pages/link/admin_pages';
                break;
        }
        $('sitepage_my_pages').innerHTML = '<div class="clr"></div><div class="seaocore_content_loader"></div>';

        var params = {
            requestParams:<?php echo json_encode($this->params) ?>
        };

        params.requestParams.is_ajax = 0;

        var request = new Request.HTML({
            url: url,
            data: $merge(params.requestParams, {
                format: 'html',
                subject: en4.core.subject.guid,
                is_ajax: 0,
                pagination: 0,
                page: 0,
                columnWidth:<?php echo $this->columnWidth; ?>,
                columnHeight:<?php echo $this->columnHeight; ?>,
                list_view:<?php echo $this->list_view; ?>,
                grid_view:<?php echo $this->grid_view; ?>,
                map_view:<?php echo $this->map_view; ?>,
                selected_layout_view:<?php echo "'".$this->selected_layout_view."'"; ?>,
                layout_views:<?php echo json_encode($this->ShowViewArray); ?>
            }),
            evalScripts: true,
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $('hidden_ajax_my_page_data').innerHTML = responseHTML;
                $('sitepage_my_pages').innerHTML = $('hidden_ajax_my_page_data').getElement('#sitepage_my_pages').innerHTML;
                $('hidden_ajax_my_page_data').innerHTML = '';
                fundingProgressiveBarAnimation();
                Smoothbox.bind($('sitepage_my_pages'));
                en4.core.runonce.trigger();
                changeView(<?php echo "'".$this->selected_layout_view."'"; ?>);
            }
        });
        request.send();
    }
    var currentLink = "<?php echo $currentLink; ?>";
    var allLinks = $$('div.sitepage_mypages_top_filter_links_<?php echo $this->identity; ?> > a');
    allLinks.removeClass('active');
    $(currentLink).addClass('active');
</script>
<script>
    changeView = function(viewName) {
        if (viewName == 'GridView') {
            if ($('rgrid_view_page'))
                $('rgrid_view_page').style.display = 'none';
            if ($('rimage_view_page'))
                $('rimage_view_page').style.display = 'block';
            if ($('rmap_canvas_view_page'))
                $('rmap_canvas_view_page').style.display = 'none';

            var mapIconId = document.getElementById("seaocore_tab_icon_map_view");
            var listIconId = document.getElementById("seaocore_tab_icon_list_view");
            var gridIconId = document.getElementById("seaocore_tab_icon_grid_view");
            mapIconId.classList.remove("active");
            listIconId.classList.remove("active");
            gridIconId.classList.add("active");

        } else if(viewName == 'ListView'){
            if ($('rgrid_view_page'))
                $('rgrid_view_page').style.display = 'block';
            if ($('rimage_view_page'))
                $('rimage_view_page').style.display = 'none';
            if ($('rmap_canvas_view_page'))
                $('rmap_canvas_view_page').style.display = 'none';

            var mapIconId = document.getElementById("seaocore_tab_icon_map_view");
            var listIconId = document.getElementById("seaocore_tab_icon_list_view");
            var gridIconId = document.getElementById("seaocore_tab_icon_grid_view");
            mapIconId.classList.remove("active");
            listIconId.classList.add("active");
            gridIconId.classList.remove("active");

        }else {
            if ($('rgrid_view_page'))
                $('rgrid_view_page').style.display = 'none';
            if ($('rimage_view_page'))
                $('rimage_view_page').style.display = 'none';
            if ($('rmap_canvas_view_page'))
                $('rmap_canvas_view_page').style.display = 'block';

            var mapIconId = document.getElementById("seaocore_tab_icon_map_view");
            var listIconId = document.getElementById("seaocore_tab_icon_list_view");
            var gridIconId = document.getElementById("seaocore_tab_icon_grid_view");
            mapIconId.classList.add("active");
            listIconId.classList.remove("active");
            gridIconId.classList.remove("active");

            <?php if ( (count($this->locations) > 0) && $this->map_view ) : ?>
                rinitializePage();
            <?php endif; ?>

        }
    }
</script>
<script type="text/javascript">
    en4.core.runonce.add(function () {
        changeView(<?php echo "'".$this->selected_layout_view."'"; ?>);

        <?php if ( (count($this->locations) > 0) && $this->map_view ) : ?>
            rinitializePage();
        <?php endif; ?>
    });
</script>

<style>
    .sitepage_mypages_top_filter_views {
        right: 10px;
        position: absolute;
        top: 12px;
    }
    #rmap_canvas_page {
        width: 100% !important;
        height: 400px;
        float: left;
    }
    #rmap_canvas_page > div{
        height: 300px;
    }
    #infoPanel {
        float: left;
        margin-left: 10px;
    }
    #infoPanel div {
        margin-bottom: 5px;
    }
</style>