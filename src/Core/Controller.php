<?php

namespace App\Core;

use App\Utils\Session;

class Controller
{
    protected function view($view, $data = [], $layout = 'main')
    {
        extract($data);

        $viewFile = BASE_PATH . '/src/views/' . $view . '.php';

        if (file_exists($viewFile)) {
            ob_start();
            require $viewFile;
            $content = ob_get_clean();

            if ($layout) {
                require BASE_PATH . '/src/views/layouts/' . $layout . '.php';
            } else {
                echo $content;
            }
        } else {
            die("View $view not found.");
        }
    }

    protected function redirect($url)
    {
        http_response_code(302);
        header("Location: $url");
        exit();
    }

    protected function requireAuth()
    {
        if (!Session::get('user_id')) {
            $this->redirect('/login');
        }
    }

    protected function requireAdmin()
    {
        $this->requireAuth();
        if (Session::get('role') !== 'admin') {
            $this->redirect('/dashboard');
        }
    }

    protected function requireGuest()
    {
        if (Session::get('user_id')) {
            $this->redirect(Session::get('role') === 'admin' ? '/admin/dashboard' : '/dashboard');
        }
    }
}
