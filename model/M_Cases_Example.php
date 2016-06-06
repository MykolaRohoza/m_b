<?php

//
// Менеджер для примера
//
class M_Cases_Example
{
    private static $instance; 	// ссылка на экземпляр класса
    private $msql; 				// драйвер БД	
    private $mUsers;            // менеджер пользователей
    private $mContVariables;            // менеджер пользователей
    
   

    //
    // Получение единственного экземпляра (одиночка)
    //
    public static function Instance() {
        if (self::$instance == null) {
            self::$instance = new M_Cases_Example();
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


    
        // сбор для главной
    //$user, $query, $privs, $privsVars, $names, $groups, $opers, $contractors
    public function getUserViewCasesVars($user, $query){
        

        $privs = $this->mUsers->getUserPrivs();
        $privsVars = $this->getPrivsVars($user, $privs);
        $names = $this->getNames($user, $privs);
        $groups = $this->getGroups($user, $privs);
        $opers = $this->getOperations($privs);
        $contractors = $this->mContVariables->getContractors();
        
        $result['add'] = $this->getUserViewAddVars($query, $privs, $names, $groups, $opers, $contractors);
        $result['control'] = $this->getUserViewControlVars($privs, $query);
        $result['cases'] = $this->getUserCasesVars($user, $privs, $privsVars, $names);
        $result['report'] = $this->getUserViewReportVars($user, $privs, $names, $groups, $opers, $contractors);
        
        return $result;
    }
    
    private function getUserViewAddVars($query, $privs, $names, $groups, $opers, $contractors)
    {

        if($privs['GLOBAL_OPERATIONS']){

            
           
           
            $result['names'] = $names;
            $result['groups'] = $groups;
            $result['opers'] = $opers;
            $result['contractors'] = $contractors;
            $result['autoCor'] =  $this->mContVariables->getAutoCorVal();
            //$result['userAutoCorVal'] = $this->mContVariables->getUserAutoCorVal($this->names, $avtoCorVal);
            $result['message'] = $query['message'];
            
            return $result;
        }
        else{
            return null;
        }


    }
    private function getUserViewControlVars($privs, $query)
    {
        if($privs['GLOBAL_OPERATIONS']){
            $result['nameHead'] = 'Имена:';
            $result['contrHead'] = 'Контрагенты:';
            $result['groupHead'] = 'Группы:';
            $result['operHead'] = 'Операции:';
            $result['message'] = $query['message'];
        }

        return $result;
    }
    private function getUserCasesVars($user, $privs, $privsVars, $names)
    {
        if($privs['VIEW_GLOBAL_BANKROLL']){
            $headName ="Суммарный остаток в кассе :";
        }
        else{
            if($privs['VIEW_GROUP_BANKROLL']){
                $headName ="Суммарный остаток в кассе группы:";
            }
            else{
                $headName ="Текущий результат:";
            }
        }
        
        $result['headSum'] = $this->mContVariables->getSum(
                        $privsVars['id_user'],
                        $privsVars['id_group'], 0, 0, 0, 0, true
                        );
        $result['headName'] = $headName;
        
        $result['caseData'] = $this->getCaseData($user, $privs, $names);
        return $result;
    }

    private function getUserViewReportVars($user, $privs, $names, $groups, $opers, $contractors)
    {

        if($privs['VIEW_ALL_USERS'] || $privs['VIEW_GROUP_USERS']){
            $names["0"] = "Все";
        }
        
        $groups["0"] = "Все группы";
        $opers["0"] = "Все";
        
        $result['opers'] = $opers;
        $result['names'] = $names;
        $result['contractors'] = $contractors;
        $result['groups'] = $groups;
        if($user['id_role']*1 !== 1){
            $result['display'] = 'style = "display: none"';
        }
       
        return $result;
    }

   
    private function getCaseData($user, $privs, $names){
            if($privs['VIEW_GROUP_USERS']) {
                $forGroupAdmin = $user['id_group'];
            }
            else{
                $forGroupAdmin = 0;
            }
        $result = array();
        foreach ($names as $id => $name){
            $result[$id]['user_name'] = $name;
            $result[$id]['group'] = $this->mUsers->getActualUserGroupName($id);
            
            $result[$id]['game'] = $this->mContVariables->getSum($id, $forGroupAdmin , 0, 1, 0, 0, true);
            $result[$id]['discont'] = $this->mContVariables->getSum($id, $forGroupAdmin ,0, 4, 0, 0, true);
            $result[$id]['costs'] = $this->mContVariables->getSum($id, $forGroupAdmin , 0, 2, 0, 0, true);
            $result[$id]['salery'] = $this->mContVariables->getSum($id, $forGroupAdmin, 0, 3, 0, 0, true);
        }
        
        return $result;
    }
    
    

    private function getPrivsVars($user, $privs){
        if($privs['VIEW_GLOBAL_BANKROLL']){
            $privsVars['id_user'] = 0;
            $privsVars['id_group'] = 0;
        }
        else{
            if($privs['VIEW_GROUP_BANKROLL']){
                $privsVars['id_user'] = 0;

                $privsVars['id_group'] = $user['id_group'];
            }
            else{
                $privsVars['id_user'] = $user['id_user'];
                $privsVars['id_group'] = $user['id_group'];

            }
        }
        return $privsVars;
    }
    private function getNames($user, $privs){

        
        if($privs['VIEW_ALL_USERS']){
            $names = $this->mContVariables->getNamesAndGroups();
            
        }
        else{
            if($privs['VIEW_GROUP_USERS']){
                $names = $this->mContVariables->getNamesAndGroups($user['id_group'], true);
            }
            else{
                $names = array($user['id_user'] => $user['user_name']); 
            }

        }
        return $names;
    }
    
    private function getGroups($user, $privs){  
        if(!$privs['VIEW_GLOBAL_BANKROLL']){
            $groups = $this->mUsers->getUserGroups($user['id_user']);

        }
        else{
           $groups = $this->mContVariables->getGroups(); 
        }
        return $groups;
    }
    private function getOperations($privs){
        
        $operations = $this->mContVariables->getOperations(
                !($privs['VIEW_ALL_USERS'] || $privs['VIEW_GROUP_USERS'])
                );
         return $operations;
    }



}


