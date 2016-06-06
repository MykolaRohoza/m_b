<?php
//
// Конттроллер страницы-примера.
//
class C_Users extends C_Base {
    
    // переменные для создания наполнения 


    protected $contVars;




    //
    // Конструктор.
    //
    function __construct() 
    {
    	parent::__construct();
        $this->mUsers = M_Users::Instance();
        $this->needLogin = true;
        $this->needLoginForm = false;
        $this->needStocks = false;
    	$this->needTimeTest = true;
    	$this->needStocks = false;
        $this->controllerPath = "/users";
        $this->needCarosel = FALSE;
        $this->isEdit = true;

    }


    
    //
    // Виртуальный обработчик запроса.
    //
    protected function OnInput(){
        
        parent::OnInput();

        
        // Обработка отправки формы.
        if ($this->IsPost()) {

            header("Location: /");
            die();

        }
        else
        {	
            if (!$this->isAdmin)
            {       	
                header("Location: /");
                die();
            }
            // сбор разрешений и организация массивов
            
            $this->content['nav']['users'] = 'class="active"';
            $this->content['images'] =  $this->galery;
            
            $roles = $this->getRoles($this->_get[1]);

            $this->content['users'] = $this->getUsersByRoles($roles);

            
            $mExe = M_Exercises::Instance();
            $this->content['exercises'] = $this->getExercises($mExe);

            
            
           
        }
                
        
    }
    private function getUsersByRoles($roles){
        if($roles >= 0) {
            $result = $this->mUsers->getUsers($roles);
        }
        else{
            $result = array();
            $result[0] = array(
                "id_user" => "-1",  
                "login" => "", 
                "user_name" => "",
                "user_second_name" => "",
                "id_role" => "3",
                "description" => 'Посетитель',
                );
            $result[0]["user_name"] = "Имя";
            $result[0]["user_second_name"] = "Фамилия";
            $result[0]['contacts'] = array();
            $result[0]['exercises'] = array();
        }
        return $result;
    }
    private function getRoles($roleName){
        switch ($roleName){
            case 'admins':
                return 1;
            case 'couchers':
                return 2;
            case 'all':
                return 0;
            case 'new':
                return -1;
            default : return 3; // users
        }
    }
    private function getExercises(M_Exercises $mExe){
        $result = $mExe->getExercises();
        return $result;
    }

    //
    // Виртуальный генератор HTML.
    //
    public function OnOutput() {   	

        //Генерация вложенных шаблонов

        $this->content['container_main'] = $this->View('V/view_users.php', $this->content);
        parent::OnOutput();
        
        
            
    }
            
            
          
 }
