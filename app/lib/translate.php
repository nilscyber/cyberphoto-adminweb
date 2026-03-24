<?php

/**
 * Include this file in the beginning of php file to add translation possibilities
 */
// the translation class

require_once("Locs.php");

// set language the "old" way
if (isset($fi) || isset($sv) || isset($no)) {
    Locs::setOldWay();
}
// check if current locale is set and supported, 
// if not check cookie for preferred language, 
// if it doesn't exist fall back to default locale
else if (!Locs::is_locale_supported($_SESSION['currentLocale'])) {

    if ($_COOKIE['preferredLang'] != "" ) {
        Locs::switchLang($_COOKIE['preferredLang']);
    } else {
        Locs::setDefaultLocale();
    }
} else {
    // set old variables if currentLocale is already set
    Locs::setOldVars();
}
/**
 * Function that do the actual translation
 * The function uses the class "Locs.php";
 * Translate string into current locale. If locale is not set default will be used
 * @param type $string
 * @return type
 */
function l($string) {
    return Locs::translate($string, null);
}

?>
