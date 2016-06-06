<?php


//
// Менеджер пользователей
//
class M_Users 
{	
    private static $instance;	// экземпляр класса
    private $msql;				// драйвер БД
    private $sid;				// идентификатор текущей сессии
    private $uid;				// идентификатор текущего пользователя
    private $onlineMap;			// карта пользователей online
    

    //
    // Получение экземпляра класса
    // результат	- экземпляр класса MSQL
    //
    public static function Instance()
    {
        if (self::$instance == null){
                self::$instance = new M_Users();
        }
        return self::$instance;
    }

    //
    // Конструктор
    //
    private function __construct()
    {
        $this->msql = M_MSQL::Instance();
        $this->sid = null;
        $this->uid = null;
        $this->onlineMap = null;
    }

    //
    // Очистка неиспользуемых сессий
    // 
    public function ClearSessions()
    {
        $min = date('Y-m-d H:i:s', time() - 60 * 20); 			
        $t = "time_last < '%s'";
        $where = sprintf($t, $min);
        $this->msql->Del('sessions', $where);
    }

    public function setNewPassword($id_user, $password){
        $temp = "id_user = '%d'";
        $where = sprintf($temp, $id_user);
        
        return (bool)(($this->msql->Update('users', array('password'=>$password), $where))*1 > 0);
    }
    
    //
    // Авторизация
    // $login 		- логин
    // $password 	- пароль
    // $remember 	- нужно ли запомнить в куках
    // результат	- true или false
    //
    public function Login($login, $password, $remember = true)
    {
            // вытаскиваем пользователя из БД 
            $user = $this->GetByLogin($login);

            if ($user == null)
                    return false;

            $id_user = $user['id_user'];
            // проверяем пароль

            if ($user['password'] != md5(md5($password)))
                    return false;

            // запоминаем имя и md5(пароль)
            if ($remember)
            {
                    $expire = time() + 3600 * 24 * 100;
                    setcookie('login', $login, $expire);
                    setcookie('password', md5(md5($password)), $expire);
            }		

            // открываем сессию и запоминаем SID
            $this->sid = $this->OpenSession($id_user);

            return true;
    }
        

    
    public function registration($login, $password, $telephone, $name, $second)
    {
        if($this->checkLogin($login)){
            return -1;
        }
         if($this->checkPhone($telephone)){
             return -2;
        }
        
        $code = $this->GenerateStr(20);
        $obj = array('user_name' => $name, 'user_second_name' => $second, 'login' => $login,
            'password' => md5(md5($password)), 'user_code' => $code); 
        
        // TODO Занесение в контакты 
        if($this->checkLogin($login, 0) || $this->checkPhone($telephone, 0)){
            $result = $this->activate('', $obj);
        }
        else{
            $result = $this->msql->Insert('users', $obj);
            if($result > 0){
                $cont_obj = array();
                $cont_obj[] = array('contact_info' => $result, 'contact' => $login, 'contact_dest' => 2);
                $cont_obj[] = array('contact_info' => $result, 'contact' => $telephone, 'contact_dest' => 1);
                foreach ($cont_obj as $value) {
                    $this->msql->Insert('contact_infos', $value);
                }

            }
            $result = ($result > 0);
            
        }
        
        if($result) {
            $sender = new M_Sender($login, $code); 
            $sender->start();
            $result = $sender->getStatus();
        }
        else{
           $result = false; 
        }
        return $result;
    }
    
    public function checkContact($contact, $user_code_status)
    {	
            $t = "SELECT DISTINCT u.id_user FROM users u RIGHT JOIN contact_infos c_i "
                    . "ON u.id_user = c_i.contact_info WHERE c_i.contact = '%s' AND "
                    . "u.user_code_status=$user_code_status";
            $query = sprintf($t, mysql_real_escape_string($contact));
            $result = $this->msql->Select($query);
            return $result[0]['id_user'] > 0;
    }
    private function checkLogin($contact, $user_code_status = 1)
    {	
        return $this->checkContact($contact, $user_code_status);
    }
    
    private function checkPhone($telephone, $user_code_status=1)
    {	
        return $this->checkContact($telephone, $user_code_status);    
    }
    
    
    public function activate($code, $resentObj = null){

        if(!is_null($resentObj)){
            foreach ($resentObj as $key => $val){
                $object[$key] = $val;
            }
            $where = "login='{$resentObj['login']}'";
            $object = array('user_code' => $resentObj['user_code'], 'user_code_status' => 0);
        }
        else{
            $t = "SELECT DISTINCT id_user, user_code_status FROM users WHERE user_code = '%s'";
            $query = sprintf($t, $code);
            $temp = $this->msql->Select($query);
            $result = $temp[0];
            if($result['user_code_status'] == 1){
                return -1;
            }
            $where = "user_code='$code' AND user_code_status='0'";
            $object = array('user_code' => $code, 'user_code_status' => 1);
        }
        return $this->msql->Update('users', $object, $where);
    }

        //
    // Выход
    //
    public function Logout()
    {
            setcookie('login', '', time() - 1);
            setcookie('password', '', time() - 1);
            unset($_COOKIE['login']);
            unset($_COOKIE['password']);
            unset($_SESSION['sid']);		
            $this->sid = null;
            $this->uid = null;
    }

    //
    // Получение пользователя
    // $id_user		- если не указан, брать текущего
    // результат	- объект пользователя
    //
    public function Get($id_user = null)
    {	
            // Если id_user не указан, берем его по текущей сессии.
            if ($id_user == null)
                    $id_user = $this->GetUid();

            if ($id_user == null)
                    return null;

            // А теперь просто возвращаем пользователя по id_user.
            $t = "SELECT * FROM users WHERE id_user = '%d'";
            $query = sprintf($t, $id_user);
            $result = $this->msql->Select($query);
            return $result[0];	
    }

    //
    // Получает пользователя по логину 
    //
    public function GetByLogin($login)
    {	
            $t = "SELECT * FROM users WHERE login = '%s'";
            $query = sprintf($t, mysql_real_escape_string($login));
            $result = $this->msql->Select($query);
            return $result[0];
    }

    
     public function getUserNameByLogin(){
            $login = $this->Get();
            $t = "SELECT `user_name` FROM users WHERE login = '%s'";
            $query = sprintf($t, mysql_real_escape_string($login['login']));
            $result = $this->msql->Select($query);

            return $result[0]['user_name'];
     }		
    //
    // Проверка наличия привилегии
    // $priv 		- имя привилегии
    // $id_user		- если не указан, значит, для текущего
    // результат	- true или false
    //
    public function Can($priv, $id_user = null){		
        if ($id_user == null) {
            $id_user = $this->GetUid();
        }

        if ($id_user == null) {
            return false;
        }

        $t = "SELECT count(*) as cnt FROM privs2roles p2r
                      LEFT JOIN users u ON u.id_role = p2r.id_role
                      LEFT JOIN privs p ON p.id_priv = p2r.id_priv 
                      WHERE u.id_user = '%d' AND p.priv_name = '%s'";

            $query  = sprintf($t, $id_user, $priv);
            $result = $this->msql->Select($query);

            return ($result[0]['cnt'] > 0);
    }


    public function getUserPrivs($id_user = null){		
        if ($id_user === null) {
            $id_user = $this->GetUid();
        }

        if ($id_user === null) {
            return false;
        }

        $t = "SELECT privs.priv_name FROM privs2roles JOIN users USING(id_role) 
                      JOIN privs USING(id_priv) 
                      WHERE id_user = '%d'";

            $query  = sprintf($t, $id_user);
            $result = $this->msql->Select($query);
            $userPrivs = array();
            foreach ($result as $value){
                $userPrivs[$value['priv_name']] = true;
            }

            return $userPrivs;
    }
    public function getUserContacts($id_user = null){
        
        $t = "SELECT privs.priv_name FROM privs2roles JOIN users USING(id_role) 
                      JOIN privs USING(id_priv) 
                      WHERE id_user = '%d'";

            $query  = sprintf($t, $id_user);
            $result = $this->msql->Select($query);
            $userPrivs = array();
            foreach ($result as $value){
                $userPrivs[$value['priv_name']] = true;
            }

            return $userPrivs;
    }



    
    //
    // Проверка активности пользователя
    // $id_user		- идентификатор
    // результат	- true если online
    //
    public function IsOnline($id_user){		
            if ($this->onlineMap == null){	    
                $t = "SELECT DISTINCT id_user FROM sessions";		
                $query  = sprintf($t, $id_user);
                $result = $this->msql->Select($query);

                foreach ($result as $item) {
                $this->onlineMap[$item['id_user']] = true;
                }
            }

            return ($this->onlineMap[$id_user] != null);
    }



            //
    // Получение id текущего пользователя
    // результат	- UID
    //
    public function GetUid() {	
        // Проверка кеша.
        if ($this->uid != null) {
            return $this->uid;
        }

        // Берем по текущей сессии.
        $sid = $this->GetSid();

        if ($sid === null) {
            return null;
        }

        $t = "SELECT id_user FROM sessions WHERE sid = '%s'";
        $query = sprintf($t, mysql_real_escape_string($sid));
        $result = $this->msql->Select($query);

        // Если сессию не нашли - значит пользователь не авторизован.
        if (count($result) === 0) {
            return null;
        }

        // Если нашли - запоминм ее.
        $this->uid = intval($result[0]['id_user']);
        return $this->uid;
    }

//
// Функция возвращает идентификатор текущей сессии
// результат	- SID
//
private function GetSid(){
        // Проверка кеша.
        if ($this->sid != null) {
            return $this->sid;
        }

        // Ищем SID в сессии.
        $sid = $_SESSION['sid'];

        // Если нашли, попробуем обновить time_last в базе. 
        // Заодно и проверим, есть ли сессия там.
        if ($sid != null)
        {
                $session = array();
                $session['time_last'] = date('Y-m-d H:i:s'); 			
                $t = "sid = '%s'";
                $where = sprintf($t, mysql_real_escape_string($sid));
                $affected_rows = $this->msql->Update('sessions', $session, $where);

                if ($affected_rows == 0){
                    $t = "SELECT count(*) FROM sessions WHERE sid = '%s'";		
                    $query = sprintf($t, mysql_real_escape_string($sid));
                    $result = $this->msql->Select($query);

                    if ($result[0]['count(*)'] === 0) {
                        $sid = null;
                    }
                }			
        }		

        // Нет сессии? Ищем логин и md5(пароль) в куках.
        // Т.е. пробуем переподключиться.
        if ($sid == null && isset($_COOKIE['login'])) {
                $user = $this->GetByLogin($_COOKIE['login']);

                if ($user != null && $user['password'] == $_COOKIE['password']) {
                    $sid = $this->OpenSession($user['id_user']);
                }
        }

        // Запоминаем в кеш.
        if ($sid != null)
                $this->sid = $sid;

        // Возвращаем, наконец, SID.
        return $sid;		
    }


    //
    // Генерация случайной последовательности
    // $length 		- ее длина
    // результат	- случайная строка
    //
    private function GenerateStr($length = 10) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
        $code = "";
        $clen = strlen($chars) - 1;  

        while (strlen($code) < $length) 
        $code .= $chars[mt_rand(0, $clen)];  

        return $code;
    }

    public function getUsers($roles = 0, $id_user = 0){
        $query = "SELECT u.id_user, u.login, u.user_name, u.user_second_name, u.id_role, u.exercises, u.diagnosis, "
                . "r.description, c_i.contact, c_i.id_info, c_i.contact_dest "
                . "FROM users u LEFT JOIN roles r USING(id_role) LEFT JOIN contact_infos c_i "
                . "ON c_i.contact_info=u.id_user WHERE 1=1 ";
        $pr_key = 'id_user';
        $container = 'contacts';
        $unique_columns = array('contact', 'id_info', 'contact_dest');
        if($roles !== 0){
       
            $t =  "AND id_role = '%d'";   
     
            $query .= sprintf($t, mysql_real_escape_string($roles));
        }
        if($id_user !== 0){
           $t =  "AND id_user = '%d'";
           $query .= sprintf($t, mysql_real_escape_string($id_user));
        }        
        $result = $this->msql->SelectGroupByPrKey($query, $pr_key, $container, $unique_columns);
        foreach ($result as $key => $value) {

            $result[$key]['exercises'] = $this->validateExercises($value['exercises']);
        }


        return $result;
     
    }
 
    public function getRoles($id_user = 0){
        $query = "SELECT id_role, description FROM roles";
        if($id_user){
            $query .= " WHERE id_user='%d'";  
            $query = sprintf($query, mysql_real_escape_string($id_user));
        }
        $result = $this->msql->Select($query);
        $roles = array();
        foreach ($result as $value) {
            $roles[$value['id_role']] = $value['description'];
        }
        return $roles;
    }
    public function getRoleByID($id_user){
        $t = "SELECT u.id_user, r.id_role, r.description FROM users u LEFT JOIN roles r USING(id_role) "
                . "WHERE u.id_user='%d'";
        $query = sprintf($t, mysql_real_escape_string($id_user));
        $result = $this->msql->Select($query);
        $roles = array('id_role'  => $result[0]['description']);
        return $roles;
    }
    public function getDiagnosis($id_user){
        $t = "SELECT id_user, diagnosis FROM users WHERE id_user='%d'";
        $query = sprintf($t, mysql_real_escape_string($id_user));
        $result = $this->msql->Select($query);
        $roles = array('diagnosis'  => $result[0]['diagnosis']);
        return $roles;
    }
    public function saveDiagnosis($request){
        $tmp = "id_user='%d'";
        $where = sprintf($tmp, $request['id_user']);
        $object = array('diagnosis' => $request['diagnosis']);
        $result = $this->msql->Update('users', $object, $where);
        return $result;
    }
    public function saveContact($request){
        if(!$request['id_info']){
            $object = array('contact' => $request['contact'], 'contact_info' => $request['id_user']);
            $result = $this->msql->Insert('contact_infos', $object);
        }
        else{   
            $tmp = "id_info='%d'";
            $where = sprintf($tmp, $request['id_info']);
            $object = array('contact' => $request['contact']);
            $this->msql->Update('contact_infos', $object, $where);
            $result = $request['id_info'];
        }
        return $result;
    }
    public function getContact($request){
        $t = "SELECT id_info, contact FROM contact_infos WHERE id_info='%d'";
        $query = sprintf($t, mysql_real_escape_string($request['id_info']));
        $result = $this->msql->Select($query);
        $contact = array('id_info' => $result[0]['id_info'], 'contact'  => $result[0]['contact']);
        return $contact;
    }
    public function changeUserRole($request){
        $tmp = "id_user='%d'";
        $where = sprintf($tmp, $request['id_user']);
        $object = array('id_role' => $request['id_role']);
        $result = $this->msql->Update('users', $object, $where);
        return $result;
    }

    
    public function addUserEx($id_user, $exercises){
            $tmp = "id_user='%d'";
            $where = sprintf($tmp, $id_user);
            $object = array('exercises' => trim($exercises));
        
        $result = $this->msql->Update('users', $object, $where, true, true);
        


        return $result;
    }
    public function getUserEx($id_user){
        $t = "SELECT exercises FROM users WHERE id_user = '%d'";
        $query .= sprintf($t, mysql_real_escape_string($id_user));
        $result = $this->msql->Select($query);
        $ex = $this->validateExercises($result[0]['exercises']);

        return $ex;
    }
    
    private function validateExercises($str_exer) {
        $temp = explode('==||##', $str_exer);
        $result = array();
        for($i = 2; $i < count($temp); $i += 4){
            $result[] = array('id' => $temp[$i], 'ex' => $temp[$i + 1],
                'count' => $temp[$i + 2], 'repeat' => $temp[$i + 3]);
        }
        return $result;
    }
        //
    // Открытие новой сессии
    // результат	- SID
    //
    private function OpenSession($id_user) {
        // генерируем SID
        $sid = $this->GenerateStr(10);

        // вставляем SID в БД
        $now = date('Y-m-d H:i:s'); 
        $session = array();
        $session['id_user'] = $id_user;
        $session['sid'] = $sid;
        $session['time_start'] = $now;
        $session['time_last'] = $now;				
        $this->msql->Insert('sessions', $session); 

        // регистрируем сессию в PHP сессии
        $_SESSION['sid'] = $sid;				

        // возвращаем SID
        return $sid;	
    }

}
