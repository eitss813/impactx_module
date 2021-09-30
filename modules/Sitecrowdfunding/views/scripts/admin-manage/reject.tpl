<div class="reject-container">
    <div class="organization-div">
        <?php if(count($this->adminnotes) > 0): ?>
            <h3>
                <?php echo $this->translate("Previous notes"); ?>
            </h3>
            <?php foreach($this->adminnotes as $adminnote): ?>
                <ul class="organization-list" >
                    <li>
                        <span><?php echo  $this->translate("Notes:"); ?></span>
                        <span><?php echo  $adminnote['description']; ?></span>
                    </li>
                    <li>
                        <span><?php echo  $this->translate("Created on:"); ?></span>
                        <span><?php echo  date('Y-m-d', strtotime($adminnote['created_date'])); ?></span>
                    </li>
                </ul>
            <?php endforeach;?>
        <?php endif; ?>
    </div>
    <div class="reject-form-container">
        <?php echo $this->form->render($this); ?>
    </div>
</div>
<style type="text/css">
    .reject-container{
        padding: 10px;
    }
    .reject-form-container{
        padding-top:10px;
    }
    .organization-list{
        border-bottom: 1px solid #f2f0f0;
        padding: 5px 0px 5px 0px;
    }
    .organization-list li span{
        display: block;
        float: left;
        overflow: hidden;
        width: 175px;
        margin-right: 15px;

    }
    .organization-list > li > span + span {
        min-width: 0px;
        display: block;
        float: none;
        overflow: hidden;
        width: 400px
    }
    .organization-list > li > span + span {
        display: inline-block !important;
    }
</style>