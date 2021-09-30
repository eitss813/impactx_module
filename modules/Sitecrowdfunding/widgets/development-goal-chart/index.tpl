<?php $this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js')
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/modernizr-2.6.2.min.js');
?>
<h3>Sustainable Development Goals</h3>
<div class="chart_container development-goal-chart">
    <div class="component" id="sustainable_component">
        <div class="smartcms_wrapper development-goal-chart-smartcms_wrapper opened-nav" id="smartcms_wrapper">
            <ul>
                <?php foreach($this->goals as $goal):
                    $imageURL = $this->layout()->staticBaseUrl.$goal['imageSrc'];
                ?>

                <li id="goal_list_id_<?php echo $goal['Project_Primary_key'] ?>" style="<?php echo $goal['style']; ?>" title="<?php echo $goal['Project_Name']; ?>" class="goal_list_common <?php echo $goal['isSelected'] ? 'kkitl': '' ?>">
                    <a id="goal_id_<?php echo $goal['Project_Primary_key'] ?>" data-text="<?php echo $goal['goal']; ?>" onclick="showSelectedDevelopmentGoal()" href="javascript:void(0)" style="background: radial-gradient(transparent 35%, <?php echo $goal['backgroundColor'] ?> 35%)">
                        <span class="indexcss development-goal-chart-indexcss">
                          <?php echo $goal['Project_Primary_key']; ?>
                        </span>
                        <span>
                          <img class="primg development-goal-chart-primg" src="<?php echo $imageURL; ?>">
                        </span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
<script type="text/javascript">
    var $j = jQuery.noConflict();
    function showSelectedDevelopmentGoal(){
        $j('.layout_main > .layout_middle').children('.layout_activity_feed').hide();
        $j('.layout_main > .layout_middle').children('.layout_sitecrowdfunding_project_backstory').hide();
        $j('.layout_main > .layout_middle').children('.layout_sitecrowdfunding_project_initiativeanswers').hide();
        $j('.layout_main > .layout_middle').children('.layout_sitecrowdfunding_project_funding_chart').hide();
        $j('.layout_main > .layout_middle').children(':not(.layout_activity_feed,.layout_sitecrowdfunding_project_initiativeanswers,.layout_sitecrowdfunding_project_backstory,.layout_sitecrowdfunding_project_funding_chart)').show();

        $j('html, body').animate({
            scrollTop: $j('.layout_sitecrowdfunding_development_goals').offset().top - 70
        }, 1000);

        // Zoom in the map
        map.fitBounds(bounds);

    }
</script>
<style type="text/css">
     .custom-tip{
        display: flex;
        height: 100%;
        align-items: center;
    }
     .icon_image{
        width: 70px;
        height: 40px;
        margin-right: 10px;
        object-fit: contain;
    }
     .common_information{
        padding: 10px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
     .common_information > h1 {
        margin-top: 10px;
        margin-bottom: 20px;

    }
     .common_information span{
        padding: 20px;
    }

     .common_information .targetcon{
        padding: 15px;
    }
     .common_information .target{
        padding: 5px;
    }

     .chart_container{
        display: flex;
        flex-direction: row;
        width: 100%;
    }
     .chart_content_container{
        width: 60%;
    }
     .hide_this{
        display: none;
    }
     .primg{
         width: 16px;
         margin-right: 16px;
    }
     .indexcss {
        margin-right: 27px;
    }
     .component {
        position: relative;
        margin-bottom: 3em;
        height: 25em;
        width: 40%;
        bottom: 100px;
        margin-left: 20px;
        margin-right: 20px;

    }
     span.indexcss.development-goal-chart-indexcss {
         left: 2px;
     }
    @media (max-width: 420px) {
         .component {
            bottom: 50px !important;
        }
    }
    @media (min-width: 421px) and (max-width: 450px) {
         .component {
            bottom: 120px !important;
            width: 55% !important;
        }
    }
    @media (min-width: 451px) and (max-width:767px){
         .chart_container{
            display: flex;
            flex-direction: column !important;
            justify-content: center !important;
        }
         .component {
            width: 65% !important;
            margin-left: 20px !important;

        }
         .chart_content_container{
            width: 100% !important;
        }

    }

    @media (min-width:768px) and (max-width: 1300px) {
         .chart_container{
            display: flex;
            flex-direction: column !important;
            justify-content: center !important;
        }
         .component {
            width: 90% !important;
            margin-left: 20px !important;
        }
         .chart_content_container{
            width: 100% !important;
        }
    }

     @media (min-width: 426px) and (max-width: 550px){
         #sustainable_component > div {
             left: 35% !important;
         }
     }
     @media (min-width: 551px) and (max-width: 767px){
         #sustainable_component > div {
             left: 40% !important;
         }
     }
     @media (max-width: 425px){
         #sustainable_component  > div  {

             left: 21% !important;
         }
     }


    /* style for demo 1 */

     .smartcms_button {

        position: absolute;

        top: 100%;

        left: 50%;

        z-index: 11;

        margin-top: -2.25em;

        margin-left: -2.25em;

        padding-top: 0;

        width: 4.5em;

        height: 4.5em;

        border: none;

        border-radius: 50%;

        background: none;

        background-color: #fff;

        color: #08c;

        text-align: center;

        font-weight: 700;

        font-size: 1.5em;

        text-transform: uppercase;

        cursor: pointer;

        -webkit-backface-visibility: hidden;

    }



     .csstransforms .smartcms_wrapper {

        position: absolute;

        top: 100%;

        left: 50%;

        z-index: 10;

        margin-top: -39em;

        margin-left: -25em;

        width: 50em;

        height: 50em;

        border-radius: 50%;

        background: transparent;

        opacity: 0;

        -webkit-transition: all .3s ease 0.3s;

        -moz-transition: all .3s ease 0.3s;

        transition: all .3s ease 0.3s;

        -webkit-transform: scale(0.1);

        -ms-transform: scale(0.1);

        -moz-transform: scale(0.1);

        transform: scale(0.1);

        pointer-events: none;

        overflow: hidden;
    }

    @media (max-width: 425px) {
         .csstransforms .smartcms_wrapper {
            top: 75%;
            left: 30%;
        }
    }



    /*cover to prevent extra space of anchors from being clickable*/

     .csstransforms .smartcms_wrapper:after {

        content: ".";

        display: block;

        font-size: 2em;

        width: 6.2em;

        height: 6.2em;

        position: absolute;

        left: 50%;

        margin-left: -3.1em;

        top: 50%;

        margin-top: -3.1em;

        border-radius: 50%;

        z-index: 10;

        color: transparent;

    }



     .csstransforms .opened-nav {

        border-radius: 50%;

        opacity: 1;

        -webkit-transition: all .3s ease;

        -moz-transition: all .3s ease;

        transition: all .3s ease;

        -webkit-transform: scale(1);

        -moz-transform: scale(1);

        -ms-transform: scale(1);

        transform: scale(1);

        pointer-events: auto;

    }



     .csstransforms .smartcms_wrapper li {

        position: absolute;

        top: 50%;

        left: 50%;

        overflow: hidden;

        margin-top: -1.3em;

        margin-left: -10em;

        width: 10em;

        height: 10em;

        font-size: 1.5em;

        -webkit-transition: all .3s ease;

        -moz-transition: all .3s ease;

        transition: all .3s ease;

        -webkit-transform: rotate(75deg) skew(62deg);

        -moz-transform: rotate(75deg) skew(62deg);

        -ms-transform: rotate(75deg) skew(62deg);

        transform: rotate(75deg) skew(62deg);

        -webkit-transform-origin: 100% 100%;

        -moz-transform-origin: 100% 100%;

        transform-origin: 100% 100%;

        pointer-events: none;
        z-index: 199;
    }

     .csstransforms .smartcms_wrapper li.kkitl a {
        transform: skew(-72deg) rotate(-77deg) scale(1.2);
    }

     .csstransforms .smartcms_wrapper li a {

        position: absolute;



        right: -8.25em;

        bottom: -7.25em;

        display: block;

        width: 14.5em;

        height: 14.5em;

        border-radius: 50%;

        background: #08c;

        background: -webkit-radial-gradient(transparent 35%, #08c 35%);

        background: -moz-radial-gradient(transparent 35%, #08c 35%);

        background: radial-gradient(transparent 35%, #eee 35%);

        color: #fff;

        text-align: center;

        text-decoration: none;

        font-size: 1.2em;

        line-height: 2;

        -webkit-transform: skew(-62deg) rotate(-75deg) scale(1);

        -moz-transform: skew(-62deg) rotate(-75deg) scale(1);

        -ms-transform: skew(-62deg) rotate(-75deg) scale(1);

        transform: skew(-72deg) rotate(-77deg) scale(1);

        -webkit-backface-visibility: hidden;

        backface-visibility: hidden;

        pointer-events: auto;


    }



     .csstransforms .smartcms_wrapper li a span {

        position: relative;

        top: 1em;

        display: block;

        font-size: .5em;

        font-weight: 700;

        text-transform: uppercase;

    }



     .csstransforms .smartcms_wrapper li a:hover,

     .csstransforms .smartcms_wrapper li a:active,

     .csstransforms .smartcms_wrapper li a:focus {

        background: -webkit-radial-gradient(transparent 35%, #329196 35%) !important;

        background: -moz-radial-gradient(transparent 35%, #329196 35%) !important;

        background: radial-gradient(transparent 35%, #329196 35%) !important;

    }



     .csstransforms .opened-nav li {

        -webkit-transition: all .3s ease .3s;

        -moz-transition: all .3s ease .3s;

        transition: all .3s ease .3s;

    }



     .csstransforms .opened-nav li:first-child {

        -webkit-transform: skew(62deg);

        -moz-transform: skew(62deg);

        -ms-transform: skew(62deg);

        transform: skew(62deg);

    }



     .csstransforms .opened-nav li:nth-child(2) {

        -webkit-transform: rotate(15deg) skew(62deg);

        -moz-transform: rotate(15deg) skew(62deg);

        -ms-transform: rotate(15deg) skew(62deg);

        transform: rotate(15deg) skew(62deg);

    }



     .csstransforms .opened-nav li:nth-child(3) {

        -webkit-transform: rotate(30deg) skew(62deg);

        -moz-transform: rotate(30deg) skew(62deg);

        -ms-transform: rotate(30deg) skew(62deg);

        transform: rotate(30deg) skew(62deg);

    }



     .csstransforms .opened-nav li:nth-child(4) {

        -webkit-transform: rotate(45deg) skew(62deg);

        -moz-transform: rotate(45deg) skew(62deg);

        -ms-transform: rotate(45deg) skew(62deg);

        transform: rotate(45deg) skew(62deg);

    }



     .csstransforms .opened-nav li:nth-child(5) {

        -webkit-transform: rotate(60deg) skew(62deg);

        -moz-transform: rotate(60deg) skew(62deg);

        -ms-transform: rotate(60deg) skew(62deg);

        transform: rotate(60deg) skew(62deg);

    }



     .csstransforms .opened-nav li:nth-child(6) {

        -webkit-transform: rotate(75deg) skew(62deg);

        -moz-transform: rotate(75deg) skew(62deg);

        -ms-transform: rotate(75deg) skew(62deg);

        transform: rotate(75deg) skew(62deg);

    }



     .csstransforms .opened-nav li:nth-child(7) {

        -webkit-transform: rotate(90deg) skew(62deg);

        -moz-transform: rotate(90deg) skew(62deg);

        -ms-transform: rotate(90deg) skew(62deg);

        transform: rotate(90deg) skew(62deg);

    }



     .csstransforms .opened-nav li:nth-child(8) {

        -webkit-transform: rotate(105deg) skew(62deg);

        -moz-transform: rotate(105deg) skew(62deg);

        -ms-transform: rotate(105deg) skew(62deg);

        transform: rotate(105deg) skew(62deg);

    }

     .csstransforms .opened-nav li:nth-child(9) {

        -webkit-transform: rotate(120deg) skew(62deg);

        -moz-transform: rotate(120deg) skew(62deg);

        -ms-transform: rotate(120deg) skew(62deg);

        transform: rotate(120deg) skew(62deg);

    }

     .csstransforms .opened-nav li:nth-child(10) {

        -webkit-transform: rotate(135deg) skew(62deg);

        -moz-transform: rotate(135deg) skew(62deg);

        -ms-transform: rotate(135deg) skew(62deg);

        transform: rotate(135deg) skew(62deg);

    }

     .csstransforms .opened-nav li:nth-child(11) {

        -webkit-transform: rotate(150deg) skew(62deg);

        -moz-transform: rotate(150deg) skew(62deg);

        -ms-transform: rotate(150deg) skew(62deg);

        transform: rotate(150deg) skew(62deg);

    }

     .csstransforms .opened-nav li:nth-child(12) {

        -webkit-transform: rotate(165deg) skew(62deg);

        -moz-transform: rotate(165deg) skew(62deg);

        -ms-transform: rotate(165deg) skew(62deg);

        transform: rotate(165deg) skew(62deg);

    }

     .csstransforms .opened-nav li:nth-child(13) {

        -webkit-transform: rotate(180deg) skew(62deg);

        -moz-transform: rotate(180deg) skew(62deg);

        -ms-transform: rotate(180deg) skew(62deg);

        transform: rotate(180deg) skew(62deg);

    }

     .csstransforms .opened-nav li:nth-child(14) {

        -webkit-transform: rotate(195deg) skew(62deg);

        -moz-transform: rotate(195deg) skew(62deg);

        -ms-transform: rotate(195deg) skew(62deg);

        transform: rotate(195deg) skew(62deg);

    }

     .csstransforms .opened-nav li:nth-child(15) {

        -webkit-transform: rotate(210deg) skew(62deg);

        -moz-transform: rotate(210deg) skew(62deg);

        -ms-transform: rotate(210deg) skew(62deg);

        transform: rotate(210deg) skew(62deg);

    }

     .csstransforms .opened-nav li:nth-child(16) {

        -webkit-transform: rotate(225deg) skew(62deg);

        -moz-transform: rotate(225deg) skew(62deg);

        -ms-transform: rotate(225deg) skew(62deg);

        transform: rotate(225deg) skew(62deg);

    }

     .csstransforms .opened-nav li:nth-child(17) {

        -webkit-transform: rotate(240deg) skew(62deg);

        -moz-transform: rotate(240deg) skew(62deg);

        -ms-transform: rotate(240deg) skew(62deg);

        transform: rotate(240deg) skew(62deg);

    }


     .no-csstransforms .smartcms_wrapper {

        overflow: hidden;

        margin: 10em auto;

        padding: .5em;

        text-align: center;

    }



     .no-csstransforms .smartcms_wrapper ul {

        display: inline-block;

    }



     .no-csstransforms .smartcms_wrapper li {

        float: left;

        width: 5em;

        height: 5em;

        background-color: #fff;

        text-align: center;

        font-size: 1em;

        line-height: 5em;

    }



     .no-csstransforms .smartcms_wrapper li a {

        display: block;

        width: 100%;

        height: 100%;

        color: inherit;

        text-decoration: none;

    }



     .no-csstransforms .smartcms_wrapper li a:hover,

     .no-csstransforms .smartcms_wrapper li a:active,

     .no-csstransforms .smartcms_wrapper li a:focus {

        background-color: #f8f8f8;

    }



     .no-csstransforms .smartcms_wrapper li.active a {

        background-color: #6F325C;

        color: #fff;

    }



     .no-csstransforms .smartcms_button {

        display: none;

    }



    @media only screen and (max-width: 620px) {

         .no-csstransforms li {

            width: 4em;

            height: 4em;

            line-height: 4em;

        }

    }



    @media only screen and (max-width: 500px) {

         .no-ccstransforms .smartcms_wrapper {

            padding: .5em;

        }



         .no-csstransforms .smartcms_wrapper li {

            width: 4em;

            height: 4em;

            font-size: .9em;

            line-height: 4em;

        }

    }



    @media only screen and (max-width: 480px) {

         .csstransforms .smartcms_wrapper {

            font-size: 0.8em;

        }

         .outpt_caret {
            display: none;

        }

         .smartcms_button {

            font-size: 1em;

        }

    }



    @media only screen and (max-width:420px) {

         .no-csstransforms .smartcms_wrapper li {

            width: 100%;

            height: 3em;

            line-height: 3em;

        }

    }

    /* end demo 1 */



     .view_demo {

        margin-top: 400px;

        text-align: center;

        width: 100%;

    }

     .view_demo>a {

        background: #08c none repeat scroll 0 0;

        color: #fff;

        padding: 10px;

        text-decoration: none;

    }

    @media(max-width: 768px) {

         .csstransforms .smartcms_wrapper {

            /*width: 40em;*/
            width: 100%;

        }

    }

     .outpt_caret {
        position: relative;

    }

     .outpt_caret i {
        position: absolute;
        right: -32px;
        margin-top: 21px;
        color: #07c776;
        font-size: 20px;

    }

     .icon1 {
        background: #e2e2e2;
        padding: 2px 8px;
        font-size: 10px;
        border-top-left-radius: 0px;
        border-top-right-radius: 30px;
        border-bottom-right-radius: 30px;
        border-bottom-left-radius: 0px;
        float: left;
        margin-right: 20px;
        left: 10px;
        position: relative;
        margin-bottom: 5px;
    }

     li.sltntab {
        cursor: pointer;
    }

     .activeCir {
        background-color: #07c676 !important;
        color: #ffffff !important;
    }

    /* Custom CSS */
     .csstransforms .development-goal-chart-smartcms_wrapper li {
         font-size: 0.9em !important;
         margin-left: -5.5em !important;
     }
     .csstransforms .development-goal-chart-smartcms_wrapper li a span{
        font-size: .6em !important;
     }
     .development-goal-chart-indexcss {
        margin-right: 17px !important;
     }
     .development-goal-chart-primg {
         width: 9px !important;
         margin-right: 10px !important;
     }

</style>