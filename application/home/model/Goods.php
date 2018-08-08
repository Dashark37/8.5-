<?php 
namespace app\home\model;
use think\Model;

class Goods extends Model{
	protected $pk = 'goods_id';


	public function getGoods($type,$limit){
		//定义初始的查询条件
		$where = [
			'is_sale' => 1
		];
		switch($type){
			case 'is_crazy':
				//按照价格升序取出来
				$data = $this->where($where)->order('goods_price asc')->limit($limit)->select();
				break;
			default :
				$where[$type] = ['=',1];
				$data = $this->where($where)->limit($limit)->select();
				break;
		}
		//返回商品数据
		return $data;
		
	}
}