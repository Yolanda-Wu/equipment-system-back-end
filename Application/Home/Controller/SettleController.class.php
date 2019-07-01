<?php
namespace Home\Controller;
use Think\Controller;
class SettleController extends Controller {
    

    public function _empty()
    {
        redirect(U('/'));
    }
	
	public function index()
    {
        $this->display();
    }


    public function settleRepair(){
        //if($_SESSION['admin'] && $_SESSION['type']==1){
        if(1){
            $repair_records    =   D('repair_record');
            
            //$condition['order_num']     =       I('post.order_num');
            $condition['order_num']     =       json_decode(file_get_contents('php://input'),true)['order_num'];
            //echo $condition['order_num'].'|';
            
            $maintenance_recode   =   D('maintenance_recode');
            $result1 = $maintenance_recode->where($condition)->setField('maintain_status',4);
          
           
            //$result     =      $repair_records->where($condition)->field('repair_status')->find();
            $result = $repair_records->where($condition)->setField('repair_status',4);
            //echo $result;
            if($result&&$result1){
                //$repair_records->where($condition)->setField('repair_status',2);
                http_response_code(200);  
                $reply['status']    =   200;
                $reply['data']      =   '';
                $this->ajaxReturn($reply);
            }else{
                http_response_code(400);  
                $reply['status']    =   400;
                $reply['data']      =   'SetteltFail';
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