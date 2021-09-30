<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl .'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js'); ?>
<ul class="custom-nav-list1">
    <?php //if($this->can_edit): ?>
    <li>
        <?php echo $this->htmlLink(array('route' => 'sitepage_dashboard', 'action' => 'overview', 'page_id' => $this->sitepage->page_id), $this->translate("Edit Page"), array('class' => 'buttonlink')); ?>
    </li>
    <?php //endif; ?>
    <li>
        <?php echo $this->htmlLink(array('route' => 'sitepage_dashboard', 'action' => 'get-link', 'page_id' => $this->sitepage->page_id), $this->translate("Get Link"), array('class' => 'buttonlink smoothbox')); ?>
    </li>
</ul>

<style type="text/css">
    .custom-nav-list1 > li{
        border-bottom: 1px solid #eee;
        font-size: 18px;
        padding: 10px;
        border-radius: 3px;
        font-family: 'fontawesome', Roboto, sans-serif;
    }
    .custom-nav-list1 > li a{
        font-family: 'fontawesome', Roboto, sans-serif;
        font-size: 18px;
    }
    .custom-nav-list1 > .active{
        background: #44AEC1;
        color: #fff;
    }
    .custom-nav-list1 li:hover{
        cursor: pointer;
    }
</style>