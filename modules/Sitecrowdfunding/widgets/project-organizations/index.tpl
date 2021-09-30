<?php $defaultLogo = $this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/nophoto_page_thumb_profile.png'; ?>
<?php if(count($this->externalorganizations) == 0 && count($this->internalorganizations) == 0 ): ?>
    <div class="tip">
            <span>
                  <?php echo $this->translate('This project does not connected any internal/external organizations.'); ?>
            </span>
    </div>
<?php else: ?>
    <div class="organization-div">
        <?php if(count($this->internalorganizations) > 0): ?>
            <h3 style="padding-top: 5px">
                <?php echo $this->translate("Listed Organizations"); ?>
            </h3>
            <?php foreach($this->internalorganizations as $org): ?>
            <div class="org_container">
                <div class="org_left">
                    <div class="org_logo">
                        <img style="width: 80px;height: 80px" src="<?php echo $org['logo'] ?>"/>
                        <p><?php echo  $org['organization_type']; ?></p>
                    </div>
                    <div class="org_title_desc">
                        <h3 class="organization-header">
                            <?php echo $this->htmlLink($org['link'],  $org['title'], array('target' => '_blank')) ?>
                        </h3>
                        <div>
                            <?php echo  $org['description']; ?>
                        </div>
                    </div>
                </div>
                <div class="org_options">

                </div>
            </div>
            <?php endforeach;?>
        <?php endif; ?>
        <?php if(count($this->externalorganizations) > 0): ?>
            <h3>
                <?php echo $this->translate("Unlisted Organizations"); ?>
            </h3>
            <?php foreach($this->externalorganizations as $org): ?>
                <div class="org_container">
                    <div class="org_left">
                        <div class="org_logo">
                            <img style="width: 80px;height: 80px" src="<?php echo !empty($org['logo']) ? $org['logo'] : $defaultLogo; ?>"/>
                            <?php if($org['organization_type'] === 'others'): ?>
                            <p><?php echo  $org['others']; ?></p>
                            <?php else:?>
                            <p><?php echo  $org['organization_type']; ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="org_title_desc">
                            <?php if(!empty($org['link'])):?>
                            <h3 class="organization-header">
                                <?php echo $this->htmlLink($org['link'],  $org['title'], array('target' => '_blank')) ?>
                            </h3>
                            <?php else: ?>
                            <h3><?php echo $org['title']; ?></h3>
                            <?php endif; ?>
                            <div>
                                <?php echo  $org['description']; ?>
                            </div>
                        </div>
                    </div>
                    <div class="org_options">
                    </div>
                </div>
            <?php endforeach;?>
        <?php endif; ?>
</div>
<?php endif; ?>
<style type="text/css">
    .org_container{
        display: flex;
        border: 1px solid #f2f0f0;
        margin-top: 20px;
        margin-bottom: 20px;
        padding: 10px;
        justify-content: space-between;
    }
    .org_left{
        display: flex;
    }
    .org_logo{
        padding-right: 15px;
        text-align: center;
        width: auto;
    }
    .org_options{
        min-width: 80px;
    }
    .organization-div{
        padding: 10px !important;
    }
    .organization-header{
        text-decoration: underline;
        font-weight: bold;
    }
</style>