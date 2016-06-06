<?php
//
// Конттроллер страницы-примера.
//
class C_Admin extends C_Base {
    
    // переменные для создания наполнения 

    protected $userName;

    protected $contVars;
    protected $mContVariables;
    protected $mUsers;
    protected $mMsql;




    //
    // Конструктор.
    //
    function __construct(){
    	parent::__construct();
        $this->mUsers = M_Users::Instance();
        $this->wrapper = "style ='width: 500px;'";
        $this->needLogin = true;
    	$this->needTimeTest = true;
        $this->mContVariables = M_ContVariables::Instance();
        $this->mMsql = M_MSQL::Instance();
    }


    
    //
    // Виртуальный обработчик запроса.
    //
    protected function OnInput(){
        
        parent::OnInput();
        // Менеджеры.

        
        // Обработка отправки формы.
        if ($this->IsPost()) {

            
                
            if (isset($this->_post['update_user'])) {                 

                $extraVars =  $this->updateUser();
               

            }
            if(isset($this->_post['delete_user'])){
                $extraVars =  $this->deleteUser();
            }
            if (isset($this->_post['update_group'])) {                 
                $extraVars =  $this->updateGroup();
               

            }
            if(isset($this->_post['delete_group'])){
                $extraVars =  $this->deleteGroup();
            }
            
            
            if (isset($this->_post['update_contr'])) {                 
                $extraVars =  $this->updateContr();
            }
            if(isset($this->_post['delete_contr'])){
                $extraVars =  $this->deleteContr();
            }
            if (isset($this->_post['update_oper'])) {                 
                $extraVars =  $this->updateOper();
               

            }
            if(isset($this->_post['delete_oper'])){
                $extraVars =  $this->deleteOper();
            }            
            if(isset($this->_post['auto_cor'])){
                $extraVars =  $this->updateAvtocor();
            }
            

  
            $request = 'Location: index.php?C=adm' . $extraVars;
            header($request);
            die();

            

        }
        else
        {	
            if ($this->user == null && $this->needLogin && !$this->isAdmDisplay)
            {       	
                header("Location: index.php");
                die();
            }
            $this->contVars['names'] = $this->mContVariables->getNamesAndGroups();
            $this->contVars['roles'] = $this->mContVariables->getRoles();
            $this->contVars['groups'] = $this->mContVariables->getGroups();
            $this->contVars['contractors'] = $this->mContVariables->getContractors();
            $this->contVars['opers'] = $this->mContVariables->getOperations();
            $this->contVars['autoCor'] = $this->mContVariables->getAutoCorVal();
            $this->contVars['message_us'] = $this->_get['message_us'];
            $this->contVars['message_gr'] = $this->_get['message_gr'];
            $this->contVars['message_co'] = $this->_get['message_co'];
            $this->contVars['message_op'] = $this->_get['message_op'];
            $this->contVars['message_ac'] = $this->_get['message_ac'];
            
            
            
         
             

        }
                

    }
    

    public function updateUser(){
        $changeData = array();
        $message = "&message_us=";
        if (trim($this->_post['login']) != '') {
            $changeData['login'] = $this->_post['login'];
        }
        if($this->_post['id_role']){
            $changeData['id_role'] = $this->_post['id_role'];
        }
        if($this->_post['id_group']){
            $changeData['id_group'] = $this->_post['id_group'];
        }
        if($this->_post['id_role'] == 2 && $this->_post['id_group'] == 0){
            return $message . 'Админу группы необходимо присвоить группу';
        }
        
        if( $changeData['login']== null && $changeData['id_role']== null && $changeData['id_group'] == null){
            return $message . 'не выбраны значения для обновления';
        }
        $temp = "id_user='%d'";
        $where =sprintf($temp, $this->_post['id_user']);
        $t = $this->mMsql->Update('users', $changeData, $where);
        if($t > 0){
            $message .= 'пользователь обновлен';
        }
        else{
           $message .= 'возникла ошибка'; 
        }
        return $message;
    }
               

    public function deleteUser(){
        $message = "&message_us=";
        $temp = "id_user='%d'";
        $where =sprintf($temp, $this->_post['id_user']);
        if($this->mMsql->Del('users', $where) > 0){
            $message .= 'пользователь удален';

        }
        else{
           $message .= 'возникла ошибка'; 
        }
        return $message;
    }
    public function updateGroup(){
        $message = "&message_gr=";
        $changeData = array('group_name' => $this->_post['group_name']);
        $temp = "id_group='%d'";
        $where =sprintf($temp, $this->_post['id_group']);
        if ($this->mMsql->Update('groups', $changeData, $where) > 0) {
            $message .= 'группа обновлена';
        }
        else{
           $message .= 'возникла ошибка'; 
        }
        return $message;
    }

    public function deleteGroup(){
        $message = "&message_gr=";
        $temp = "id_group='%d'";
        $where =sprintf($temp, $this->_post['id_group']);
        $t = $this->mMsql->Del('groups', $where);
        if($t){
            $message .= 'группа удалена';
        }
        else{
           $message .= 'возникла ошибка'; 
        }
        return $message;
    }
    public function updateContr(){
        $message = "&message_co=";
        $changeData = array('contr_name' => $this->_post['contr_name']);
        $temp = "id_contr='%d'";
        $where =sprintf($temp, $this->_post['id_contr']);
        if ($this->mMsql->Update('contractors', $changeData, $where) > 0) {
            $message .= 'контрагент обновлен';
        }
        else{
           $message .= 'возникла ошибка'; 
        }
        return $message;
    }
    public function deleteContr(){
        $message = "&message_co=";
        $temp = "id_contr='%d'";
        $where =sprintf($temp, $this->_post['id_contr']);
        if ($this->mMsql->Del('contractors', $where) > 0) {
            $message .= 'контрагент удален';
        }
        else{
           $message .= 'возникла ошибка'; 
        }
        return $message;
    }
    public function updateOper(){
        $message = "&message_op=";
        $changeData = array('operation_name' => $this->_post['oper_name']);
        $temp = "id_operation='%d'";
        $where =sprintf($temp,  $this->_post['id_operation']);
        if ($this->mMsql->Update('operations', $changeData, $where) > 0) {
             $message .= 'операция обновлена';
        }
        else{
           $message .= 'возникла ошибка'; 
        }
        return $message;
    }

    public function deleteOper(){
        $message = "&message_op=";
        $id_oper = $this->_post['id_operation'];
        if($id_oper !== 7){
            $temp = "id_operation='%d'";
            $where =sprintf($temp,  $id_oper);
            if ($this->mMsql->Del('operations', $where) > 0) {
                $message .= 'операция удалена';
            }
            else{
                $message .= 'возникла ошибка'; 
            }
        
        }
        else{
            return $message . 'Не нужно удалять автокорекцию!';
        }
        
        return $message;
    }
            
            

    public function updateAvtocor(){
         $message = "&message_ac=";
        $changeData = array('cost_value' => $this->_post['auto_cor_num']);
        if(isset($this->_post['auto_cor_checkbox'])){
            $changeData['avtocor_on'] = true;
        }
        else{
            $changeData['avtocor_on'] = false; 
        }
        if ($this->mMsql->Update('costs', $changeData, "id_cost='1'") > 0) {
            $message .= 'автокорекция обновлена';
        }
        return $message;
    }












//
    // Виртуальный генератор HTML.
    //
    
    
    
    
    
    public function OnOutput() {   	

        //Генерация вложенных шаблонов
        



        $this->content = $this->View('V/view_admin.php', $this->contVars);
        parent::OnOutput();
        
        
            
    }
            
            
          
 }
