Shadowbox.init({
  onChange: track,
  onOpen: track 
});

function track(image){
    item = $('<div style="background-color:green;">'+image.content+'</div>')
    $('#sb-body-inner').append(item);
  }