<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Kymera (nome de batismo)
 *
 * Trata-se de um biblioteca gestora de modelos a partir da tabela. 
 * Sendo assim o uso desta biblioteca requer conexão com o banco de 
 * dados.	 ;)
 *
 * Suporte a chaining  		;)
 * 
 * @package	Kymera
 * @author	Weslen Augusto Marconcin
 * @version	1.0.0
 * @mail weslenam@gmail.com
 * @date 2016/11/16
 */

class Kymera {

	private $table;
	private $field_key; 
	private $keyname;	
	private $kymera; // active record this->db nativo do CI
	private $data;

	/**
	 * __construct
	 *
	 * @param	array(	'table' => string,
	 * 					'json'	=> boolean)
	 * @return	void
	 */
	public function __construct( $config = null ){ 

		if( !empty( $config ) ){
			$this->config( $config );
		}
		else{
			$this->table = null;
		}

		$this->kymera = & get_instance();
		$this->data = null;

	}


	/**
	 * tb
	 *
	 * @param	array(	'table' => string )
	 * @return	class Kymera
	 */
	public function tb( $table ){

		$this->table = $table;
		return $this;

	}

	/**
	 * config
	 *
	 * @param	array()
	 * @return	class Kymera
	 */
	public function config( $config ){

		if( array_key_exists( 'table' , $config ) ){
			$this->table = $config['table'];
		}
		else{
			$this->table = null;				
		}
		if( array_key_exists( 'json' , $config ) ){
			$this->json = $config['json'];
		}
		else{
			$this->json = false;				
		}		
		if( array_key_exists( 'keyname' , $config ) ){
			$this->keyname = $config['keyname'];
		}
		else{
			$this->keyname = 'id';				
		}	
		return $this;

	}

	/**
	 * set
	 *
	 * @param	array()
	 * @return	class Kymera
	 */
	public function set( $data ){

		$this->data = $data;
		return $this;

	}

	/**
	 * get
	 *
	 * @param	array(	'field'	=> string,
	 * 					'join'	=> string or array(array(	'tabela' => ,
	 * 														'comparacao' => ,
	 * 														'tipo' => )) ,
	 * 					'where'	=> string or array,
	 * 					'json'	=> boolean )
	 * @return	array | json
	 */	
	public function get($field = "*", $join = null,  $where = null, $json = false){

		if( $json ){
			$this->json = true;
		}
	
		if( empty( $field ) ){
			$field = "*";
		}

		$this->kymera->db->select( $field );
		$this->kymera->db->from( $this->table );

		if( !empty( $join ) ){
			if( is_array( $join ) ){
				foreach ($join as $j) {
					$this->kymera->db->join( $j['tabela'] , 
									 $j['comparacao'] , 
									 ( array_key_exists( 'tipo' , $j ) ? $j['tipo'] : null ) );
				}
			}
			else{
				$this->kymera->db->join( $join );
			}
		}

		if( !empty( $where ) ){
			if( is_array( $where ) ){
				foreach ($where as $posicao => $valor) {
					$this->kymera->db->where( $posicao , $valor);
				}
			}
			else{
				$this->kymera->db->where( $where );
			}
		}

		$result = $this->kymera->db->get()->result_array();
		if( !empty( $result ) ){

			if( $this->json ){
				return json_encode( $result );
			}
			else{
				return $result;
			}

		}
		else{
			return false;
		}

	}

	/**
	 * doit
	 *
	 * @param	string , boolean
	 * @return	boolean
	 */	
	public function doit( $action = 'crud', $return_id = false ){

		switch ( $action ) {
			case 'crud':
				if( !empty( $this->data ) AND is_array( $this->data ) ){

					if( array_key_exists($this->keyname, $this->data )){

						$id = $this->data[ $this->keyname ];
						unset( $this->data[ $this->keyname ] );
						$this->update( 	$this->data , 
										array( $this->keyname => $id ) , 
										$return_id );	

					}
					else{
						$this->insert( $this->data , $return_id );
					}

				}
				else{
					return false;
				}
				break;			
			case 'delete':
		
				$this->delete( 	array( $this->keyname => $this->data[$this->keyname] ),
								$return_id );
				break;	

			default:
				return false;			
				break;
		}

	}

	/**
	 * doit
	 *
	 * @param	array, boolean , boolean
	 * @return	boolean
	 */	
	public function insert( $data = null , $return_id = false , $batch = null ){

		if( !empty( $data ) ){
			$this->set( $data );
		}

		if( empty( $batch ) ){

			$this->kymera->db->insert( $this->table , $this->data );
			if( $return_id ){

				if( $this->json ){
					return json_encode( array($this->keyname => $this->kymera->db->insert_id() ) );
				}
				else{
					return $this->data[ $this->keyname ];
				}		
			}

		}
		else{
			$this->kymera->db->insert_batch( $this->table , $this->data );
		}

	}
	public function update( $data = null , $where = null , $return_id = false ){

		if( !empty( $data ) ){
			$this->set( $data );
		}		

		if( empty( $where ) ){
			$where = array( $this->keyname => $this->data[ $this->keyname ] );
		}

		$this->kymera->db->update( $this->table , $this->data , $where );
		if( $return_id ){
			if( $this->json ){
				return json_encode( array($this->keyname => $this->data[$this->keyname] ) );
			}
			else{
				return $this->data[$this->keyname];
			}
		}

	}
	public function delete( $where = null , $return_id = false){

		if( empty( $where ) ){
			$where = array( $this->keyname => $this->data[ $this->keyname ] );
		}

		$this->kymera->db->delete( $this->table , $where );
		if( $return_id ){
			if( $this->json ){
				return json_encode( array( $this->keyname => $this->data[ $this->keyname ] ) );
			}
			else{
				return $this->data[ $this->keyname ];
			}
		}	

	}
}