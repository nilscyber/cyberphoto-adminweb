<?php
function correct_currency($pclass, $curr){
switch($pclass){
case 1583: // 15 månaders räntefri kampanj i sep 2010
return (0 == $curr);
case 1226: // 15 månaders räntefri kampanj i maj 2010
return (0 == $curr);
case 103:
return (0 == $curr);
case 104:
return (0 == $curr);
case 105:
return (0 == $curr);
case 106:
return (0 == $curr);
case 107:
return (0 == $curr);
case 264:
return (0 == $curr);
case 1584: // 15 månaders räntefri kampanj FI i sep 2010
return (2 == $curr);
case 1228: // 15 månaders räntefri kampanj FI i maj 2010
return (2 == $curr);
case 262:
return (2 == $curr);
case 261:
return (2 == $curr);
case 260:
return (2 == $curr);
case 259:
return (2 == $curr);
}
}

function get_months($pclass){
switch($pclass){
case 1583: // 15 månaders räntefri kampanj i sep 2010
return 15;
case 1226: // 15 månaders räntefri kampanj i maj 2010
return 15;
case 103:
return 24;
case 104:
return 36;
case 105:
return 3;
case 106:
return 6;
case 107:
return 12;
case 264:
return 12;
case 1584: // 15 månaders räntefri kampanj FI i sep 2010
return 15;
case 1228: // 15 månaders räntefri kampanj FI i maj 2010
return 15;
case 262:
return 12;
case 261:
return 36;
case 260:
return 12;
case 259:
return 6;
}
}

function get_month_fee($pclass){
switch($pclass){
case 1583: // 15 månaders räntefri kampanj i sep 2010
return 0;
case 1226: // 15 månaders räntefri kampanj i maj 2010
return 2900;
case 103:
return 2900;
case 104:
return 2900;
case 105:
return 2900;
case 106:
return 2900;
case 107:
return 2900;
case 264:
return 2900;
case 1584: // 15 månaders räntefri kampanj FI i sep 2010
return 0;
case 1228: // 15 månaders räntefri kampanj FI i maj 2010
return 395;
case 262:
return 395;
case 261:
return 395;
case 260:
return 395;
case 259:
return 395;
}
}

function get_start_fee($pclass){
switch($pclass){
case 1583: // 15 månaders räntefri kampanj i sep 2010
return 0;
case 1226: // 15 månaders räntefri kampanj i maj 2010
return 0;
case 103:
return 29500;
case 104:
return 29500;
case 105:
return 9500;
case 106:
return 29500;
case 107:
return 29500;
case 264:
return 0;
case 1584: // 15 månaders räntefri kampanj FI i sep 2010
return 0;
case 1228: // 15 månaders räntefri kampanj FI i maj 2010
return 0;
case 262:
return 0;
case 261:
return 2995;
case 260:
return 3995;
case 259:
return 2995;
}
}

function get_rate($pclass){
switch($pclass){
case 1226: // 15 månaders räntefri kampanj i maj 2010
return 0;
case 103:
return 995;
case 104:
return 995;
case 105:
return 0;
case 106:
return 0;
case 107:
return 0;
case 264:
return 0;
case 1228: // 15 månaders räntefri kampanj FI i maj 2010
return 0;
case 262:
return 0;
case 261:
return 1495;
case 260:
return 0;
case 259:
return 0;
}
}

?>