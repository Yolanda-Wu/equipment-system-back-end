<?php
namespace Home\Controller;
use Think\Controller;
class RepairController extends Controller{

   
	public function _empty()
    {
        redirect(U('/'));
    }
	
	public function index()
    {
        $this->display();
    }

    //新增一个报修单
    public function addRepair(){

        if($_SESSION['admin']){
        //if(1){
            $repair_record   =   D('repair_record');

            //$form['repair_date']           =   I('post.repair_date');
            //$form['repair_status']    	   =   I('post.repairStatus');
    		//$form['product_type']    	   =   I('post.product_type');
    		//$form['product_brand']    	   =   I('post.product_brand');
    		//$form['product_sysId']    	   =   I('post.product_sysId');
    		//$form['dev_error']    	   	   =   I('post.dev_error');
    		//$form['other']    	   		   =   I('post.orther');
            //$form['client_id']    	       =   I('post.client_id');
            //echo  json_decode(file_get_contents('php://input'),true)['repair_date']
            
            $form['repair_date']           =   json_decode(file_get_contents('php://input'),true)['repair_date'];
            $form['repair_status']    	   =   json_decode(file_get_contents('php://input'),true)['repair_status'];
    		$form['product_type']    	   =   json_decode(file_get_contents('php://input'),true)['product_type'];
    		$form['product_brand']    	   =   json_decode(file_get_contents('php://input'),true)['product_brand'];
    		$form['product_sysId']    	   =   json_decode(file_get_contents('php://input'),true)['product_sysId'];
    		$form['dev_error']    	   	   =   json_decode(file_get_contents('php://input'),true)['dev_error'];
    		$form['other']    	   		   =   json_decode(file_get_contents('php://input'),true)['other'];
    		$form['client_id']    	       =   json_decode(file_get_contents('php://input'),true)['client_id'];
    	
            //print_r($_POST);
            $condition['order_num'] = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
            $result=$repair_record->where($condition)->find();
            //print_r($result);

            while($result){
                $condition['order_num'] = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
                $result=$repair_record->where($condition)->find();
            }

            $form['order_num']=$condition['order_num'];

		    $data = $repair_record->add($form);

            if($data){
                http_response_code(200);
		    	$reply['status']  =   200;
                $reply['data']['order_num']  =  $condition['order_num'];
                $this->ajaxReturn($reply);
                //略
            }else{
                http_response_code(400); 
                $reply['status']  =   400;
                $reply['err_msg']  =  "AdditionFailed";
                $this->ajaxReturn($reply);
            }
        }else{
            http_response_code(401);   
            $reply['status']  =   401;
            $reply['err_msg']  ="Unauthorized";
            $this->ajaxReturn($reply);
        }
    }


    //获取全部保修单某页信息
	public function getRepairList(){

        if($_SESSION['admin']  ){
        //if(1){
            $repair_record   =   D('repair_record');
            $client          =   D('client');
            $maintenance_recode   =   D('maintenance_recode');
            $page    	   =   I('get.page');

		    //echo $page ;
        
        
            $repairOrders = $repair_record->page($page.',10')->field('client_id,order_num,repair_date,repair_status')->select();
            //print_r($repairOrders);
            

            
            $result     =       array();
            foreach ($repairOrders as $key => $value) {
                $condition1['client_id']     =       $value['client_id'];
                $condition2['order_num']     =      $value['order_num'];
                $contact   =       $client->where($condition1)->field('contact')->find();
                //print_r($contact);
                $maintainer    =    $maintenance_recode->where($condition2)->field('maintainer')->find();
                //print_r($maintainer);
                $value=array_merge((array)$value,(array)$contact,(array)$maintainer);
                //print_r($value);
                array_push($result,$value);
                //dump((int)($value));
            }


            if($result){
                http_response_code(200);
		    	$reply['status']  =   200;
                $reply['data']['repairslist']  =  $result;
                //print_r($reply);
                $this->ajaxReturn($reply);
                //略
            }else{
                http_response_code(400);   
                $reply['status']  =   400;
                $reply['err_msg']  =  "SearchFild";
                $this->ajaxReturn($reply);    
            }
        }else{
            http_response_code(401);   
            $reply['status']  =   401;
            $reply['err_msg']  ="Unauthorized";
            $this->ajaxReturn($reply);
        }
    }

    //根据报修单号获取相关信息
	public function getRepair(){

        if($_SESSION['admin'] ){ 
        //if(1){ 
            $repair_record   =   D('repair_record');
            $client          =   D('client');
            $maintenance_recode   =   D('maintenance_recode');

            $condition['order_num']    	   =   I('get.order_num');

		    //echo $condition['order_num'];
        
        
            $data_p1 = $repair_record->where($condition)->field('client_id,repair_date,repair_status,product_type,product_brand,product_sysId,dev_error,other')->find();
            //echo '1';
            //print_r($data_p1);

            $data_p2 = $maintenance_recode->where($condition)->field('maintainer')->find();
            //echo '2'.PHP_EOL;
            //echo $data_p2==NULL;
            //print_r($data_p2);
            //echo '3';

            $data_p3= $client->where('client_id='.$data_p1['client_id'])->field('company,telephone,address,contact')->find();

            //print_r($data_p3);

            $data=array_merge((array)$data_p1,(array)$data_p2,(array)$data_p3);

            // echo 'all';

            // print_r($data);

            if($data){
                http_response_code(200);
		    	$reply['status']  =   200;
                $reply['data']  =  $data;
                $this->ajaxReturn($reply);
                //略
            }else{
                http_response_code(400);   
                $reply['status']  =   400;
                $reply['err_msg']  =  "SearchFild";
                $this->ajaxReturn($reply);    
            }
        }else{
            http_response_code(401);   
            $reply['status']  =   401;
            $reply['err_msg']  ="Unauthorized";
            $this->ajaxReturn($reply);
        }
    }   


    
    
//************************************



    public function getClientsRepairs(){

        if($_SESSION['admin']){
            $repair_record   =   D('repair_record');

            $client    	   =   I('get.client_id');
	    	//echo $client;
	    	$condition['client_id'] = $client;

	    	$data = $repair_record->where($condition)->field('client_id,order_num,repair_status')->select();

            if($data){
                http_response_code(200);
	    		$reply['status']  =   200;
                $reply['repairslist']  =  $data;
                $this->ajaxReturn($reply);
                
            }else{
                http_response_code(400);   
                $reply['status']  =   400;
                $reply['err_msg']  =  "SearchFild";
                $this->ajaxReturn($reply);    
            }
        }else{
            http_response_code(401);   
            $reply['status']  =   401;
            $reply['err_msg']  ="Unauthorized";
            $this->ajaxReturn($reply);
        }

    }

    
    





    

    


}