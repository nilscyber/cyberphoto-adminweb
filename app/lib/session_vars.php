<?php
// den här ska inte användas
/**
function session_register2($name){

    global $$name;
    $_SESSION[$name] = $$name;
    $$name = &$_SESSION[$name]; 
}
function fix_session_register(){ 
    function session_register(){ 
        $args = func_get_args(); 
        foreach ($args as $key){ 
            $_SESSION[$key]=$GLOBALS[$key]; 
        } 
    } 
    function session_is_registered($key){ 
        return isset($_SESSION[$key]); 
    } 
    function session_unregister($key){ 
        unset($_SESSION[$key]); 
    } 
} 
if (!function_exists('session_register')) fix_session_register();

// registrera först de gamla variablerna så att vi kan...
session_register("kundnrladdaom", "ordernrladdaom");
// ... förstöra dom: 
unset ($kundnrladdaom);
unset ($ordernrladdaom);


session_register("kundnrsave", "confirm", "old_namn", "old_co", "old_adress", "old_postnr", "old_postnr", "old_postadr", "old_land_id", "old_email", "old_telnr", "old_orgnr",
"old_lnamn", "old_lco", "old_ladress", "old_lpostnr", "old_lpostadr", "old_lland_id", "old_ltelnr", "old_lemail", 
"old_levadress", "old_faktadress", "old_land", "old_land_fi", "old_lland", "old_lland_fi", "old_faktura", "order_erref", "order_erordernr", "order_kommentar", 
"paketref", "betalsatt", "spara_uppgifter", "old_faktlev", "intern", "old_foretag", "old_userName", "SesCc", "SesExpM", "SesExpY", "SesCcCode", "kortfax", 
"old_nyhetsbrev", "CcName", "old_avtalskund", "old_mobilnr", "old_sms", "old_nyhetsbreverbjudande", "old_forsakringserbjudande", "senasteLevsatt", "senasteBetalsatt", "pay", "freight", 
"pallDelivery", "discountCode", "discountCodeStatus", "campaignNr", "campaignQuestionResult", "basketValue", "old_villkor_id", "old_forsakring_new", "old_choose_villkor", 
"old_firstName", "old_lastName", "old_firstNameDel", "old_lastNameDel", "old_email_test", "old_personnr", "old_salary", "old_avisera", "old_splitOrder", 
"articles", "old_abbtype", "old_abbnumber", "old_simnumber", "old_preskort", "old_preskortdel1", "old_preskortdel2", "old_preskortdel3", "old_preskortdel4", "old_presenkort", 
"old_giftcardrebate", "old_operator", "old_one_stop", "old_abbpersonnumber", "old_invoice_address", "old_delivery_address", "order_check", "old_swishnumber");
*/
?>