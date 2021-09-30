sm4.sitepagealbum = {

  rotate : function(photo_id, angle, thisObject, setClass, url) {
    var request = $.ajax({
      url : url,
      type: "POST",
      dataType: "json",
      data : {
        format : 'json',
        photo_id : photo_id,
        angle : angle
      },
      complete: function(response) {
        thisObject.attr('class', setClass);
        // Check status
        if( $.type(response) == 'object' &&
          $.type(response.status) &&
          response.status == false ) {
          sm4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button data-rel="back">Close</button>');
          return;
        } else if( $.type(response) != 'object' ||
          !$.type(response.status) ) {
          sm4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button data-rel="back">Close</button>');
          return;
        }

        // Ok, let's refresh the page I guess
        window.location.reload(true);
      }
    });
    return request;
  },

  flip : function(photo_id, direction, thisObject, setClass, url) {
    var request = $.ajax({
      url : url,
      type: "POST",
      dataType: "json",
      data : {
        format : 'json',
        photo_id : photo_id,
        direction : direction
      },
      complete: function(response) {
        thisObject.attr('class', setClass);
        // Check status
        if( $.type(response) == 'object' &&
          $.type(response.status) &&
          response.status == false ) {
          sm4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button data-rel="back">Close</button>');
          return;
        } else if( $.type(response) != 'object' ||
          !$.type(response.status) ) {
          sm4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button data-rel="back">Close</button>');
          return;
        }
        // Ok, let's refresh the page I guess
        window.location.reload(true);
      }
    });
    return request;
  }
  
  

};