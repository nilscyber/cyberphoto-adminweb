document.write('<div id="loading"><br><br>Vänta, sidan laddas...</div>');

function addLoadEvent(func) {
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = func;
  } else {
    window.onload = function() {
      if (oldonload) {
        oldonload();
      }
      func();
    }
  }
}

addLoadEvent(function() {
  document.getElementById("loading").style.display="none";
});
