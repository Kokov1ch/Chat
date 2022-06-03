<?php
namespace Controller;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use PDO;
class HomeController{

    private function getArr($query): array
    {
        $db = new PDO ('mysql:host=localhost:3306;dbname=chat', 'root','dfdb7kjy3000');
        $pre = $db->prepare($query);
        $pre->execute();
        return $pre->fetchAll();
    }
    private function getUsers(): array
    {
        $result = array();
        foreach($this->getArr('SELECT username, password from users') as $it){
            $user=[
                'name'=>$it['username'],
                'pass'=>$it['password']
            ];
            $result[] = $user;
        }
        return $result;
    }
    private function getMessages(): array{
        $result = array();
        foreach($this->getArr ('SELECT user_id, (SELECT username from users where users.user_id = messages.user_id)
    as username, date, text from messages;')as $it){
            $user=[
                'id'=>$it['user_id'],
                'name'=>$it['username'],
                'date'=>$it['date'],
                'info'=>$it['text']
            ];
            $result[] = $user;
        }
        return $result;
    }
    private function PrintMessages(){
        /*
    $db = json_decode(file_get_contents( dirname(__DIR__,2).'/public/messages.json'));;
    foreach($db->messages as $it){
        echo date('m/d/Y H:i:s', $it->date) . ' ' . $it->username . ' ' . $it->message;
        ?>
        <br></br>
        <?php
        }
        */
        foreach ($this->getMessages() as $message){
            echo $message['name'].' '. $message['date']. ' '. $message['info'];
            ?>
            <br></br>
            <?php
        }
    }
    private function CheckUser(): bool
    {
            foreach($this->getUsers() as $user){
                if ($user['name'] == $_POST['username'] && $user['pass'] == $_POST['password']){
                    return true;
                }
            }
            return false;
    }
    private function login(){
        $sucs_log = new Logger('successfully_authorized');
        $sucs_log->pushHandler(new StreamHandler('../var/logs/successfully_authorized.log', Logger::INFO));
        $denied_log = new Logger('denied_log');
        $denied_log->pushHandler(new StreamHandler('../var/logs/denied_authorization.log', Logger::INFO));

        if (isset($_POST['btn'] )&& isset($_POST['username'])&& isset($_POST['password'])) {
            if ($this->CheckUser()){
                $username = $_POST['username'];
                setcookie('username', $username, time() + 120);
                $sucs_log->info('user: '.$_POST['username']);
                header('Location: send');
            }
            else {
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