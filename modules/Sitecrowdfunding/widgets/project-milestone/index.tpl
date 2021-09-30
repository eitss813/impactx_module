<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $defaultLogo = $this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/nophoto_page_thumb_profile.png'; ?>
<!-- <div class="milestone-div">
    <?php if(count($this->milestones) > 0): ?>
        <div>
            <?php foreach($this->milestones as $milestone): ?>
                <ul class="milestone-list" >
                    <li class="milestone-list-item">
                        <div class="milestone-first-section">
                            <div class="milestone_img_div">
                                <img class="milestone_img" src="<?php echo !empty($milestone['logo']) ? $milestone['logo'] : $defaultLogo; ?>"/>
                            </div>
                            <div class="milestone-list-item-first">
                                <div class="first-item-sub">
                                    <div class="first-item-sub-title">
                                        <h2><?php echo $milestone['title']; ?></h2>
                                        <div class="milestone-status-dates-container">
                                            <span class="status_text"><?php echo  $this->statusLabels[$milestone['status']]; ?></span>
                                            <div class="milestone-dates">
                                                <span class="milestone-start-date"><?php echo  date('M-d-Y',strtotime($milestone['start_date'])); ?></span>
                                                <span>To</span>
                                                <span class="milestone-end-date"><?php echo  date('M-d-Y',strtotime($milestone['end_date'])); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="milestone-description-1" ><?php echo  $milestone['description']; ?></span>
                                </div>
                            </div>
                        </div>
                        <span class="milestone-description-2"><?php echo  $milestone['description']; ?></span>
                    </li>
                </ul>
            <?php endforeach;?>
        </div>
    <?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate('This project does not have any milestones yet.'); ?>
        </span>
    </div>
    <?php endif; ?>
</div>-->
<!--<style type="text/css">
  .progress{
      position: relative;
      margin-bottom: 70px;
      margin-top: 70px;
      background-color: grey;
      height: 10px;
      border-radius: 20px;
      -webkit-box-shadow: inset 0 1px 2px rgba(0,0,0,.1);
      box-shadow: inset 0 1px 2px rgba(0,0,0,.1);
  }
  .success-color {
      background-color: #e2e2e2;
      text-align: center;
      padding-top: 5px;
  }
  .one_1, .one_2, .one_3, .one_4, .one_5 {
      position: absolute;
      margin-top: -11px;
      z-index: 1;
      height: 25px;
      width: 30px;
      border-radius: 20px;
  }
  .one_1 {
      left: 12%;
  }
  .one_2 {
      left: 30%;
  }
  .one_3 {
      left: 50%;
  }
  .one_4 {
      left: 70%;
  }
  .one_5 {
      left: 88%;
  }
  .green {
      color: #009c47;
  }
  .a01 {
      left: 7.5%;
  }
  .a02 {
      left: 26.5%;
  }
  .a03 {
      left: 46%;
  }
  .a04 {
      left: 63.5%;
  }
  .a05 {
      left: 82.5%;
  }
  .a01, .a02, .a03, .a04, .a05 {
      position: absolute;
      margin-top: 20px;
      z-index: 1;
      height: 40px;
      font-weight: 600;
      color: #5f5f5f;
      text-align: center;
      font-size: 12px;
  }
  .d01 {
      left: 8%;
  }
  .d02 {
      left: 25%;
  }
  .d03 {
      left: 45%;
  }
  .d04 {
      left: 65%;
  }
  .d05 {
      left: 83%;
  }
  .d01, .d02, .d03, .d04, .d05 {
      position: absolute;
      top: -34px;
      z-index: 1;
      height: 40px;
      font-weight: 600;
      color: #5f5f5f;
      text-align: center;
      font-size: 12px;
  }
  .progress-bar{
    float: left;
    width: 0;
    height: 100%;
    border-radius: 20px;
    font-size: 12px;
    line-height: 20px;
    color: #fff;
    text-align: center;
    background-color: #337ab7;
    -webkit-box-shadow: inset 0 -1px 0 rgba(0,0,0,.15);
    box-shadow: inset 0 -1px 0 rgba(0,0,0,.15);
    -webkit-transition: width .6s ease;
    -o-transition: width .6s ease;
    transition: width .6s ease;
  }
  .progress-bar-success {
      background-color: #07c776;
  }
  .desc01, .desc02, .desc03, .desc04, .desc05 {
      position: absolute;
      top: 40px;
      z-index: 1;
      height: 40px;
      /*font-weight: 600;*/
      color: #5f5f5f;
      text-align: center;
      font-size: 12px;
      max-width: 210px;
      min-width: 210px;
  }
  .desc01 {
      left: 4%;
  }
  .desc02 {
      left: 23%;
  }
  .desc03 {
      left: 42%;
  }
  .desc04 {
      left: 62%;
  }
  .desc05 {
      left: 81%;
  }
</style>

<div class="progress">
    <div>
        <div class="d01">01/01/2017 to 01/01/2017</div>
        <div class="one_1 success-color"><i class="fa fa-check-circle green"></i></div>
        <div class="a01 green">100  Homes(Prototype  Grid)</div>
        <div class="desc01">simply dummy text of the printing</div>

    </div>
    <div>
        <div class="d02">01/01/2017 to 01/01/2017</div>
        <div class="one_2 success-color"><i class="fa fa-check-circle green"></i></div>
        <div class="a02 green">500  Homes  (MicroGrid)</div>
        <div class="desc02">simply dummy text of the printing</div>
    </div>
    <div>
        <div class="d03">01/01/2017 to 01/01/2017</div>
        <div class="one_3 success-color"><i class="fa fa-check-circle yellow"></i></div>
        <div class="a03 yellow">2500  Homes  (Mini  Grid)</div>
        <div class="desc03">simply dummy text of the printing</div>
    </div>
    <div>
        <div class="d04">01/01/2017 to 01/01/2017</div>
        <div class="one_4 success-color"><i class="fa fa-check-circle Text"></i></div>
        <div class="a04 Text">5000  Homes  (Final  deployment)</div>
        <div class="desc04">simply dummy text of the printing</div>
    </div>
    <div>
        <div class="d05">01/01/2017 to 01/01/2017</div>
        <div class="one_5 success-color"><i class="fa fa-check-circle Text"></i></div>
        <div class="a05 Text">5000  Homes  (Final  deployment)</div>
        <div class="desc05">simply dummy text of the printing</div>
    </div>
    <div class="progress-bar progress-bar-success" style="width: 51%;"></div>
</div>-->

<link href="<?php echo $this->layout()->staticBaseUrl.'application/modules/Sitecrowdfunding/externals/scripts/timeline/css/timeline.min.css' ?>" rel="stylesheet">
<script src="<?php echo $this->layout()->staticBaseUrl.'application/modules/Sitecrowdfunding/externals/scripts/timeline/js/timeline.js' ?>"></script>

<link href="<?php echo $this->layout()->staticBaseUrl.'application/modules/Sitecrowdfunding/externals/scripts/simple-modal/assets/css/simplemodal.css' ?>" rel="stylesheet">
<script src="<?php echo $this->layout()->staticBaseUrl.'application/modules/Sitecrowdfunding/externals/scripts/simple-modal/simple-modal.js' ?>"></script>

<?php $milestone_id_inprogress = 1; if(count($this->milestones) > 0): ?>
    <div class="timeline" data-mode="horizontal">
        <div class="timeline__wrap">
            <div class="timeline__items">
                <?php foreach($this->milestones as $key => $milestone):
                if($milestone['status'] == 'inprogress'){
                    $milestone_id_inprogress = $key+1;
                }
                ?>
                    <div class="timeline__item">
                        <div class="timeline__content <?php echo $milestone['status'] ?>">
                            <div>
                                <img class="timeline_img" src="<?php echo !empty($milestone['logo']) ? $milestone['logo'] : $defaultLogo; ?>"/>
                                <div>
                                    <h3><?php echo $milestone['title']; ?></h3>
                                    <div class="common-status ">
                                        <span class="<?php echo 'status-'.$milestone['status'] ?>">    <?php echo  $this->statusLabels[$milestone['status']]; ?></span>
                                    </div>
                                    <div class="timeline-dates">
                                        <span class="timeline-start-date"><?php echo  date('M-d-Y',strtotime($milestone['start_date'])); ?></span>
                                        <?php if($milestone['end_date'] != null): ?>
                                        <span>To</span>
                                        <span class="timeline-end-date"><?php echo  date('M-d-Y',strtotime($milestone['end_date'])); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="timeline-description" >
                                <?php if( !empty($milestone['description']) && strlen($milestone['description']) <= 100 ) : ?>
                                    <?php echo $milestone['description']; ?>
                                <?php else: ?>
                                    <?php echo $this->string()->truncate($milestone['description'],100) ?>
                                    <button onclick="viewDesc('<?php echo $key ?>')" >View more</button>
                                <?php endif; ?>
                            </div>
                            <div class="timeline-question" >
                                <?php if(!empty($milestone['question'])): ?>
                                <?php if( !empty($milestone['question']) && strlen($milestone['question']) <= 100 ) : ?>
                                <?php echo 'To achieve milestone: '.$milestone['question']; ?>
                                <?php else: ?>
                                <?php echo 'To achieve milestone: '.$this->string()->truncate($milestone['question'],100) ?>
                                <button onclick="viewDesc('<?php echo $key ?>')" >View more</button>
                                <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            <input id="timeline-title-<?php echo $key ?>" type="hidden" value="<?php echo $milestone['title']; ?>" >
                            <textarea id="timeline-desc-<?php echo $key ?>"  cols="20" rows="20" style="display:none;">
                                <?php echo $milestone['description']; ?>
                            </textarea>
                            <textarea id="timeline-ques-<?php echo $key ?>"  cols="20" rows="20" style="display:none;">
                                <?php echo $milestone['question']; ?>
                            </textarea>
                            <input id="timeline-date-<?php echo $key ?>" type="hidden" value="<?php echo  date('M-d-Y',strtotime($milestone['start_date'])). ' To '. date('M-d-Y',strtotime($milestone['end_date'])); ?>" >
                            <input id="timeline-status-<?php echo $key ?>" type="hidden" value="<?php echo $this->statusLabels[$milestone['status']]; ?>" >
                            <input id="timeline-img-<?php echo $key ?>" type="hidden" value="<?php echo !empty($milestone['logo']) ? $milestone['logo'] : $defaultLogo; ?>">
                            <div class="view-desc-btn">
                                <button  onclick="viewDesc('<?php echo $key ?>')" >View more</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="tip">
            <span>
                <?php echo $this->translate('This project does not have any milestones yet.'); ?>
            </span>
    </div>
<?php endif; ?>
<style type="text/css">
    @media screen and (max-width: 767px) {
        .view-desc-btn{
            display: block !important;
        }
        .timeline_img{
            display: none !important;
        }
        .timeline-description{
            display: none !important;
        }
        .timeline-question{
            display: none !important;
        }
    }
    @media screen and (min-width: 768px) {
        .view-desc-btn{
            display: none !important;
        }
        .timeline_img{
            display: block !important;
        }
        .timeline-description{
            display: block !important;
        }
        .timeline-question{
            display: block !important;
        }
    }
    .view-desc-btn button{
        font-size: 12px;
        padding: 5px;
    }
    .timeline{
        margin-bottom: 10px;
        margin-top: 10px;
    }
    .timeline_img{
        width: 80px;
        height: 80px;
        border-radius: 25%;
        float: left;
        margin-right: 10px;
    }
    .timeline-description,.timeline-question,common-status,.timeline-dates{
        padding: 5px;
    }
    .common-status{
        margin-top: 10px;
        margin-bottom: 10px;
    }
    .status-completed{
        background-color: lightgreen;
        border-radius: 5%;
        font-size: 12px;
        color: white;
        padding: 8px;
    }
    .status-inprogress{
        background-color: #FDE541;
        border-radius: 5%;
        font-size: 12px;
        color: white;
        padding: 8px;
    }
    .status-yettostart{
        background-color: #f2f0f0;
        border-radius: 5%;
        font-size: 12px;
        color: #44AEC1;
        padding: 8px;
    }
    .timeline__content{
        padding: 0.5rem !important
    }
    .timeline--horizontal .timeline__item--top .timeline__content.inprogress:before{
        border-top: 12px solid #FDE541 !important
    }
    .timeline--horizontal .timeline__item--top .timeline__content.completed:before{
        border-top: 12px solid lightgreen !important
    }
    .timeline--horizontal .timeline__item--bottom .timeline__content.inprogress:before{
        border-bottom: 12px solid #FDE541 !important
    }
    .timeline--horizontal .timeline__item--bottom .timeline__content.completed:before{
        border-bottom: 12px solid lightgreen !important
    }

    .timeline--horizontal .timeline__item--top .timeline__content.inprogress:after,
    .timeline--horizontal .timeline__item--top .timeline__content.completed:after{
        border-top: none !important;
    }
    .timeline--horizontal .timeline__item--bottom .timeline__content.inprogress:after,
    .timeline--horizontal .timeline__item--bottom .timeline__content.completed:after{
        border-bottom: none !important
    }
    .timeline__content.completed{
        border: 3px solid lightgreen !important;
    }
    .timeline__content.inprogress{
        border: 3px solid #FDE541 !important;
    }
    .timeline__content.yettostart{

    }

    .timeline--mobile .timeline__item .timeline__content.inprogress:before{
        border-right: 12px solid #FDE541 !important;
    }
    .timeline--mobile .timeline__item .timeline__content.completed:before{
        border-right: 12px solid lightgreen !important;
    }

    .timeline--mobile .timeline__item .timeline__content.inprogress:after{
        border-right: none !important;
    }
    .timeline--mobile .timeline__item .timeline__content.completed:after{
        border-right: none !important;
    }

    .timeline-nav-button{
        background-color: #44AEC1 !important;
    }
    /*.timeline--horizontal .timeline__item:after{*/
    /*    font-family: FontAwesome;*/
    /*    content: '\f058';*/
    /*}*/

    /*.timeline--vertical .timeline__item:after{*/
    /*    font-family: FontAwesome;*/
    /*    content: '\f058';*/
    /*}*/

    #simple-modal{
        display: block !important;
        overflow-y: initial !important;
    }
    @media screen and (max-width: 676px){
        .simple-modal-body{
            height: 500px !important;
            overflow-y: auto !important;
        }
    }


</style>
<script>
    function viewDesc(id){
        var titleInput = document.id('timeline-title-'+id);
        var descInput = document.id('timeline-desc-'+id);
        var quesInput = document.id('timeline-ques-'+id);
        var imgInput = document.id('timeline-img-'+id);
        var statusInput = document.id('timeline-status-'+id);
        var dateInput = document.id('timeline-date-'+id);
        var SM = new SimpleModal({
            "btn_ok":"Close",
            "width": window.innerWidth - 100,
        });

        // let desc = ''
        //
        // if(){
        //     try{
        //        desc =  JSON.parse(descInput.value)
        //     }catch (e) {
        //
        //     }
        // }


        SM.show({

            "title":titleInput.value ,
            "contents": "<div style='text-align:center'  ><img width='200' height='200' src=" + imgInput.value + " />" + "</br>"
                + statusInput.value + "</br> "
                + dateInput.value + "</br>"
                + descInput.value + "</br>"
                + "To achieve milestone: "+ quesInput.value + "</div>"
        });
    }
    window.addEvent('domready', function() {
            var startIndex1 = parseInt('<?php echo $milestone_id_inprogress ?>');
            timeline(document.querySelectorAll('.timeline'), {
                forceVerticalMode: 10,
                mode: 'horizontal',
                verticalStartPosition: 'left',
                visibleItems: 3,
                startIndex: startIndex1
            });
    });
</script>