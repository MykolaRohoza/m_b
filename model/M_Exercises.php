<?php


//
// Менеджер пользователей
//
class M_Exercises
{	
    private static $instance;	// экземпляр класса
    private $msql;				// драйвер БД


    //
    // Получение экземпляра класса
    //
    public static function Instance()
    {
        if (self::$instance == null) {
            self::$instance = new M_Exercises();
        }

        return self::$instance;
    }

    //
    // Конструктор
    //
    private function __construct()
    {
            $this->msql = M_MSQL::Instance();


    }

    


    
    
    public function saveExercises($id_ex, $new_ex){
        $object = array('exercise' => $new_ex);
        $table = 'exercises';
        if ($id_ex != 0){
            $object['id_exercise'] = $id_ex;
            $message = $this->msql->Update($table, $object, $where, true, true);
        }
        else{
            $message = $this->msql->Insert($table, $object) ;
            if(is_numeric($message)){
                $message =  'Упражнение успешно добавлено';
                
            }
            else{
                
                $message =  'Упражнение не было добавлено';
            }
        }
        return message;
    }
    public function getExercises(){
        $query  = "SELECT id_exercise, exercise FROM exercises ORDER BY exercise";
        $result = $this->msql->Select($query);
        $exercises = array();
        foreach ($result as $value) {    
            $exercises[$value['id_exercise']] = $value['exercise'];
        }
        return $exercises;
    }
    public function deleteExercises($id_ex){
        $tmp = "id_exercise='%d'";
        $where = sprintf($tmp, $id_ex);
        return ($this->msql->Del('exercises', $where) > 0);
    }


    public function delete($table, $where){    
        return ($this->msql->Del($table, $where) > 0);
    }


    
    private function validateArtCont($assocRes, $lang) {
        $articles = array();      
        if(count($assocRes) > 0){
            foreach ($assocRes as $article){
                $tmp = array();  
                foreach ($article as $key => $value) {
                    if(preg_match('~title~', $key) || preg_match('~text~', $key) ){
                        $fineKey = str_replace('_' . $lang, '', $key);

                    }
                    else{
                        $fineKey = $key;
                    }

                    $tmp[$fineKey] = $value;
                }
                $articles[] = $tmp;
            }
        }
        return $articles;
    }
    
    
}