<h3>
    Category
</h3>
<ul class="custom-nav-list1">
    <li>
        <a href="<?php echo $this->category->getHref(); ?>"> <?php echo $this->category->getTitle(); ?></a>
    </li>
</ul>
<style type="text/css">
    .custom-nav-list1 > li{
        border-bottom: 1px solid #eee;
        font-size: 16px;
        padding: 10px;
        border-radius: 3px;
        font-family: 'fontawesome', Roboto, sans-serif;
    }
    .custom-nav-list1 > .active{
        background: #44AEC1;
        color: #fff;
    }
    .custom-nav-list1 li:hover{
        cursor: pointer;
    }
    .custom-nav-list1 li a{
        font-size: 16px !important;
    }
    .custom-nav-list1 li a::before{
        content: '';
    }
</style>