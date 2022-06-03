<?php
namespace Routes;
use Controller\HomeController;
use Controller\SendController;
class Router
{
    private $uri;
    function __construct()
    {
        $this->uri = $_SERVER['REQUEST_URI'];
    }

    function execute()
    {
        switch ($this->uri) {
            case '/logout':
            {
                setcookie('username', '');
                header('Location: /');
                break;
            }
            case '/':
            {
                $response = new HomeController();
                $response->run();
                break;
            }
            case '/send':
            {
                $response = new SendController();
                $response->run();
                break;
            }
        }
    }
}

?>
