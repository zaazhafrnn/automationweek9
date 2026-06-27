<?php

namespace App\Controllers;

use App\Core\Controller;

class LandingController extends Controller
{
    public function index()
    {
        $this->view('landing', [], null);
    }
}
