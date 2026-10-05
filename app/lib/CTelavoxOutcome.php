<?php
/**
 * CTelavoxOutcome
 *
 * Central klassificering av Telavox avslutsstatus (outcome). Importen sparar
 * alltid Telavox ursprungliga kod; all tolkning av koderna görs här, så att
 * statistiken kan justeras utan att röra importerad data.
 *
 * Betydelserna kommer från filhuvudet i Telavox köexport.
 */
class CTelavoxOutcome
{
    const ANSWERED  = 'answered';   // besvarat, räknas i svarsfrekvensen
    const ABANDONED = 'abandoned';  // inringande lade på i kön
    const MISSED    = 'missed';     // vidarekopplat men ej besvarat
    const FORWARDED = 'forwarded';  // lämnade kön utan att vi vet utfallet
    const OTHER     = 'other';      // kod som saknas i mappningen nedan

    /**
     * queue_time: om fält 5 är verklig kötid för koden. För ROK/RNA anger
     * Telavox utringningstiden i stället, så de hålls utanför snittkötiden.
     */
    private static $codes = array(
        'OK'  => array('class' => self::ANSWERED,  'queue_time' => true,  'label' => 'Besvarat'),
        'TC'  => array('class' => self::ANSWERED,  'queue_time' => true,  'label' => 'Plockat ur kön av agent'),
        'TR'  => array('class' => self::ANSWERED,  'queue_time' => true,  'label' => 'Besvarat och vidarekopplat'),
        'ROK' => array('class' => self::ANSWERED,  'queue_time' => false, 'label' => 'Vidarekopplat, besvarat'),
        'RNA' => array('class' => self::MISSED,    'queue_time' => false, 'label' => 'Vidarekopplat, ej besvarat'),
        'AB'  => array('class' => self::ABANDONED, 'queue_time' => true,  'label' => 'Övergivet'),
        'KEY' => array('class' => self::FORWARDED, 'queue_time' => true,  'label' => 'Sifferval, kopplat vidare'),
        'TO'  => array('class' => self::FORWARDED, 'queue_time' => true,  'label' => 'Maximal väntetid, kopplat vidare'),
    );

    /** Rader som inte är samtal (agenters in-/utloggning). Hoppas över vid import. */
    private static $agentEvents = array('AGENTLOGIN', 'AGENTLOGOUT', 'LI', 'LO');

    private static $classLabels = array(
        self::ANSWERED  => 'Besvarade',
        self::ABANDONED => 'Övergivna',
        self::MISSED    => 'Ej besvarade (vidarekopplade)',
        self::FORWARDED => 'Vidarekopplade',
        self::OTHER     => 'Oklassificerade',
    );

    public static function isAgentEvent($code)
    {
        return in_array($code, self::$agentEvents, true);
    }

    public static function classOf($code)
    {
        return isset(self::$codes[$code]) ? self::$codes[$code]['class'] : self::OTHER;
    }

    public static function label($code)
    {
        return isset(self::$codes[$code]) ? self::$codes[$code]['label'] : 'Okänd kod';
    }

    public static function classLabel($class)
    {
        return isset(self::$classLabels[$class]) ? self::$classLabels[$class] : $class;
    }

    /** Alla koder som hör till en klass, t.ex. codesIn(self::ANSWERED). */
    public static function codesIn($class)
    {
        $out = array();
        foreach (self::$codes as $code => $def) {
            if ($def['class'] === $class) {
                $out[] = $code;
            }
        }
        return $out;
    }

    /** Koder där kötiden är verklig kötid (se $codes). */
    public static function queueTimeCodes()
    {
        $out = array();
        foreach (self::$codes as $code => $def) {
            if ($def['queue_time']) {
                $out[] = $code;
            }
        }
        return $out;
    }
}
