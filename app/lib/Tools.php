<?php

/**
 * Description of Tools
 *
 * @author nils
 */
class Tools {
    
    
    
    public static function session_reset($excludeLogin = true) {
        $excludedVars = array(               
                                "kundnrsave", "confirm"                                        
                                );  
        foreach($_SESSION as $fieldname => $fieldvalue) {
            if (!$excludeLogin || ( !in_array($excludedVars, $_SESSION) && !substr($fieldname, 0, 4) == "old_" ))
                unset ($_SESSION[$fieldname]);
        }        
    }
    public static function session_reset2() {

        $sessionvars = array(  "CKreditor", "PayPal", "dibs", "pay", "freight"
                                );          
        foreach($_SESSION as $fieldname => $fieldvalue) {
            if (in_array($fieldname, $sessionvars))
                unset ($_SESSION[$fieldname]);
        }        
    }
    public static function print_rw($string, $return = false) {
        $string = print_r($string, true);
        $string = preg_replace("/\n/", "<br>\n", $string);
        $string = preg_replace("/\t/", "&nbsp;&nbsp;&nbsp;&nbsp;", $string);
        $string = preg_replace("/\s/", "&nbsp;&nbsp;&nbsp;&nbsp;", $string);    
        if ($return)
            return $string;
        else
            echo $string;
    }
    public static function sql_inject_clean($str) {
        $str = str_ireplace("union", "", $str);
        $str = str_ireplace(";", "",  $str);
        $str = str_ireplace("--", "",  $str);
            $str = str_ireplace("\'", "", $str);
        $str = str_ireplace("'", "", $str);
        $str = str_ireplace(" select ", "", $str);
        $str = str_ireplace(" drop ", "", $str);
        $str = str_ireplace(" update ", "", $str);
        $str = str_ireplace("/*", "", $str);
            // allt nedan är antagligen överkurs - NK
        $str = str_ireplace(" HAVING ", "", $str);
        $str = str_ireplace(" CAST ", "", $str);
        $str = str_ireplace(" CONVERT ", "", $str);
        $str = str_ireplace(" INSERT ", "", $str);
        $str = str_ireplace(" WHERE ", "", $str);
        $str = str_ireplace(" CREATE ", "", $str);
        $str = str_ireplace(" PROCEDURE ", "", $str);
        $str = str_ireplace(" EXEC ", "", $str);
        $str = str_ireplace("_cmd", "", $str);
        $str = str_ireplace("script", "", $str);
        return $str;
    }
    public static function sql_inject_clean_light($str) {
        $str = str_ireplace("union", "", $str);
        $str = str_ireplace(";", "",  $str);
        $str = str_ireplace("--", "",  $str);
        //$str = str_ireplace("\'", "", $str);
            $str = str_ireplace("'", "", $str);	
        $str = str_ireplace(" select ", "", $str);
        $str = str_ireplace(" drop ", "", $str);
        $str = str_ireplace(" update ", "", $str);
        $str = str_ireplace("/*", "", $str);

        return $str;
    }
    
    public static function replace_special_char($string) {
    	$from = array("å", "ä", "ö", "Å", "Ä", "Ö", ".", "-", "?", " ", "ø", "(", ")", "!", "%", "+", "&", ":", "/", "<", ">", "=", ",");
    	$to = array("a", "a", "o", "A", "A", "O", "-", "-", "-", "-", "o", "", "", "", "procent", "plus", "och", "-", "-", "-", "-", "-", "-");
    	$newstring = str_replace($from, $to, $string);
    	$newstring = preg_replace("/---/", "-", $newstring);
    	$newstring = preg_replace("/--/", "-", $newstring);
    	return $newstring;
    }
    
}

?>
