<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function login()
    {
        $this->load->view('auth/login');
    }

    public function registro()
    {
        $this->load->view('auth/registro');
    }

    public function cambiar_password()
    {
        $this->load->view('auth/cambiar-password');
    }

    public function olvide_password()
    {
        $this->load->view('auth/olvide-password');
    }
}