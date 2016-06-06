<?php
class C_Del extends C_Base{
    function __construct() {
    	parent::__construct();
        $this->mUsers = M_Users::Instance();
        $this->needLogin = true;
        $this->needLoginForm = false;
        $this->needStocks = false;
    	$this->needTimeTest = true;
    	$this->needStocks = false;
        $this->controllerPath = "/del";
        $this->needCarosel = FALSE;
        $this->isEdit = true;

    }
    
    protected function OnInput(){
        parent::OnInput();

        
        // Обработка отправки формы.
        if ($this->IsPost()) {
            $result = $this->mUsers->deleteUser($_POST['id_user']);
            if($result){
                header("Location: /users");
                die();
            }
            else{
                header("Location: $this->controllerPath/{$this->_get[1]}/Неудачная попытка удаления");
                die();
            }
            
        }
        else{
            $this->content['users'] = $this->mUsers->getUsers(0, $this->_get[1]); 
            if(isset($this->_get[2])){
                $this->content['message'] = $this->_get[2];
            }
            else{
                //$this->content['message'] = '';
            }
                
        }
    }
    public function OnOutput() {   	

    //Генерация вложенных шаблонов

        $this->content['container_main'] = $this->View('V/view_del.php', $this->content);
        parent::OnOutput();

    }
   
    
    
}
