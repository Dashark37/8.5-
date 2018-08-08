<?php 
namespace app\home\controller; //定义当前类所在的命名空间
use think\Controller; 	//引入Controller核心控制器
use app\home\model\Member;
class PublicController extends Controller {

	//密码重置页
	public function setNewPassword($member_id,$hash,$time){
		//判断邮件地址是否被篡改,判断hash加密字符串的结果; 不一样则被篡改了
		if (md5($member_id.$time.config('email_salt')) != $hash) {
						exit('不能擅自篡改地址');
		}
		//判断是否在有效期内30分钟
		if (time()>$time+1800) {
			exit('已过有效期');
		}
		//判断是否是post提交
		if(request()->isPost()){
			$postData = input('post.');
			$result = $this->validate($postData,"Member.setNewPassword",[],true);
			if($result!==true){
				$this->error( implode(',',$result) );
			}
			//更新密码
			$data = [
				'member_id' => $member_id,
				'password'  => md5( $postData['password'].config('password_salt') )
			];
			$memModel = new Member();
			if($memModel->update($data)){
				$this->success("重置密码成功",url("/home/public/login"));
			}else{
				$this->error("重置失败");
			}
		}

		

		return $this->fetch('');
	}

	//发送邮件
	public function sendEmail(){
		// dump($_SERVER);die;
		if(request()->isAjax()){
			//接收参数
			$email = input('email');
			//验证器邮箱必须存在系统中
			$result = Member::where('email','=',$email)->find();
			if(!$result){
				//说明没有这个邮箱
				$response = ['code'=>-1,'message'=>'邮箱不存在'];
				echo json_encode($response);die;
			}
			//构造找回密码的链接地址
			
			$member_id = $result['member_id'];
			$time = time(); //记录当前数据 用来做有效期
			$hash = md5($result['member_id'].$time.config('email_salt')); //把用户和id和邮箱的盐进行加密，防止用户篡改
			$href =$_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST']."/index.php/home/public/setNewPassword/".$member_id.'/'.$hash.'/'.$time;
			$content = "<a href='{$href}' target='_blank'>京西商城-找回密码</a>";
			//发送邮件
			if( sendEmail([$email],'找回密码',$content) ){
				$response = ['code'=>200,'message'=>'发送成功，请登录邮箱查看'];
				echo json_encode($response);die;
			}else{
				$response = ['code'=>-2,'message'=>'发送失败，请稍后再试'];
				echo json_encode($response);die;
			}
		}
	}

	//密码找回页
	public function forgetPassword(){
		
		return $this->fetch('');
	}

	//短信验证
	public function sendSms(){
		if(request()->isAjax()){
			//接收phone参数
			$phone = input('phone');
			//验证器验证手机号没有被注册过
			$result = $this->validate(["phone"=>$phone],"Member.sendSms",[],false);
			if($result !== true){
				//说明手机号已被注册过
				$response = ['code'=>-1,'message'=>'手机号占用，请更换一个'];
				echo json_encode($response);die;
			}
			//发送短信
			$rand = mt_rand(1000,9999);
			$result = sendSms($phone,array($rand,'5'),'1');
			//判断是否发送成功，返回json数据
			if($result->statusCode!=0){
				$response = ['code'=>-2,'message'=>'网络异常请重试或'.$result->statusMsg];
				echo json_encode($response);die;
			}else{
				//有效期五分钟
				
				cookie('phone',md5($rand.config('sms_salt')),300);
				$response = ['code'=>200,'message'=>'发送短信成功'];
				echo json_encode($response);die;
			}
		}
	}

	//注册会员
	public function register(){
		//判断是否是post提交
		if (request()->isPost()) {
			//获取post提交的数据
			$postData = input('post.');
			//进行验证器验证
			$result = $this->validate($postData,'Member.register',[],true);
			//判断验证是否通过
			if ($result !== true) {
				$this->error( implode(',',$result) );
			}
			//写入数据库
			$memModel = new Member();
			//允许表单所有字段通过
			if ($memModel->allowField(true)->save($postData)) {
				$this->success('注册成功',url('/'));
			}else{
				$this->error('注册失败');
			}

		}

		return $this->fetch('');
	}

	//登录账户
	public function login(){
		//判断是否是post提交
		if(request()->isPost()){
			//获取post提交数据
			$postData = input('post.');
			//使用验证器验证
			$result = $this->validate($postData,"Member.login",[],true);
			if($result !== true){
				$this->error( implode(',',$result) );
			}
			//判断用户名和密码是否匹配
			$memModel = new Member();
			//模型里简历一个方法 会自动检查用户名和密码
			$flag = $memModel->checkUser($postData['username'],$postData['password']);
			if($flag){
				$this->redirect("/");
			}else{
				$this->error("用户名或密码失败");
			}

		}
		return $this->fetch('');		
	}

	//退出登录
	public function logout(){
		//清除session
		session('member_id',null);
		session('member_username',null);
		//重定向到登录页
		$this->redirect('/home/public/login');
	}
}
