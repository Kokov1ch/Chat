<?php
namespace Controller;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
class HomeController{

    private function PrintMessages(){
    $db = json_decode(file_get_contents( dirname(__DIR__,2).'/public/messages.json'));;
    foreach($db->messages as $it){
        echo date('m/d/Y H:i:s', $it->date) . ' ' . $it->username . ' ' . $it->message;
        ?>
        <br></br>
        <?php
        }
    }
    private function login(){
        $users = array(
            'admin' => '123',
            'fedor' => 'fet',
        );
        $sucs_log = new Logger('successfully_authorized');
        $sucs_log->pushHandler(new StreamHandler('../var/logs/successfully_authorized.log', Logger::INFO));
        $denied_log = new Logger('denied_log');
        $denied_log->pushHandler(new StreamHandler('../var/logs/denied_authorization.log', Logger::INFO));
        if (isset($_POST['btn'] )&& isset($_POST['username'])&& isset($_POST['password'])) {
            if (array_key_exists($_POST['username'], $users) && in_array($_POST['password'],$users)){
                $username = $_POST['username'];
                $password = $_POST['password'];
                setcookie('username', $username, time() + 120);
                $sucs_log->info('user: '.$_POST['username']);
                header('Location: send');
            } else {
                $denied_log->info('user: '. $_POST['username']);
                echo "authentication error";
            }
        }
    }
   public function run(){
       $loader = new FilesystemLoader(dirname(__DIR__, 2).'/templates/');
       $twig = new Environment($loader);
       $template = $twig->load('home.html.twig');
       echo $template->render();
       $this->PrintMessages();
       $this->login();
    }
}
?>