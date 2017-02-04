<?php

/**Получить необходимый список котировок*/
/**
 *  Функция возвращает необходимый список данных из базы данных (всегда по возрастанию от меньшего id к большему)
 *
 *  @param string $tableName Имя таблицы, откуда берутся данные
 *  @param string $timeFrame Аббревиатура таймфрейма, для которого берутся данные
 *  @param string или array $startTime Время, начиная с которого выбираются данные. Не менее 5 значений в массиве(или строке).
 *                                     Если входные данные пустая строка или пустой массив, то выводятся последние актуальные данные ($endTimeOrCount штук)
 *  @param mixed $endTimeOrCount Если тип integer, то выбираются котировки в количестве равном $endTimeOrCoun
 *                               Если тип array, то выбираются котировки до даты $endTimeOrCoun включительно
 *                               Если тип boolean и значение true, то выбираются котировки до самой актуальной включительно
 * @param string $namesOutputColumn Содержит поля, необходимые для получения
 * @param integer $shiftDecimalPlaces Количество разрядов, на которое нужно сдвинуть число (эквивалентно 10^n, где n и есть $shiftDecimalPlaces).
 *                                    Если стоит значение по умолчанию (""), то значение берется из базы данных.
 *
 * @return array список требуемых данных или false в случае ошибки
 */
function getDataFromDB($tableName, $timeFrame, $startTime, $endTimeOrCount = 100, $shiftDecimalPlaces="", $namesOutputColumn = 'YEAR,MONTH,DAY,HOUR,MINUTE,OPEN,HIGH,LOW,CLOSE,VOLUME') {
    global $pdo;
    $namesOutputColumn = strtoupper($namesOutputColumn);
    $arrDefaultTime=['YEAR','MONTH','DAY','HOUR','MINUTE'];
    $newNamesOutputColumn = $namesOutputColumn;


    if( !is_string( $tableName ) || !is_string( $timeFrame ) || !is_string( $namesOutputColumn ) || !$endTimeOrCount || !isset($pdo) || $startTime === "" && $endTimeOrCount < 1  || is_string( $shiftDecimalPlaces ) && $shiftDecimalPlaces !== '' || is_string( $namesOutputColumn ) && !$namesOutputColumn ||
        is_string($startTime) && preg_match( "/[\d\s,]*/", $startTime, $res ) && strcmp( $startTime, $res[0]) !== 0 || is_string($startTime) && !preg_match( "/[\d\s,]*/", $startTime, $res) || preg_match( "/[\w\s,]*/", $namesOutputColumn, $res ) && strcmp( $namesOutputColumn, $res[0]) !== 0 || !preg_match( "/[\w\s,]*/", $namesOutputColumn, $res) ) {
        writeLog('Не верный формат параметров функции');
        return false;
    }

    /**Если $startTime и $endTimeOrCount строковые переменные, то преобразуем их в массив*/
    if( is_string($startTime) ) {
        if( $startTime === "")
            $startTime = array();
        else
            $startTime = explode(',', $startTime);
    }

    if( is_string($endTimeOrCount) )
        $endTimeOrCount = explode(',',$endTimeOrCount);

    $startTime = array_map('intval', $startTime);
    if( is_array($endTimeOrCount))
        $endTimeOrCount = array_map('intval', $endTimeOrCount);

    if( is_array($endTimeOrCount) && count($endTimeOrCount) < 5 || !is_array($startTime) || count($startTime) > 0 && count($startTime) < 5 || count($startTime) === 0 && !is_int($endTimeOrCount) || !is_array($endTimeOrCount) && !is_string($endTimeOrCount) && !is_int($endTimeOrCount)) {
        writeLog('Неверный формат либо $startTime, либо $endTimeOrCount');
        return false;
    }

    if( stripos($namesOutputColumn, 'open') !== false || stripos($namesOutputColumn, 'high') !== false || stripos($namesOutputColumn, 'low') !== false || stripos($namesOutputColumn, 'close') !== false ) {
        /**Вычисляем $shiftDecimalPlaces - количество разрядов для сдвига (эквивалентно 10^n, где n и есть $numShift)*/
        $strShiftQuery = "select DECIMAL_PLACES from `decimal_places` where TABLE_NAME = \"{$tableName}\" ";
        if( !($resObj = $pdo->query( $strShiftQuery )) ) {
            writeLog('Ошибка в запросе к базе данных');
            return false;
        }
        $shiftDecimalPlacesDefault = (int)($resObj->fetch()[0]);

        if($shiftDecimalPlaces !== "")
            $shiftDecimalPlaces = (int)$shiftDecimalPlaces;
        else{
            $shiftDecimalPlaces = $shiftDecimalPlacesDefault;
        }
        if($shiftDecimalPlaces < 0){
            writeLog('Сдвигать точку в котировках float нельзя в левом направлении');
            return false;
        }

        /**Получаем новую строку с выходными полями, где для цен добавлены псевдонимы (N1, N2, N3, N4)**/
        $arrSearch=array( 'OPEN','HIGH','LOW','CLOSE' );
        $arrReplace=array( 'N1','N2','N3','N4' );

        $newNamesOutputColumn = str_ireplace( $arrSearch, $arrReplace, $namesOutputColumn);
        /**Преобразуем строку $namesOutputColumn в строку, учитывающую $shiftDecimalPlaces*/
        $arrOutputColumn = explode(',', $namesOutputColumn);
        $arrOutputColumn = array_map('trim',$arrOutputColumn);
        foreach ($arrOutputColumn as &$arr) {
            if (stripos($arr, 'open') !== false || stripos($arr, 'high') !== false || stripos($arr, 'low') !== false || stripos($arr, 'close') !== false) {
                if(($i = array_search($arr,$arrSearch)) !== false){
                    $i++;
                    $placesAfterPoint = $shiftDecimalPlacesDefault - $shiftDecimalPlaces;
                    $arr = 'ROUND(' . $arr . "*POW(10,{$shiftDecimalPlaces})" . ',' . $placesAfterPoint . ") as N{$i}";
                }
            }
        }
        $namesOutputColumn = implode(',',$arrOutputColumn);

        /**Если в выходном списке нет какой-либо даты(времени), то добавляем в $namesOutputColumn (необходимы для внутренних выборок и генерации COMBO_ID)*/
        foreach($arrDefaultTime as $elemTime){
            if( stripos($namesOutputColumn,$elemTime) === false)
                $namesOutputColumn = $elemTime.','.$namesOutputColumn;
        }
    }
    /***********************************************************************************/

    if( count( $startTime ) > 0 ) {
        /**Вычисляем $startID*/
        $arrTime = array_values( $startTime );
        for( $i = 0, $n = 5, $startID=""; $i < $n - 1; $i++ ){
            if( $arrTime[$i+1]<10)
                $arrTime[$i] *= 10;
            $startID = $startID.$arrTime[$i];
            if( $i === $n-2)
                $startID = $startID.$arrTime[$i+1];
        }
    }

    /**Формирование запроса, для вывода последних актуальных данных. */
    if(count( $startTime ) === 0){
        $strInnerSelect = "(SELECT {$namesOutputColumn},concat( if(month<10,year*10,year), if(day<10,month*10,month), if(hour<10,day*10,day), if(minute<10,hour*10,hour), minute ) AS COMBO_ID FROM {$tableName} where TIMEFRAME=\"{$timeFrame}\" ORDER BY COMBO_ID DESC LIMIT {$endTimeOrCount}) as INNER_TABLE ";
        $strQuery = "SELECT {$newNamesOutputColumn} from (SELECT * FROM ".$strInnerSelect.' order by COMBO_ID ASC ) as INNER_TABLE2';
        echo $strQuery;
    }else
    /**Формирование строки запроса исходя из типа $endTimeOrCount*/
    if ( is_array( $endTimeOrCount ) ) {
        /**Вычисляем $finalID*/
        $arrTime = array_values( $endTimeOrCount );
        for( $i = 0, $n = 5, $finalID=""; $i < $n - 1; $i++ ){
            if( $arrTime[$i+1]<10)
                $arrTime[$i] *= 10;
            $finalID = $finalID.$arrTime[$i];
            if( $i === $n-2)
                $finalID = $finalID.$arrTime[$i+1];
        }
        /***********************/
        if( $startID > $finalID ) {
            $temp = $startID;
            $startID = $finalID;
            $finalID = $temp;
        }
        $strInnerSelect = "(SELECT {$namesOutputColumn},concat( if(month<10,year*10,year), if(day<10,month*10,month), if(hour<10,day*10,day), if(minute<10,hour*10,hour), minute ) AS COMBO_ID FROM {$tableName} where TIMEFRAME=\"{$timeFrame}\"  ORDER BY COMBO_ID asc) as INNER_TABLE ";
        $strQuery = "SELECT {$newNamesOutputColumn} from ".$strInnerSelect." WHERE COMBO_ID >= {$startID} AND COMBO_ID <= {$finalID}";
    } elseif( is_bool( $endTimeOrCount ) ) {
        $strInnerSelect = "(SELECT {$namesOutputColumn},concat( if(month<10,year*10,year), if(day<10,month*10,month), if(hour<10,day*10,day), if(minute<10,hour*10,hour), minute ) AS COMBO_ID FROM {$tableName} where TIMEFRAME=\"{$timeFrame}\"  ORDER BY COMBO_ID asc) as INNER_TABLE ";
        $strQuery = "SELECT {$newNamesOutputColumn} from ".$strInnerSelect." WHERE COMBO_ID >= {$startID} ";
    } elseif( is_int( $endTimeOrCount ) ) {
        $absCount = abs($endTimeOrCount);
        if( $endTimeOrCount > 0 ) {
            $strInnerSelect = "(SELECT {$namesOutputColumn},concat( if(month<10,year*10,year), if(day<10,month*10,month), if(hour<10,day*10,day), if(minute<10,hour*10,hour), minute ) AS COMBO_ID FROM {$tableName} where TIMEFRAME=\"{$timeFrame}\"  ORDER BY COMBO_ID ASC) as INNER_TABLE ";
            $strQuery = "SELECT {$newNamesOutputColumn} from " . $strInnerSelect . " WHERE COMBO_ID >= {$startID} "." LIMIT {$absCount}";
        }elseif( $endTimeOrCount < 0 ) {
            $strInnerSelect = "(SELECT {$namesOutputColumn},concat( if(month<10,year*10,year), if(day<10,month*10,month), if(hour<10,day*10,day), if(minute<10,hour*10,hour), minute ) AS COMBO_ID FROM {$tableName} where TIMEFRAME=\"{$timeFrame}\"  ORDER BY COMBO_ID DESC ) as INNER_TABLE ";
            $strInnerSelect2 = "(SELECT * from " . $strInnerSelect . " WHERE COMBO_ID <= {$startID}  LIMIT {$absCount}) AS QUERY_DESC";
            $strInnerSelect3 = "(SELECT * from " . $strInnerSelect2 . " ORDER BY COMBO_ID) AS QUERY_ASC";
            $strQuery = "SELECT {$newNamesOutputColumn} FROM ".$strInnerSelect3;
        }
    } else {
        writeLog();
        return false;
    }
/**************************************************************************/

    $resObj = $pdo->query( $strQuery );
    if( !$resObj ) {
        writeLog('Ошибка в запросе к базе данных');
        return false;
    }

    $arrTerms = $resObj->fetchAll( PDO::FETCH_NUM );

    if( empty($arrTerms) ){
        writeLog('Массив полученных данных ПУСТ');
        return false;
    }
    return $arrTerms;
}

?>