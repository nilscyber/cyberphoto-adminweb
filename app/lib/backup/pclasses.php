<?php
function correct_currency($pclass, $curr){
switch($pclass){
case 104:
return (0 == $curr);
case 103:
return (0 == $curr);
case 107:
return (0 == $curr);
case 106:
return (0 == $curr);
case 105:
return (0 == $curr);
}
}

function get_months($pclass){
switch($pclass){
case 104:
return 36;
case 103:
return 24;
case 107:
return 12;
case 106:
return 6;
case 105:
return 3;
}
}

function get_month_fee($pclass){
switch($pclass){
case 104:
return 2900;
case 103:
return 2900;
case 107:
return 2900;
case 106:
return 2900;
case 105:
return 2900;
}
}

function get_start_fee($pclass){
switch($pclass){
case 104:
return 29500;
case 103:
return 29500;
case 107:
return 29500;
case 106:
return 29500;
case 105:
return 9500;
}
}

function get_rate($pclass){
switch($pclass){
case 104:
return 995;
case 103:
return 995;
case 107:
return 0;
case 106:
return 0;
case 105:
return 0;
}
}

?>