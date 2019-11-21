<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {


	public function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		
	 	//print_r($this->session->userdata);

	 	//print_r($teste);
	 	$dados = array(
	 		'funcao' =>'index',
	 		'controller' => 'simplex'
	 		);
	 	if (!empty($this->session->userdata('variavel'))) {
	 		$this->session->unset_userdata('variavel');
	 		$this->session->unset_userdata('restricao');
	 	}
	 	$this->template->load('template/template', 'home/home', $dados);
		
		
	}

	public function teste()
	{
		
	 	//print_r($this->session->userdata);

	 	//print_r($teste);
	 	$dados = array(
	 		'funcao' =>'index',
	 		'controller' => 'simplex'
	 		);
	 	$this->template->load('template/template', 'home/teste', $dados);
		
		
	}


}
