<?php

//
// Конттроллер страницы-примера.
//
class C_Contacts extends C_Base{
    
    // переменные для создания наполнения 


    protected $contVars;




    //
    // Конструктор.
    //
    function __construct() 
    {
    	parent::__construct();
        $this->mUsers = M_Users::Instance();
        $this->needLogin = false;
    	$this->needTimeTest = true;
        $this->controllePath = '/contacts';
    }


    
    //
    // Виртуальный обработчик запроса.
    //
    protected function OnInput(){
        
        parent::OnInput();

        
        // Обработка отправки формы.
        if ($this->IsPost()) {

                header("Location: $this->controllerPath");
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
            $this->content['nav']['contacts'] = 'class="active"'; 
            
            $this->metaTags['keywords'] = 'Центр Mind Body, Харьков, профилактор Евминова, лечение и профилактика заболеваний позвоночника';
            $this->metaTags['description'] = 'Профилактика и лечение проблем позвоночника, межпозвоночные грыжи,'
                    . ' избыточный вес, реабилитация пациентов после перенесенных травм и оперативного вмешательства,'
                    . ' укрепление мышечного корсета, Индивидуальные занятия с каждым пациентом.';
            
            $this->metaTags['og:url'] = "www.mind-body.ho.ua/";
            $this->metaTags['og:description'] = 'Профилактика и лечение проблем позвоночника, межпозвоночные грыжи,'
                    . ' избыточный вес, реабилитация пациентов после перенесенных травм и оперативного вмешательства,'
                    . ' укрепление мышечного корсета, Индивидуальные занятия с каждым пациентом.';
            $this->metaTags['og:title'] = 'Центр Mind Body - профилактор Евминова';
            $this->metaTags['og:type'] = "Contacts";

        
             

        }
                
        //parent::OnInput();
    }
    //
    // Виртуальный генератор HTML.
    //
    public function OnOutput() {   	

        //Генерация вложенных шаблонов

        $this->content['container_main'] = $this->View('V/view_contacts.php');
        parent::OnOutput();
        
        
            
    }
            
            
          
 }
