<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Utils\Session;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Must be logged in
        if (!Session::get('user_id')) {
            $this->redirect('/login');
        }

        // Must be an admin
        if (Session::get('role') !== 'admin') {
            $this->redirect('/dashboard');
        }

        $this->view('admin/dashboard', [
            'user_name' => Session::get('user_name'),
            'page_title' => 'Dasbor'
        ], 'admin');
    }

    public function members()
    {
        if (!Session::get('user_id') || Session::get('role') !== 'admin') {
            $this->redirect('/login');
        }

        $userModel = new \App\Models\User();
        $members = $userModel->getAllMembers();

        $this->view('admin/members', [
            'members' => $members,
            'page_title' => 'Anggota'
        ], 'admin');
    }

    public function teams()
    {
        if (!Session::get('user_id') || Session::get('role') !== 'admin') {
            $this->redirect('/login');
        }

        $teamModel = new \App\Models\Team();
        $teams = $teamModel->getAllTeams();

        $this->view('admin/teams', [
            'teams' => $teams,
            'page_title' => 'Tim'
        ], 'admin');
    }
}
