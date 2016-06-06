<?php


 //   $sender = new Sender(); 
//    $sender->start();
//    echo $sender->getLog();
   
class M_Sender {

    private $website;
    private $send_to;
    private $from;
    private $code;

    private $logPage;

    private $status;     
    public function __construct($send_to, $code)
    {

        $this->logPage = '';
        /***************************************************
                E-mail settings
        ****************************************************/
        $this->website = 'mind-body.ho.ua';       // Your site's domain (without www. part)
        $this->send_to = $send_to;  // backup file will be sent to?
        $this->from = 'mind-body@' . $this->website;    // some hosting providers won’t let you send backups from invalid e-mail address
        $this->code = $code; 

    }
/*
 * 
 *
 */
    

    private function send_mail() {
        $html = '<html><head></head><body>'
                . '<DIV>Активация аккаунта MIND_BODY<DIV> <a href="' . $this->website . '/activate/' . $this->code . '"></a>'
                . '</body></html>';
        
        
        
        $EOL = "\r\n";

        $message = "Для активации перейдите по данной ссылке $EOL http://$this->website/activate/$this->code $EOL "
                . "Если ссылка не отработала $EOL вы можете вставить регистрационный код $this->code вручную "
                . "в окне активации на странице $EOL  http://$this->website/activate";
        $subject = 'Активация аккаунта Mind-Body';

        $boundary = '_1_' . md5(date('r', time())) . '_2_'; // рандомное число
        $headers = "From: " . $this->from . $EOL; // см. наиболее часто используемые заголовки
        $headers .= "Reply-To: " . $this->from . $EOL;
        $headers .= "MIME-Version: 1.0" . $EOL;
        $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"";

        $body = "--$boundary" . $EOL; 

        $body .= "Content-Type: text/plain; charset=\"utf-8\"" . $EOL;
        $body .= "Content-Transfer-Encoding: 7bit" . $EOL . $EOL;
        $body .= $message . $EOL; 

        $body .= "--$boundary" . $EOL;

        $body .= "Content-Type: text/html; charset=\"utf-8\"" . $EOL;
        $body .= "Content-Transfer-Encoding: base64" . $EOL . $EOL;
        $body .= chunk_split(base64_encode($html)) . $EOL . $EOL;


        $body .= "--$boundary--";

        $this->logPage .=  $subject . '';
        if ($this->status = mail($this->send_to, $subject, $body, $headers)) {
                $sent = 'Yes';
                
        }
        else {
            $sent = 'NO';
            
        }

        $this->logPage .=  'Sent? ' . $sent . $EOL;    
    }




public function start() {
    error_reporting(E_ALL);
    $this->send_mail();
}
public function getLog() {
    return $this->logPage;
}
public function getStatus() {
    return $this->status;
}



}
