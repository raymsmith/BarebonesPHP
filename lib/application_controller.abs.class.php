<?php
namespace BarebonesPHP;
abstract class ApplicationController{
	public function send_success($options=""){
		$retval = array("success"=>"1");
		if(is_array($options))
			$retval = array_merge($retval,$options);
        return $retval;
	}
	public function send_error($error=""){
		return array("success"=>"0","error"=>$error);
	}
	public function send_db_error(){
		global $db;
		$this->send_error($db->get_error());
	}
	protected function validateCollectionOptions($args){
		if( isset($args['limit']) && ( trim($args['limit']) == ""  || !is_numeric($args['limit']) || $args['limit'] < 1 ) ){
			throw new Exception("Limit is missing or improperly formatted");
		}
		if( isset($args['offset']) && ( trim($args['offset']) == ""  || !is_numeric($args['offset']) || $args['offset'] < 0 ) ){
			throw new Exception("Offset is missing or improperly formatted");
		}
		if( isset($args['order_by']) && ( trim($args['order_by']) == ""  || !isset($args['sort_by']) ) ){
			throw new Exception("Order By is improperly formatted");
		}
		if( isset($args['sort_by']) && ( !preg_match('/^(ASC|DESC)$/',strtoupper($args['sort_by']))  || !isset($args['order_by']) ) ){
			throw new Exception("Sort By is improperly formatted");
		}
		if( isset($args['filters']) && !is_array($args['filters']) ){
			throw new Exception("Filters is improperly formatted");
		}
		if( isset($args['return']) && !is_array($args['return']) ) {
			throw new Exception("Return is improperly formatted");
		}
	}
	//Builds options array for get/getrows/search
	public static function buildCollectionOptions($args){
		//echo "===args===\n";
		//print_r($args);
		//echo "==Eargs===\n";
		$limit = $offset = $order_by = $sort_by = $dist = "";
		if(isset($args['limit']))
			$limit = $args['limit'];
		if(isset($args['offset']))
			$offset = $args['offset'];
		if(isset($args['order_by']))
			$order_by = $args['order_by'];
		if(isset($args['sort_by']))
			$sort_by = $args['sort_by'];
		$options = array(
			"limit"=>$limit,
			"offset"=>$offset,
			"order_by"=>$order_by,
			"sort_by"=>$sort_by
		);
		if(isset($args['options'])){
			foreach($args['options'] as $key => $val){
				$options[$key] = $val;
			}
		}
		if(!isset($args['where'])){
			$args['where'] = array();
		}

		if(isset($args['filters'])){
			foreach($args['filters'] as $val){
				array_push($args['where'], $val);
			}
		}
		if(isset($args['where'])){
			if(!isset($options['where'])){
				$options['where'] = array();
			}
			foreach($args['where'] as $a){
				array_push($options['where'],$a);
			}
		}

		if(isset($args['join'])){
			$options['join'] = array();
			foreach($args['join'] as $a){
				array_push($options['join'],$a);
			}
		}

		if(isset($args['return'])){
			$options['return'] = $args['return'];
		}

		//echo "===options===\n";
		//print_r($options);
		//echo "==Eoptions===\n";
		return $options;
	}
}
?>
