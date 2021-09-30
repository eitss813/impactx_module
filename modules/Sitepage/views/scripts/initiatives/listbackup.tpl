<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: profiltype.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $defaultLogo = $this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/nophoto_page_thumb_profile.png'; ?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/sitepage_dashboard_main_header.tpl'; ?>

<div class="generic_layout_container layout_middle">
    <div class="generic_layout_container layout_core_content">

        <div class="layout_middle">
            <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
            <?php echo $this->
            partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array(
            'sitepage_id'=>$this->sitepage->page_id,'sectionTitle'=> 'Initiatives', 'sectionDescription' => '')); ?>

            <div class="sitepage_edit_content">
                <div id="show_tab_content">
                    <div class="fright">
                        <?php echo $this->htmlLink(array('module'=>'sitepage', 'controller'=> 'initiatives' ,
                        'action'=>'create', 'page_id' => $this->page_id), $this->translate('Add Initiatives'),
                        array('class' => 'button seaocore_icon_add')) ?>
                    </div>
                    <br/><br/>

                    <div class="initiatives-div">

                        <div class="organization-list">
                            <?php if(count($this->initiatives) > 0): ?>
                            <?php foreach($this->initiatives as $initiative): ?>
                            <?php $item = Engine_Api::_()->getItem('sitepage_initiative', $initiative['initiative_id']); ?>

                            <div class="initiative_item">

                                <div class="initiative_item_info">
                                    <div class="initiative_header">

                                        <div class="fright" id="fright">
                                            <?php echo $this->htmlLink(
                                            array(
                                            'route' => 'sitepage_initiatives',
                                            'controller' => 'initiatives',
                                            'action' => 'landing-page',
                                            'page_id' => $this->page_id,
                                            'initiative_id' => $initiative['initiative_id'],
                                            ),
                                            $this->translate('View'), array(
                                            'class' => 'button seaocore_icon_view outcome_btn'
                                            )) ?>

                                            <?php echo $this->htmlLink(
                                            array(
                                            'route' => 'sitepage_initiatives',
                                            'controller' => 'initiatives',
                                            'action' => 'edit',
                                            'initiative_id' => $initiative['initiative_id'],
                                            'page_id' => $this->page_id,
                                            ),
                                            $this->translate('Edit'), array(
                                            'class' => 'button seaocore_icon_edit outcome_btn'
                                            )) ?>

                                            <?php echo $this->htmlLink(
                                            array(
                                            'route' => 'sitepage_initiatives',
                                            'controller' => 'initiatives',
                                            'action' => 'delete',
                                            'initiative_id' => $initiative['initiative_id'],
                                            'page_id' => $this->page_id,
                                            ),
                                            $this->translate('Delete'), array(
                                            'class' => 'button smoothbox seaocore_icon_remove outcome_btn',
                                            )) ?>

                                        </div>
                                    </div>
                                    <br/><br/>
                                    <?php $initiativesURL = $this->url(array('action' => 'landing-page','page_id' => $this->page_id, 'initiative_id' => $initiative['initiative_id']), "sitepage_initiatives");
                                    ?>
                                    <a href="<?php echo $initiativesURL?>" title="<?php echo $initiative['title']?>">

                                        <div class="initiative_item_thumb" style="display: flex;justify-content: center">
                                            <div class="image_container">
                                                <img class="img" style="margin-right: 10px;width: 100%;height: 300px;object-fit: contain;" src="<?php echo !empty($item['logo']) ? $item->getLogoUrl('thumb.cover') : $defaultLogo; ?>"/>
                                            </div>
                                        </div>
                                    </a>
                                    <br/>
                                    <div class="title">
                                        <?php echo $this->htmlLink(
                                        array(
                                        'route' => 'sitepage_initiatives',
                                        'controller' => 'initiatives',
                                        'action' => 'landing-page',
                                        'page_id' => $this->page_id,
                                        'initiative_id' => $initiative['initiative_id'],
                                        ), $initiative['title'] , array(
                                        'class' => 'initiative_title'
                                        )) ?>
                                    </div>
                                    <br/>
                                    <div>
                                        <?php $sections = preg_split('/[,]+/', $initiative['sections']); ?>
                                        <?php $sections = array_filter(array_map("trim", $sections)); ?>

                                        <?php if(count($sections) != 0):?>
                                        <div class="initiative_about_container">
                                            <h3 class="initiative_about_header">Project Galleries</h3><br/>
                                            <?php foreach($sections as $section): ?>
                                            <div class="projects_section">
                                                <?php echo $section; ?>
                                            </div>
                                            <?php endforeach;?>
                                        </div>
                                        <br/>
                                        <?php endif; ?>

                                        <?php if(!empty($initiative['no_of_projects'])):?>
                                        <div class="initiative_about_container">
                                            <h3 class="initiative_about_header">Number of Projects:</h3><?php echo $initiative['no_of_projects']; ?>
                                        </div>
                                        <br/>
                                        <?php endif; ?>

                                        <?php if(!empty($initiative['no_of_families_helped'])):?>
                                        <div class="initiative_about_container">
                                            <h3 class="initiative_about_header">Families Helped:</h3><?php echo $initiative['no_of_families_helped']; ?>
                                        </div>
                                        <br/>
                                        <?php endif; ?>

                                        <?php if(!empty($initiative['no_of_children_bettered'])): ?>
                                        <div class="initiative_about_container">
                                            <h3 class="initiative_about_header">Children Bettered:</h3><?php echo $initiative['no_of_children_bettered']; ?>
                                        </div>
                                        <br/>
                                        <?php endif; ?>

                                        <?php if(!empty($initiative['no_of_funding_to_date'])):?>
                                        <div class="initiative_about_container">
                                            <h3 class="initiative_about_header">Funding to Date:</h3><?php echo $initiative['no_of_funding_to_date']; ?>
                                        </div>
                                        <br/>
                                        <?php endif; ?>

                                        <?php $initiativeMetrics = Engine_Api::_()->getItemTable('sitepage_initiativemetric')->getAllInitiativesMetricById($this->page_id, $initiative['initiative_id']); ?>
                                        <?php if(count($initiativeMetrics) != 0):?>
                                        <div class="initiative_metric_container">
                                            <h3 class="initiative_about_header">Metrics</h3>
                                            <?php foreach($initiativeMetrics as $initiativeMetric): ?>
                                            <div class="initiative_metrics">
                                                <?php echo $initiativeMetric['initiativemetric_name']; ?> - <?php echo $initiativeMetric['initiativemetric_value']; ?>
                                            </div>
                                            <?php endforeach;?>
                                        </div>
                                        <br/>
                                        <?php endif; ?>

                                        <?php if(!empty($initiative['about'])):?>
                                        <div class="initiative_about_container">
                                            <h3 class="initiative_about_header">About:</h3>
                                            <?php echo $initiative['about']; ?>
                                        </div>
                                        <br/>
                                        <?php endif; ?>

                                        <?php if(!empty($initiative['back_story'])):?>
                                        <div class="initiative_back_story_container">
                                            <h3 class="initiative_back_story_header">Backstory:</h3>
                                            <?php echo $initiative['back_story']; ?>
                                        </div>
                                        <?php endif; ?>

                                    </div>
                                </div>
                            </div>

                            <?php endforeach;?>

                            <?php else: ?>

                            <div class="tip">
                                    <span>
                                        <?php echo $this->translate('No Initiatives'); ?>
                                    </span>
                            </div>

                            <?php endif; ?>
                        </div>
                    </div>

                    <br/><br/>

                </div>
            </div>
        </div>
    </div>
</div>

<style type="text/css">

    .initiatives-div {
        padding-top: 20px
    }

    .initiative_item {
        padding: 10px;
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        margin-bottom: 10px;
    }

    .initiative_about_header, .initiative_back_story_header {
        text-decoration: underline;
        font-weight: bold;
    }

    .initiative_header{
        padding: 20px;
    }

    .initiative_title{
        font-size: 15px;
        margin-bottom: 5px;
        font-weight: bold;
    }

    .projects_section{
        border: 2px solid #b6b6b675;
        display: inline;
        padding: 7px 16px;
        font-size: 14px;
        margin: 0 10px;
        border-radius: 20px;
    }

    .title{
        display: flex;
        justify-content: center;
        font-size: 24px !important;
    }
    .image_container {
        height: 300px;
        width: 50%;
    }
    @media (max-width:767px){
        #fright {
            display: flex;
            position: relative;
            top: 20px;
        }
        a.button.seaocore_icon_view.outcome_btn ,
        a.button.seaocore_icon_edit.outcome_btn ,
        a.button.seaocore_icon_remove.outcome_btn {
            margin-bottom: 5%;
            width: max-content;
            margin-right: 2%;
            display: flex;
            justify-content: center;
        }
        .initiative_item_thumb img {
            width: 75% !important;
        }
        .projects_section {
            display: block !important;
            margin-bottom: 14px !important;
            text-align: center !important;
        }
        .fright {
            float: unset !important;
        }
    }
</style>