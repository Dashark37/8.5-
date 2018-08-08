<?php 
namespace app\home\model;
use think\Model;

class Category extends Model{
	protected $pk = 'cat_id';

	//获取当前分类及其子孙分类
	public function getSonsCatId($data,$cat_id){
		static $sonsId = [];
		foreach($data as $k=>$v){
			if($v['pid'] == $cat_id){
				$sonsId[] = $v['cat_id']; //只存储cat_id即可
				unset( $data[ $k] );
				//递归调用
				$this->getSonsCatId($data,$v['cat_id']);
			}
		}
		return $sonsId;
	}


	//获取当前商品分类的祖先分类的数据
	public function getFamilysCat($data,$cat_id){
		static $result = [];
		foreach ($data as $k => $v) {
			//第一次循环, 肯定是要先找到自己
			if ($v['cat_id'] == $cat_id) {
				$result[] = $v;
				//删除已经判断过的分类
				unset($data[$k]);
				//递归调用  找父分类:传递当前分类的 pid 进行递归查找 (判断谁的cat_id等于我的pid则找到上一级)
				$this->getFamilysCat($data,$v['pid']);
			}
		}
		//返回结果把(数组反转)
		return array_reverse($result);
	}
	


	//获取导航栏的分类的数据
	public function getNavData($limit){
		//is_show = 1
		return $this->where("is_show",'=','1')->limit($limit)->select();
	}
	
}