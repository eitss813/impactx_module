
<?php
  $request = Zend_Controller_Front::getInstance()->getRequest();
  $action = $request->getActionName();
  $param = $request->getParam('format');
  $templateData = ($action == 'preview') ? $this->templateData : Engine_Api::_()->getApi('core','sitepage')->getTemplateData() ;
  $best_choice = $templateData['best_choice'];
  $templateStyle = $templateData['templateStyle'];
  $features = $templateData['features'];
  $fieldValues = $templateData['fieldValues'];

  $tick_image = $templateStyle['tick_image']['value'];
  $cross_image = $templateStyle['cross_image']['value'];

  $footer_enabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.footer.enabled', '0');
  if ($footer_enabled == '1')
    $footer_text = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.footer.text', '');
    ?>
    
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/seaomooscroll/SEAOMooHorizontalScrollBar.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/reset_row_height.js'); ?>
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/_plansTemplate_0.css'; ?>">


  <form method="get" action="" class="" enctype="application/x-www-form-urlencoded" id="signup" name="signup">
  <div class="plan_container" id="plan_container">

      <div id="scroll-areas-main">
        <div id="list-scroll-areas" class="plan_subscriptions_row" style="overflow:hidden;">
          <div id="plan_subscriptions_row">
          <?php if(!empty($this->check)) : ?>
          <?php foreach( $this->form->getPackages() as $package_id => $package ) : ?>
            <div class="plan_subscriptions_col1 <?php echo ($package->package_id == $best_choice) ? 'classic' : 'basic' ;?>">
              <div class="plan_subscriptions_header">
              <?php if($package->package_id == $best_choice): ?>
                <p class="popular_package"><?php echo $this->translate( ($templateStyle['template_mostpopularblock_text']['value'] == null) ? '' : $templateStyle['template_mostpopularblock_text']['value'] ) ; ?></p>
              <?php endif; ?> 
              <h2 title="<?php echo $package->title?>"><?php echo $this->translate(Engine_Api::_()->seaocore()->seaocoreTruncateText($package->title, 15)); ?></h2>
              </div>
              <div class="plan_subscriptions_price_box"> 
                <?php echo Engine_Api::_()->getApi('core','sitepage')->getCurrencySymbol(); ?> <?php echo $this->translate($package->price); ?> 
                <span><?php $duration = Engine_Api::_()->getApi('core','sitepage')->getReccurenceDuration($package->package_id);
                echo (empty($duration)) ? 'once' : $duration; ?></span>
              </div>
              <div class="plan_subscriptions_details">
                <ul>
                  <?php $rowCount = 1;?>
                  <?php foreach($features as $fields): ?>
                  <?php $value = $fieldValues[$fields['field_id']][$package_id];?>
                  <li title='<?php echo $value; ?>' class="<?php echo "compareField details_row_".$rowCount++ ?>">
                  <?php $label = $fieldValues[$fields['field_id']][$package_id] == null ? '-' : $fieldValues[$fields['field_id']][$package_id];
                      echo $this->translate(Engine_Api::_()->seaocore()->seaocoreTruncateText($label, 15)); ?>  
                  </li>
                  <?php endforeach; ?>
                </ul>
              </div>
              <div class="plan_subscriptions_button">
                <button name="btn">
                <?php 
                echo $this->translate($templateStyle['template_button_text']['value'] == null ? '' : $templateStyle['template_button_text']['value']); ?>
                </button>
              </div>
            </div>
            <?php endforeach; ?> 
          </div> 
        </div>
      </div>
      <div class="scrollbarArea" id ="scrollbar_after">   </div>
  </div>
  </form>
  <?php if ($footer_enabled == '1' && !empty($footer_text)): ?>
    <div class="l_banner">
      <p><?php echo $footer_text; ?></p>
    </div>
  <?php endif; ?>
          <?php else : ?>  
            <?php foreach( $this->paginator as $package_id => $package ) : ?>
            <div class="plan_subscriptions_col1 <?php echo ($package->package_id == $best_choice) ? 'classic' : 'basic' ;?>">
              <div class="plan_subscriptions_header">
              <?php if($package->package_id == $best_choice): ?>
                <p class="popular_package"><?php echo $this->translate( ($templateStyle['template_mostpopularblock_text']['value'] == null) ? '' : $templateStyle['template_mostpopularblock_text']['value'] ) ; ?></p>
              <?php endif; ?> 
              <h2 title="<?php echo $package->title?>"><?php echo $this->translate(Engine_Api::_()->seaocore()->seaocoreTruncateText($package->title, 15)); ?></h2>
              </div>
              <div class="plan_subscriptions_price_box"> 
                <?php echo Engine_Api::_()->getApi('core','sitepage')->getCurrencySymbol(); ?> <?php echo $this->translate($package->price); ?> 
                <span><?php $duration = Engine_Api::_()->getApi('core','sitepage')->getReccurenceDuration($package->package_id);
                echo (empty($duration)) ? 'once' : $duration; ?></span>
              </div>
              <div class="plan_subscriptions_details">
                <ul><?php $rowCount = 1;?>
                  <?php foreach($features as $fields): ?>
                  <?php $value = $fieldValues[$fields['field_id']][$package->package_id];?>
                  <li title='<?php echo $value; ?>' class="<?php echo " details_row_".$rowCount++ ?>">
                  <?php $label = $fieldValues[$fields['field_id']][$package->package_id] == null ? '-' : $fieldValues[$fields['field_id']][$package->package_id];
                      echo $this->translate(Engine_Api::_()->seaocore()->seaocoreTruncateText($label, 15)); ?>  
                  </li>
                  <?php endforeach; ?>
                </ul>
              </div>
              <div class="plan_subscriptions_button">
                <button type="button" name="" id="<?php echo $package->package_id; ?>" onclick="submitForm(this.id)">
                <?php 
                echo $this->translate($templateStyle['template_button_text']['value'] == null ? '' : $templateStyle['template_button_text']['value']); ?>
                </button>
              </div>
            </div>
            <?php endforeach; ?> 
          </div> 
        </div>
      </div>
      <div class="scrollbarArea" id ="scrollbar_after">   </div>
  </div>
  </form>
  <?php if ($footer_enabled == '1' && !empty($footer_text)): ?>
    <div class="l_banner">
      <p><?php echo $footer_text; ?></p>
    </div>
  <?php endif; ?>
<?php endif; ?>

<style type="text/css">
.basic .plan_subscriptions_price_box 
{
  color: #<?php echo $templateStyle['template_price_textcolor_normal']['value']; ?>;
  background: #<?php echo $templateStyle['template_price_bgcolor_normal']['value']; ?>;
}
.classic .plan_subscriptions_price_box
{
  color: #<?php echo $templateStyle['template_price_textcolor_popular']['value']; ?>;
  background: #<?php echo $templateStyle['template_price_bgcolor_popular']['value']; ?>;
}
.plan_subscriptions_button button
{
  font-size: <?php echo $templateStyle['template_button_textsize']['value']; ?>px ;
  font-style: <?php echo $templateStyle['template_button_textstyle']['value']; ?>;
  font-family: <?php echo $templateStyle['template_button_textfamily']['value']; ?>;
}
.basic .plan_subscriptions_button button
{
  background: #<?php echo $templateStyle['template_price_textcolor_normal']['value']; ?>;
}
.classic .plan_subscriptions_button button
{
  background: #<?php echo $templateStyle['template_price_bgcolor_popular']['value']; ?>;
}
.classic
{
  background: #<?php echo $templateStyle['template_bgcolor_popular']['value']; ?>;
  border: 5px solid #<?php echo $templateStyle['template_bordercolor_popular']['value']; ?>;
}
.basic
{
  background: #<?php echo $templateStyle['template_bgcolor_normal']['value']; ?>;
}
.plan_row
{
  background:  <?php echo ( Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.header.color', '#A9A9A9') == '#ffffff' ) ? '#A9A9A9' : Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.header.color', '#A9A9A9'); ?>;
}
.plan_subscriptions_details ul li, .plan_subscriptions_details ul li b
{
  font-size: <?php echo $templateStyle['template_feature_textsize']['value']; ?>px;
  font-style: <?php echo $templateStyle['template_feature_textstyle']['value']; ?>;
  font-family: <?php echo $templateStyle['template_feature_textfamily']['value']; ?>;
}
.plan_subscriptions_price_box
{
  font-family: <?php echo $templateStyle['template_price_textfamily']['value']; ?>;
}l

.horizontalThumb {
  background-color: #<?php echo $templateStyle['template_price_textcolor_normal']['value']; ?>;
}
.plan_subscriptions_details ul li b
{
  font-weight: 700 !important; 
}
</style>

<?php if (!($header_enabled == '1') && (!empty($header_title) || !empty($header_desc)) ): ?>
<style type="text/css">
  #plan_subscriptions_row
  {
    margin-top: 80px;
  }
</style>

<?php endif; ?>

<script type="text/javascript">
  function submitForm(id)
  {
    window.location.href = '<?php echo $url; ?>' + '/id/' + id;
    //var newstring = '/id/'+$id;
    //var url = window.location.href;
    //var segements = url.split("/");
    //segements[segements.length - 1] = "" + newstring;
    //var newurl = segements.join("/");
    //console.log(newurl);
    //console.log(url);
    //var newurl = '<?php echo $url; ?>' + newstring;    
   //document.getElementById("signup").action = newurl;
   //document.getElementById("signup").submit();    
  }
</script>


<script type="text/javascript">
  var smoothbox = <?php echo ($param == 'smoothbox') ? 1 : 0; ?>;  
  en4.core.runonce.add(function () { 
    
    var check = typeof SEAOMooHorizontalScrollBar;
            
            if(check === "undefined") { 
            Asset.javascript('<?php echo ($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/scripts/seaomooscroll/SEAOMooHorizontalScrollBar.js");?>');    
            }
    
    var children = $("plan_subscriptions_row").childElementCount;
    var containerWidth = smoothbox ? 1210 : $('plan_container').offsetWidth;
    en4.core.runonce.add(function () {
    resetContent();
    (function () {
      
      // $('list-scroll-areas').setStyle('height', $('scroll-content').offsetHeight + 'px');
      $('list-scroll-areas').setStyle('width', containerWidth + 'px');
      scrollBarContentArea = new SEAOMooHorizontalScrollBar('scroll-areas-main', 'list-scroll-areas', {
        'arrows': false,
        'horizontalScroll': true,
        'horizontalScrollElement': 'scrollbar_after',
        'horizontalScrollBefore': false,
      });
      $$('#scroll-areas-main').setStyle('width',containerWidth);
    }).delay(700);
  });

  var resetContent = function () {
    var width = ( containerWidth / children);

    width = width - 2;
    if (width < 250)
      width = 250;
    width++;

    if ( containerWidth < width ) {
      width = containerWidth - 2;
      $(scrollbar_after).setStyle('max-width',containerWidth+'px');
    }

    var numberOfItem = (containerWidth / width);
    var numberOfItemFloor = Math.floor(numberOfItem);
    var extra = (width * (numberOfItem - numberOfItemFloor) / numberOfItemFloor);
    width = width + extra;

    $('plan_subscriptions_row').setStyle('width', (width * children) + 'px');
    $('plan_subscriptions_row').getElements('.plan_subscriptions_col1').each(function (el) {
      width = (width > 300 && numberOfItemFloor >= children) ? 300 : width;
      // marginEnabled = (numberOfItemFloor >= children) ? 5 : 2;
      el.setStyle('width', width - 1.5 + 'px');
    });
    if((width * children) <= containerWidth){
      $('scrollbar_after').setStyle('display','none');
    }
  };
  })

    

</script>
