<?php
    $min = date('Y-m-d H:i:s', time() - 60 * 20); 			
    $t = "`time_last` < '%s'";
    $where = sprintf($t, $min);
    $query = "DELETE FROM `sessions` WHERE $where";	
    
    // Настройки подключения к БД.
    $hostname = 'localhost';	
    $username = 'f-i'; 
    $password = '0okmnji9';

    $dbName   = "f-i";

    // Языковая настройка.
    setlocale(LC_ALL, 'ru_RU.utf8');	

    // Подключение к БД.
    mysql_connect($hostname, $username, $password) or die('No connect with data base'); 
    mysql_query('SET NAMES utf8');
    mysql_select_db($dbName) or die('No data base');

    // Открытие сессии.
    session_start();

    $result = mysql_query($query);
    if (!$result){
            die(mysql_error());
    }
    var_dump('Удалено:' . mysql_affected_rows());
?>