<?php

class M_Lib {

    /**
     * <p style="margin-top:0;">
     * подстановка виртуального пути
     * используется при открытии кроном
     * </p>
     * @var string
     */
    private static $virt_path = '/virt/homes/f-i/htdocs/';
    

    /**
     * <p>фунукция логирования в указанный файл, записывает номер, дату и время</p>
     * @param mixed $log - тело для логирования
     * @param string $path - полный адрес от корневой папки зарание созданного файла
     * @return int возвращает количество записанных байт или <b>FALSE</b> в случае ошибки. 
     */
    public static function addLog($log, $path = 'log/log.txt'){

        ob_start();
        var_dump($log);
        $tmp = ob_get_clean();
        $textLog  = '[' . date("H:i:s d.m.Y" , time()) . '] - ' . $tmp;
        // подсчет строк и подстановка слудующего номера в строку лога 
        set_time_limit(0);    
        if(!$handle = @fopen($path, "r")){
            if(!$handle = @fopen(self::$virt_path . $path, "r")) {
                
                return false;
            }
            $path = self::$virt_path . $path;
        } 
        
        
        $n = 1; 
        while (!feof($handle)) {
                $bufer = fread($handle, 1048576);
                $n+=substr_count($bufer,"#"); 
        }
        $textLog = "#$n $textLog"; 
        fclose($handle); 
        // запись сформированной лог строки в файл
        $fp = fopen($path, "a");

        $res = fwrite ($fp, "\r\n" . $textLog);  
        fclose($fp); 
        return $res; 
    }

    public static function backTF($firstRem = true) {

        ob_start();
        debug_print_backtrace();
        $trace = ob_get_clean();
        // Удаляем первую функцию из стека это данная
        if ($firstRem) {
            $trace = preg_replace('/^#0.+#/', '#', $trace);
        }
        // меняем номер 
        $trace = preg_replace ('/^#(\d+)/me', '\'#\' . ($1 - 1)', $trace); 
                $trace .= "";

            self::addLog($trace, 'log/btf.txt');

    } 
	
}

