<?php 
/**Пример вызова
* $fileName = 'C:/Apache24/htdocs/trade/pair/EURUSD/h1/EURUSD60_0.txt';
* $tableName = 'eurusd';
* addTxtToDb( $fileName, $pdo, $tableName )
*/

#Построчное чтение файла .txt с котировками - преобразование строки для добавления в БД - добавление в БД
	function addTxtToDb( $fileName, $pdo, $tableName ) {
			$file = fopen( $fileName, 'r') or die('Ошибка открытия файла');
			while(!feof($file)) {
				$str = fgets( $file );
				$strCol = convertColDbToStr( $pdo, $tableName );
				$strValues = convertStringToRowDb( $str );
				
				
				file_put_contents('pair/EURUSD/mn/EURUSD_mn_0_toDB.csv',$strValues, FILE_APPEND );

				// $strQuery = "INSERT INTO $tableName ({$strCol}) VALUES ({$strValues})";
				// $pdo->query($strQuery);
			}
	}
#Функция заменяет в строке точки у даты и двоеточие у времени на запятые
	function convertStringToRowDb( $str ){
		$res = preg_replace('/[\.:]/',",",substr($str,0,16));
	  $res = substr_replace($str,$res,0,16);
	  // $res = preg_replace('/\./','',$res);
	  return $res;
	}
#Ф-ия выдает в строке список полей таблицы в БД
	function convertColDbToStr( $pdo, $tableName ) {
		$strQuery = "SHOW COLUMNS FROM $tableName";
		try {
			$resObj = $pdo->query( $strQuery );
			$data = $resObj->fetchAll( PDO::FETCH_ASSOC );
			$str = implode( ',', array_slice( array_column( $data, 'Field'), 1 ) );
		}catch(PDOException $e) {
			echo "Exception: Ошибка выполнения запроса. func: add_txt_to_db.php - convertColDbToStr( $pdo, $tableName )".$e->errorInfo();
			return false;
		}
		return $str;
	}
	
// $tableName = 'eurusd';
// $fileName = 'C:/Apache24/htdocs/trade/pair/EURUSD/mn/EURUSD43200.csv';
// addTxtToDb( $fileName, $pdo, $tableName );
	
	
 ?>