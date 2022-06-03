<?php
namespace Controller;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class SendController{

     private function PrintMessages(){
    $db = json_decode(file_get_contents( dirname(__DIR__,2).'/public/messages.json'));
    foreach($db->messages as $it){
        echo date('m/d/Y H:i:s', $it->date) . ' ' . $it->username . ' ' . $it->message;
        ?>
        <br></br>
        <?php
        }
    }

    private function AddMessage($message){
        $username = $_COOKIE['username'];
        if ($message != ''){
            $db = json_decode(file_get_contents( dirname(__DIR__,2).'/public/messages.json'));
            $info = (object) ['date'=>time()+ 60*60*10, 'username' => $username, 'message' => $message];
            $db->messages[] = $info;
            file_put_contents("messages.json", json_encode($db));
        }
        else
            echo 'Ur message is empty';
    }

    public function run(){
        $loader = new FilesystemLoader(dirname(__DIR__, 2).'/templates/');
        $twig = new Environment($loader);
        $template = $twig->load('send.html.twig');
        echo $template->render();
        if(isset($_POST['send'])){
            $message = $_POST['message'];
            $this->AddMessage($message);
        }
        $this->PrintMessages();
    }
}
?>