<?php
//
// Конттроллер страницы-примера.
//
class C_Office extends C_Base {
    
    // переменные для создания наполнения 

    protected $userName;

    protected $contVars;




    //
    // Конструктор.
    //
    function __construct() 
    {
    	parent::__construct();
        $this->mUsers = M_Users::Instance();
        $this->needLogin = true;
        $this->wrapper = "style ='width: 300px;'";
    	$this->needTimeTest = true;
    }


    
    //
    // Виртуальный обработчик запроса.
    //
    protected function OnInput(){
        
        parent::OnInput();

        
        // Обработка отправки формы.
        if ($this->IsPost()) {

        if($this->mUsers->setNewPassword($this->user['id_user'], md5(md5($this->_post['new_pass'])))){
            $message = 'пароль успешно изменен';
            
        }
        else{
            $message = 'ошибка вышла';
        }
            
                header("Location: index.php?C=office&message=$message");
                die();

        }
        else
        {	
            if ($this->user == null && $this->needLogin)
            {       	
                header("Location: index.php");
                die();
            }
            // сбор разрешений и организация массивов

            $this->report['message'] = $this->_get['message'];
            $this->report['nameHead'] = $this->user['user_name'];
         
             

        }
                
        //parent::OnInput();
    }
    //
    // Виртуальный генератор HTML.
    //
    public function OnOutput() {   	

        //Генерация вложенных шаблонов
        

        if($this->needStocks && count($this->content['stocks']) > 0){
            $vars['stocks'] = $this->View('V/view_stocks.php',
                    array('stocks' => $this->content['stocks'], 'isAdmin' => $this->isAdmin));
        }

        $this->content = $this->View('V/view_office.php', $this->report);
        parent::OnOutput();
        
        
            
    }
            
            
          
 }
