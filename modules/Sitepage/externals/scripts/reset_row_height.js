/* $Id: reset_row_height.js 9572 2018-01-05 23:41:06Z john $ */

en4.core.runonce.add(function () {
  
      $('signup').getElements('.compareField').each(function(element){
            element.get('class').split(' ').each(function(className){
            className = className.trim();
            if( className.match(/^details_row_[0-9]+$/) ) {
              var MaxHeight = 0;
              $$('.'+className).each(function(el){
                if(MaxHeight <el.offsetHeight)
                  MaxHeight = el.offsetHeight;
              });

              if( MaxHeight > 50 ) {
                MaxHeight = MaxHeight- 45;
              } else {
                MaxHeight= MaxHeight;
              }

              $$('.'+className).setStyle('min-height',MaxHeight+'px');
            }
          });
        });
});

