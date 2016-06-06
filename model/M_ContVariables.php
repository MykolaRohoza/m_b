<?php


//
// Менеджер пользователей
//
class M_ContVariables
{	
    private static $instance;	// экземпляр класса
    private $msql;				// драйвер БД


    //
    // Получение экземпляра класса
    //
    public static function Instance()
    {
        if (self::$instance == null) {
            self::$instance = new M_ContVariables();
        }

        return self::$instance;
    }

    //
    // Конструктор
    //
    private function __construct()
    {
            $this->msql = M_MSQL::Instance();


    }


    public function getOperations($isUser = true) {
        $query  = "SELECT id_operation, operation_name FROM operations";
        if($isUser) {
            $query .= ' WHERE id_operation != 7';
        } 
        $result = $this->msql->Select($query);
        $oper = array();
        foreach ($result as $value){
            $oper[$value['id_operation']] = $value['operation_name'];
        }
        return $oper;
    }
    public function getContractors() {
        $query  = "SELECT id_contr, contr_name FROM contractors";
        $result = $this->msql->Select($query);
        $contractor = array();
        foreach ($result as $value){
            $contractor[$value['id_contr']] = $value['contr_name'];
        }
        return $contractor;
    }
    /**
     * 
     * @param int $id = 0 - возращает всех, id 
     * @param bool $isGroup = false - регулирует принадлежность id (группа/пользователь)
     * @return array(int id => string name)
     */
    public function getNamesAndGroups($id = 0, $isGroup = false) {

        $query  = "SELECT id_user, user_name FROM users";
        if($id !== 0 && !$isGroup){
            $query .= " WHERE id_user = '%d'";
            $query  = sprintf($query, $id);
        }
        elseif ($isGroup) {
            $query .= " WHERE id_group = '%d'";
            $query  = sprintf($query, $id);
        }

        $result = $this->msql->Select($query);
        $namesAndGroups = array();
        foreach ($result as $value){
            $namesAndGroups[$value['id_user']] = $value['user_name'];
//            $namesAndGroups['names'][$value['id_user']] = $value['user_name'];
//            if (count($value['id_group'])) {
//                $namesAndGroups['groups'][$value['id_group']] = $value['name'];
//            }
//            $namesAndGroups['id_user'][$value['id_user']] = array('user_name' => $value['user_name'], 
//                'id_group'=> $value['id_group'], 'name'=> $value['name']);
        }

        return $namesAndGroups;

    }
    public function getGroups($id = 0){

        $query  = "SELECT id_group, group_name FROM groups";
        if ($id != 0){
            $query .= " WHERE id_group = '%d'";
            $query  = sprintf($query, $id);
        }
        $result = $this->msql->Select($query);
        $groups = array();
        foreach ($result as $value){
            $groups[$value['id_group']] = $value['group_name'];
        }

        return $groups;
    }
    public function getUserAutoCorVal($names, $avtoCorVal){
        if(count($names) < 1){
            return $avtoCorVal;
        }
        
        $query  = "SELECT id_user, salery_mod FROM users WHERE ";
        $temp ="";
        foreach ($names as $key => $value) {
            if(strlen($temp) > 1) {
                 $temp .= ' OR ';
            }
            $temp .= "id_user='$key'";
        }
        $query .= $temp;
        $result = $this->msql->Select($query);
        $userAutoCorVal = array();
        foreach ($result as $value) {
            if ($value['salery_mod'] === '0' || $value['salery_mod'] === "NULL") {
                $userAutoCorVal[$value['id_user']] = $avtoCorVal;
            } 
            else {
                $userAutoCorVal[$value['id_user']] = $value['salery_mod'];
            }
        }
        return $userAutoCorVal;
       
    }
    public function getAutoCorVal(){
        $query  = "SELECT cost_value, avtocor_on FROM costs WHERE id_cost='1'";
        $result = $this->msql->Select($query);
        if($result[0]['avtocor_on'] == 1){
            $checked = 'checked="checked"';
        }
        $avtocor = array('value' => $result[0]['cost_value'], 'checked' => $checked);
        
        return $avtocor;
    }
    public function getSum($id_user, $id_group = 0, $id_contr = 0, $id_operation = 0, $date1 = null, $date2 = null, $isAllDates = false){
        $selectStr  = 'SUM(sum)';
        $query = $this->generateSQL($selectStr, 'transactions', $id_user, $id_group,  $id_contr, $id_operation, $date1, $date2, $isAllDates);
        $result = $this->msql->Select($query);
        
        if($result != "NULL"){
            $sum = number_format($result[0]["SUM(sum)"], 2, ',', ' '); 
        }
        else {
            $sum = 0;  
        }
        return $sum;  
        
        
    }
    
    public function getTransCount($id_user = 0, $id_group = 0, $id_contr = 0, $id_operation = 0 , $date1l, $date2) {

        $selectStr = 'count(*)';
        $query = $this->generateSQL($selectStr, 'transactions', $id_user, $id_group,  $id_contr, $id_operation, $date1, $date2, true);
        $result = $this->msql->Select($query);
        
        if($result != "NULL"){
            $count = intval($result[0]["count(*)"]);
        }
        else {
            $count = 0;  
        }
        return $count;

    }
   
    public function getReportData($id_user, $id_group,  $id_contr = 0, $id_operation = 0,
            $date1, $date2, $pageNum = 0, $isAllDates = false) {
        $limit = 25;
        if($pageNum > 1){
            $start = ($pageNum - 1)* $limit;
        }
        else{
            $start =  0;
        }
             

        $selectStr  =  "id_trans, date, sum, transactions.discription, user_name, operations.operation_name, contr_name, group_name";
        $from = 'transactions LEFT JOIN users USING(id_user) LEFT JOIN operations USING(id_operation) '
                . 'LEFT JOIN contractors USING(id_contr) LEFT JOIN groups ON  transactions.id_group = groups.id_group';
        $query  = $this->generateSQL($selectStr, $from, $id_user, $id_group,  $id_contr, $id_operation, $date1, $date2, $isAllDates);
        

        $query  .= " ORDER BY id_trans DESC LIMIT $start, $limit";

        $result = $this->msql->Select($query);
       
        if(count($result) != 0){
            $transactions = array();
            foreach ($result as $resKey => $trans){
                foreach ($trans as $key => $value){
                    if($value != null){
                        switch ($key) {
                            case 'sum':
                                
                                $transactions[$resKey][$key] = number_format($value, 2, ',', ' '); 
                                break;
                            case 'date':// защититься от dd-mm-yyyy
                                $transactions[$resKey][$key] = date("d-m-Y", strtotime($value));
                                break;
                            default :
                            $transactions[$resKey][$key] = $value;   

                        }
                    }
                    else{
                        $transactions[$resKey][$key] = '-';
                    }
                }
            }



            
        }
        else{
            $transactions = array(
            0 => array('id_trans' => '-', 'date' => '-', 'user_name' => '-', 'group_name' => '-',
                'oper_name' => '-', 'contractor' => '-', 'sum' => '-', 'discription' => '-')
                );
            }
   
        return $transactions;
    }

    private function generateSQL($selectStr, $from , $id_user, $id_group,  $id_contr, $id_operation, $date1, $date2, $isAllDates){
        $query  = "SELECT %s FROM %s";
        $query  = sprintf($query, $selectStr, $from);
        if($id_user !=0 ){
            // если получен ИД пользователя возвращается его сумма
            $query .= " WHERE transactions.id_user = '%d' AND id_operation != 7" ;
            $query  = sprintf($query, $id_user);
        }
        else{
            if($id_group != 0){
                // если получен ИД группы возвращается ее сумма
                $query .= " WHERE transactions.id_group = '%d'";
                $query  = sprintf($query, $id_group);
            }
            else{
            // если не получено ничего возвращаем всю сумму
                // затычка для AND
                $query .= " WHERE 1=1";
            }
        }
            
        if( $id_operation !=  0){
            $query .= " AND transactions.id_operation = '%d'" ;
            $query  = sprintf($query, $id_operation);
           
        }
        if($id_contr != 0){
            $query .= " AND transactions.id_contr = '%d'" ;
            $query  = sprintf($query, $id_contr);           
        }
        if(!$isAllDates){
            
            $query .= " AND transactions.date >='%s' AND transactions.date <= '%s'" ;
            $query  = sprintf($query, date("Y-m-d", strtotime($date1)), date("Y-m-d", strtotime($date2)));
        }
        //M_Lib::addLog($query);
        return $query;
    }
    public function getRoles() {
        $query = "SELECT id_role, role_name FROM roles";
        $result = $this->msql->Select($query);
        $roles = array();
        if($result != null){
            foreach ($result as $value){
                $roles[$value['id_role']] = $value['role_name'];
            }
        }
        return $roles;
    }
    
    
}
