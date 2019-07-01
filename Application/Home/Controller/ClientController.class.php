<?php
namespace Home\Controller;
use Think\Controller;
class ClientController extends Controller{

   
	public function _empty()
    {
        redirect(U('/'));
    }
	
	public function index()
    {
        $this->display();
    }
	
    public function getClient(){

        if($_SESSION['admin'] ){
        //if(1){
            //echo 'authorizetm pass';
            if(is_array($_GET)&&count($_GET)>0){
                //get含参数时返回指定用户信息
                //echo '1client id:'. I('get.client_id');
                $clients   =   D('client');
                $condition['client_id']        =        I('get.client_id');
                $result    =    $clients->where($condition)->field('company,telephone,address,contact')->find(); 
                //print_r($result);
                if($result){
                    
                    $reply['status']=200;
                    $data=array_merge(array('exit'=>true),$result);
                    $reply['data']=$data;
                    $this->ajaxReturn($reply);
                    http_response_code(200);
                }else{
                    http_response_code(200);   
                    $reply['status']  =   200;
                    $reply['data']['exit']  =  false;
                    $this->ajaxReturn($reply);
                }
            }else{

                //不含参数时返回所用用户信息
                //echo '2';
                $clients   =   D('client');
                $repair_records    =   D('repair_record');
                $clientsArray     =   $clients->field('client_id,company,telephone,address,contact')->select();

                $result     =       array();
                foreach ($clientsArray as $key => $value) {
                    $condition['client_id']     =       $value['client_id'];
                    $repairLists['repairLists']    =       $repair_records->where($condition)->field('order_num,repair_date,repair_status')->select();
                    $value=array_merge($value,$repairLists);
                    //print_r($value);
                    array_push($result,$value);
	        		//dump((int)($value));
                }
                //echo '***'.PHP_EOL;
	        	
                if($clientsArray){
                    http_response_code(200);
	            	$reply['status']  =   200;
                    $reply['data']['client']    =   $result;
                    //print_r($result);
                    $this->ajaxReturn($reply);
                    //略
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

    public function updateClient(){
        //客户存在时更新客户记录
        //客户不存在时添加改客户记录
        if($_SESSION['admin'] ){  
        //if(1){ 
            $clients            =   D('client');
            
            
    		//$form['client_id']         =   I('post.client_id');
    		//$form['company']    =   I('post.company');
    		//$form['telephone']  =   I('post.telephone');
    		//$form['address']    =   I('post.address');
            //$form['contact']       =   I('post.contact');
            $form['client_id']      =  json_decode(file_get_contents('php://input'),true)['client_id']; 
            $form['company']        =  json_decode(file_get_contents('php://input'),true)['company'];
            $form['telephone']      =  json_decode(file_get_contents('php://input'),true)['telephone'];
            $form['address']        =  json_decode(file_get_contents('php://input'),true)['address'];
            $form['contact']        =  json_decode(file_get_contents('php://input'),true)['contact'];
            
            $condition['client_id']     =       $form['client_id'] ;    
            $result = $clients->where($condition)->find(); 

            //print_r($result);

            //echo $form['client_id'].PHP_EOL;

            //print_r($_POST);
            
    		$data = 0;
    		if($result){
                //echo 'save';
                $data = $clients->where('client_id='.$form['client_id'])->save($form);

                if(false===$data){

                    $reply['status']=400;
                    $reply['err_msg']="UpdateFailed";
                    $this->ajaxReturn($reply);
                    http_response_code(400);
                }else{
                    $reply['status']=200;
                    $reply['data']="";
                    $this->ajaxReturn($reply);
                    http_response_code(200);
                }

    		}else{
                //echo 'add';
                $data = $clients->add($form);

                if($data){
                    $reply['status']=200;
                    $reply['data']=$data;
                    $this->ajaxReturn($reply);
                    http_response_code(200);
                }else{
                    $reply['status']=400;
                    $reply['err_msg']="UpdateFailed";
                    $this->ajaxReturn($reply);
                    http_response_code(400);
                }
            }
        }else{
            http_response_code(401);   
            $reply['status']  =   401;
            $reply['err_msg']  ="Unauthorized";
            $this->ajaxReturn($reply);
        } 
        
		
    }


    public function deleteClient(){
        if($_SESSION['adimin']){
       // if(1){
            $client     =       D('client');
            $condition['client_id']     =       I('post.client_id');
            echo $condition['client_id'];
            $result     =       $client->where($condition)->delete();
            if($result!== false){
                echo $result;
                http_response_code(200);   
                $reply['status']  =   200;
                $reply['data']  ="";
                $this->ajaxReturn($reply);
            }else{
                http_response_code(400);   
                $reply['status']  =   400;
                $reply['data']  ="DeleteFailed";
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