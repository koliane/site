<?php
function writeLog( $strError="", $priority = false ){
        global $siteDirOnPC;
        global $phpLogLevel;

        /**Корректировка $phpLogLevel, если указана не верно*/
        if( !isset($phpLogLevel) )
            $phpLogLevel = 0;

        if( $phpLogLevel < 0)
            $phpLogLevel = 0;
        elseif($phpLogLevel > 4)
            $phpLogLevel = 4;

        if( !($phpLogLevel >= 0 && $phpLogLevel <= 4) )
            $phpLogLevel = 0;

        if( !$phpLogLevel )
            return false;
        /**********/

        $arrDebug = debug_backtrace();
        if(!empty( $arrDebug )) {
            $line = $arrDebug[0]['line'];
            $file = $arrDebug[0]['file'];
            if (isset($arrDebug[1]['function']))
                $func = $arrDebug[1]['function'];
            else
                $func = "";
        }

        $date = date('Y-M-d');
        $time = date('H:i:s');
        $microDate = microtime();
        $arrMicroSec = explode(' ', $microDate);
        $microSec = (int)($arrMicroSec[0] * 1000);
        if( $microSec < 100 && $microSec >= 10 ) $microSec *= 10;
        elseif( $microSec < 10) $microSec *= 100;

        if( $phpLogLevel === 1 && $priority === true || $phpLogLevel >= 2 ) {
            if ($func && !empty($arrDebug))
                $str = 'PHP  ' . $date . ' ' . $time . ':' . $microSec . ' ' . " Error __{$strError}__ in  file = " . $file . ';  func = ' . $func . ';  line = ' . $line . ';  REMOTE_ADDR = ' . $_SERVER['REMOTE_ADDR'] . ';  SERVER_PROTOCOL = ' . $_SERVER['SERVER_PROTOCOL'] . ';  REQUEST_METHOD = ' . $_SERVER['REQUEST_METHOD'] . "\n";
            elseif (!empty($arrDebug))
                $str = 'PHP  ' . $date . ' ' . $time . ':' . $microSec . ' ' . " Error __{$strError}__ in  file = " . $file . ';  line = ' . $line . ';  REMOTE_ADDR = ' . $_SERVER['REMOTE_ADDR'] . ';  SERVER_PROTOCOL = ' . $_SERVER['SERVER_PROTOCOL'] . ';  REQUEST_METHOD = ' . $_SERVER['REQUEST_METHOD'] . "\n";
            else
                $str = 'PHP  ' . $date . ' ' . $time . ':' . $microSec . ' ' . " Error __{$strError}__" . ';  REMOTE_ADDR = ' . $_SERVER['REMOTE_ADDR'] . ';  SERVER_PROTOCOL = ' . $_SERVER['SERVER_PROTOCOL'] . ';  REQUEST_METHOD = ' . $_SERVER['REQUEST_METHOD'] . "\n";
        }
        if( $phpLogLevel === 3 && $priority === true || $phpLogLevel >= 4)
            $str .= convertToString($arrDebug);

        if( isset($str) ) {
            file_put_contents($siteDirOnPC . '/error_log', $str, FILE_APPEND);
            return $str;
        }
        return false;
}

/**Преобразовать двумерный массив в строки*/
function convertToString( $arr ){
    if( empty( $arr )) {
        return "";
    }
    $strText="";
    $tab = "\t";
    foreach ( $arr as $elem ){
        $strLine=$tab;
        foreach ( $elem as $key=>$item ){
            if( is_array($item)) {
                $item = print_r($item, true);
                $item = preg_replace("/[\t\n\s]+/", ' ', $item);
            }
            $strLine .= '['.$key.'] => '. $item.';  ';
        }
        $strText .= $strLine."\n";
    }
    return $strText;
}
 ?>