
<?php 
  $request = Zend_Controller_Front::getInstance()->getRequest();
  $action = $request->getActionName();
  $param = $request->getParam('format');

  $templateData = ($action == 'preview') ? $this->templateData : Engine_Api::_()->getApi('core','sitepage')->getTemplateData() ;

  $best_choice = $templateData['best_choice'];
  $templateStyle = $templateData['templateStyle'];
  $features = $templateData['features'];
  $fieldValues = $templateData['fieldValues'];

  $footer_enabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.footer.enabled', '0');
  if ($footer_enabled == '1')
    $footer_text = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.footer.text', '');
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/seaomooscroll/SEAOMooHorizontalScrollBar.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/reset_row_height.js'); ?>

<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/_plansTemplate_1.css'; ?>">

  <form method="get" action="" class="" enctype="application/x-www-form-urlencoded" id="signup" name="signup">
      <div id="plan_container">
      <div id="scroll-areas-main">
      <div class="container" id="list-scroll-areas" style="overflow:hidden;">
        <div class="row" id='row'>
          <?php if(!empty($this->check)) : ?>
          <?php foreach( $this->form->getPackages() as $package_id => $package ) : ?>
          <div class="columns <?php echo ($package->package_id == $best_choice) ? 'table2' : 'table1' ;?>">
              <?php if($package->package_id == $best_choice): ?>
                <div class="mini_head">
                  <p><?php echo $this->translate( ($templateStyle['template_mostpopularblock_text']['value'] == null) ? '' : $templateStyle['template_mostpopularblock_text']['value'] ) ; ?></p>
                </div>
              <?php endif; ?>
              <div class="<?php echo ($package->package_id == $best_choice) ? 'heading2' : 'heading' ;?>">
                <h1 title="<?php echo $package->title?>"><?php echo $this->translate(Engine_Api::_()->seaocore()->seaocoreTruncateText($package->title, 15)); ?></h1>
                <br>
                <h2><?php echo Engine_Api::_()->getApi('core','sitepage')->getCurrencySymbol(); ?> <?php echo $this->translate($package->price); ?>  <?php echo Engine_Api::_()->getApi('core','sitepage')->getReccurenceDuration($package->package_id); ?></h2>
              </div>
              <div class="content">
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
              <a class="<?php echo ($package->package_id == $best_choice) ? 'button' : 'button2' ;?>" id="<?php echo $package_id; ?>" onclick="submitForm(this.id)"><?php echo $this->translate($templateStyle['template_button_text']['value'] == null ? '' : $templateStyle['template_button_text']['value']); ?></a>
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
    <?php echo $footer_text; ?>
  </div>
<?php endif; ?>
          <?php else : ?>
          <?php foreach( $this->paginator as $package_id => $package ) : ?>
          <div class="columns <?php echo ($package->package_id == $best_choice) ? 'table2' : 'table1' ;?>">
              <?php if($package->package_id == $best_choice): ?>
                <div class="mini_head">
                  <p><?php echo $this->translate( ($templateStyle['template_mostpopularblock_text']['value'] == null) ? '' : $templateStyle['template_mostpopularblock_text']['value'] ) ; ?></p>
                </div>
              <?php endif; ?>
              <div class="<?php echo ($package->package_id == $best_choice) ? 'heading2' : 'heading' ;?>">
                <h1 title="<?php echo $package->title?>"><?php echo $this->translate(Engine_Api::_()->seaocore()->seaocoreTruncateText($package->title, 15)); ?></h1>
                <br>
                <h2><?php echo Engine_Api::_()->getApi('core','sitepage')->getCurrencySymbol(); ?> <?php echo $this->translate($package->price); ?>  <?php echo Engine_Api::_()->getApi('core','sitepage')->getReccurenceDuration($package->package_id); ?></h2>
              </div>
              <div class="content">
                 <ul><?php $rowCount = 1;?>
                  <?php foreach($features as $fields): ?>
                  <?php $value = $fieldValues[$fields['field_id']][$package->package_id];?>
                  <li title='<?php echo $value; ?>' class="<?php echo "compareField details_row_".$rowCount++ ?>">
                  <?php $label = $fieldValues[$fields['field_id']][$package->package_id] == null ? '-' : $fieldValues[$fields['field_id']][$package->package_id];
                      echo $this->translate(Engine_Api::_()->seaocore()->seaocoreTruncateText($label, 15)); ?>  
                  </li>
                  <?php endforeach; ?>
                 </ul>
              <a class="<?php echo ($package->package_id == $best_choice) ? 'button' : 'button2' ;?>" id="<?php echo $package->package_id; ?>" onclick="submitForm(this.id)"><?php echo $this->translate($templateStyle['template_button_text']['value'] == null ? '' : $templateStyle['template_button_text']['value']); ?></a>
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
    <?php echo $footer_text; ?>
  </div>
<?php endif; ?>
<?php endif; ?>


<style type="text/css">
.heading>h1, .heading2>h1, h2
{
  color: #<?php echo $templateStyle['template_packagename_price_textcolor']['value']; ?>;
  font-style: <?php echo $templateStyle['template_packagename_price_textstyle']['value']; ?>;
  font-size: <?php echo $templateStyle['template_packagename_price_textsize']['value']; ?>px;
  font-family: <?php echo $templateStyle['template_packagename_price_textfamily']['value']; ?>;
}
.heading, .table1>.content>.button2
{
  background-color: #<?php echo $templateStyle['template_packagename_price_bgcolor_normal']['value']; ?>;
}
.heading2, .table2>.content>.button
{
  background-color: #<?php echo $templateStyle['template_packagename_price_bgcolor_popular']['value']; ?>;
}
.container, .row 
{
  background-color: #<?php echo $templateStyle['template_bgcolor']['value']; ?>;
}
.content ul li, .content ul li b
{
  font-size: <?php echo $templateStyle['template_feature_textsize']['value']; ?>px;
  font-style: <?php echo $templateStyle['template_feature_textstyle']['value']; ?>;
  font-family: <?php echo $templateStyle['template_feature_textfamily']['value']; ?>;
}
.content>.button2, .content>.button
{
  font-size: <?php echo $templateStyle['template_button_textsize']['value']; ?>em ;
  font-style: <?php echo $templateStyle['template_button_textstyle']['value']; ?>;
  font-family: <?php echo $templateStyle['template_button_textfamily']['value']; ?>;
}
.table1>.content>.button2:hover
{
  color: #<?php echo $templateStyle['template_packagename_price_bgcolor_normal']['value']; ?>;
  border:1px solid #<?php echo $templateStyle['template_packagename_price_bgcolor_normal']['value']; ?>;
  background-color: #fff;
}
.table2>.content>.button:hover
{
  color: #<?php echo $templateStyle['template_packagename_price_bgcolor_popular']['value']; ?>;
  border:1px solid #<?php echo $templateStyle['template_packagename_price_bgcolor_popular']['value']; ?>;
  background-color: #fff !important;
}
.mini_head
{
  background-color: #<?php echo $templateStyle['template_mostpopularblock_bgcolor']['value'] ; ?>;
  color: #<?php echo $templateStyle['template_mostpopularblock_textcolor']['value'] ; ?>;
  font-size: <?php echo $templateStyle['template_mostpopularblock_textsize']['value'] ; ?>;
  font-style: <?php echo $templateStyle['template_mostpopularblock_textstyle']['value'] ; ?>;
  font-family: <?php echo $templateStyle['template_mostpopularblock_textfamily']['value'] ; ?>;
}
.horizontalThumb {
  background-color: #<?php echo $templateStyle['template_packagename_price_bgcolor_normal']['value']; ?>;
}
.horizontalTrack {
  background-color: ;
}
.banner {
  background-color: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.header.color', '#A9A9A9'); ?>;
}
.content ul li b
{
  font-weight: 700 !important;
}
</style>

<?php if ($param == 'smoothbox'): ?>
  <style type="text/css">
    #scroll-areas-main, #list-scroll-areas
    {
      margin: 0 auto !important;
    }
  </style>
<?php endif; ?>

<script type="text/javascript">
  function submitForm(id)
  {
    window.location.href = '<?php echo $url; ?>' + '/id/' + id; 
  }
</script>

<script type="text/javascript">
  var smoothbox = <?php echo ($param == 'smoothbox') ? 1 : 0; ?>;
  en4.core.runonce.add(function () {  
  
      var check = typeof SEAOMooHorizontalScrollBar;
            
            if(check === "undefined") { 
            Asset.javascript('<?php echo ($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/scripts/seaomooscroll/SEAOMooHorizontalScrollBar.js");?>');    
            }
  
  
  var children = $("row").childElementCount;
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

    $('row').setStyle('width', (width * children) + 'px');
    $('row').getElements('.columns').each(function (el) {
      width = (width > 300 && numberOfItemFloor >= children) ? 300 : width;
      // marginEnabled = (numberOfItemFloor >= children) ? 5 : 2;
      el.setStyle('width', width - 7 + 'px');
    });
    if((width * children) <= containerWidth){
      $('scrollbar_after').setStyle('display','none');
    }  
  };
  })
</script>
