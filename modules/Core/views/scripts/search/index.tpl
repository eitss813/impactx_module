<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<?php
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js')
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/_commonFunctions.js')
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js')
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/selectric/jquery.selectric.js');
?>
<link href="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/selectric/selectric.css' ?>" rel="stylesheet">


<!-- todo: 5.2.1 Upgrade => Added missing custom search functions which was present earlier -->

<!--  To avoid Confirm Form Resubmission -->
<?php header('Cache-Control: no cache'); ?>

<?php $tab_link= $this->tab_link; ?>

<div class="generic_layout_container layout_middle">
    <div class="generic_layout_container layout_core_content">
        <div class="layout_middle">

            <!-- form -->
            <div class="search_form_container">
                <h2>Search</h2>
                <div id="searchform" class="global_form_box">
                    <?php echo $this->form->render($this) ?>
                    <div id="search_spinner"></div>
                    <div id="error_msg_container" style="display: none"></div>
                </div>
            </div>

            <!-- Menu-->
            <div class="transaction_menu headline sitecrowdfunding_inner_menu" id="sitecrowdfunding_inner_menu">
                <div class='tabs sitecrowdfunding_nav'>
                    <ul class='transaction_menu_nav navigation'>
                        <li>
                            <a id="all_tab" class="tab_heading active" href="javascript:void(0);" onclick="selected_ui('all_tab')">
                                <?php echo $this->translate('All'); ?> (<?php echo $this->alltabCount; ?>)
                            </a>
                        </li>
                        <li>
                            <a id="members_tab"  class="tab_heading" href="javascript:void(0);" onclick="selected_ui('members_tab')">
                                <?php echo $this->translate('Members'); ?> (<?php echo $this->membersCount; ?>)
                            </a>
                        </li>
                        <li>
                            <a id="projects_tab"  class="tab_heading" href="javascript:void(0);"  onclick="selected_ui('projects_tab')">
                                <?php echo $this->translate('Projects'); ?> (<?php echo $this->projectsCount; ?>)
                            </a>
                        </li>
                        <li>
                            <a id="organizations_tab"  class="tab_heading" href="javascript:void(0);"  onclick="selected_ui('organizations_tab')">
                                <?php echo $this->translate('Organizations'); ?> (<?php echo $this->organisationCount; ?>)
                            </a>
                        </li>
                        <li>
                            <a id="initiatives_tab"  class="tab_heading" href="javascript:void(0);"  onclick="selected_ui('initiatives_tab')">
                                <?php echo $this->translate('Initiatives'); ?> (<?php echo $this->initiativesCount; ?>)
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Menu Content-->
            <div id="search_container">

                <div class="count_found">
                    <?php if ( $tab_link == 'members_tab') : ?>
                        <?php echo $this->translate(array('%s member(s) found', '%s member(s) found',
                        $this->paginator->getTotalItemCount()),
                        $this->locale()->toNumber($this->paginator->getTotalItemCount()) ) ?>
                    <?php elseif ( $tab_link == 'projects_tab') : ?>
                        <?php echo $this->translate(array('%s project(s) found', '%s project(s) found',
                        $this->paginator->getTotalItemCount()),
                        $this->locale()->toNumber($this->paginator->getTotalItemCount()) ) ?>
                    <?php elseif ( $tab_link == 'organizations_tab') : ?>
                        <?php echo $this->translate(array('%s organisation(s) found', '%s organisation(s) found',
                        $this->paginator->getTotalItemCount()),
                        $this->locale()->toNumber($this->paginator->getTotalItemCount()) ) ?>
                    <?php elseif ( $tab_link == 'initiatives_tab') : ?>
                        <?php echo $this->translate(array('%s initiative(s) found', '%s initiative(s) found',
                        $this->paginator->getTotalItemCount()),
                        $this->locale()->toNumber($this->paginator->getTotalItemCount()) ) ?>
                    <?php else: ?>
                        <?php echo $this->translate(array('%s record found', '%s record(s) found',
                        $this->paginator->getTotalItemCount()),
                        $this->locale()->toNumber($this->paginator->getTotalItemCount()) ) ?>
                    <?php endif; ?>
                </div>

                <?php if ( $tab_link == 'projects_tab') :?>
                    <div class="goal_drop_down">

                        <?php
                            $category = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding')->getAllCategories();
                            $goals = Engine_Api::_()->getDbTable('sdggoals','sitecrowdfunding')->getSDGGoals();
                            $targets = Engine_Api::_()->getDbTable('sdgtargets','sitecrowdfunding')->getSDGTargets();
                        ?>
                        <!-- Goal -->
                        <div class="goals_search_form">
                            <div id="sdg_goal_id-wrapper" class="form-wrapper">
                                <div id="sdg_goal_id-label" class="form-label"></div>
                                <div id="sdg_goal_id-element" class="form-element">
                                    <select name="sdg_goal_id" id="sdg_goal_id" tabindex="0">
                                        <option value="0">Select SDG Goal</option>
                                        <?php foreach( $goals as $key => $value ): ?>
                                            <?php
                                              $targetArr = explode(" ",$value);
                                              $numIndexing = "SDG GOAL ".rtrim($targetArr[0], ". ");
                                              $value = str_replace($targetArr[0]," ",$value);
                                             ?>
                                            <option value="<?php echo $key?>"><?php echo $numIndexing.': '.$value;?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="category_search_form">
                            <div id="search-category_id-wrapper" class="form-wrapper">
                                <div id="search-category_id-label" class="form-label"></div>
                                <div id="search-category_id-element" class="form-element">
                                    <select name="search-category_id" id="search-category_id" tabindex="0">
                                        <option value="0">Select Category</option>
                                        <?php foreach( $category as $key => $value ): ?>
                                            <option value="<?php echo $value['category_id']?>"><?php echo $value['category_name'];?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <br>

                    </div>
                <?php endif; ?>

                <div class="pagination">
                    <?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl","sitecrowdfunding")); ?>
                </div>

                <!-- Empty Content-->
                <?php if( empty($this->paginator) || ($this->paginator->getTotalItemCount() <= 0) ): ?>
                    <?php if ( $tab_link == 'members_tab') : ?>
                        <?php $message = "Your search - <b>".$this->query."</b> did not match any members";?>
                    <?php elseif ( $tab_link == 'projects_tab') : ?>
                        <?php $message = "Your search - <b>".$this->query."</b> did not match any projects";?>
                    <?php elseif ( $tab_link == 'organizations_tab') : ?>
                        <?php $message = "Your search - <b>".$this->query."</b> did not match any organisations";?>
                    <?php elseif ( $tab_link == 'initiatives_tab') : ?>
                        <?php $message = "Your search - <b>".$this->query."</b> did not match any initiatives";?>
                    <?php else: ?>
                        <?php $message = "Your search - <b>".$this->query."</b> did not match any records";?>
                    <?php endif; ?>
                    <div class="tip">
                        <span>
                          <?php echo $message ?>
                        </span>
                    </div>
                <?php endif; ?>


                <!-- All Tab-->
                <?php if ( $tab_link == 'all_tab') : ?>
                    <div id="all_tab">
                        <div class="cardview">
                            <?php include APPLICATION_PATH . '/application/modules/Core/views/scripts/_searchGrid.tpl'; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Member Tab-->
                <?php if ( $tab_link == 'members_tab') : ?>
                    <div id="members_tab">
                        <div class="cardview">
                            <?php include APPLICATION_PATH . '/application/modules/Core/views/scripts/_searchGrid.tpl'; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Project Tab-->
                <?php if ( $tab_link == 'projects_tab') : ?>
                    <div id="projects_tab">
                        <div id="all">
                            <div class="cardview">
                                <?php include APPLICATION_PATH . '/application/modules/Core/views/scripts/_searchGrid.tpl'; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Organization Tab-->
                <?php if ( $tab_link == 'organizations_tab') : ?>
                    <div id="projects_tab">
                        <div class="cardview">
                            <?php include APPLICATION_PATH . '/application/modules/Core/views/scripts/_searchGrid.tpl'; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Initiative Tab-->
                <?php if ( $tab_link == 'initiatives_tab') : ?>
                    <div id="initiatives_tab">
                        <div class="cardview">
                            <?php include APPLICATION_PATH . '/application/modules/Core/views/scripts/_searchGrid.tpl'; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <div id="hidden_ajax_data" style="display: none;"></div>

        </div>
    </div>
</div>


<script>

    var $j = jQuery.noConflict();

    // called when page is loaded
    window.addEvent('domready', function () {

        // delete the above search form
        $j('#sitecoretheme_fullsite_search').remove();

        // set menu highlight
        if ($('all_tab')) {
            $('all_tab').removeClass('active');
        }
        if ($('members_tab')) {
            $('members_tab').removeClass('active');
        }
        if ($('projects_tab')) {
            $('projects_tab').removeClass('active');
        }
        if ($('organizations_tab')) {
            $('organizations_tab').removeClass('active');
        }
        if ($('initiatives_tab')) {
            $('initiatives_tab').removeClass('active');
        }

        if ($('searched_from_page').value === "null" || $('searched_from_page').value === "" || $('searched_from_page').value === null ) {
            $('type').hide();
        }

        // add search click event
        $('search').addEvent('click', function (e) {
            $('page_no').value = 1;
            $('tab_link').value = null;
            e.stop();
            $('search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
            ajaxRenderData();
        });

        // highlight the tab by default
        var tabLink = $('tab_link').value;
        $(tabLink).addClass('active');

        // if goal_id is passed then highlight those only
        $j(document).ready(function() {

            // sgd_goal_id
            $j('#sdg_goal_id').selectric().on('change', function() {
                $('selected_goal_id').value = $j(this).val();
                $('search_container').innerHTML = '<div class="clr"></div><div class="seaocore_content_loader"></div>';
                ajaxRenderData();
            });
            var selected_goal_id = "<?php echo $this->sdg_goal_id;?>";
            $j('#sdg_goal_id').prop('selectedIndex', selected_goal_id).selectric('refresh');

            // category_id
            $j('#search-category_id').selectric().on('change', function() {
                $('selected_category_id').value = $j(this).val();
                $('search_container').innerHTML = '<div class="clr"></div><div class="seaocore_content_loader"></div>';
                ajaxRenderData();
            });
            var selected_category_id = "<?php echo $this->category_id;?>";
            $j('#search-category_id').val(selected_category_id).selectric('refresh');

        });

    });


    // page action
    function pageAction(page) {
        $('page_no').value = page;
        $('paginate_search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
        ajaxRenderData();
    }

    function selected_ui(tabLink){

        $('tab_link').value = tabLink;
        $('page_no').value = 1;

        $('selected_goal_id').value = null;
        $('selected_category_id').value = null;

        // remove active from all class
        if($('all_tab')){
            $('all_tab').removeClass('active');
        }
        if($('members_tab')){
            $('members_tab').removeClass('active');
        }
        if($('projects_tab')){
            $('projects_tab').removeClass('active');
        }
        if($('organizations_tab')){
            $('organizations_tab').removeClass('active');
        }
        if($('initiatives_tab')){
            $('initiatives_tab').removeClass('active');
        }

        // Add class
        $(tabLink).addClass('active');
        $('search_container').innerHTML = '<div class="clr"></div><div class="seaocore_content_loader"></div>';
        ajaxRenderData();

    }

    function ajaxRenderData() {

        var params = {
            requestParams: <?php echo json_encode($this->params) ?>
        };

        var request = new Request.HTML({
            url: en4.core.baseUrl + "search/index/page",
            data: {
                format: 'html',
                subject: en4.core.subject.guid,
                page_no: $('page_no').value,
                tab_link: $('tab_link').value,
                searched_from_page: $('searched_from_page').value,
                searched_from_page_id: $('searched_from_page_id').value,
                searched_from_initiative_id: $('searched_from_initiative_id').value,
                searched_from_project_id: $('searched_from_project_id').value,
                query: $('query').value,
                type: $('type').value,
                sdg_goal_id: $('selected_goal_id').value,
                category_id: $('selected_category_id').value
            },
            evalScripts: true,
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {

                $('hidden_ajax_data').innerHTML = responseHTML;

                // set menu label
                $('search_container').innerHTML = $('hidden_ajax_data').getElement('#search_container').innerHTML;

                // set tab menus
                $('all_tab').innerHTML = $('hidden_ajax_data').getElement('#all_tab').innerHTML;
                $('members_tab').innerHTML = $('hidden_ajax_data').getElement('#members_tab').innerHTML;
                $('projects_tab').innerHTML = $('hidden_ajax_data').getElement('#projects_tab').innerHTML;
                $('organizations_tab').innerHTML = $('hidden_ajax_data').getElement('#organizations_tab').innerHTML;
                $('initiatives_tab').innerHTML = $('hidden_ajax_data').getElement('#initiatives_tab').innerHTML;

                $('hidden_ajax_data').innerHTML = '';

                if ($('paginate_search_spinner')) {
                    $('paginate_search_spinner').innerHTML = '';
                }

                if ($('search_spinner')) {
                    $('search_spinner').innerHTML = '';
                }

                fundingProgressiveBarAnimation();
                Smoothbox.bind($('transaction_container'));
                en4.core.runonce.trigger();
            }
        });
        request.send();
    }

    function resetGoalFilter(){
        $j('#sdg_goal_id').prop('selectedIndex', 0).selectric('refresh');
        $j('#category_id').prop('selectedIndex', 0).selectric('refresh');
        $('tab_link').value= 'projects_tab';
        ajaxRenderData();
    }

</script>

<style>

    .cardview {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
    }

    .search_result {
        box-shadow: 0 2px 2px 0 rgba(0, 0, 0, .14), 0 3px 1px -2px rgba(0, 0, 0, .2), 0 1px 5px 0 rgba(0, 0, 0, .12);
        display: flex !important;
        flex-direction: column;
        width: 267px;
        height: 273px;
        margin: 10px;
        padding: 10px;
    }

    .search_result img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    /* Search Form */
    #searchform {
        float: left;
        clear: right;
        padding: 5px;
        width: 100%;
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 6px;
    }

    .search_form_container {
        text-align: center;
    }

    .search_form_container > h2 {
        margin-bottom: 14px;
        font-size: 23px !important;
    }

    .search_photo {
        display: block;
        float: unset !important;
        overflow: hidden;
        width: 100%;
        height: 158px;
        margin-right: unset !important;
    }

    .search_info {
        margin-top: 8px;
    }

    .tabs.sitecrowdfunding_nav {
        border-bottom: 1px solid #e4e3e3;
        display: flex;
        justify-content: center;
    }

    ul.transaction_menu_nav.navigation {
        margin-bottom: 14px;
    }

    p.search_description {
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        padding: 6px;
        margin-left: -5px;
    }

    .search_title {
        font-weight: 500;
        font-size: 16px !important;
        color: #201f1f;
    }

    .search_result .highlighted-text {
        font-weight: bold;
        color: #0a0a0a !important;
    }

    .active {
        border-color: #44AEC1 !important;
        color: #44AEC1 !important;
    }

    .tab_heading {
        font-size: 18px !important;
    }

    element.style {
    }
    .selectric-open .selectric {
        border-color: #c4c4c4;
    }

    .selectric-items {
        width: 235px !important;
    }
    /* Pagination Count */
    .count_found {
        display: flex;
        justify-content: center;
        font-size: 18px;
        font-weight: 800;
        margin-top: 9px;
        margin-bottom: 20px;
    }

    /* Display Label  */
    .search_type {
        display: flex;
    }

    #user_type,
    #project_type,
    #organization_type,
    #initiative_type {
        border-radius: 3px;
        padding: 1px 5px;
        color: white;
        text-align: center;
    }

    #initiative_type {
        background-color: #61b661;
    }

    #user_type {
        background-color: #206a8d;
    }

    #project_type {
        background-color: #d32727;
    }

    #organization_type {
        background-color: #3939cf;
    }

    /* Error Container */
    #error_msg_container {
        display: flex;
        padding: 7px;
        color: #D8000C;
    }

    #error_msg_outer_container {
        margin-top: 11px;
    }
    /* Goal dopdown view css */
    .goal_drop_down{
        display: flex;
        justify-content: center;
        font-weight: 800;
    }
    .selectric .label{
        font-size: 14px !important;
    }
    .selectric {
        border: 2px solid #DDD;
        border-radius: 29px;
        background: #F8F8F8;
        position: relative;
        overflow: hidden;
    }

    @media(min-width:1200px){
        .selectric {
            width: 430px !important;

        }
        .selectric-items {
            width: 430px !important;
        }
    }
    @media(min-width:768px) and (max-width:991px){

        .selectric {
            width: 350px !important;

        }
        .selectric-items {
            width: 350px !important;
        }
    }
    @media(min-width:992px) and (max-width:1024px){
        .selectric {
            width: 350px !important;

        }
        .selectric-items {
            width: 350px !important;
        }
    }
    @media (max-width: 767px) {
        #searchform input {
            width: 60% !important;
            margin-right: 10px !important;
        }
        .project_count_dropdown {
            flex-direction: column !important;
            align-items: center;
            display: flex;
        }
        .selectric {
            width: 296px !important;

        }
        .selectric-items {
            width: 296px !important;
        }
    }
</style>