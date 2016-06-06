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
            if ($this->user == null && $this->needLogin)
            {       	
                header("Location: /");
                die();
            }
            // сбор разрешений и организация массивов
            
            $this->content['nav']['users'] = 'class="active"';
            $this->content['images'] =  $this->galery;
            
            $roles = $this->getRoles($this->_get[1]);
            var_dump($roles);
            $this->content['users'] = $this->getUsersByRoles($roles);
            $mExe = M_Exercises::Instance();
            $this->content['exercises'] = $this->getExercises($mExe);

            
            
           
        }
                
        
    }
    private function getUsersByRoles($roles){
        $result = $this->mUsers->getUsers($roles);
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
