<?php
namespace Home\Controller;
use Think\Controller;
class LogoutController extends Controller {
    //退出登录
    public function checkout()
    {
            if($_SESSION['admin']){
            //echo 'pppp';
            session(null); // 清空当前的session
            //$reply['data']['redirect_url']=$redirect_url;
            $reply['status']=200;
            $this->ajaxReturn($reply);
            http_response_code(200);
           
        }
    }

}