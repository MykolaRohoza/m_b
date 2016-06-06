<?php
//
// Конттроллер страницы-примера.
//
class C_Report extends C_Base {
    
    // переменные для создания наполнения 
    private $report;
    private $isEdit;
    private $mMsql;
    protected $userName;

    protected $contVars;




    //
    // Конструктор.
    //
    function __construct($isEdit = false) 
    {
    	parent::__construct();
        $this->mUsers = M_Users::Instance();
        $this->report = array();
        $this->isEdit = $isEdit;
        $this->wrapper = "style ='width: 1250px;'";
        $this->needLogin = true;
        $this->mMsql = M_MSQL::Instance();
    	$this->needTimeTest = true;
    }


    
    //
    // Виртуальный обработчик запроса.
    //
    protected function OnInput(){
        
        parent::OnInput();
        // Менеджеры.
        $mExample = M_Example::Instance();
        
        // Обработка отправки формы.
        if ($this->IsPost()) {
                
            if(isset($this->_post['del'])){
                $extraVars = 'C=report_edit' . $this->delete();
            }
            if(isset($this->_post['edit'])){
                $extraVars = 'C=report_edit' . $this->edit();
            }
            if(isset($this->_post['home'])){
                $extraVars = 'mod=-2';
            }
            if(isset($this->_post['back'])){
                $extraVars = 'mod=-1';
            }
            if(isset($this->_post['next'])){
                $extraVars = 'mod=1';
            }
            if(isset($this->_post['end'])){
                $extraVars = 'mod=2';
            }

            
            $extraVars .= $this->_post['saveLast'];

            
            $request = "Location: index.php?" . $extraVars;
           
            header($request);
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
           
            $this->report = $mExample->getUserReportVars($this->user, $this->_get);
         
             

        }
                
        
    }
    private function delete() {
        $temp = "id_trans='%d'";
        $where =sprintf($temp, $this->_post['id_trans']);
        $this->mMsql->Del('transactions', $where);
    }
    private function edit() {
        $changeData = array();

        if($this->_post['sum']){
            $changeData['sum'] = $this->_post['sum'];
        }
        if($this->_post['discription']){
            $changeData['discription'] = $this->_post['discription'];
        }
        if($this->_post['date']){
            $changeData['date'] = date("Y-m-d", strtotime($this->_post['date']));
        }

        $temp = "id_trans='%d'";
        $where =sprintf($temp, $this->_post['id_trans']);
       
        $this->mMsql->Update('transactions', $changeData, $where);
    }


    //
    // Виртуальный генератор HTML.
    //
    public function OnOutput() {   	

        //Генерация вложенных шаблонов
        


        if($this->isEdit && $this->isAdmDisplay) {
            $path = 'V/view_editor_report.php';
        }
        else {
            $path = 'V/view_final_report.php';
        }
        $this->content = $this->View($path, $this->report);
        parent::OnOutput();
        
        
            
    }
            
            
          
 }
