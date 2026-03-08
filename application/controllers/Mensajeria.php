<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mensajeria extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('session');
        
        // Verificar sesión
        if (!$this->session->userdata('usuario_id') && !get_cookie('usuario_id')) {
            redirect('auth/login');
        }
    }

    public function index() {
        $data['usuario_id'] = $this->session->userdata('usuario_id') ?? get_cookie('usuario_id');
        $data['nombre_usuario'] = $this->session->userdata('nombre_usuario') ?? get_cookie('nombre_usuario');
        
        $this->load->view('mensajeria/index', $data);
    }
}