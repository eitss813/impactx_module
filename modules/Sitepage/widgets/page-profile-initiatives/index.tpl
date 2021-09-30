<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php $defaultLogo = $this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/nophoto_page_thumb_profile.png'; ?>

<?php
$currentLink = 'all';
if(isset($this->params['initiatives_link']) && !empty($this->params['initiatives_link'])){
    $currentLink = $this->params['initiatives_link'];
}
?>

<div class="layout_core_container_tabs">

    <?php if($this->isPartnersPresentYN == true):?>
    <?php if(count($this->allPartnerPages) > 0):?>
        <div class="sitepage_page_top_links b_medium">
            <div class="sitepage_page_initiatives_top_filter_links txt_center sitepage_page_initiatives_top_filter_links">

                <a href="javascript:void(0);" id='all_initiatives' onclick="filter_initiative_rsvp('all')">
                    <?php echo $this->translate('All'); ?> (<?php echo $this->allTabCount; ?>)
                </a>

                <?php foreach($this->allPartnerPages as $allPartnerPage): ?>
                    <a href="javascript:void(0);" id='<?php echo $allPartnerPage->page_id ?>_initiatives'
                       onclick="filter_initiative_rsvp('<?php echo $allPartnerPage->page_id ?>')">
                        <?php echo $this->translate($allPartnerPage->title); ?> (<?php echo $allPartnerPage->initiatives_count; ?>)
                    </a>
                <?php endforeach;?>

            </div>
        </div>
    <?php endif; ?>
    <?php endif; ?>

    <br/><br/>

    <div id='sitepage_page_initiatives_content'>
        <div class="sr_card_view">
            <ul>
                <?php foreach($this->initiatives as $initiative): ?>
                    <?php
                       $item = Engine_Api::_()->getItem('sitepage_initiative', $initiative['initiative_id']);
                      $projects = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectsCountByPageIdAndInitiativesId($initiative['page_id'],$item['initiative_id']);
                   ?>

                    <li class="wow slideInUp animated" style="height: 349px; visibility: visible;">
                        <div class="sr_card_view_image">
                            <?php $initiativesURL = $this->url(array('action' => 'landing-page','page_id' => $this->page_id, 'initiative_id' => $initiative['initiative_id']), "sitepage_initiatives");
                            ?>
                            <a  href="<?php echo $initiativesURL?>" title="<?php echo $initiative['title']?>">
                                <span class="aspect-ratio" style="background-image: url(<?php echo !empty($item['logo']) ? $item->getLogoUrl('thumb.cover') : $defaultLogo; ?>); ">
                                </span>
                            </a>
                            <span class="sr_card_view_image_hover">
                                <i title="Featured" class="sr_icon seaocore_icon_featured"></i>
                            </span>
                        </div>
                        <div class="sr_card_view_info">
                            <div class="sr_title">
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
                            <div class="project_count">
                                <span > <?php echo $projects; ?> Projects</span>
                            </div>
                            <div class="listing_description">
                                <?php echo $this->string()->truncate($this->string()->stripTags($initiative['about']), 75); ?>
                            </div>
                        </div>
                    </li>

                <?php endforeach;?>
            </ul>
            <?php if(count($this->initiatives) == 0):?>
            <div class="tip">
                <span>
                    <?php echo $this->translate('No Initiatives Found.'); ?>
                </span>
            </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<div id="hidden_ajax_page_initiatives_data" style="display: none;"></div>

<script type="text/javascript">
    // active the option
    var currentLink = "<?php echo $currentLink; ?>"+'_initiatives';
    var allLinks = $$('div.sitepage_page_initiatives_top_filter_links > a');
    allLinks.removeClass('active');
    $(currentLink).addClass('active');

    function addInitiativeBoldClass(reqType) {
        $$('div.sitepage_page_initiatives_top_filter_links > a').each(function (el) {
            el.removeClass('active');
        });
        $(reqType+'_initiatives').addClass('active');
    }

    function filter_initiative_rsvp(req_type) {
        addInitiativeBoldClass(req_type);
        var url = null;
        switch (req_type) {
            case 'all':
                url = en4.core.baseUrl + 'widget/index/mod/sitepage/name/page-profile-initiatives/initiatives_link/all';
                break;
            default:
                url = en4.core.baseUrl + 'widget/index/mod/sitepage/name/page-profile-initiatives/initiatives_link/' + req_type;
                break;
        }
        $('sitepage_page_initiatives_content').innerHTML = '<div class="clr"></div><div class="seaocore_content_loader"></div>';

        var params = {
            requestParams: <?php echo json_encode($this->params) ?>
        }

        var request = new Request.HTML({
            url: url,
            data: $merge(params.requestParams, {
                format: 'html',
                subject: en4.core.subject.guid,
                is_ajax: 0,
                pagination: 0,
                page: 0,
            }),
            evalScripts: true,
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $('hidden_ajax_page_initiatives_data').innerHTML = responseHTML;
                $('sitepage_page_initiatives_content').innerHTML = $('hidden_ajax_page_initiatives_data').getElement('#sitepage_page_initiatives_content').innerHTML;
                $('hidden_ajax_page_initiatives_data').innerHTML = '';
                Smoothbox.bind($('sitepage_page_initiatives_content'));
                en4.core.runonce.trigger();
            }
        });
        request.send();
    }
</script>

<style>
    .sr_card_view {
        overflow: hidden;
        margin-bottom: 15px;
        clear: both;
    }

    @media only screen and (min-width: 1200px) {
        .sr_card_view ul > li {
            width: 335px;
        }
    }

    @media only screen and (max-width: 1199px) {
        .sr_card_view ul > li {
            width: 31.5%;
        }
    }

    @media only screen and (max-width: 767px) {
        .sr_card_view ul > li {
            width: 47.9%;
        }
    }

    @media only screen and (max-width: 600px) {
        .sr_card_view ul > li {
            width: 98%;
        }
    }

    .sr_card_view ul > li {
        display: inline-block;
        box-sizing: border-box;
        position: relative;
        vertical-align: top;
        overflow: hidden;
        margin: 5px 5px 8px 5px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
    }

    .sr_card_view ul > li {
        border-bottom: 2px solid transparent;
        background-color: #fff;
        border: 0 solid transparent;
        border-radius: 0;
    }

    .slideInUp {
        -webkit-animation-name: slideInUp;
        animation-name: slideInUp;
    }

    .sr_card_view_image {
        overflow: hidden;
        border-bottom: 1px solid rgba(0, 0, 0, .04);
    }

    .sr_card_view_info {
        padding: 15px;
        position: relative;
    }

    .sr_card_view .listing_readmore {
        position: absolute;
        bottom: 15px;
        width: 100%;
        text-align: center;
    }

    .sr_card_view_image_hover {
        position: absolute;
        right: 10px;
        top: -10px;
        opacity: 0;
        transition: all 0.35s ease-in-out 0s;
    }

    .sr_card_view ul > li a > span {
        background-size: cover;
        background-position: center 50%;
        background-repeat: no-repeat;
        display: block;
        height: 210px;
        margin: 0 auto;
    }

    .sr_card_view_image_hover i, .sr_card_view_image_hover a {
        margin: 1px;
        border-radius: 2px;
        vertical-align: middle;
        border: 1px solid transparent;
        transition: background-color 0.2s ease-in-out 0s;
    }

    .sr_card_view .sr_title {
        font-size: 16px;
        font-weight: bold;
        text-align: center;
    }

    .sr_card_view_info > div {
        margin-bottom: 6px;
        text-align: center;
    }

    .sr_card_view .listing_description {
        height: 62px;
        overflow: hidden;
    }

    .sr_card_view ul > li:hover {
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);
    }

    .sr_card_view ul > li:hover {
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);
        border-bottom: 2px solid #44AEC1;
        transition: box-shadow .3s linear;
    }

    .block_title {
        border-bottom: 0;
        padding-bottom: 10px;
        text-transform: capitalize;
        background: transparent;
        font-size: 28px;
        margin-bottom: 20px;
        text-align: center;
        position: relative;
        line-height: normal;
    }

    .block_title::before {
        left: 0;
        margin: 0 auto;
        right: 0;
        text-align: center;
        width: 85px;
        background: #44AEC1;
        top: 100%;
        content: "";
        display: block;
        min-height: 2px;
        position: absolute;
    }

    .block_title {
        font-size: 30px;
    }

    .intro_desc {
        font-size: 18px !important;
        line-height: 30px;
    }
    .project_count{
        color: black;
        font-weight: 500;
        font-size: 14px;
    }
    .aspect-ratio{
        background-size: cover !important;
        height: 200px !important;
     }
    .sr_card_view ul{
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
    }
    .sitecrowdfunding_thumb_wrapper a i, .featured_slidshow_img a i {
        background-position: unset !important;
    }
    .sitepage_page_initiatives_top_filter_links .active {
        color: #44AEC1;
    }
    .sitepage_mypages_top_links a:last-child,
    .sitepage_page_top_links a:last-child {
        border-right: none;
    }
</style>