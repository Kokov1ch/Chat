<?php
namespace Controller;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use PDO;
class SendController{
    private function getArr($query): array
    {
        $db = new PDO ('mysql:host=localhost:3306;dbname=chat', 'root','***********');
        $pre = $db->prepare($query);
        $pre->execute();
        return $pre->fetchAll();
    }
    private function getMessages(): array{
        $result = array();
        foreach($this->getArr ('SELECT user_id, (SELECT username from users where users.user_id = messages.user_id)
    as username, date, text from messages')as $it){
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
         foreach ($this->getMessages() as $message){
             echo $message['name'].' '. $message['date']. ' '. $message['info'];
             ?>
             <br></br>
             <?php
         }
    }
    private function AddMessage($message){
        $username = $_COOKIE['username'];
        if ($message != ''){
            $db = new PDO ('mysql:host=localhost:3306;dbname=chat', 'root','************');
            $first_q = $db->prepare("SELECT user_id from users where username = '$username'");
            $first_q->execute();

            $arr = $first_q->fetchAll();
            foreach ($arr as $it) {
                $result = [
                    'id' => $it['user_id']
                ];
                $id = $result;
                }
            $second_q = $db->prepare('INSERT INTO `chat`.`messages` (`user_id`, `date`, `text`) VALUES (:id, :date, :text)');
            $date = date("Y-m-d H:i:s");
            $second_q->bindParam(':date', $date, PDO::PARAM_STR);
            $second_q->bindParam(':text', $message, PDO::PARAM_STR);
            $second_q->bindParam(':id', $id['id'], PDO::PARAM_STR);
            $second_q->execute();
        }
        else{
            echo 'Ur message is empty'."<br></br>";
        }
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
