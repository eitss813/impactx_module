<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css'); ?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/Adintegration.tpl';
?>

<div class="sitecrowdfunding_view_top">
    <?php echo $this->htmlLink($this->project->getHref(), $this->itemPhoto($this->project, 'thumb.icon', '', array('align' => 'left'))) ?>
    <h2>	
        <?php echo $this->translate($this->project->__toString()) ?>	
        <?php echo $this->translate('&raquo; '); ?>
        <?php echo $this->htmlLink($this->project->getHref(array('tab' => $this->tab_selected_id)), $this->translate('Discussions')) ?>
    </h2>
</div>

<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.adtopicview', 3) && $project_communityad_integration): ?>
    <div class="layout_right" id="communityad_adtopicview">
        <?php echo $this->content()->renderWidget("communityad.ads", array("itemCount" => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.adtopicview', 3), "loaded_by_ajax" => 0, 'widgetId' => 'project_adtopicview')); ?>
    </div>
<?php endif; ?>

<div class="layout_middle">
    <div class="sitecrowdfunding_sitecrowdfundings_options">

        <?php echo $this->htmlLink($this->project->getHref(), $this->translate("Back to Project"), array('class' => 'buttonlink seaocore_txt_green seaocore_back_icon')) ?>
        <?php
        if ($this->can_post) {
            echo $this->htmlLink(array('route' => "sitecrowdfunding_extended", 'controller' => 'topic', 'action' => 'create', 'project_id' => $this->project->getIdentity(), 'content_id' => $this->tab_selected_id), $this->translate('Post New Topic'), array('class' => 'buttonlink icon_sitecrowdfunding_post_new'));
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

    <ul class="sitecrowdfunding_sitecrowdfundings">
        <?php foreach ($this->paginator as $topic): ?>
            <?php
            $lastpost = $topic->getLastPost();
            $lastposter = Engine_Api::_()->getItem('user', $topic->lastposter_id);
            ?>
            <li>
                <div class="sitecrowdfunding_sitecrowdfundings_replies seaocore_txt_light">
                    <span>
                        <?php echo $this->locale()->toNumber($topic->post_count - 1) ?>
                    </span>
                    <?php echo $this->translate(array('reply', 'replies', $topic->post_count - 1)) ?>
                </div>

                <div class="sitecrowdfunding_sitecrowdfundings_lastreply">
                    <?php echo $this->htmlLink($lastposter->getHref(), $this->itemPhoto($lastposter, 'thumb.icon')) ?>
                    <div class="sitecrowdfunding_sitecrowdfundings_lastreply_info">
                        <?php echo $this->htmlLink($lastpost->getHref(), $this->translate('Last Post')) ?> <?php echo $this->translate('by'); ?> <?php echo $lastposter->__toString() ?>
                        <br />
                        <?php echo $this->timestamp(strtotime($topic->modified_date), array('tag' => 'div', 'class' => 'sitecrowdfunding_sitecrowdfundings_lastreply_info_date seaocore_txt_light')) ?>
                    </div>
                </div>

                <div class="sitecrowdfunding_sitecrowdfundings_info">
                    <h3<?php if ($topic->sticky): ?> class='sitecrowdfunding_sitecrowdfundings_sticky'<?php endif; ?>>
                        <?php echo $this->htmlLink($topic->getHref(), $this->translate($topic->getTitle())) ?>
                    </h3>
                    <div class="sitecrowdfunding_sitecrowdfundings_blurb">
                        <?php echo $this->viewMore(strip_tags($this->translate($topic->getDescription()))) ?>
                    </div>
                </div>

            </li>
        <?php endforeach; ?>
    </ul>
    <?php if ($this->paginator->count() > 1): ?>
        <div>
            <?php echo $this->paginationControl($this->paginator) ?>
        </div>
    <?php endif; ?>
</div>