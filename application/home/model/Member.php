<?php 
namespace app\home\model;
use think\Model;

class Member extends Model{
	protected $pk = 'member_id';
	//时间戳自动写入
	protected $autoWriteTimestamp = true;


	protected static function init(){
		//入库前事件
		Member::event('before_insert',function($member){
			$member['password'] = md5($member['password'].config('password_salt'));
		});
	}


	public function checkUser($username,$password){
		$where = [
			'username' => $username,
			//密码必须经过处理
			'password' => md5($password.config('password_salt'))
		];
		//获取当前会员信息
		$userInfo = $this->where($where)->find();
		if($userInfo){
			//设置用户的到sessio中去
			session('member_username',$userInfo['username']);
			session('member_id',$userInfo['member_id']);
			return true;
		}else{
			return false;
		}
	}
	
}