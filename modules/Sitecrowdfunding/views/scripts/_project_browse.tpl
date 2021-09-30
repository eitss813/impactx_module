<!-- Used in initiative landing page -->
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css');
?>

<div class="layout_core_container_tabs" id="project_browse_tabs">

    <div id="project_browse_loader" style="display: none">
        <div class="clr"></div><div class="seaocore_content_loader"></div>
    </div>

    <div id="project_browse_content">

        <div class="sitecrowdfunding_browse_lists_view_options txt_right" id='projectViewFormat'>
            <div class="fleft">
                <?php echo $this->translate(array('%s project found.', '%s projects found.', $this->totalCount), $this->totalCount); ?>
            </div>
        </div>

        <div id="dynamic_app_info_sitecrowdfunding">

            <div class="sitecrowdfunding_container" id="grid_view_sitecrowdfunding_">
                <?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/project/_grid_view.tpl'; ?>
            </div>

            <div class="seaocore_loading" id="" style="display: none;">
                <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' style='margin-right: 5px;' />
                <?php echo $this->translate("Loading ...") ?>
            </div>

            <?php echo $this->paginationControl($this->gPaginator, null, array("pagination/pagination.tpl", "sitecrowdfunding"), array("orderby" => $this->orderby));?>

        </div>

    </div>

</div>

<script type="text/javascript">

    var pageAction = function (page) {
        $('page').value = page;
        searchSiteprojects();
    }

    function getNextPage() {
        return <?php echo sprintf('%d', $this->gPaginator->getCurrentPageNumber() + 1) ?>
    }

</script>
<style>
    .sitecrowdfunding_browse_lists_view_options{
         padding: 10px 15px !important;
         margin: 15px 0 !important;
    }
    .sitecrowdfunding_thumb_wrapper.sitecrowdfunding_thumb_viewer{
        width: 316px !important;
    }
</style>
