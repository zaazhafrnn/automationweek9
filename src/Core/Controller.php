<?php

namespace App\Core;

class Controller
{
    protected function view($view, $data = [])
    {
        extract($data);

        $viewFile = BASE_PATH . '/src/views/' . $view . '.php';

        if (file_exists($viewFile)) {
            ob_start();
            require $viewFile;
            $content = ob_get_clean();

            require BASE_PATH . '/src/views/layouts/main.php';
        } else {
            die("View $view not found.");
        }
    }

    protected function redirect($url)
    {
        header("Location: $url");
        exit();
    }
}
