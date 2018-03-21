<?php

namespace Core\Utils;

use App;

class Utils{

    public function __construct() {}

    public static function XMLToArray( $file ){
        $content = @simplexml_load_file( $file );
        return $content;
    }

    public static function getOS() {
        $oses = array (
            'iPhone'            => '(iPhone)',
            'Windows 3.11'      => 'Win16',
            'Windows 95'        => '(Windows 95)|(Win95)|(Windows_95)',
            'Windows 98'        => '(Windows 98)|(Win98)',
            'Windows 2000'      => '(Windows NT 5.0)|(Windows 2000)',
            'Windows XP'        => '(Windows NT 5.1)|(Windows XP)',
            'Windows 2003'      => '(Windows NT 5.2)',
            'Windows Vista'     => '(Windows NT 6.0)|(Windows Vista)',
            'Windows 7'         => '(Windows NT 6.1)|(Windows 7)',
            'Windows NT 4.0'    => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
            'Windows ME'        => 'Windows ME',
            'Open BSD'          => 'OpenBSD',
            'Sun OS'            => 'SunOS',
            'Linux'             => '(Linux)|(X11)',
            'Mac OS'            => '(Mac_PowerPC)|(Macintosh)',
            'QNX'               => 'QNX',
            'BeOS'              => 'BeOS',
            'OS/2'              => 'OS/2',
            'Search Bot'        => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp/cat)|(msnbot)|(ia_archiver)'
        );

        foreach($oses as $os => $preg_pattern) {
            if ( preg_match('@' . $preg_pattern . '@', $_SERVER['HTTP_USER_AGENT']) ) {
                return $os;
            }
        }
        return 'n/a';
    }

    public static function setActivePageInMenu($routeName, $activePageClass = 'class="active"'){
        if(App::getCurrentRouteName() == $routeName){
            return $activePageClass;
        }
        return false;
    }

    public static function format_date($date, $format = 1){
        $jour_fr = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
        $mois_fr = Array("", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août",
            "Septembre", "Octobre", "Novembre", "Décembre");

        $date = date("w/d/n/Y/G/i", strtotime($date));
        list($nom_jour, $jour, $mois, $annee, $hours, $minutes) = explode('/', $date);

        if($format == 1){
            return $jour_fr[$nom_jour] . ' ' . $jour . ' ' . $mois_fr[$mois] . ' ' . $annee . ' à ' . $hours . 'h' . $minutes;
        }elseif($format == 2){
            return $jour . ' ' . $mois_fr[$mois] . ' ' . $annee;
        }elseif($format == 3){
            return $jour_fr[$nom_jour] . ' ' . $jour . ' ' . $mois_fr[$mois] . ' ' . $annee;
        }elseif($format == 4){
            return $annee;
        }else{
            return $date;
        }
    }

    public function getClassName($class){

    }

    public function getBundleName($class){

    }


}