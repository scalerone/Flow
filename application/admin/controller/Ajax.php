<?php

namespace app\admin\controller;

use app\admin\model\NodeModel;
use think\Controller;

class Ajax extends Controller
{
    public function getMenuList(){
        if(!session('admin_uid')){

            return json(array('code'=>'-1','error_info'=>$this->error('抱歉，您没有登录')));
        }

        $auth = new \com\Auth();

        $module     = strtolower(request()->module());
        $controller = strtolower(request()->controller());
        $action     = strtolower(request()->action());
        $url        = $module."/".$controller."/".$action;

        //跳过检测以及主页权限
        if(session('admin_uid')!=1){

            if(!in_array($url, ['admin/index/index','admin/index/center','admin/index/indexpage'])){
                // if(!$auth->check($url,session('admin_uid'))){
                //     return json(array('code'=>'-100','error_info'=>$this->error('抱歉，您没有操作权限')));
                // }
            }
        }
        $node = new NodeModel();
        $res = $node->getMenu(session('rule'));
        // print_r($res);die;
        return json($node->getMenu(session('rule')));

    }
}