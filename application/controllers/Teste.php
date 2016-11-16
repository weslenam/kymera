<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Teste extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index(){

		$data['nome'] = "Franciel";
		$data['id'] = "38";

		// $this->kymera->tb('nome')->set( $data )->doit();
		// $this->kymera->tb('nome')->set($data)->insert();

		$config['table'] = 'nome';
		$this->kymera->config( $config );

		// $this->kymera->set( $data );
		// $this->kymera->delete();

		// echo "<pre>";
		print_r( $this->kymera->tb('nome')->get( 'nome' ) );

		// $this->kymera->tb('nome')->delete( array('id'=> $data['id'] ) );
		// $this->kymera->tb('nome')->set( $data )->doit('delete');

	}

}

/* End of file  */
/* Location: ./application/controllers/ */
