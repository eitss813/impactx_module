<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit_tabs.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="headline">
    <span style="font-size: 24px">
        <?php $metrics = Engine_Api::_()->getItem('sitepage_metric', $this->metric_id);?>
        Dashboard: <?php
                    echo $this->htmlLink(array(
                        'route' => 'sitepage_metrics',
                        'action' =>'index',
                        'metric_id' => $metrics->metric_id
                        ),
                        $metrics['metric_name']
                    )
                    ?>

  <div class="section_header_info" style="display: flex;flex-direction: column;align-items: center;">
        <div class="btn_container_custom">
            <?php echo $this->htmlLink(array(
                'route' => 'sitepage_metrics',
                'action' =>'index',
                'metric_id' => $metrics->metric_id
                ),
                'View Metrics',
                array("class" => 'common_btn_custom submit_for_approval_btn')
            )?>
        </div>
    </div>

    </span>
</div>
<style>
    .headline .status_container {
        background: gray;
        padding: 7px;
        border-radius: 5%;
        min-width: 120px;
        text-align: center;
        margin-left: 10px;
    }

    .headline {
        display: flex;
    }

    .headline span {
        display: flex;
        align-items: center;
    }

    .section_header_info {
        display: flex;
        flex-direction: column;
        align-items: center;
        float: right;
        position: absolute;
        right: 4%;
    }

    @media (max-width: 767px) {
        .status_container {
            margin-left: 0px !important;
        }

        .headline a {
            text-align: center !important;
            margin-bottom: 8px;
            margin-top: 8px;
        }

        .headline span {
            flex-direction: column;
        }

        .section_header_info {
            position: unset !important;
        }
    }
</style>