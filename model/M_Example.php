<?php

class M_Example
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
            self::$instance = new M_Example();
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



    
    // сбор для отчетов
    
    public function getUserReportVars($user, $query){
       
        $privs = $this->mUsers->getUserPrivs(); 
        $valQuery = $this->validateQuery($query, $user, $privs);

        $headSum = $this->getHeadSumByQuery($valQuery);
        $transCount = $this->getTransCount($valQuery);
        $pageNav =  $this->getPageNavigation($valQuery, $transCount, $valQuery['mod']);
        $valQuery['page_num'] = $pageNav['pageNum'];
        $reportData = $this->getReportData($valQuery, $transCount);
        
        
        $result['headSum'] = $headSum;
        $result['reportData'] = $reportData;
        $result['pageNav'] = $pageNav;
        $result['reportMessage'] = $valQuery['reportMessage'];
        $result['save'] = $this->getSaveData($valQuery);
        return $result;
        
    }
    
        private function validateQuery($query, $user, $privs){
        // если это админ оставляем запрос без коррекции
        if (!$privs['VIEW_GLOBAL_BANKROLL']){

            if (!$privs['VIEW_GROUP_BANKROLL']){
                if ($user['id_user'] !== $query['id_user']) {
                    $query['id_user'] = $user['id_user'];
                }          
                if($query['id_operation'] == 7) {
                    $query['id_operation'] = 0;
                }
  
            }
            else{
                if ($user['id_user'] !== $query['id_user']) {
                    
                    if($query['id_group'] != $user['id_group']){
                        $query['id_group'] = $user['id_group']; 
                        if($user['id_group'] === null){
                            $query['id_user'] = $user['id_user'];
                        }
                    }
                    
                }
            }
            
            if($query['id_group'] != $user['id_group']){
                $groups = $this->mUsers->getUserGroups($user['id_user']);
                if(count($groups) > 0){
                    foreach ($groups as $id_group => $group_name){
                        if($id_group == $query['id_group']){
                            $temp = $id_group;
                            break;
                        }
                        $query['id_group'] = $user['id_group'];
                    }
                }
                else{
                    $query['id_group'] = 0;
                }
            }
            
        }
       
        
        
        if($query['all_date_checkbox']*1 == 1){
            $query['all_date_checkbox'] = true;
        }
        
        if(!isset($query['page_num'])) {
           $query['page_num'] = 1;
        }

        return $query;

    }
    
    
    


    

    
    private function getReportData($query, $transCount) {
        if($transCount != 0){
            $result = $this->mContVariables->getReportData($query['id_user'], $query['id_group'],
                    $query['id_contr'], $query['id_operation'], $query['date1'],
                    $query['date2'], $query['page_num'], $query['all_date_checkbox']);
       
        }
        else{
            $result = array(
            '-' => array('id_trans' => '-', 'date' => '-', 'user_name' => '-', 'group_name' => '-', 'oper_name' => '-', 'contr_name' => '-',
             'sum' => '-', 'discription' => '-') 
            );
        }

        return $result;
    }
    
    private function getPageNavigation($query, $transCount, $mod, $transNum = 25){
        $pageNav = array();
        
        if($transCount == 0){
            $pageNav['pageNum'] = $query['page_num'];
            $pageNav['next']['disabled'] = "disabled='disabled' style='color:grey'";
            $pageNav['end']['disabled'] = "disabled='disabled' style='color:grey'";
            $pageNav['home']['disabled'] = "disabled='disabled' style='color:grey'";
            $pageNav['back']['disabled'] = "disabled='disabled' style='color:grey'";
            $pageNav['next']['pagesLeft'] = '';
            $pageNav['end']['pagesLeft'] = '';
            $pageNav['pageTotal'] = $pageNav['pageNum'];
            return $pageNav;
            
        }
        $pageNum = (int)($transCount/$transNum);
        if( $transCount % $transNum != 0) {
            $pageNum++;
            $pagesLeft = $transCount % $transNum;
        }
        else {
            $pagesLeft = $transNum;
        }
        $pageNav['pageTotal'] = $pageNum;
        
        switch ($mod){
            case -2:
                $query['page_num'] = 1;
                break;
            case -1:
                --$query['page_num'];  
                break;
            case 1:
                ++$query['page_num'];
                break;
            case 2:
                $query['page_num'] = $pageNum;
                break;
        }
        
        
        
        
        
        $pageNav['pageNum'] = $query['page_num'];
        if($query['page_num'] <= 1) {
            $pageNav['home']['disabled'] = "disabled='disabled' style='color:grey'";
            $pageNav['back']['disabled'] = "disabled='disabled' style='color:grey'";
        }
        else{
            $pageNav['back']['pagesLeft'] = $transNum;
        }

        if($query['page_num'] == ($pageNum - 1)){
            $pageNav['next']['pagesLeft'] = $pagesLeft;
        }
        else{
            $pageNav['next']['pagesLeft'] = $transNum;
        }

        $pageNav['end']['pagesLeft'] = $pagesLeft;

        if($pageNum  == $query['page_num']){
            $pageNav['next']['disabled'] = "disabled='disabled' style='color:grey'";
            $pageNav['end']['disabled'] = "disabled='disabled' style='color:grey'";
            $pageNav['next']['pagesLeft'] = '';
            $pageNav['end']['pagesLeft'] = '';
        }


        return $pageNav;

    }
    
    private function getTransCount($query) {
        return $this->mContVariables->getTransCount($query['id_user'], $query['id_group'],
            $query['id_contr'], $query['id_operation'], $query['date1'],
            $query['date2'], $query['pageNum'], $query['all_date_checkbox']);
    }
    

    
    private function getHeadSumByQuery($query){
        return $this->mContVariables->getSum($query['id_user'], $query['id_group'],
            $query['id_contr'], $query['id_operation'], $query['date1'],
            $query['date2'], $query['all_date_checkbox']);
    }

        private function getSaveData($query){
        $result ='';
        foreach ($query as $key => $value) {
            if($key != null && $key !== 'home' && $key !== 'back' && 
                    $key !== 'next'&& $key !== 'end'&& $key !== 'mod' && $key !== 'saveLast'){
                $result .= '&' . $key . '=' . $value; 
            }
        }

       
        return $result;
    }

}
