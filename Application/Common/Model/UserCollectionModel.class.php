<?php
namespace Common\Model;
use Think\Model;

class UserCollectionModel extends Model{
	protected $tableName = 'user_collection';

	/**
	 * @desc 收藏列表下拉
	 * @param $createTime  collection表创建时间
	 * @param $type        类型1：下拉加载  2：上拉加载
	 * $param $limit       展示条数
	 */
	public function getCollecitonList($device_id,$createTime,$type=1,$limit= 20,$env=0){
			if($type ==1)
			{
				$where .= " and ucl.device_id = '".$device_id."' and ucl.state= 1 and mc.state=2";
				$order= " ucl.create_time desc";
			}else if($type ==2 && !empty($createTime)){
				$where .= " and ucl.device_id = '".$device_id."' and ucl.state= 1 and mc.state=2  and ucl.create_time < '".$createTime."'";
				$order= " ucl.create_time desc";
			}
		$sql = "  select ucl.id colid, ucl.artid, ucl.create_time ucreateTime,ucl.state,m.oss_addr as name,mcat.name as category,mc.index_img_url,mc.title,mc.duration,mc.img_url as imgUrl,mc.content_url as contentUrl,mc.tx_url as videoUrl,mc.share_title as shareTitle,
	           mc.share_content as shareContent,mc.type,mc.content,mc.media_id as mediaId,mc.create_time acreateTime,mc.source as sourceName  from  savor_user_collection ucl left join savor_mb_content mc on ucl.artid = mc.id left join savor_media m on mc.media_id = m.id left  join savor_mb_hot_category as mcat on mc.hot_category_id = mcat.id where 1=1 $where order by $order  limit $limit";
		$result = $this->query($sql);
		return $result;
	}



	public function saveData($data, $where) {
		$bool = $this->where($where)->save($data);
		return $bool;
	}


	/**
	 * @desc 添加数据
	 */
	public function addData($data  = array()){
		if(!empty($data) && is_array($data)){
		    $this->add($data);
		    $id = $this->getLastInsID();
		    return $id;
		}else {
		    return false;
		}
	}
	public function getOne($map = array()){
	    if(!empty($map)){
	        $result = $this->where($map)->find();
	        return $result;
	    }
	}
}