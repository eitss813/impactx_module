
<h2>
  <?php echo $this->translate('Directory / Pages Plugin - Configure Plans, Layout and Mapping with Profile Types / Member Levels') ?>
</h2>
<?php if( count($this->navigation) ): ?>
<div class='seaocore_admin_tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
</div>
<?php endif; ?>

<?php if( count($this->subnavigation) ): ?>
<div class='seaocore_admin_tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->subnavigation)->render();
    ?>
</div>
<?php endif; ?>

<?php 
if( Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0  ) 
{
  echo "<ul class='form-errors'><li>Payment gateways not enabled or configured properly.</li></ul>";
  // return ;
}
?>
<?php $option = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.package.view', 1);
if($option == 2): ?>
<div class="clear">
<div class="settings">
    <form class="global_form" method="post" id="myForm">
        <div>
            <div>
              <h3>Custom Feature Sets </h3>
              <span>Here you can add and manage the features you want to provide the users in each package. This form enables you to design packages which will get displayed to users while creating their Pages.</span>
              <br>
              <br>
              <span>
                You can use the markdown styling format. ie asterisk (**) for bold, tilds (~~) for italics and underscores (__) for scratch like:<br>
                **word** will appear as <b>word</b>.<br>
                ~~word~~ will appear as <i>word</i>.<br>
                __word__ will appear as <strike>word</strike>.
              </span>
            </div>

                       <br>
                <div class="tip">
                    <span><?php echo $this->translate("Truncation will apply to all the fields having text content for the below packages. "); ?></span>
                </div>
            <br><br>
            
            <?php if(count($this->packageOrder)<=0):?>
              <div class="tip">
                <span><?php echo $this->translate("There are currently no subscriptions") ?></span>
              </div>
            <?php else: ?>
              <div style="overflow-x : scroll;">
                <table class="admin_table">
                  <thead>
                    <tr>
                      <th  style='width: 1%;'>
                      </th>
                      <!-- Check if the selected template uses feature field  -->
                      <?php if($this->isFeatureEnabled): ?>
                        <th style='width: 1%;' class="admin_table_centered">
                          <?php echo $this->translate("Feature"); ?>
                        </th>
                      <?php endif; ?>
                      <?php foreach( $this->packageOrder as $package_id => $order ): ?>
                        <th style='min-width: 130px;' class="admin_table_centered">
                          <p style="cursor: pointer; font-weight: bold;" title="<?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($this->packages[$package_id]['title'], 15); ?>"><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($this->packages[$package_id]['title'], 15);  ?></p>
                        </th>
                      <?php endforeach; ?>
                      <th style='width: 1%;' class="admin_table_centered">
                          <?php echo $this->translate("Options") ?>
                      </th>
                    </tr>
                  </thead>
                  <tbody id="tbody">
                    <tr>
                      <td></td>
                      <?php if($this->isFeatureEnabled): ?>
                        <td></td>
                      <?php endif;?>
                      <?php foreach( $this->packageOrder as $package_id => $order ): ?>
                        <td onmousedown="_sortables.ignoreDrag(event);" class="admin_table_centered"><?php echo "$ ".$this->packages[$package_id]['price']; ?></td>
                      <?php endforeach; ?>
                      <td></td>
                    </tr>
                    <!-- Check if there are already some fields in table -->
                    <?php if (count($this->features)>=0): ?>
                      <?php foreach($this->features as $field_id => $options): ?>
                        <tr  id="<?php echo 'field_'.$field_id; ?>">
                          <td class='sortable'  style='width: 1%;'>
                            <img title="Sort features" style="cursor: pointer;" src="<?php echo 'application/modules/Sitepage/externals/images/sortable.png'?>">
                          </td>
                          <!-- Check if the selected template uses feature field  -->
                          <?php if($this->isFeatureEnabled): ?>
                            <td style='width: 1%;' class="admin_table_centered" onmousedown="_sortables.ignoreDrag(event)">
                              <input type="text" name="<?php echo $field_id.'_field_name'; ?>" value="<?php echo $options['label']; ?>">
                            </td>
                          <?php endif; ?>
                          <?php foreach($this->packageOrder as $package_id => $order): ?>
                            <td onmousedown="_sortables.ignoreDrag(event)">
                              <?php if($this->isFeatureEnabled): ?>
                                <!-- Edit options wrapper for feature enabled-->
                                    <div class="edit_options_wrapper" id="<?php echo $field_id.'_'.$package_id.'_edit_options_wrapper'; ?>">
                                      <input type="text" id="<?php echo $field_id.'_'.$package_id.'_textbox'; ?>" name="<?php echo $field_id.'_'.$package_id.'_textbox'; ?>" style="display: none; margin-bottom:10px;">
                                      <div style="min-width: 120px; ">
                                        <select style="display :inline;" name="<?php echo $field_id.'_'.$package_id.'_selectbox'; ?>" id="<?php echo $field_id.'_'.$package_id.'_selectbox'; ?>" onclick="_obj.displayTextbox(this)">
                                            <option></option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                            <option value="textbox">Textbox</option>
                                        </select>
                                        <span style="float: right;">
                                          <img title="Save" style="cursor: pointer;margin-top: 5px;" id="<?php echo $field_id.'_'.$package_id.'_save'; ?>" src="<?php echo 'application/modules/Sitepage/externals/images/save.svg'?>" onclick="_obj.saveEditOptions(this.id)" height="20px" width="20px">
                                        </span>
                                      </div>
                                    </div>
                                    <div class="edit_wrapper" id="<?php echo $field_id.'_'.$package_id.'_edit_wrapper'; ?>">
                                      <span style="float: right; padding-left: 7px;">
                                        <img title="Edit" style="cursor: pointer;" id="<?php echo $field_id.'_'.$package_id.'_edit'; ?>" src="<?php echo 'application/modules/Sitepage/externals/images/edit.png'?>" onclick="_obj.showEditOptions(this.id)" height="10px" width="10px">
                                      </span>
                                      <span id="<?php echo $field_id.'_'.$package_id.'_value'; ?>">
                                        <?php echo ''; ?>
                                      </span>
                                    </div>
                                <!-- Edit options wrapper for feature enabled end-->
                              <?php else: ?>
                                <!-- Edit options wrapper for feature disabled-->
                                    <div class="edit_options_wrapper" id="<?php echo $field_id.'_'.$package_id.'_edit_options_wrapper'; ?>">
                                      <input type="text" id="<?php echo $field_id.'_'.$package_id.'_textbox'; ?>" name="<?php echo $field_id.'_'.$package_id.'_textbox'; ?>" style="margin-bottom:10px;">
                                      <div style="min-width: 120px;display :inline; ">
                                        <span style="float: right;">
                                          <img title="Save" style="cursor: pointer;margin-top: 5px;" id="<?php echo $field_id.'_'.$package_id.'_save'; ?>" src="<?php echo 'application/modules/Sitepage/externals/images/save.svg'?>" onclick="_obj.saveEditOptions(this.id)" height="20px" width="20px">
                                        </span>
                                      </div>
                                    </div>
                                    <div class="edit_wrapper" id="<?php echo $field_id.'_'.$package_id.'_edit_wrapper'; ?>">
                                      <span style="float: right;">
                                        <img title="Edit" style="cursor: pointer;" id="<?php echo $field_id.'_'.$package_id.'_edit'; ?>" src="<?php echo 'application/modules/Sitepage/externals/images/edit.png'?>" onclick="_obj.showEditOptions(this.id)" height="10px" width="10px">
                                      </span>
                                      <span id="<?php echo $field_id.'_'.$package_id.'_value'; ?>">
                                        <?php echo ''; ?>
                                      </span>
                                    </div>
                                <!-- Edit options wrapper for feature disabled end-->
                              <?php endif; ?>
                            </td>
                          <?php endforeach; ?>
                          <td  style='width: 1%;' class="admin_table_centered">
                            <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage' , 'controller' => 'package' , 'action' => 'delete-field' , 'field_id' => $field_id ), $this->translate('Delete'), array( 'class' => 'smoothbox')); ?>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else:?>
                      <div class="tip">
                        <span> <?php echo $this->translate("There are no features added."); ?></span>
                      </div>
                    <?php endif;?>
                  </tbody>
                </table>
              </div>
              <br/>
              <div>
                  <a style="font-weight: bold; cursor: pointer;" onclick="_obj.addFeature()" style="cursor: pointer;">Add Row</a>
              </div>
              <br><br>
              <input type="hidden" name="structure" value="<?php echo $this->isFeatureEnabled; ?>">
              <button name="save" id="submit" type="submit">Save</button> &nbsp;
              <button name="preview" id="submit" type="button" onclick="_obj.previewPlans()">Preview</button> &nbsp;&nbsp;
              <span id="loader" style="display: none;"> Creating preview ... &nbsp;  &nbsp; <img length="20px" width="20px" style="vertical-align: middle;" src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/loader.gif'; ?>" ></span>
            <?php endif; ?>
            
        </div>
    </form>
    <?php else:?>
    <div class="tip">
                    <span><?php echo $this->translate("Please enable the Custom option for Package View in the Global Settings "); ?></span>
    </div>
    <?php endif;?>
</div>
</div>

<script type="text/javascript">
  _obj = {
    flag: false,
    isFeatureEnabled: null,
    fieldValuesObject: null,
    features: null,
    newFeatureCount: 1,
    writePreviewFileUrl:  '<?php echo $this->url(array('module' => 'sitepage', 'controller' => 'package', 'action'=>'write-preview-file', 'previewtype' => 'fields'), 'admin_default', true) ?>',
    packageOrder: new Array(),
    sb_url: '<?php echo $this->url(array('module' => 'sitepage', 'controller' => 'package', 'action'=>'preview', 'previewtype' => 'fields'), 'admin_default', true) ?>',
    setValues: function() {
      var valuesAt = _obj.fieldValuesObject;
      if (valuesAt == null) 
        return;
      Object.keys(valuesAt).forEach(function(field_id) {
        Object.keys(valuesAt[field_id]).forEach(function(package_id) {
          var id_prefix = field_id+'_'+package_id,
              valueField = document.getElementById(id_prefix+'_value'),
              selectbox = document.getElementById(id_prefix+'_selectbox'),
              textbox = document.getElementById(id_prefix+'_textbox');
          switch (valuesAt[field_id][package_id])
          {
              case 'yes' :
                      if (valueField == null) break;
                      valueField.innerHTML = 'Yes';
                      if (_obj.isFeatureEnabled == '1') selectbox.selectedIndex = '1';
                      break;
              case 'no' :
                      if (valueField == null) break;
                      valueField.innerHTML = 'No';
                      if (_obj.isFeatureEnabled == '1') selectbox.selectedIndex = '2';
                      break;
              case '0' :
                      if (valueField == null) break;
                      valueField.innerHTML = '-';
                      if (_obj.isFeatureEnabled == '1') selectbox.selectedIndex = '0';
                      break;
              case null :
                      if (valueField == null) break;
                      valueField.innerHTML = '-';
                      if (_obj.isFeatureEnabled == '1') selectbox.selectedIndex = '0';
                      break;
              case undefined :
                      if (valueField == null) break;
                      _obj.showEditOptions(field_id+"_"+package_id+"_edit");
                      break;
              case '' :
                      if (valueField == null) break;
                      valueField.innerHTML = '-';
                      if (_obj.isFeatureEnabled == '1') selectbox.selectedIndex = '0';
                      break;
              default :
                      if (valueField == null) break;
                      valueField.innerHTML = _obj.convertFromMarkDownFormat(valuesAt[field_id][package_id]);
                      if (_obj.isFeatureEnabled == '1') selectbox.selectedIndex = '3';
                      textbox.style.display = "block";
                      textbox.value = _obj.fromJsonReadableFormat(_obj.convertToMarkDownFormat(valuesAt[field_id][package_id]));
                      break; 
          }
        });
      });
    },
    showEditOptions: function(id) {
      var edit_wrapper_id = "#"+id.replace('edit','edit_wrapper'),
          edit_options_wrapper_id = "#"+id.replace('edit','edit_options_wrapper');
      $$(edit_wrapper_id).setStyle("display","none");
      $$(edit_options_wrapper_id).setStyle("display","block");
    },
    saveEditOptions: function(id) {
      var value_id = id.replace('save','selectbox'),
          edit_wrapper_id = "#"+id.replace('save','edit_wrapper'),
          edit_options_wrapper_id = "#"+id.replace('save','edit_options_wrapper'),
          selectedIndex = _obj.isFeatureEnabled == '0' ? 3 : document.getElementById(value_id).selectedIndex;

      $$(edit_options_wrapper_id).setStyle("display","none");
      $$(edit_wrapper_id).setStyle("display","block");

      switch(selectedIndex)
      {
        case 0 : 
                document.getElementById(id.replace('save','value')).innerHTML = ''; 
                break; 
        case 1 :
                document.getElementById(id.replace('save','value')).innerHTML = 'Yes';
                break;
        case 2 :
                document.getElementById(id.replace('save','value')).innerHTML = 'No';
                break;
        case 3 :
                var text = document.getElementById(id.replace('save','textbox')).value;
                document.getElementById(id.replace('save','value')).innerHTML = _obj.convertFromMarkDownFormat(text);
                break;
      }
    },
    displayTextbox: function(element) {
      var textbox_id = element.id.replace('selectbox','textbox');
      if (element.value=='textbox')
          document.getElementById(textbox_id).style.display = "block";
      else
          document.getElementById(textbox_id).style.display = "none";
      },
    previewPlans: function() {
      var formData = $('myForm').toQueryString().parseQueryString();
      Object.keys(formData).forEach(function(index,keys,value){
        formData[index] = _obj.convertFromMarkDownFormat(formData[index]);
      });

      var content = 'content='+JSON.stringify(formData);
      if (_obj.flag == false) {
        _obj.flag = true;
        _obj.setLoader(_obj.flag);
        var request = new Request.JSON({
        'url' : _obj.writePreviewFileUrl,
        'data' : content,
        onError: function(text,error) {
            console.warn(error);
          },
        onSuccess : function(responseJSON) {
          _obj.flag = false;
          _obj.setLoader(_obj.flag);
          if (responseJSON.return == 0) 
            alert(responseJSON.message);
          else
            Smoothbox.open(_obj.sb_url
              // ,{width : 980, height : 400,}
              );
          }
        });
        request.send();
      }

    },
    setLoader: function(flag) {
      if (flag == true) {
        $$('#loader').setStyle('display','inline');
      } else {
        $$('#loader').setStyle('display','none');
      }
    },
    addFeature: function() {
      var rowInstance = $('tbody').insertRow(-1);
      rowInstance.id = 'field_new'+ _obj.newFeatureCount;
      _obj.insertSortableCell(rowInstance), _obj.insertFeatureCell(rowInstance), _obj.insertFieldValuesCell(rowInstance), _obj.insertDeleteFieldCell(rowInstance), 
      _sortables.init();
      _obj.newFeatureCount++;
    },
    insertSortableCell: function(rowInstance) {
      var sortableCell = rowInstance.insertCell(-1);
      sortableCell.addClass('sortable');
      sortableCell.innerHTML = '<img title="Sort features" style="cursor: pointer;" src="'+'<?php echo "application/modules/Sitepage/externals/images/sortable.png"?>'+'">';
    },
    insertFeatureCell: function(rowInstance) {
      if (_obj.isFeatureEnabled == '0') 
        return;
      var featureCell = rowInstance.insertCell(-1);
      featureCell.addClass('admin_table_centered');
      featureCell.addEvent('mousedown',function(event){
        _sortables.ignoreDrag(event);
      });
      featureCell.innerHTML = '<input type="text" name="new'+_obj.newFeatureCount+'_field_name">';
    },
    insertFieldValuesCell: function(rowInstance) {
      _obj.packageOrder.forEach(function(index,keys,value){
        var featureValueCell = rowInstance.insertCell(-1);
        featureValueCell.innerHTML = _obj.getCellContent(rowInstance.id.substr(-4),index);
        featureValueCell.addEvent('mousedown',function(event){
          _sortables.ignoreDrag(event);
        });
      });
    },
    getCellContent: function(field_id,package_id) {
      var id_prefix = field_id+'_'+package_id;
      if (_obj.isFeatureEnabled == '1') 
        return '<div class="edit_options_wrapper" id="'+id_prefix+'_edit_options_wrapper" style="display: none;"><input type="text" id="'+id_prefix+'_textbox" name="'+id_prefix+'_textbox" style="display: none; margin-bottom:10px;"><div style="min-width: 120px;"><select style="display :inline;" name="'+id_prefix+'_selectbox" id="'+id_prefix+'_selectbox" onclick="_obj.displayTextbox(this)"><option></option><option value="yes">Yes</option><option value="no">No</option><option value="textbox">Textbox</option></select><span style="float: right;"><img  title="Save" style="cursor: pointer;margin-top: 5px;" id="'+id_prefix+'_save" src="'+'<?php echo "application/modules/sitepage/externals/images/save.svg"?>'+'" onclick="_obj.saveEditOptions(this.id)" height="20px" width="20px"></span></div></div><div class="edit_wrapper" id="'+id_prefix+'_edit_wrapper"><span style="float: right;"><img title="Edit" style="cursor: pointer;" id="'+id_prefix+'_edit" src="<?php echo 'application/modules/Sitepage/externals/images/edit.png'?>" onclick="_obj.showEditOptions(this.id)" height="10px" width="10px"></span><span id="'+id_prefix+'_value"></span></div>';
      else
        return '<div class="edit_options_wrapper" id="'+id_prefix+'_edit_options_wrapper" style="display: none;"><input type="text" id="'+id_prefix+'_textbox" name="'+id_prefix+'_textbox" style="margin-bottom:10px;"><div style="min-width: 120px;display :inline;"><span style="float: right;"><img title="Save" style="cursor: pointer;margin-top: 5px;" id="'+id_prefix+'_save" src="'+'<?php echo "application/modules/Sitepage/externals/images/save.svg"; ?>'+'" onclick="_obj.saveEditOptions(this.id)" height="20px" width="20px"></span></div></div><div class="edit_wrapper" id="'+id_prefix+'_edit_wrapper"><span style="float: right;"><img title="Edit" style="cursor: pointer;" id="'+id_prefix+'_edit" src="'+'<?php echo "application/modules/Sitepage/externals/images/edit.png"?>'+'" onclick="_obj.showEditOptions(this.id)" height="10px" width="10px"></span><span id="'+id_prefix+'_value"></span></div>';
      
    },
    insertDeleteFieldCell: function(rowInstance) {
      var deleteFieldCell = rowInstance.insertCell(-1);
      deleteFieldCell.addClass('admin_table_centered');
      deleteFieldCell.innerHTML = '<a style="cursor:pointer;" onclick="_obj.deleteRow(this.parentNode.parentNode)">Delete</a>';
    },
    deleteRow: function(element) {
      element.parentNode.parentNode.deleteRow(element.rowIndex);
    },
    getTag: function(markdown,pos) {
      switch(markdown)
      {
        case '**':
          tag = "b>";
          break;

        case '~~':
          tag = "i>";
          break;

        case '__':
          tag = "strike>";
          break;
        
        default:
          break;
      }

      if ( (parseInt(pos)%2) == 0)
        tag = '</'+tag;
      else
        tag = '<'+tag;

      return tag;
    },
    convertFromMarkDownFormat: function(string) {
      var markdown_array = {
        '**' : new RegExp("\\*\\*",'gi'),
        '~~' : new RegExp("\\~\\~",'gi'),
        '__' : new RegExp("__",'gi'),
      };

      var markdown_replacement_array = {
        '**' : new RegExp("\\*\\*",'i'),
        '~~' : new RegExp("\\~\\~",'i'),
        '__' : new RegExp("__",'i'),
      };

      Object.keys(markdown_array).forEach(function(index,keys,value){
        var count = string.match(markdown_array[index]);
        if (count != null) {
          for (var i = 1; i <= count.length; i++) {
            var tag = _obj.getTag(index,i);
            string = string.replace(markdown_replacement_array[index],tag);
          }
        }
      });
      return string;
    },
    convertToMarkDownFormat: function(string) {
      var tags_array = {
        '**' : ["<b>","<\/b>"],
        '~~' : ["<i>","<\/i>"],
        '__' : ["<strike>","<\/strike>"]
      };
      Object.keys(tags_array).forEach(function(index,keys,value){
        var regex = new RegExp("("+tags_array[index][0]+"|"+tags_array[index][1]+")",'gi');
        string = string.replace(regex,index);
      });

      return string;
    },
    fromJsonReadableFormat: function(string) {
      var singlequote_regex = new RegExp("\\&\\#39\\;",'g');
      var doublequote_regex = new RegExp("\\&\\#34\\;",'g');

      if (string.length == 0) 
        return '';

      string = string.replace( singlequote_regex, "'",string);
      string = string.replace( doublequote_regex, '"',string);

      return string;
    },
  };

  _sortables = {
    url: '<?php echo $this->url(array('controller' => 'package', 'action' => 'order-items','item' => 'fields')) ?>',
    SortablesInstance: null,
    init: function() {
      _sortables.SortablesInstance = new Sortables('tbody', {
          clone: false,
          handel: 'sortable',
          constrain: true,
          onComplete: function(e) {
            _sortables.reorder(e);
          }
      });
    },
    reorder: function(e) {
      var steps = e.parentNode.childNodes;
       var ordering = {};
       var i = 1;
       for (var step in steps)
       {
         var child_id = steps[step].id;
         if (child_id == undefined) 
         {
          continue;
         }
         if (child_id.substr(0,6) == 'field_')
         {
           ordering[child_id] = i;
           i++;
         }
      }
      ordering['format'] = 'json';
      // Send request
      var request = new Request.JSON({
        'url' : _sortables.url,
        'data' : ordering,
        onError: function(text,error) {
            console.warn(error);
          },
        onSuccess : function(responseJSON) {
        }
      });
      request.send();
    },
    ignoreDrag: function(event) {
      event.stopPropagation();
      return false;
    }
  }

  function initializeValues() {
    fieldValuesJSON = '<?php echo json_encode($this->fieldValues, JSON_FORCE_OBJECT); ?>';
    featuresJSON = '<?php echo json_encode($this->features); ?>';

    var packageOrderJSONObject = JSON.parse('<?php echo json_encode($this->packageOrder, JSON_FORCE_OBJECT);?>');
    Object.keys(packageOrderJSONObject).forEach(function(index,keys,value){
      _obj.packageOrder[packageOrderJSONObject[index]] = index;
    });

    _obj.isFeatureEnabled = '<?php echo $this->isFeatureEnabled; ?>';
    _obj.fieldValuesObject = JSON.parse(fieldValuesJSON);
    _obj.features = JSON.parse(featuresJSON);
  }

  window.addEvent('load', function() {
    initializeValues();
    $$(".edit_options_wrapper").setStyle("display","none");
    _obj.setValues();
    _sortables.init();
  });
  
</script>

<?php if(!$this->isFeatureEnabled): ?>
<style type="text/css">
  .edit_options_wrapper
  {
    text-align: center !important; 
  }
  input[type=text]
  {
    display: inline !important;
    margin-left: auto;
    margin-right: auto;
  }
</style>
<?php endif; ?>

<style type="text/css">
  .edit_wrapper span b
  {
    font-weight: 700 !important;
  }
</style>
