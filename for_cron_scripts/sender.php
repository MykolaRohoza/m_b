<?php  
require_once ('/virt/homes/f-i/htdocs/model/M_Lib.php');    



    $sender = new Sender(); 
    $sender->start();
    echo $sender->getLog();

   // 	/usr/local/bin/php -f htdocs/for_cron_scripts/sessions.php > /dev/null 2>&1
   /*    	/usr/local/bin/mysqldump -u f-i -h s2.ho.ua --password=0okmnji9 karevar -c >     /virt/homes/f-i/htdocs/dumps/f-i.sql.gz */
   //		 	/usr/local/bin/php -f htdocs/for_cron_scripts/sender.php

 

   
   
class Sender {

    private $website;
    private $send_to;
    private $from;
    
    private $full_path;
    private $file;
    private $delete_backup;    
    
    private $logPage;

            
    public function __construct()
    {

        $this->logPage = '';
        /***************************************************
                E-mail settings
        ****************************************************/
        $this->website = 'f-i.ho.ua';       // Your site's domain (without www. part)
        $this->send_to = 'rohoza_mykola@rambler.ru';  // backup file will be sent to?
        $this->from = 'f-i@' . $this->website;    // some hosting providers won’t let you send backups from invalid e-mail address



        /***************************************************
            Misc options
        ****************************************************/

        $this->full_path   = '/virt/homes/f-i/htdocs/dumps/'; 

        // Full path to folder where you are running the script, usually "/home/username/public_html"
        // (mt) servers have something like "/nfs/c01/h01/mnt/12345/domains/yourdomain.mobi/html/tools/backup2mail"

        $this->file = 'f-i'; 

        $this->delete_backup	= true;							
            // delete gziped database from server after sending?
						
        // send follow-up report?
            // - true = send log file to an e-mail after each backup transfer
            // - false = don't send log file, just leave it on the server   
    }

private function date_stamp() {
	$backup_date = date('d-m-Y[H-i]');
	$this->logPage .=  'Database backup date: ' . $backup_date . ' ' . "\n";
	return $backup_date;
}

private function renameDB() {
    
    $db_backup_filename = $this->file . '_' . $this->date_stamp() . '.sql.gz';
    $susexfuly = copy($this->full_path . $this->file . '.sql.gz', $this->full_path . $db_backup_filename);
    //$susexfuly = rename($this->full_path . $this->file . '.sql.gz', $this->full_path . $db_backup_filename);
    if($susexfuly){
        $this->logPage .=  'Database backup file: ' . $db_backup_filename . "\n";   
    }
    else {
        $this->logPage .=  'Database backup file: ' . 'not found, check path to directory' . '  ' . "\n";
        die($this->logPage);
    }
    return $db_backup_filename;
}

private function send_attachment($file) {
    /*
    //$message = 'База ннада?';
    $sent       = 'No';
    $subject    = 'MySQL backup - db dump  [' . $this->website . '] ';
    $EOL = "\r\n";
    $boundary   = '===========' . md5(uniqid(time())) . '=========';
    $mailer     = 'Отправлено с сайта Каревар';
    $message = 'Сабж';

    $headers  = 'From: ' . $this->from . " " . $EOL;
    $headers  = 'From: ' . $this->send_to . " " . $EOL;
    $headers .= 'MIME-Version: 1.0'  . $EOL;
    $headers .= 'Content-type: multipart/mixed;';
    $headers .= ' boundary="' . $boundary . '";' . $EOL;
    $body = $EOL . $EOL . '--' . $boundary . $EOL . $EOL;

    $body .= 'Content-Type: text/plain; charset="utf-8"' . $EOL;
    $body .= 'Content-Transfer-Encoding: 7bit' . $EOL;
    $body .= $mailer;
    $body .= $message;
    
    $body .= $EOL . $EOL . '--' . $boundary . $EOL;
    
    $body .= 'Content-Transfer-Encoding: base64' . $EOL; // x2-
    $body .= 'Content-Disposition: attachment;' . $EOL;
    $body .= 'Content-Type: Application/Octet-Stream; name="' . $file . "\"" . $EOL;
    $body .= 'filename="' . $file . "\"" . $EOL;
    $body .= chunk_split(base64_encode(implode('', file($this->full_path . $file)))) . " " . $EOL . $EOL;


    $body .= '--' . $boundary . '--' . $EOL; */


  $EOL = "\r\n";


  $to =     $this->send_to; // адрес почты получателя
  $from =   $this->from; // адрес почты отправителя
  $subject = 'MySQL backup - db dump  [' . $this->website . '] ';
  $message_1 = "База ннада?";
  M_Lib::addLog(file_get_contents($this->full_path . $file));
  //$attachment = chunk_split(base64_encode(file_get_contents($_FILES['fileFF']['tmp_name'])));
  $attachment = chunk_split(base64_encode(implode('', file($this->full_path . $file))));

  $filename = $file;
  M_Lib::addLog($filename);
  $filetype = 'text/plain';

  $boundary = md5(date('r', time())); // рандомное число

  $headers = "From: " . $from . $EOL; // см. наиболее часто используемые заголовки
  $headers .= "Reply-To: " . $from . $EOL;
  $headers .= "MIME-Version: 1.0" . $EOL;
  $headers .= "Content-Type: multipart/mixed; boundary=\"_1_$boundary\"";

    $message="
--_1_$boundary 
Content-Type: text/plain; charset=\"utf-8\"
Content-Transfer-Encoding: 7bit

            $message_1

--_1_$boundary
Content-Disposition: attachment; name=\"$filename\"
Content-Type: Application/Octet-Stream; name=\"$filename\"
Content-Transfer-Encoding: base64

$attachment

--_1_$boundary
Content-Type: text/plain; charset=\"utf-8\"
Content-Transfer-Encoding: 7bit

Послесловия

--_1_$boundary--";
    
//Content-Type: Application/Octet-Stream;
    
    $this->logPage .=  $subject . '';
    if (mail($to, $subject, $message, $headers)) {
            $sent = 'Yes';		
            $this->logPage .=  'Backup file sent to ' . $to . '. ' . "\n";
            if ($this->delete_backup) {
                    unlink($this->full_path . $file);
                $this->logPage .=  'Backup file REMOVED from disk. ' . "\n";
            } 
            else {
                    $this->logPage .=  'Backup file LEFT on disk. ' . "\n";
            }

    }
    else {
        $this->logPage .=  ' Database not sent! Please check your mail settings. ' . "\n";
        $sent = 'NO';
    }

    $this->logPage .=  'Sent? ' . $sent . $EOL;
/*    $send_mail = new Send_mail();
 
$send_mail->email($this->send_to)  // Адресат (можно массив адресов)
          ->from_name('Каревар')  // Имя отправителя
          ->from_mail($this->from)   // Адрес отправителя
          ->subject($subject)  // Тема сообщения
          ->message($message) // Тело сообщения
          ->files($this->full_path . $file) // Путь до прикрепляемого файла (можно массив)
          ->charset() // Кодировка (по умолчанию utf-8)
          ->time_limit()  // set_time_limit (по умолчанию == 30с.)
          ->content_type()  // тип сообщения (по умолчанию 'plain') 'html'
          ->send(); // Отправка почты  */
    
    
}




public function start() {
    error_reporting(E_ALL);
    $this->logPage .= " \n " . ' Setup ' . " \n ";
    $file = $this->renameDB();
    $this->send_attachment($file);
}
public function getLog() {
    return $this->logPage;
}
#' from this folder’s .htaccess file NOW.';

/*
    $sent       = 'No';
    $EOL = "\r\n";
    $subject    = 'MySQL backup - db dump  [' . $this->website . '] ' . $EOL;
    $boundary   = md5(uniqid(time()));
    $mailer     = 'Отправлено с сайта Каревар';

    $body = 'Database backup file: ' . $EOL . ' - ' . $file . " " . $EOL;
    $body .= '---' . $EOL . $mailer;

    $headers  = 'From: ' . $this->from . " " . $EOL;
    $headers .= 'MIME-Version: 1.0' . " $EOL";
    $headers .= 'Content-type: multipart/mixed; boundary="' . $boundary . '";' . $EOL;
    $headers .= 'This is a multi-part message in MIME format. ';
    $headers .= 'If you are reading this, then your e-mail client probably doesn\'t support MIME.' . $EOL;
    $headers .= $mailer . $EOL;
    $headers .= '--' . $boundary . $EOL;

    $headers .= 'Content-Type: text/plain; charset="utf-8"' . $EOL;
    $headers .= 'Content-Transfer-Encoding: 7bit' . $EOL;
    $headers .= $body . " " . $EOL;
    $headers .= '--' . $boundary . $EOL;

    $headers .= 'Content-Disposition: attachment;' . $EOL;
    $headers .= 'Content-Type: Application/Octet-Stream; name="' . $file . "\"" . $EOL;
    $headers .= 'Content-Transfer-Encoding: base64' . $EOL; // x2-
    $headers .= chunk_split(base64_encode(implode('', file($this->full_path . $file)))) . " " . $EOL;
    $headers .= '--' . $boundary . '--' . $EOL; 
  
  
  
 */
/*
     $sent       = 'No';
    $subject    = 'MySQL backup - db dump  [' . $this->website . '] ';
    $EOL = "\r\n";
    $boundary   = '===========' . md5(uniqid(time())) . '=========';
    $mailer     = 'Отправлено с сайта Каревар';
    $message = 'Сабж';

    $headers  = 'From: ' . $this->from . " " . $EOL;
    $headers  = 'From: ' . $this->send_to . " " . $EOL;
    $headers .= 'MIME-Version: 1.0'  . $EOL;
    $headers .= 'Content-type: multipart/mixed;';
    $headers .= ' boundary="' . $boundary . '";' . $EOL;
    $body = $EOL . $EOL . '--' . $boundary . $EOL . $EOL;

    $body .= 'Content-Type: text/plain; charset="utf-8"' . $EOL;
    $body .= 'Content-Transfer-Encoding: 7bit' . $EOL;
    $body .= $mailer;
    $body .= $message;
    
    $body .= $EOL . $EOL . '--' . $boundary . $EOL;
    
    $body .= 'Content-Transfer-Encoding: base64' . $EOL; // x2-
    $body .= 'Content-Disposition: attachment;' . $EOL;
    $body .= 'Content-Type: Application/Octet-Stream; name="' . $file . "\"" . $EOL;
    $body .= 'filename="' . $file . "\"" . $EOL;
    $body .= chunk_split(base64_encode(implode('', file($this->full_path . $file)))) . " " . $EOL . $EOL; 
 
 */




}