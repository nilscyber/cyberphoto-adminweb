<?php

/**
 * Class to handle translations of php-files
 * @version 1.0 RC1
 *
 * @author Nils Kohlström
 */
class Locs {

    public static $defaultLocale = "sv_SE";
    public static $supportedLocales = array('sv_SE', 'sv', 'sv_FI', 'fi_FI', 'fi', 'no', 'no_NO');
    public static $defaultTranslationFile = '/home/www/locales/translations.bin';
    public static $translationArray = array();
    public static $defaultDelimiter = ";";

    const COUNTRY_FI = "FI";
    const COUNTRY_NO = "NO";
    const COUNTRY_SE = "SE";
    const COUNTRY_DK = "DK";

    public static function setLocale($locale = null) {
        
        if (!isset($locale)) {
            // set to default for domain
            if (preg_match("/\.se$/", $_SERVER["HTTP_HOST"])) {
                $locale = "sv_SE";
            } else {
                $locale = substr($_SERVER["HTTP_HOST"], -2, 2) . "_" . strtoupper(substr($_SERVER["HTTP_HOST"], -2, 2));
            }
        }
        // reset translation array if locale is changed
        if ($_SESSION['currentLocale'] != $locale) {
            Locs::$translationArray = array();
        }
        $_SESSION['currentLocale'] = $locale;
        //setlocale(LC_ALL, $locale);
        //putenv("LC_ALL=" . $locale);
        // for backward compatibility 
        self::setOldVars();
    }

    /**
     * Set default language using browser settings     
     */
    public static function setDefaultLocale() {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            // break up string into pieces (languages and q factors)
            preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse);
            if (count($lang_parse[1])) {
                // create a list like "en" => 0.8
                $langs = array_combine($lang_parse[1], $lang_parse[4]);
                // set default to 1 for any without q factor
                foreach ($langs as $lang => $val) {
                    if ($val === '')
                        $langs[$lang] = 1;
                }
                // sort list based on value	
                arsort($langs, SORT_NUMERIC);
                // look through sorted list and use first one that matches our languages
                foreach ($langs as $lang => $val) {
                    // replace - with _ i.e. standardize loc
                    $lang = str_replace("-", "_", $lang);
                    $lang = substr($lang, 0, 2);
                    // set country from host i.e. .no .se .fi
                    $lang = $lang . "_" . strtoupper(substr($_SERVER["HTTP_HOST"], -2, 2));
                    if (self::is_locale_supported($lang)) {
                        self::setLocale($lang);
                        return;
                    }
                }
                // lang nog found, set default
                self::setLocale();
            }
        } else {
            // no languages from browser, set default
            self::setLocale();
        }
    }

    public static function is_locale_supported($locale) {

        if ($locale == "") {

            return false;
        }
        // check if domain matches locale first //TODO: this needs to be more sophisticated        
        if (substr($locale, 3, 2) != strtoupper(substr($_SERVER["HTTP_HOST"], -2, 2))) {
            return false;
        }
        foreach (self::$supportedLocales as $loc) {
            if ($loc == $locale) {
                return true;
            }
        }
        return false;
    }

    /**
     * Switch language but keeping country. Only used for Finland (and Aaland)
     * TODO: handle if we end up with non supported locale
     * @param string $lang language to switch go e.g. "sv" for Swedish
     */
    public static function switchLang($lang) {

        // remove potential " and stuff from $lang;
        $lang = stripslashes($lang);
        $lang = str_replace('"', '', $lang);
        $lang = str_replace("'", "", $lang);
        if (strlen($lang) != 2)
            return false;

        $lang = strtolower($lang);

        // set default locale if not set for current domain
        if (!isset($_SESSION['currentLocale']))
            self::setDefaultLocale();

        // try to set the lang (will not be set if it's not supported)
        self::setLocale($lang . substr($_SESSION['currentLocale'], -3, 3));
        // if it was possible to set save preferred lang in a cookie (valid one year)
        if ($lang == self::getLang()) {
            setcookie("preferredLang", $lang, time() + 60 * 60 * 24 * 365, "/", self::getDomainName());
            //setcookie ("kundvagn", "", time() - 3600, "/", ".cyberphoto.se");
        }
    }

    /**
     * Set locale using "old" variables
     * @global type $fi
     * @global type $sv
     * @global type $no
     */
    public static function setOldWay() {
        global $fi, $sv, $no;

        if ($fi && !$sv) {
            self::setLocale("fi_FI");
        } elseif ($fi && $sv) {

            self::setLocale("sv_FI");
        } elseif ($no) {
            self::setLocale("no_NO");
        } else {
            self::setLocale("sv_SE");
        }
    }

    /**
     * Set "old" vars. Used after setting e.g. default locale for backward compability
     * @global type $fi
     * @global type $sv
     * @global type $no
     */
    public static function setOldVars() {
        global $fi, $sv, $no;
        $fi = $sv = $no = false;
        if (!$_SESSION['currentLocale'])
            self::setDefaultLocale();
        $lang = substr($_SESSION['currentLocale'], 0, 2);
        $country = substr($_SESSION['currentLocale'], -2, 2);
        if ($lang == "fi" || $country == "FI") {
            $fi = true;
        } if ($lang == "sv") {
            $sv = true;
        } else if ($lang == "no") {
            $no = true;
        }
    }

    /**
     * Reads file into formatted array
     * Note! If enoding is different than UTF-8, then locale needs to be set with encoding before this function 
     * is run, e.g. "setlocale(LC_all, "sv_SE.ISO-8859-1");"
     * @param type $filename
     * @param type $delimiter
     * @return boolean
     */
    static function csv_to_array($filename = '', $delimiter = ',') {
        if (!file_exists($filename) || !is_readable($filename))
            return FALSE;

        $header = NULL;
        $data = array();

        if (($handle = fopen($filename, 'r')) !== FALSE) {

            $data = array();
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                if (!$header) {
                    $header = $row;
                } else {
                    $first = true;
                    $id = null;
                    $i = 0;
                    foreach ($header as $k => $h) {
                        if ($first) {
                            $id = $row[$k];
                        } else {
                            $data[$id][$h] = $row[$k];
                        }
                        $first = false;
                    }
                }
            }
            fclose($handle);
        }
        return $data;
    }

    public static function serialize_csv_to_array($csv_file, $delimiter, $serialized_file) {
        if ($delimiter == null)
            $delimiter = self::$defaultDelimiter;

        $data = self::csv_to_array($csv_file, $delimiter);
        return $data;
    }

    public static function serialize_csv($csv_file, $delimiter, $serialized_file) {
        if ($serialized_file == null)
            $serialized_file = self::$defaultTranslationFile;
        $array = self::serialize_csv_to_array($csv_file, $delimiter, $serialized_file);
        file_put_contents($serialized_file, gzcompress(serialize($array)));
    }

    /**
     * Function that do the actual translation
     * The function uses the class "Locs.php";
     * Translate string into current locale. If locale is not set default will be used
     * @param type $string string to be translated
     * @param type $string translation file. If null then default is used
     * @return type
     */
    public static function translate($string, $translationFile) {
        if (!isset($_SESSION['currentLocale']))
            self::setDefaultLocale();
        if (!isset($_SESSION['currentLocale']))
            self::setOldWay();

        if (!isset($translationFile))
            $translationFile = self::$defaultTranslationFile;

        // unserialize only first time
        if (count(self::$translationArray) == 0) {
            $array = unserialize(gzuncompress(file_get_contents($translationFile)));
            self::$translationArray = $array;
            // print_r($array);
            // echo $translationFile;
        } else {
            $array = self::$translationArray;
        }

        $res = $array[$string][$_SESSION['currentLocale']];

        // if no result try two letter version of language, i.e. language with no country specified
        if (strlen($res) == 0)
            $res = $array[$string][substr($_SESSION['currentLocale'], 0, 2)];
        // if no result use translation of default language
        if (strlen($res) == 0)
            $res = $array[$string][self::$defaultLocale];

        // if still no result return original string
        if (strlen($res) == 0)
            return $string;

        return $res;
    }

    /**
     * 
     * @return current lang as two letters lower case, e.g. "sv" (=swedish)
     */
    public static function getLang() {

        if (!isset($_SESSION['currentLocale']))
            self::setDefaultLocale();
        if (!isset($_SESSION['currentLocale']))
            self::setOldWay();
        if (!isset($_SESSION['currentLocale']))
            return null;

        return substr($_SESSION['currentLocale'], 0, 2);
    }

    public static function getCurrency() {
        if (!isset($_SESSION['currentLocale']))
            self::setDefaultLocale();
        if (!isset($_SESSION['currentLocale']))
            self::setOldWay();
        if (!isset($_SESSION['currentLocale']))
            return null;

        if (substr($_SESSION['currentLocale'], 3, 2) == "NO") {
            $currency = "NOK";
        } else if (substr($_SESSION['currentLocale'], 3, 2) == "FI") {
            $currency = "EUR";
        } else if (substr($_SESSION['currentLocale'], 3, 2) == "SE") {
            $currency = "SEK";
        } else {
            $currency = "SEK";
        }
        return $currency;
    }

    /**
     * 
     * @return Current country in upper case e.g. "SE"
     */
    public static function getCountry() {

        if (!isset($_SESSION['currentLocale'])) {

            self::setDefaultLocale();
        } if (!isset($_SESSION['currentLocale'])) {

            self::setOldWay();
        } if (!isset($_SESSION['currentLocale'])) {

            return null;
        }
        return substr($_SESSION['currentLocale'], 3, 2);
    }

    public static function l($string) {
        return self::translate($string, null);
    }

    public static function HTTP_HOST_From_Locale() {
        if (!isset($_SESSION['currentLocale'])) {
            self::setDefaultLocale();
        } if (!isset($_SESSION['currentLocale'])) {
            self::setOldWay();
        } if (!isset($_SESSION['currentLocale'])) {
            return null;
        }
        // return "www.cyberphoto." . strtolower(self::getCountry());
        return "www2.cyberphoto." . strtolower(self::getCountry());
    }

    public static function getDomainName() {
        $darray = explode('.', $_SERVER['HTTP_HOST']);
        $darray = array_reverse($darray);
        $domain = $darray[1] . "." . $darray[0];
        return $domain;
    }

}

?>
