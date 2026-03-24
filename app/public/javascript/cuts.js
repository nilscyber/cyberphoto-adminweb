$(function() {
  $("[autofocus]").on("focus", function() {
    if (this.setSelectionRange) {
      var len = this.value.length * 2;
      this.setSelectionRange(len, len);
    } else {
      this.value = this.value;
    }
    this.scrollTop = 999999;
  }).focus();
});

function search() {
  var search = $('#searchbar');
  search.val('');
  search.focus();
}
<script>
Mousetrap.bind('esc', pageRedirect);
Mousetrap.bind('alt+1', pageRedirectActual);
Mousetrap.bind('alt+2', pageRedirectTradeIn);
</script>
