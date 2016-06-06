<?php


//
// Менеджер для примера
//
class M_Editor
{
    private static $instance; 	// ссылка на экземпляр класса
    private $msql; 				// драйвер БД	
    private $mUsers;            // менеджер пользователей
    private $mContVariables;            // менеджер пользователей


    //
    // Получение единственного экземпляра (одиночка)
    //
    public static function Instance()
    {
        if (self::$instance == null) {
            self::$instance = new M_Editor();
        }

        return self::$instance;
    }

    //
    // Конструктор
    //
    private function __construct()
    {
            $this->msql   = M_MSQL::Instance();
            $this->mUsers = M_Users::Instance();
            $this->mContVariables = M_ContVariables::Instance();

            
    }


        
        
    public function addTransaction($addArr){
        $transaction = array();
        $transaction['id_user'] = $addArr['id_user'];
        $transaction['id_operation'] = $addArr['id_operation'];
        
        if($addArr['id_contr'] != 0){
            $transaction['id_contr'] = $addArr['id_contr'];
        }
        if($addArr['id_group'] != 0){
            $transaction['id_group'] = $addArr['id_group'];
        }
        $transaction['date'] = date("Y-m-d", strtotime($addArr['date']));
        
        $transaction['sum'] = $addArr['sum'];
        $transaction['discription'] = $addArr['discription'];
        
       
        $isSuccessfully = (bool)($this->msql->Insert('transactions', $transaction) > 0);
        return $isSuccessfully; 
        
    }

    
    public function addName($tableName, $key, $value){
        $obj[$key] = $value;
        $isSuccessfully = (bool)($this->msql->Insert($tableName, $obj) > 0);
        return $isSuccessfully;
    }
    


}
