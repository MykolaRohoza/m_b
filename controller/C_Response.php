<?php
class C_Response extends C_Controller{
    private $content;
    private $mUser;

    
    function __construct() {
        parent::__construct();
        $this->mUser = M_Users::Instance();

    }
   
    protected function OnInput() {
        parent::OnInput();
        if($this->IsPost()){
            if(isset($_POST['registration_test'])){

                $this->content = $this->registrate_test($_POST['login']);
            }
            if(isset($_POST['registration'])){
                
                $this->content = $this->registrate($_POST['login'], $_POST['password'], 
                        $_POST['telephone'], $_POST['user_name'], $_POST['user_second_name']);
            }
            if(isset($_POST['check'])){

                if(isset($_POST['login'])){
                    $this->content = $this->checkLogin($_POST['login']);

                }
                if(isset($_POST['telephone'])){
                    $this->content = $this->checkPhone($_POST['telephone']);

                }
            }
            if(isset($_POST['add_new_ex'])){
                $mExe = M_Exercises::Instance();
                $this->content['message'] = $this->save($_POST['id_exercise'], $_POST['add_new_ex'], $mExe);
                $this->content = $this->getExercises($mExe);
            }
            if(isset($_POST['del_ex'])){
                $mExe = M_Exercises::Instance();
                $this->content['message'] = $this->deleteExercises($_POST['id_exercise'], $mExe);
                $this->content = $this->getExercises($mExe);
            }
            if(isset($_POST['add_user_ex'])){
                $this->content = $this->addUserEx($_POST['id_user'], $_POST['add_user_ex']);

            }
            /* должны возвращать то что принимают */
            if(isset($_POST['contacts_menu'])){
                $this->content = $this->saveContact($_POST);
            }
            if(isset($_POST['diagnosis_menu'])){
                $this->content = $this->saveDiagnosis($_POST);
            }
            if(isset($_POST['role_menu'])){ 
                if(!is_numeric($_POST['id_role'])){
                    $this->content = $this->getRoles();
                }
                else{                    
                    $this->content = $this->saveRole($_POST);
                }

            }
            if(isset($_POST['display_menu'])){ 
                if(!is_numeric($_POST['id_display'])){
                    $this->content = $this->getDispVars();
                }
                else{                    
                    $this->content = $this->saveDispVars($_POST);
                }
            }
            if(isset($_POST['image_menu'])){ 
                $this->content = $this->setUserImage($_POST);
            }
        }
        
       
    }
    private function addUserEx($id_user,$exercises){
        $this->mUser->addUserEx($id_user,$exercises);
        return $this->mUser->getUserEx($id_user);
        
        
    }

    private function saveContact($request){
        $request['id_info'] = $this->mUser->saveContact($request); 
        $result = $this->mUser->getContact($request);
        $result['id_user'] = $request['id_user'];
        return $result;
    }
    private function setUserImage($request){

        $result = $this->mUser->setUserImage($request['id_user'], $request['user_image']); 
        return $result;
    }
    private function saveDiagnosis($request){
        $this->mUser->saveDiagnosis($request);
        $result = $this->mUser->getDiagnosis($request['id_user']);
        return $result;
    }
    private function getRoles(){
        $result = $this->mUser->getRoles();
        return $result;

    }
    private function saveRole($request){
        $this->mUser->changeUserRole($request);
        $result = $this->mUser->getRoleByID($request['id_user']);
        return $result;
    }
    private function getDispVars(){
        $result = $this->mUser->getDispVars();
        return $result;

    }
    private function saveDispVars($request){   
        $this->mUser->changeDispVars($request);
        $result = $this->mUser->getDispVarsByID($request['id_user']);
        return $result;
    }

    



    private function deleteExercises($id_ex, M_Exercises $mExe){
        $result = $mExe->deleteExercises($id_ex);
        return $result;
    }
    private function getExercises(M_Exercises $mExe){
        $result = $mExe->getExercises();
        return $result;
    }
    private function save($id_ex, $new_ex, M_Exercises $mExe){
       
        $mExe->saveExercises($id_ex, $new_ex);
    }
    private function checkLogin($login){
        $result = $this->mUser->checkLogin($login);
        return $result;
    }
    private function checkPhone($tel){
        $result = $this->mUser->checkPhone($tel); 
        return $result;
    }
    private function registrate($login, $password, $telephone, $name, $second){

        $result = $this->mUser->registration($login, $password, $telephone, $name, $second);
                
        return $result;

}
    public function registrate_test() {
        $code = md5(time(true));
        $sender = new M_Sender($_POST['login'], $code); 
        $sender->start();
        return $sender->getLog();
    }
    
    
    
    protected function OnOutput() {
        parent::OnOutput();
        
        
        if(true){
            $json = json_encode($this->content);
        }
        echo $json;
        

    }

}