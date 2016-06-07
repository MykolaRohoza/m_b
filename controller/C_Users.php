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

            if(isset($_POST['create_new_user'])){
                $id_user = $this->mUsers->registrateNewUserForAdmin($_POST['user_name'],
                        $_POST['user_second_name']);
                header("Location: $this->controllerPath/new/$id_user");
                die(); 
                
            }            
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
            $this->content['require']['users'] = true;
            $this->content['require']['ui'] = true;
        }
                
        
    }
    private function getUsersByRoles($roles){
        if($roles >= 0) {
            $result = $this->mUsers->getUsers($roles);
        }
        else{
            $result = $this->mUsers->getUsers(3, $this->_get[2]);
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
            case 'del':
                return -2;
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
