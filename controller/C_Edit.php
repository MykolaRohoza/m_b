<?php

//
// Конттроллер страницы-примера.
//
class C_Edit extends C_Base{
    
    // переменные для создания наполнения 


    protected $contVars;
    private $mArticles;




    //
    // Конструктор.
    //
    function __construct() 
    {
    	parent::__construct();
        $this->mUsers = M_Users::Instance();
        $this->needLogin = true;
        $this->needStocks = false;
        $this->needLoginForm = false;
        $this->needCarosel = false;
    	$this->needTimeTest = true;
        $this->mArticles = M_Articles::Instance();
        $this->mImages = M_Images::Instance();
        $this->controllerPath = '/edit';

        $this->isEdit = true;
        $this->content = array();
    }


    
    //
    // Виртуальный обработчик запроса.
    //
    protected function OnInput(){
        
        parent::OnInput();
       
        
        // Обработка отправки формы.
        if ($this->IsPost()) {
            if($_POST['id_article']){
                $this->controllerPath .= '/' . $_POST['id_article'];

            }

            if(isset($_POST['save'])){
                $message = '/' . $this->save($_POST);
            }
            if(isset($_POST['delete'])){
                $this->delete($_POST['id_article']);
                $this->controllerPath = '/edit';
            }

            if(isset($_POST['delete_img'])){
                $message = '/' . $this->mImages->deleteImg($_POST['old_name']);     
            }
            if(isset($_POST['upload_img'])){
                $message = '/' . $this->imgOper($_FILES['img'], $_POST);
                
            }            

            header("Location: $this->controllerPath$message");
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
            $this->content['nav']['edit'] = 'class="active"';
            $this->content['require']['edit'] = true;
            $this->content['require']['ui'] = true;
            
            if(isset($this->_get[1]) && $this->_get[1] > 0){
                $temp_arr = $this->mArticles->getArticles(0, $this->_get[1]);
                $this->content['articles'] = $temp_arr[0];
            }

            
            $this->content['articles']['article_list'] = $this->mArticles->getArticlesNames();
            if(!is_numeric($this->_get[1])){

                $this->content['articles']['message'] = $this->_get[1];
            }
            else{
                if($this->_get[2]){
                    $this->content['articles']['message'] = $this->_get[2];
                }
            }
        }
                
        
    }
    private function save($request){
       
        $queryKeys = array('article_title', 'article_text', 'article_order', 'article_func', 'article_dest', 'article_img_name',
            'article_img_place', 'article_secondary_to');
        $queryObj = array();
        foreach ($queryKeys as  $key) {
            if($key != 'article_text' && $key != 'article_title'){
                $queryObj[$key] = $request[$key];
            }
            else{

               $queryObj[$key . '_ru'] = $request[$key]; 
            }
        }
        $message = $this->mArticles->save('articles', $queryObj, "id_article={$request['id_article']}");
        return $message;
        
        
        
    }
    private function delete($id_article){
        return $this->mArticles->delete('articles', "id_article=$id_article");
    }

    private function imgOper($file, $request){
        $query = array();
        $query['file_name'] = ($file['name'] != '');
        $query['new_name'] = ($_POST['name'] != '');
        $query['alt'] = ($_POST['alt'] != '');
        return $this->mImages->imgOper($file, $request, $query);
    }




        //
    // Виртуальный генератор HTML.
    //
    public function OnOutput() {   	

        //Генерация вложенных шаблонов
        
        $this->content['articles']['images'] =  $this->galery;

        $this->content['container_main'] = $this->View('V/view_edit.php', $this->content['articles']);
        parent::OnOutput();
             
    }          
          
 }
