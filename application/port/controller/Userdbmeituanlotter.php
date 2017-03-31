<?php
/**
 * 极客之家 高端PHP - 抽奖模块
 * 东标-美团抽奖模块
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2016-6-12 16:36:52
 */
namespace app\port\controller;
use think\Controller;
use think\Config;
use think\Db;
use think\Session;
use think\Cache;
use think\Loader;
use app\port\model\SecureModel;
use app\port\model\UserlotterdbmeituanModel;
use app\port\model\LotteryModel;
class Userdbmeituanlotter extends Controller
{
	/**
	 * 用户注册抽奖
	 * @return [type] [description]
	 */
	public function UserLotter()
	{
		//end by song
		$secure = new SecureModel;
		$Userlotter = new UserlotterdbmeituanModel;

		//edit by song
		$data = input("param.");
	 	if(!isset($data) || empty($data['numberphone'])){
			exit(json_encode(array("start"=>1001,"msg"=>"数据传入有误")));
		}
		// $encckend = 1; //设置默认值
		if(!isset($data['key']) || empty($data['key']))
		{
			exit(json_encode(array("start"=>1007,"msg"=>"未定义key值")));
		}
	 
		$enc = $data['key'];  //加密串 检测 
		$encckend = $secure->Ckencstr($enc); //检测结果 
		
		//return json_encode( $encckend);
		if($encckend != 1)
		{
			exit(json_encode($encckend));
		} 

		// //判断手机号是否合法
		if(!preg_match("/1[34578]{1}\d{9}$/",$data['numberphone'])){
			exit(json_encode(array("start"=>1002,"msg"=>"手机号不合法")));
		}

		// //判断手机号是否存在
		$phoneBe = DB::name("user_db_meituan")->where("phone",$data['numberphone'])->find();
		if($phoneBe || !empty($phoneBe)){
			exit(json_encode(array("start"=>1003,"msg"=>"该手机已经注册")));
		}

		//判断当前是否为手机客户端访问
		$isMobile = $secure->isMobile();
		if($isMobile == false){
			exit(json_encode(array("start"=>1004,"msg"=>"访问设备出错")));
		}
		
		//开始入库 抽奖
		$arr = $Userlotter->UserAdd($data);
		exit(json_encode($arr));
	}
	

	/**
	 * 检测中游注册用户 此手机号是否已在本系统注册
	 * @param [type] $data [description]
	 */
	function DuserBphone()
	{
		$secure = new SecureModel;
		$Userlotter = new UserlotterdbmeituanModel;

		$data = input('param.');
		if(!isset($data) || empty($data['numberphone']))
		{
			exit(json_encode(array("start"=>1001,'msg'=>'数据传入有误')));   //请刷新页面,请勿重复提交
		}
		else
		{
			if(!preg_match("/1[34578]{1}\d{9}$/",$data['numberphone']))
			{
		      exit(json_encode(array("start"=>1002,"msg"=>"手机号不合法")));
		    }
		}

		$phone = $data['numberphone']; //手机号
		if(empty($data['key']))
		{
			exit(json_encode(array("start"=>1007,'msg'=>'未定义key值')));
		}
		
		$enc = $data['key']; //密钥	 
		$encckend = $secure->Ckencstr($enc); 
		if($encckend != 1)
		 {
			 exit(json_encode($encckend));
		 } 
		
		//判断手机号是否存在
		$phoneBe = DB::name("user_db_meituan")->where("phone",$data['numberphone'])->find();
		if($phoneBe || !empty($phoneBe))
		{
			exit(json_encode(array("start"=>1003,"msg"=>"该手机已经注册")));
		}
		else
		{
			exit(json_encode(array("start"=>2008,'msg'=>'验证通过')));
		}
	}

	
}