<?php $defaultLogo = $this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/nophoto_page_thumb_profile.png'; ?>
<?php if(count($this->externalfunding) > 0): ?>
<div class="organization-div">
    <h3 class="layout_title">Funders Not In ImpactNet</h3>
    <?php foreach($this->externalfunding as $org): ?>
    <div class="org_container">
        <div class="org_left">
            <div class="org_logo">
                <img style="width: 80px;height: 80px" src="<?php echo !empty($org['logo']) ? $org['logo'] : $defaultLogo ?>"/>
                <p><?php echo $org['type']; ?></p>
            </div>
            <div class="org_title_desc">
                <h3 class="organization-header">
                    <?php if(!empty($org['link'])): ?>
                    <?php echo $this->htmlLink($org['link'], $org['title']. ' - '.Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency( $org['amount'] ), array('target' => '_blank')) ?>
                    <?php else: ?>
                    <?php echo $org['title']. ' - '.Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency( $org['amount'] ) ?>
                    <?php endif; ?>
                </h3>
                <div>
                    <h4>
                        <?php echo "Funded on: ".date('Y-m-d',strtotime($org['funding_date'])) ?>
                    </h4>
                </div>
                <div>
                    <?php echo $org['notes']; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach;?>
</div>
<?php endif; ?>
<style type="text/css">
    /*edit funding form*/
    .add-funding-btn{
        margin-top: 20px;
        margin-bottom: 20px;
    }
    .sitecrowdfunding_project_form{
        padding: 10px;
        border: 1px solid #eee;
    }
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
        padding-top: 20px
    }
    .add-org-btn{
        margin-top: 10px;
    }
    .organization-header{
        text-decoration: underline;
        font-weight: bold;
    }
    .layout_title {
        position: relative;
        text-align: center;
        font-size: 18px;
        border-bottom: unset !important;
        font-weight: 500 !important;
        font-size: 18px !important;
        border-bottom: 1px solid #f2f0f0;
        letter-spacing: .2px;
        text-transform: capitalize;
        line-height: normal;
        background: #ffffff;
        font-family: 'fontawesome', Roboto, sans-serif;
        padding: 15px;
    }
    .layout_title::before {
        left: 0 !important;
        margin: 0 auto !important;
        right: 0 !important;
        text-align: center !important;
        width: 85px !important;
        background: #44AEC1 !important;
        top: 100% !important;
        content: "" !important;
        display: block !important;
        min-height: 2px !important;
        position: absolute !important;
        border-bottom: unset !important;
    }
    .layout_title:before, .layout_title:after {
        content: '';
        display: inline-block;
        background-repeat: no-repeat;
        width: 0px;
        border-width: 0;
        height: 0px;
        margin: 0;
        position: absolute;
    }
</style>