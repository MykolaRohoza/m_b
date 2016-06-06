<?php

class C_Office extends C_Base {
    
    protected $contVars;

    //
    // Конструктор.
    //
    function __construct() 
    {
    	parent::__construct();
        $this->mUsers = M_Users::Instance();
        $this->needLogin = true;
        $this->needCarosel = false;
        $this->needLoginForm = FALSE;
    }


    
    //
    // Виртуальный обработчик запроса.
    //
    protected function OnInput(){
        
        parent::OnInput();

        
        // Обработка отправки формы.
        if ($this->IsPost()) {
}
        else
        {	
            if ($this->user == null && $this->needLogin)
            {       	
                header("Location: index.php");
                die();
            }
            // сбор разрешений и организация массивов
            $this->content['users'] = $this->mUsers->getUsers(0, $this->user['id_user']);
        
         
             

        }

    }
    //
    // Виртуальный генератор HTML.
    //
    public function OnOutput() {   	

        //Генерация вложенных шаблонов

        $this->content['container_main'] = $this->View('V/view_office.php', $this->content);
        parent::OnOutput();
        
        
            
    }
            
            
          
 }
