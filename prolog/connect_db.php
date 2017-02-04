<?php  
#Подключение к БД
$host = 'localhost';
$dbname = 'koliane';
$login = 'root';
$password = 'oijp7a5z';
function connectToDB()
{
    global $host,$dbname,$login,$password;
    try {
        $pdo = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $login, $password, array(PDO::ATTR_PERSISTENT => true));
    } catch (PDOException $e) {
        writeLog();
        echo 'Невозможно установить соединение с базой данных. $e->getMessage(): ' . $e->getMessage();
    }
    return $pdo;
}
?>