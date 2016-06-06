<?php
//
// Помощник работы с БД
//
class M_MSQL
{
    private static $instance; 	// ссылка на экземпляр класса
    
    //
	// Получение единственного экземпляра (одиночка)
	//
    
    
	public static function Instance(){
            if (self::$instance == null) {
                self::$instance = new M_MSQL();
            }

            return self::$instance;
	}
	


    /**
     * Создание объекта запрещено
     * Клонирование запрещено
     * сериализация и десеариализация запрещены
     */
    private function __construct(){}
    private function __clone(){}
    private function __sleep(){}
    private function __wakeup(){}

    
    	/** Выборка строк
         * @param String $query  полный текст SQL запроса
         * результат	- массив выбранных объектов
	*/
    public function Select($query){
        $result = mysql_query($query);
        if (!$result) {
            die(mysql_error());
        }

	$arr = array();
        while($row = mysql_fetch_assoc($result)){
            $arr[] = $row;
        }

	return $arr;				
    }
    
    
    /**
     * 
     * @param String $query полный текст SQL запроса
     * @param String $pr_key имя основного ключа
     * @param String $container Название ключа для обращения к массиву колонок
     * @param String[] $unique_columns Массив колонок, остальные параметры будут схлапываться в строке
     * @return mixed[] 
     */
    public function SelectGroupByPrKey($query, $pr_key, $container, $unique_columns){
        $result = mysql_query($query);
        if (!$result) {
            die(mysql_error());
        }
        $arr = array();
        while($row = mysql_fetch_assoc($result)){
            $rpk = $row[$pr_key];
            if(is_null($arr[$rpk])){
                $arr[$rpk]  = $row;
                foreach ($unique_columns as $value) {
                    unset($arr[$rpk][$value]);
                }
                $arr[$rpk][$container] = array();
            }
            $arr[$rpk][$container][] = self::uniqueCol2Arr($row, $unique_columns);
        }

	return $arr;				
    }
    private function uniqueCol2Arr($row , $unique_columns) {
        $result = array();
        foreach ($unique_columns as $value) {
            if(!is_null($row[$value])){
                $result[$value] = $row[$value];
            }
            else {
                $result = array();
                break;
            }
        }
        return $result;
    }
	/**
	* Вставка строки
	* @param string $table
         *  имя таблицы
        * @param string[][] $object
         * ассоциативный массив с парами вида "имя столбца - значение"
	* @return int - идентификатор новой строки
	*/
	public function Insert($table, $object)
	{			
		$columns = array();
		$values = array();
	
		foreach ($object as $key => $value)
		{
			$key = mysql_real_escape_string($key . '');
			$columns[] = $key;
			
			if ($value === null)
			{
				$values[] = 'NULL';
			}
			else
			{
				$value = mysql_real_escape_string($value . '');							
				$values[] = "'$value'";
			}
		}
		
		$columns_s = implode(',', $columns);
		$values_s = implode(',', $values);
			
		$query = "INSERT INTO $table ($columns_s) VALUES ($values_s)";
		$result = mysql_query($query);
								
	if (!$result) {
            die(mysql_error());
        }
        return mysql_insert_id();
	}
	
	//
	// Изменение строк
	// $table 		- имя таблицы
	// $object 		- ассоциативный массив с парами вида "имя столбца - значение"
	// $where		- условие (часть SQL запроса)
	// результат	- число измененных строк
	//	
	public function Update($table, $object, $where, $pre = false, $cutNull = false)
	{
		$sets = array();
                
		foreach ($object as $key => $value)
		{
			if(!$pre) {
                            $key = mysql_real_escape_string($key . '');
                        }
			if ($value === null)
			{
                            if(!$cutNull) {
                                $sets[] = "$key=NULL";
                            }
			}
			else
			{
                            if(!pre) {
                                $value = mysql_real_escape_string($value . '');					
                            }
                            $sets[] = "$key='$value'";			
			}			
		}
		
		$sets_s = implode(',', $sets);			
		$query = "UPDATE $table SET $sets_s WHERE $where";
		$result = mysql_query($query);

		if (!$result) {
                    die(mysql_error() . ' ' . $query);
                }
        $res = mysql_affected_rows();

        if(!$res && $result) $res = $result;
        return 	$res;
	}
	
	//
	// Удаление строк
	// $table 		- имя таблицы
	// $where		- условие (часть SQL запроса)	
	// результат	- число удаленных строк
	//
        /**
         *  @param String $table 
         *  Название таблицы
         * 
         * 
         */
	public function Del($table, $where){
        $query = "DELETE FROM $table WHERE $where";	
        
            $result = mysql_query($query);
            if (!$result){
                    die(mysql_error());
            }
            return mysql_affected_rows();
	}
        
}
