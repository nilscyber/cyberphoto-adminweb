function winPopupCenter(hojd, bredd, loc)
{

    var x, y;		   
 
    y = (screen.height / 2) - (hojd / 2);
    x = (screen.width / 2) - (bredd / 2);
    
    
    var features = "width=" + bredd + ",height=" + hojd + ",top=" + y + ",left=" + x + ",scrollbars=yes" + ",toolbar=no";
    var w = window.open(loc, "smallshopwin", features);
    w.focus();

}

function winPop(hojd, bredd, x, y, loc)
{
       
    var features = "width=" + bredd + ",height=" + hojd + ",top=" + y + ",left=" + x + ",scrollbars=yes" + ",toolbar=no";
    var w = window.open(loc, "smallshopwin", features);
    w.focus();

}