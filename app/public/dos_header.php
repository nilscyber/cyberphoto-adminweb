<?php
	echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
	echo "<html>\n\n";
	echo "<head>\n";
	if (preg_match("/dos_product\.php/i", $_SERVER['PHP_SELF'])) {
		echo "<link rel=\"icon\" type=\"image/png\" href=\"https://admin.cyberphoto.se/dos_favicon_p.png\">\n";
	} elseif (preg_match("/dos_order\.php/i", $_SERVER['PHP_SELF'])) {
		echo "<link rel=\"icon\" type=\"image/png\" href=\"https://admin.cyberphoto.se/dos_favicon_o.png\">\n";
	} elseif (preg_match("/dos_customer\.php/i", $_SERVER['PHP_SELF'])) {
		echo "<link rel=\"icon\" type=\"image/png\" href=\"https://admin.cyberphoto.se/dos_favicon_k.png\">\n";
	} else {
		echo "<link rel=\"icon\" type=\"image/png\" href=\"https://admin.cyberphoto.se/dos_favicon.png\">\n";
	}
	echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n";
	$admin->displayPageTitle();
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"/css/dos.css?ver=ad" . date("ynjGi") . "\">\n";
	echo "<script type=\"text/javascript\" src=\"https://admin.cyberphoto.se/javascript/winpop.js\"></script>\n";
	echo "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js\"></script>\n";
	echo "<script src=\"//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js\"></script>\n";
	echo "<script src=\"//cdnjs.cloudflare.com/ajax/libs/mousetrap/1.4.6/mousetrap.min.js\"></script>\n";

?>
<script type="text/javascript">
<?php if (preg_match("/dos_product\.php/i", $_SERVER['PHP_SELF'])) { ?>
    function pageRedirect() {
        window.location.replace("https://admin.cyberphoto.se/p");
    }
<?php } elseif (preg_match("/dos_order\.php/i", $_SERVER['PHP_SELF'])) { ?>
    function pageRedirect() {
        window.location.replace("https://admin.cyberphoto.se/o");
    }
<?php } else { ?>
    function pageRedirect() {
        window.location.replace("https://admin.cyberphoto.se/k");
    }
<?php } ?>
    function pageRedirectActual() {
        window.location.replace("https://admin.cyberphoto.se/p?q=<?php echo $beskrivning; ?>&sortera=tillverkare");
    }
    function pageRedirectTradeIn() {
        window.location.replace("https://admin.cyberphoto.se/p?q=<?php echo $beskrivning; ?>&sortera=old_tradein");
    }
    function pageRedirectDiscontinued() {
        window.location.replace("https://admin.cyberphoto.se/p?q=<?php echo $beskrivning; ?>&sortera=discontinued");
    }
<?php if (preg_match("/dos_product\.php/i", $_SERVER['PHP_SELF'])) { ?>
    function pageRedirectNewSearch() {
        window.location.replace("https://admin.cyberphoto.se/p?s=yes");
    }
<?php } elseif (preg_match("/dos_order\.php/i", $_SERVER['PHP_SELF'])) { ?>
    function pageRedirectNewSearch() {
        window.location.replace("https://admin.cyberphoto.se/o?s=yes");
    }
<?php } else { ?>
    function pageRedirectNewSearch() {
        window.location.replace("https://admin.cyberphoto.se/k?s=yes");
    }
<?php } ?>
    function pageRedirectCustomer() {
        window.location.replace("https://admin.cyberphoto.se/k?s=yes");
    }
    function pageRedirectProduct() {
        window.location.replace("https://admin.cyberphoto.se/p?s=yes");
    }
    function pageRedirectOrder() {
        window.location.replace("https://admin.cyberphoto.se/o?s=yes");
    }
</script>

<script>
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
</script>

<script>
function search() {
  var search = $('#searchbar');
  search.val('');
  search.focus();
}
</script>
<?php if ($beskrivning != "" || $newsearch == "yes") { ?>
	<script type="text/javascript">
	  setInterval("my_function();",300000); 
	 
	<?php if (preg_match("/dos_product\.php/i", $_SERVER['PHP_SELF'])) { ?>
			function my_function(){
				window.location.replace("https://admin.cyberphoto.se/p");
			}
	<?php } elseif (preg_match("/dos_product\.php/i", $_SERVER['PHP_SELF'])) { ?>
			function my_function(){
				window.location.replace("https://admin.cyberphoto.se/o");
			}
	<?php } else { ?>
			function my_function(){
				window.location.replace("https://admin.cyberphoto.se/k");
			}
	<?php } ?>
	</script>
<?php } ?>
<script>
  Mousetrap.bind('esc', pageRedirect);
  Mousetrap.bind('ctrl+1', pageRedirectNewSearch);
  Mousetrap.bind('/', pageRedirectNewSearch);
  Mousetrap.bind('c m d', pageRedirectNewSearch);
  Mousetrap.bind('alt+1', pageRedirectActual);
  Mousetrap.bind('alt+2', pageRedirectDiscontinued);
<?php if (CCheckIP::checkIfLoginIsTradeIn($_SERVER['REMOTE_ADDR'])) { ?>
  Mousetrap.bind('alt+q', pageRedirectTradeIn);
<?php } ?>
  Mousetrap.bind('alt+k', pageRedirectCustomer);
  Mousetrap.bind('alt+p', pageRedirectProduct);
  Mousetrap.bind('alt+o', pageRedirectOrder);
</script>
<?php
	echo "</head>\n\n";
	if (preg_match("/pricelist\.php/i", $_SERVER['PHP_SELF']) && $addart == "yes") {
		echo "<body onLoad=sf()>\n\n";
	} elseif (preg_match("/accessories\.php/i", $_SERVER['PHP_SELF']) && $addart == "yes") {
		echo "<body onLoad=sf()>\n\n";
	} elseif (preg_match("/adtrigger\.php/i", $_SERVER['PHP_SELF']) && $addart == "yes") {
		echo "<body onLoad=sf()>\n\n";
	} elseif (preg_match("/lagervarde\.php/i", $_SERVER['PHP_SELF'])) {
		echo "<body onload=\"showStoreValue()\">\n\n";
	} elseif (preg_match("/searchlogg\.php/i", $_SERVER['PHP_SELF'])) {
		echo "<body onload=\"showSearch();\">\n\n";
	} elseif (preg_match("/index\.php/i", $_SERVER['PHP_SELF'])) {
		echo "<body onload=\"showValues();\">\n\n";
	} else {
		echo "<body>\n\n";
	}
	
	if ($emptysearch && !$clearsearch) { // visa matrix
?>
        <canvas id="c"></canvas>
        
        <script>
        // geting canvas by id c
        var c = document.getElementById("c");
        var ctx = c.getContext("2d");

        //making the canvas full screen
        c.height = window.innerHeight;
        c.width = window.innerWidth;

        //chinese characters - taken from the unicode charset
        var matrix = "ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789@#$%^&*()*&^%";
        //converting the string into an array of single characters
        matrix = matrix.split("");

        var font_size = 10;
        var columns = c.width/font_size; //number of columns for the rain
        //an array of drops - one per column
        var drops = [];
        //x below is the x coordinate
        //1 = y co-ordinate of the drop(same for every drop initially)
        for(var x = 0; x < columns; x++)
            drops[x] = 1; 

        //drawing the characters
        function draw()
        {
            //Black BG for the canvas
            //translucent BG to show trail
            ctx.fillStyle = "rgba(0, 0, 0, 0.04)";
            ctx.fillRect(0, 0, c.width, c.height);

            ctx.fillStyle = "#0F0"; //green text
            ctx.font = font_size + "px arial";
            //looping over drops
            for(var i = 0; i < drops.length; i++)
            {
                //a random chinese character to print
                var text = matrix[Math.floor(Math.random()*matrix.length)];
                //x = i*font_size, y = value of drops[i]*font_size
                ctx.fillText(text, i*font_size, drops[i]*font_size);

                //sending the drop back to the top randomly after it has crossed the screen
                //adding a randomness to the reset to make the drops scattered on the Y axis
                if(drops[i]*font_size > c.height && Math.random() > 0.975)
                    drops[i] = 0;

                //incrementing Y coordinate
                drops[i]++;
            }
        }

        setInterval(draw, 35);

        
        </script>

<?php

	}

	echo "<div id=\"content\">\n";

?>