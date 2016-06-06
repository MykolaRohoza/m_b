<?php 
//$s_name = $_SERVER['SERVER_NAME'];   
if(isset($_POST['clear_log'])){

        $fp = fopen('../log/log.txt', "w");  
        fwrite ($fp, '');  
        fclose($fp); 

        header("Location: /log");
        die();
    }
    if(isset($_POST['clear_btf'])){
        $fp = fopen('../log/btf.txt', "w");  
        fwrite ($fp, '');  
        fclose($fp); 

        header("Location: /log");
        die();
    }
   
?>
<!DOCTYPE html>
<html>
    <head>
        <title>LOG</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div><a href="log.txt">Логфайл</a></div>
        <br>
        <form  method="post">
            <div><input type="submit" value="Очистить логфайл" name="clear_log"></div>
        </form>
          <br>
        <div><a href="btf.txt">Файл регистрации вложенности функций</a></div>
          <br>
        <form method="post">
            <div><input type="submit" value="Очистить" name="clear_btf"></div>
        </form>
        
    </body>
</html>
