<?php
namespace Home\Controller;
use Think\Controller;
class MaintenanceController extends Controller{

   
	public function _empty()
    {
        redirect(U('/'));
    }
	
	public function index()
    {
        $this->display();
    }
    
    
    public function assignMaintenance(){

        //任务调度员工可以使用该项功能
        if($_SESSION['admin']){
        //if(1){
            $maintenance_recode   =   D('maintenance_recode');
            $repair_record   =   D('repair_record');

            //$form['assign_date']    	   =   date_create();
            //$form['assign_date']           =   I('post.assign_date');
    		//$form['maintainer']    	       =   I('post.maintainer');
            //$form['order_num']    	   	   =   I('post.order_num');
            
            $form['assign_date']           =json_decode(file_get_contents('php://input'),true)['assign_date'];   
    		$form['maintainer']    	       =json_decode(file_get_contents('php://input'),true)['maintainer'];   
            $form['order_num']    	   	   =json_decode(file_get_contents('php://input'),true)['order_num'];   
            $form['maintain_status']    	   	   =2;
            $form            = array_merge($repair_record->where('order_num='.$form['order_num'])->field('client_id')->find(),$form);               

            //print_r($form);

    		//dump( $form);
    		//die();
            $data = $maintenance_recode->add($form);
            $condition['order_num']     =       $form['order_num'];
            $r_result = $repair_record->where($condition)->setField('repair_status',2);

            if($data){
                http_response_code(200);
    			$reply['status']  =   200;
                $reply['data']  =  "";
                $this->ajaxReturn($reply);
                //略
            }else{

                http_response_code(400);   
                $reply['status']  =   400;
                $reply['err_msg']  =  "AssignFailed";
                $this->ajaxReturn($reply);
            }

        }else{
            http_response_code(401);   
            $reply['status']  =   401;
            $reply['err_msg']  ="Unauthorized";
            $this->ajaxReturn($reply);
        }
    }

    //（有maintainerId参数）--返回该维修人员订单信息 ||（无maintainerId参数）--返回所有订单信息
    public function getMaintenanceList(){
        //任务调度人员，技术工程师，运营监督员可以使用该功能
        if($_SESSION['admin']){
        //if(1){
            if(is_array($_GET)&&count($_GET)>0){
                $maintenance_recode   =   D ('maintenance_recode');


        		$condition['maintainer']    	   =  array ('like', '%'.I('get.maintainer').'%'); 
        		//echo $client;


        		$data = $maintenance_recode->where ($condition)->field('order_num,maintainer,assign_date,maintain_status')->select();
                //print_r($data);
                if($data){
                    http_response_code(200);
                    $reply['status']  =   200;
                    //print_r($date);
                    $reply['data']['maintenanceList']  =  $data;
                    $this->ajaxReturn($reply);
                    //略
                }else{
                    http_response_code(400);   
                    $reply['status']  =   400;
                    $reply['err_msg']  =  "SearchFaild";
                    $this->ajaxReturn($reply);  
                }
            }else{
                $maintenance_recode   =   D ('maintenance_recode');

                $data = $maintenance_recode->field('order_num,maintainer,assign_date,maintain_status')->select();
                //print_r($data);
                if($data){
                    http_response_code(200);
        			$reply['status']  =   200;
                    $reply['data']['maintenanceList']  =  $data;
                    $this->ajaxReturn($reply);
                }else{
                    http_response_code(400);   
                    $reply['status']  =   400;
                    $reply['err_msg']  =  "SearchFild";
                    $this->ajaxReturn($reply);
                }
            }
        }else{
            http_response_code(401);   
            $reply['status']  =   401;
            $reply['err_msg']  ="Unauthorized";
            $this->ajaxReturn($reply);
        }
    
    }


    //根据订单号获取订单信息
    public function getMaintenance(){
         //任务调度人员，技术工程师，运营监督员可以使用该功能
        if($_SESSION['admin'] ){
        //if(1){
            $maintenance_recode   =   D('maintenance_recode');

            $condition['order_num']    	   =   I('get.order_num');
	    	//echo $condition['order_num'];

	    	$data = $maintenance_recode->where($condition)->field('maintainer,assign_date,detect_record,maintain_record,manual_cost,manual_cost,material_cost,note,maintain_status')->find();
            //print_r($data);
            if($data){
                http_response_code(200);
		    	$reply['status']  =   200;
                $reply['data']  =  $data;
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



    public function updateMaintenance(){
        //仅技术工程师可以使用该功能
        if($_SESSION['admin']){
        //if(1){
            $maintenance_recode   =   D('maintenance_recode');
            $repair_records    =   D('repair_record');

            //$form['detect_record']    	   =   I('post.detect_record');
    		//$form['maintain_record']       =   I('post.maintain_record');
    		//$form['manual_cost']    	   =   I('post.manual_cost');
    		//$form['material_cost']    	   =   I('post.material_cost');
    		//$form['note']    	   	       =   I('post.note');
    		//$form['maintain_status']       =   I('post.maintain_status');
            //$form['order_num']    	       =   I('post.order_num');
        

            $form['detect_record']    	   =        json_decode(file_get_contents('php://input'),true)['detect_record'];   
    		$form['maintain_record']       =        json_decode(file_get_contents('php://input'),true)['maintain_record'];  
    		$form['manual_cost']    	   =        json_decode(file_get_contents('php://input'),true)['manual_cost'];   
    		$form['material_cost']    	   =        json_decode(file_get_contents('php://input'),true)['material_cost'];  
            $form['note']    	   	       =        json_decode(file_get_contents('php://input'),true)['note'];       		
            $form['maintain_status']       =        json_decode(file_get_contents('php://input'),true)['maintain_status'];   
            $form['order_num']    	       =        json_decode(file_get_contents('php://input'),true)['order_num'];   
        
    		//dump($form);
            //die();
            //print_r($form);
            $condition['order_num']        =        $form['order_num'];
            $data = $maintenance_recode->where($condition)->field('manual_cost,material_cost,note,maintain_status,order_num,detect_record,maintain_record')->save($form);
            $m_result = $repair_records->where($condition)->setField('repair_status',$form['maintain_status']);

            if($data||$m_result){
                http_response_code(200);
    			$reply['status']  =   200;
                $reply['data']  =  '';
                $this->ajaxReturn($reply);
                //略
            }else{
                echo $client;
                http_response_code(400);   
                $reply['status']  =   400;
                $reply['err_msg']  =  "UpdateFailed";
                $this->ajaxReturn($reply);
            }
        }else{
            http_response_code(401);   
            $reply['status']  =   401;
            $reply['err_msg']  ="Unauthorized";
            $this->ajaxReturn($reply);
        }
    }

    //获取维修人员信息（其实没什么luan用）
	public function getMaintainers(){
        //任务调度人员，技术工程师，运营监督员可以使用该功能
        if($_SESSION['admin'] ){
        //if(1){
            $user   =   D('user');

            //$client    	   =   I('get.order_num');
	    	
            $condition['type']      =       2;
	    	$data = $user->where($condition)->field('id,user_name')->select();

            if($data){
                http_response_code(200);
	    		$reply['status']  =   200;
                $reply['data']['maintainers']  =  $data;
                $this->ajaxReturn($reply);
                //略
            }else{
                //echo $client;
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