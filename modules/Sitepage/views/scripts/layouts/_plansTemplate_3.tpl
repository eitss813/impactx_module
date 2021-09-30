
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

<link href="https://fonts.googleapis.com/css?family=Roboto:300" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/_plansTemplate_3.css'; ?>">

  <form method="get" action="" class="" enctype="application/x-www-form-urlencoded" id="signup" name="signup">
      
    <div id="plan_container">
      <div id="scroll-areas-main">
      <div class="container" id="list-scroll-areas" style="overflow: hidden;">
        <div class="row" id="row">
          <?php if(!empty($this->check)) : ?>
          <?php foreach( $this->form->getPackages() as $package_id => $package ) : ?>
          <div class="columns <?php echo ($package->package_id == $best_choice) ? 'table2' : 'table1' ;?>">
              <div class="<?php echo ($package->package_id == $best_choice) ? 'head_section2' : 'head_section1' ;?>">
                <h1 title="<?php echo $package->title?>"><?php echo $this->translate(Engine_Api::_()->seaocore()->seaocoreTruncateText($package->title, 15)); ?></h1>
              </div>
              <div class="<?php echo ($package->package_id == $best_choice) ? 'price2' : 'price1' ;?>">
                <h1>
                  <sup><?php echo Engine_Api::_()->getApi('core','sitepage')->getCurrencySymbol(); ?></sup> <?php echo $this->translate($package->price); ?><span> <?php echo Engine_Api::_()->getApi('core','sitepage')->getReccurenceDuration($package->package_id); ?></span>
                </h1>
              </div>
              <?php if($package->package_id == $best_choice): ?>
                <div class="mini_head">
                  <p><?php echo $this->translate(($templateStyle['template_mostpopularblock_text']['value'] == null) ? '' : $templateStyle['template_mostpopularblock_text']['value'] ) ; ?></p>
                </div>
              <?php endif; ?>
              <div class="content">
                 <ul><?php $rowCount = 1;?>
                  <?php foreach($features as $fields): ?>
                  <?php $value = $fieldValues[$fields['field_id']][$package_id];?>
                  <li title='<?php echo $value; ?>' class="<?php echo "compareField details_row_".$rowCount++ ?>">
                  <?php $label = $fieldValues[$fields['field_id']][$package_id] == null ? '-' : $fieldValues[$fields['field_id']][$package_id];
                      echo $this->translate(Engine_Api::_()->seaocore()->seaocoreTruncateText($label, 15)); ?>  
                  </li>
                  <?php endforeach; ?>
                 </ul>
              <a class="<?php echo ($package->package_id == $best_choice) ? 'button2' : 'button' ;?>" id="<?php echo $package_id; ?>" disabled="disabled" onclick="submitForm(this.id)"><?php echo $this->translate($templateStyle['template_button_text']['value'] == null ? '' : $templateStyle['template_button_text']['value']); ?></a>
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
              <div class="<?php echo ($package->package_id == $best_choice) ? 'head_section2' : 'head_section1' ;?>">
                <h1 title="<?php echo $package->title?>"><?php echo $this->translate(Engine_Api::_()->seaocore()->seaocoreTruncateText($package->title, 15)); ?></h1>
              </div>
              <div class="<?php echo ($package->package_id == $best_choice) ? 'price2' : 'price1' ;?>">
                <h1>
                  <sup><?php echo Engine_Api::_()->getApi('core','sitepage')->getCurrencySymbol(); ?></sup> <?php echo $this->translate($package->price); ?><span> <?php echo Engine_Api::_()->getApi('core','sitepage')->getReccurenceDuration($package->package_id); ?></span>
                </h1>
              </div>
              <?php if($package->package_id == $best_choice): ?>
                <div class="mini_head">
                  <p><?php echo $this->translate(($templateStyle['template_mostpopularblock_text']['value'] == null) ? '' : $templateStyle['template_mostpopularblock_text']['value'] ) ; ?></p>
                </div>
              <?php endif; ?>
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
              <a class="<?php echo ($package->package_id == $best_choice) ? 'button2' : 'button' ;?>" id="<?php echo $package->package_id; ?>" disabled="disabled" onclick="submitForm(this.id)"><?php echo $this->translate($templateStyle['template_button_text']['value'] == null ? '' : $templateStyle['template_button_text']['value']); ?></a>
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
.head_section1>h1 , .head_section2>h1
{
  color: #<?php echo $templateStyle['template_packagename_textcolor']['value']; ?>;
  font-style: <?php echo $templateStyle['template_packagename_textstyle']['value']; ?>;
  font-size: <?php echo $templateStyle['template_packagename_textsize']['value']; ?>px;
  font-family: <?php echo $templateStyle['template_packagename_textfamily']['value']; ?>;
}
.head_section1, .table1>.content>.button:hover
{
  background-color: #<?php echo $templateStyle['template_packagename_bgcolor_normal']['value']; ?> !important;
  color : #fff !important;
}
.head_section2, .table2>.content>.button2:hover
{
  background-color: #<?php echo $templateStyle['template_packagename_bgcolor_popular']['value']; ?> !important;
  color : #fff !important;
}
.row>.table2
{
  border: 5px solid #<?php echo $templateStyle['template_packagename_bgcolor_popular']['value']; ?>;
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
.content>.button, .content>.button2
{
  background-color: #fff;
  font-size: <?php echo $templateStyle['template_button_textsize']['value']; ?>px ;
  font-style: <?php echo $templateStyle['template_button_textstyle']['value']; ?>;
  font-family: <?php echo $templateStyle['template_button_textfamily']['value']; ?>;
}
.table1>.content>.button
{
  color: #<?php echo $templateStyle['template_packagename_bgcolor_normal']['value']; ?> !important;
  border:1px solid #<?php echo $templateStyle['template_packagename_bgcolor_normal']['value']; ?>;
}
.table2>.content>.button2
{
  color: #<?php echo $templateStyle['template_packagename_bgcolor_popular']['value']; ?>;
  border:1px solid #<?php echo $templateStyle['template_packagename_bgcolor_popular']['value']; ?>;
}
.price1>h2 , .price2>h2
{
  font-size: <?php echo $templateStyle['template_price_textsize']['value']; ?>px ;
  font-style: <?php echo $templateStyle['template_price_textstyle']['value']; ?>;
  font-family: <?php echo $templateStyle['template_price_textfamily']['value']; ?>;
}
.mini_head
{
  background-color: #<?php echo $templateStyle['template_mostpopularblock_bgcolor']['value'] ; ?>;
  color: #<?php echo $templateStyle['template_mostpopularblock_textcolor']['value'] ; ?>;
  font-size: <?php echo $templateStyle['template_mostpopularblock_textsize']['value'] ; ?>;
  font-style: <?php echo $templateStyle['template_mostpopularblock_textstyle']['value'] ; ?>;
  font-family: <?php echo $templateStyle['template_mostpopularblock_textfamily']['value'] ; ?>;
  margin-bottom: 10px;
}
.horizontalThumb {
  background-color: #<?php echo $templateStyle['template_packagename_bgcolor_normal']['value']; ?>;
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
      marginEnabled = (children == 3 && containerWidth < 400) ? 5.8 : 5.6;
      el.setStyle('width', width - marginEnabled + 'px');
    });
    if((width * children) <= containerWidth){
      $('scrollbar_after').setStyle('display','none');
    } 
  };
  })
</script>

