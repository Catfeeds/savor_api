<?php
/**
 * @desc 餐厅端2.0-预订
 * @author zhang.yingtao
 * @since  20171220
 */
namespace Dinnerapp2\Controller;
use Think\Controller;
use \Common\Controller\BaseController as BaseController;
class OrderController extends BaseController{
    private $pagesize = 20;
    /**
     * 构造函数
     */
    function _init_() {
        switch(ACTION_NAME) {
            case 'addOrder':
                $this->is_verify = 1;
                $this->valid_fields = array('invite_id'=>1001,'mobile'=>1001,'order_name'=>1001,
                                            'order_mobile'=>1001,'person_nums'=>1000,'room_id'=>1001,
                                            'room_type'=>1001,'order_time'=>1001
                );
                break;
            case 'getOrderList':
                $this->is_verify = 1;
                $this->valid_fields = array('invite_id'=>1001,'mobile'=>1001,'order_date'=>1001);
                break;
            case 'upateOrderService':
                $this->is_verify = 1;
                $this->valid_fields = array('invite_id'=>1001,'mobile'=>1001,'order_id'=>1001,'type'=>1001,'ticket_url'=>1000);
                break;
            case 'updateOrder':
                $this->is_verify = 1;
                $this->valid_fields = array('invite_id'=>1001,'mobile'=>1001,'order_id'=>1001,'order_name'=>1001,
                                            'order_mobile'=>1001,'person_nums'=>1000,'room_id'=>1001,
                                            'room_type'=>1001,'order_time'=>1001);
                break;
            case 'deleteOrder':
                $this->is_verify = 1;
                $this->valid_fields = array('invite_id'=>1001,'mobile'=>1001,'order_id'=>1001);
                break;
            case 'getOrderDetail':
                $this->is_verify = 1;
                $this->valid_fields = array('invite_id'=>1001,'mobile'=>1001,'order_id'=>1001);
                break;
        }
        parent::_init_();
    }
    public function getOrderList(){
        $invite_id  = $this->params['invite_id'];
        $mobile     = $this->params['mobile'];    //用户手机号
        $order_date = $this->params['order_date'];     //预订时间 
        $page_num   = $this->params['page_num'] ? intval($this->params['page_num']) : 1;
        if(!check_mobile($mobile)){
            $this->to_back('60002');
        }
        $m_hotel_invite_code = new \Common\Model\HotelInviteCodeModel();
        $where = array();
        $where['id'] = $invite_id;
        $where['state'] = 1;
        $where['flag'] = '0';
        $invite_info = $m_hotel_invite_code->getOne('bind_mobile,hotel_id', $where);
        if(empty($invite_id)){
            $this->to_back(60018);
        }
        if($invite_info['bind_mobile'] != $mobile){
            $this->to_back(60019);
        }
        $start_date = $order_date.' 00:00:00';
        $end_date   = $order_date.' 23:59:59';
        $m_dinner_order = new \Common\Model\DinnerOrderModel();
        $fields = 'a.id order_id,a.customer_id,a.room_id,a.room_type,a.order_time,a.person_nums,a.order_name,a.order_mobile,a.remark,a.is_welcome,a.is_recfood,a.ticket_url,b.face_url';
        $where = array();
        //$where['hotel_id']    = $invite_info['hotel_id'];
        $where['a.invite_id']   = $invite_id;
        $where['a.order_time']  = array(array('EGT',$start_date),array('ELT',$end_date)) ;
        $where['a.flag']        = 0;
        $order = 'a.order_time asc,a.id asc';
        $offset = ($page_num-1)*$this->pagesize;
        $limit = "$offset,$this->pagesize";
        
        $data = $m_dinner_order->getList($fields,$where,$order,$limit);
        $m_room = new \Common\Model\RoomModel();
        $m_dinner_room = new \Common\Model\DinnerRoomModel();
        $oss_path = C('TASK_REPAIR_IMG');
        foreach($data as $key=>$v){
            if($v['room_type']==1){//酒楼包间
                $room_info = $m_room->getOne('name',array('id'=>$v['room_id']));
            }else if($v['room_type']==2){//手动添加包间
                $room_info = $m_dinner_room->getOne('name', array('id'=>$v['room_id']));  
            }    
            $data[$key]['room_name'] = $room_info['name'];
            /* $order_times = date('His',strtotime($v['order_time']));
            if($order_times<110000){
                $data[$key]['time_str'] = '上午 ';
            }else if($order_times>=110000 && $order_times<160000){
                $data[$key]['time_str'] = '中午 ';
            }else {
                $data[$key]['time_str'] = '晚上 ';
            } */
            $data[$key]['time_str'] = $order_date;
            $data[$key]['moment_str'] = date('H:i',strtotime($v['order_time']));
            /* if(empty($v['ticket_url'])){//消费记录
                $data[$key]['is_expense'] = 0;
            }else {
                $data[$key]['is_expense'] = 1;
            } */
            $m_dinner_consume_record = new \Common\Model\DinnerConRecModel();
            $nums = $m_dinner_consume_record->countNums(array('order_id'=>$v['order_id'],'flag'=>0));
            if(empty($nums)){
                $data[$key]['is_expense'] = 0;
            }else {
                $data[$key]['is_expense'] = 1;
            }
            
            if(empty($v['remark'])){
                //unset($data[$key]['remark']);
                $data[$key]['remark'] = "";
            }
            if(!empty($v['face_url'])){
                $data[$key]['face_url'] = $oss_path.$v['face_url'].'?x-oss-process=image/resize,w_100';
            }
            //unset($data[$key]['room_id']);
            //unset($data[$key]['room_type']);
            unset($data[$key]['order_time']);
            unset($data[$key]['ticket_url']);
        }
        //获取四个日期的预订总数（昨天，当天，明天，后天）
        $now_time = time();
        //昨天
        
        $where = array();
        $where['invite_id'] = $invite_id;
        $where['flag']      = '0';
        $start_time = date('Y-m-d 00:00:00',$now_time-86400);
        $end_time   = date('Y-m-d 23:59:59',$now_time-86400);
        $where['order_time']= array(array('EGT',$start_time),array('ELT',$end_time));
        $yesterday_order_nums = $m_dinner_order->countNums($where);
        //当天
        $where = array();
        $where['invite_id'] = $invite_id;
        $where['flag']      = '0';
        $start_time = date('Y-m-d 00:00:00',$now_time);
        $end_time   = date('Y-m-d 23:59:59',$now_time);
        $where['order_time']= array(array('EGT',$start_time),array('ELT',$end_time));
        $today_order_nums = $m_dinner_order->countNums($where);

        //明天
        $where = array();
        $where['invite_id'] = $invite_id;
        $where['flag']      = '0';
        $start_time = date('Y-m-d 00:00:00',$now_time+86400);
        $end_time   = date('Y-m-d 23:59:59',$now_time+86400);
        $where['order_time']= array(array('EGT',$start_time),array('ELT',$end_time));
        $tomorrow_order_nums = $m_dinner_order->countNums($where);
        //后天
        $where = array();
        $where['invite_id'] = $invite_id;
        $where['flag']      = '0';
        $start_time = date('Y-m-d 00:00:00',$now_time+172800);
        $end_time   = date('Y-m-d 23:59:59',$now_time+172800);
        $where['order_time']= array(array('EGT',$start_time),array('ELT',$end_time));
        $after_tomorrow_order_nums =  $m_dinner_order->countNums($where);
        
        $result['yesterday_order_nums'] = $yesterday_order_nums;
        $result['today_order_nums']     = $today_order_nums;
        $result['tomorrow_order_nums'] = $tomorrow_order_nums;
        $result['after_tomorrow_order_nums'] = $after_tomorrow_order_nums;
        $result['order_list'] = $data;
        
        $this->to_back($result);
    }
    public function addOrder(){
        $invite_id  = $this->params['invite_id'];
        $mobile     = $this->params['mobile'];    //用户手机号
        if(!check_mobile($mobile)){
            $this->to_back('60002');
        }
        $m_hotel_invite_code = new \Common\Model\HotelInviteCodeModel();
        $where = array();
        $where['id'] = $invite_id;
        $where['state'] = 1;
        $where['flag'] = '0';
        $invite_info = $m_hotel_invite_code->getOne('bind_mobile,hotel_id', $where);
        if(empty($invite_id)){
            $this->to_back(60018);
        }
        if($invite_info['bind_mobile'] != $mobile){
            $this->to_back(60019);
        }
        $order_name   = $this->params['order_name'];
        $order_mobile = $this->params['order_mobile'];
        
        $customer_id  = $this->params['customer_id'];   //客户id
        
        /* if(!empty($order_mobile) && !check_mobile($order_mobile)){
            $this->to_back('60034');
        } */
        $m_dinner_customer = new \Common\Model\DinnerCustomerModel();
        $where = " (`mobile`='$order_mobile' or `mobile1`='$order_mobile') and invite_id=".$invite_id." and flag=0";
        $customer_info = $m_dinner_customer->getOne('id',$where);
        if(!empty($customer_id)){
            //如果客户id不为空
            
            if($customer_info['id']!=$customer_id){
                $this->to_back(60118);
            }
            
        }else {
            if(empty($customer_info)){
                $data = array();
                $data['invite_id'] = $invite_id;
                $data['name']      = $order_name;
                $data['mobile']    = $order_mobile;
                $data['sex']       = intval($this->params['sex']);
                $data['consume_ability'] = intval($this->params['consume_ability']);
                $data['bill_info'] = !empty($this->params['bill_info']) ? $this->params['bill_info'] :'';
                
                $face_url = $this->params['face_url'];
                if(!empty($face_url)){
                    $face_url_arr = parse_url($face_url);
                    $data['face_url']  = $face_url_arr['path'];
                }
                $data['remark']    = $this->params['c_remark'];
                
                $data['birthday']  = !empty($this->params['birthday']) ? $this->params['birthday'] :'';
                $data['birthplace']= !empty($this->params['birthplace']) ? $this->params['birthplace'] :'';
                $m_dinner_customer->add($data);
                $customer_id = $m_dinner_customer->getLastInsID();
            }else {
                $customer_id = $customer_info['id'];
            }   
        }
        
        
        
        $data = array();
        
        $data['invite_id']     = $invite_id;
        $data['customer_id']   = $customer_id; 
        $data['hotel_id']      = $invite_info['hotel_id'];
        $data['order_name']    = $this->params['order_name'];
        $data['order_mobile']  = $this->params['order_mobile'];
        $data['person_nums']   = $this->params['person_nums'];
        $data['room_id']       = $this->params['room_id'];
        $data['room_type']     = $this->params['room_type'];
        $data['order_time']    = $this->params['order_time'];
        $data['remark']        = $this->params['remark'];
        $m_dinner_order = new \Common\Model\DinnerOrderModel();
        $ret = $m_dinner_order->addInfo($data);
        $order_id = $m_dinner_order->getLastInsID();
        if($ret){
            $data = array();
            $data['action_id'] = $customer_id;
            $data['type']      = 4;
            $data['invite_id'] = $invite_id;
            $m_dinner_action_log = new \Common\Model\DinnerActionLogModel();
            $m_dinner_action_log ->add($data);
            $data = array();
            $data['customer_id'] = $customer_id;
            $this->to_back($data);
        }else {
            $this->to_back(60023);
        }
    }
    /**
     * @desc 更新预订的服务功能
     */
    public function upateOrderService(){
        $order_id   = intval($this->params['order_id']);
        $type       = intval($this->params['type']);
        $ticket_url = $this->params['ticket_url'];
        $invite_id  = $this->params['invite_id'];
        $mobile     = $this->params['mobile'];    //用户手机号
        if(!check_mobile($mobile)){
            $this->to_back('60002');
        }
        $m_hotel_invite_code = new \Common\Model\HotelInviteCodeModel();
        $where = array();
        $where['id'] = $invite_id;
        $where['state'] = 1;
        $where['flag'] = '0';
        $invite_info = $m_hotel_invite_code->getOne('bind_mobile,hotel_id', $where);
        if(empty($invite_id)){
            $this->to_back(60018);
        }
        if($invite_info['bind_mobile'] != $mobile){
            $this->to_back(60019);
        }

        $m_dinner_order = new \Common\Model\DinnerOrderModel();
        $fields = 'id,invite_id,is_welcome,is_recfood,ticket_url';
        $where = array();
        $where['id']  = $order_id;
        $where['flag']= 0;
        $order_info = $m_dinner_order->getOne($fields,$where);
        if(empty($order_info)){
            $this->to_back(60024);
        }
        if($invite_id != $order_info['invite_id']){
            $this->to_back(60033);
        }
        $data  = array();
        switch ($type){
            case '1':
                if($order_info['is_welcome']==1){
                    $this->to_back(60025);
                }
                $data['is_welcome'] = 1;
                break;
            case '2':
                if($order_info['is_recfood']==1){
                    $this->to_back(60026);
                }
                $data['is_recfood'] = 1;
                break;
            case '3':
                if(empty($ticket_url)){
                    $this->to_back(60029);
                }
                if(!empty($order_info['ticket_url'])){
                    $this->to_back(60027);
                }
                $ticket_url_info = parse_url($ticket_url);
                $data['ticket_url'] = $ticket_url_info['path'];
        }
        $ret = $m_dinner_order->updateInfo($where, $data);
        if($ret){
            $this->to_back(10000);
        }else {
            $this->to_back(60028);
        }
    }
    /**
     * @desc 修改预订信息
     */
    public function updateOrder(){
        $order_id = $this->params['order_id'];
        $invite_id  = $this->params['invite_id'];
        $mobile     = $this->params['mobile'];    //用户手机号
        if(!check_mobile($mobile)){
            $this->to_back('60002');
        }
        $m_hotel_invite_code = new \Common\Model\HotelInviteCodeModel();
        $where = array();
        $where['id'] = $invite_id;
        $where['state'] = 1;
        $where['flag'] = '0';
        $invite_info = $m_hotel_invite_code->getOne('bind_mobile,hotel_id', $where);
        if(empty($invite_id)){
            $this->to_back(60018);
        }
        if($invite_info['bind_mobile'] != $mobile){
            $this->to_back(60019);
        }
        $m_dinner_order = new \Common\Model\DinnerOrderModel();
        $fields = 'id,hotel_id,invite_id';
        $where = array();
        $where['id']  = $order_id;
        $where['flag']= 0;
        $order_info = $m_dinner_order->getOne($fields,$where);
        if(empty($order_info)){
            $this->to_back(60024);
        }
        if($invite_id != $order_info['invite_id']){
            $this->to_back(60033);
        }
        
        $order_name   = $this->params['order_name'];
        $order_mobile = $this->params['order_mobile'];
        $m_dinner_customer = new \Common\Model\DinnerCustomerModel();
        $where = " (`mobile`='$order_mobile' or `mobile1`='$order_mobile') and flag=0";
        $customer_info = $m_dinner_customer->getOne('id',$where);
        if(empty($customer_info)){
            $data = array();
            $data['invite_id'] = $invite_id;
            $data['name']      = $order_name;
            $data['mobile']    = $order_mobile;
            $m_dinner_customer->add($data);
            $customer_id = $m_dinner_customer->getLastInsID();
        }else {
            $customer_id = $customer_info['id'];
        }
        
        $where = array();
        $where['id']  = $order_id;
        $where['flag']= 0;
        $data = array();
        $data['customer_id']  = $customer_id;
        $data['order_mobile'] = $this->params['order_mobile'];
        $data['order_name']   = $this->params['order_name'];
        $data['order_time']   = $this->params['order_time'];
        $data['person_nums']  = $this->params['person_nums'];
        $data['room_id']      = $this->params['room_id'];
        $data['room_type']    = $this->params['room_type'];
        $data['remark']       = $this->params['remark'];
        $data['update_time']  = date('Y-m-d H:i:s');
        $ret = $m_dinner_order->updateInfo($where, $data);
        if($ret){
            $this->to_back(10000);
        }else {
            $this->to_back(60032);
        }
        
    }
    
    /**
     * @desc 删除预订信息
     */
    public function deleteOrder(){
        $order_id = $this->params['order_id'];
        $invite_id  = $this->params['invite_id'];
        $mobile     = $this->params['mobile'];    //用户手机号
        if(!check_mobile($mobile)){
            $this->to_back('60002');
        }
        $m_hotel_invite_code = new \Common\Model\HotelInviteCodeModel();
        $where = array();
        $where['id'] = $invite_id;
        $where['state'] = 1;
        $where['flag'] = '0';
        $invite_info = $m_hotel_invite_code->getOne('bind_mobile,hotel_id', $where);
        if(empty($invite_id)){
            $this->to_back(60018);
        }
        if($invite_info['bind_mobile'] != $mobile){
            $this->to_back(60019);
        }
        
        $m_dinner_order = new \Common\Model\DinnerOrderModel();
        $fields = 'id,hotel_id,invite_id';
        $where = array();
        $where['id']  = $order_id;
        $where['flag']= 0;
        $order_info = $m_dinner_order->getOne($fields,$where);
        if(empty($order_info)){
            $this->to_back(60024);
        }
        if($invite_id != $order_info['invite_id']){
            $this->to_back(60033);
        }
        
        /* 
        if($invite_info['hotel_id']!==$order_info['hotel_id']){
            $this->to_back(60030);
        } */
        $data = array();
        $data['flag'] = 1;
        $ret = $m_dinner_order->updateInfo($where, $data);
        if($ret){
            $this->to_back(10000);
        }else {
            $this->to_back(60031);
        }
    }
    /**
     * @desc 获取预订详情
     */
    public function getOrderDetail(){
        $order_id = $this->params['order_id'];
        $invite_id  = $this->params['invite_id'];
        $mobile     = $this->params['mobile'];    //用户手机号
        
        if(!check_mobile($mobile)){
            $this->to_back('60002');
        }
        $m_hotel_invite_code = new \Common\Model\HotelInviteCodeModel();
        $where = array();
        $where['id'] = $invite_id;
        $where['state'] = 1;
        $where['flag'] = '0';
        $invite_info = $m_hotel_invite_code->getOne('bind_mobile,hotel_id', $where);
        if(empty($invite_id)){
            $this->to_back(60018);
        }
        if($invite_info['bind_mobile'] != $mobile){
            $this->to_back(60019);
        }
      
        
        $m_dinner_order = new \Common\Model\DinnerOrderModel();
        $fields = 'a.id order_id,a.customer_id,a.room_id,a.room_type,a.order_time,
                   a.person_nums,a.order_name,a.order_mobile,a.remark,a.is_welcome,
                   a.is_recfood,a.ticket_url,b.face_url';
        $where = array();
        //$where['hotel_id']    = $invite_info['hotel_id'];
        $where['a.id'] = $order_id;
        $where['a.flag']        = 0;
        $order = 'a.order_time asc,a.id asc';
      
        
        $data = $m_dinner_order->getList($fields, $where);
        if($data){
            //$data = $data[0];
            $m_room = new \Common\Model\RoomModel();
            $m_dinner_room = new \Common\Model\DinnerRoomModel();
            $oss_path = C('TASK_REPAIR_IMG');
            $m_dinner_consume_record = new \Common\Model\DinnerConRecModel();
            foreach($data as $key=>$v){
                
                if($v['room_type']==1){//酒楼包间
                    $room_info = $m_room->getOne('name',array('id'=>$v['room_id']));
                }else if($v['room_type']==2){//手动添加包间
                    $room_info = $m_dinner_room->getOne('name', array('id'=>$v['room_id']));
                }
                $data[$key]['room_name'] = $room_info['name'];
                
                $data[$key]['time_str'] = date('Y-m-d',strtotime($v['order_time']));
                $data[$key]['moment_str'] = date('H:i',strtotime($v['order_time']));
                /* if(empty($v['ticket_url'])){//消费记录
                    $data[$key]['is_expense'] = 0;
                }else {
                    $data[$key]['is_expense'] = 1;
                } */
                
                $nums = $m_dinner_consume_record->countNums(array('order_id'=>$v['order_id'],'flag'=>0));
                if(empty($nums)){
                    $data[$key]['is_expense'] = 0;
                }else {
                    $data[$key]['is_expense'] = 1;
                }
                if(empty($v['remark'])){
                    //unset($data[$key]['remark']);
                    $data[$key]['remark'] = "";
                }
                if(!empty($v['face_url'])){
                    $data[$key]['face_url'] = $oss_path.$v['face_url'].'?x-oss-process=image/resize,w_100';
                }
                
                //unset($data[$key]['room_id']);
                //unset($data[$key]['room_type']);
                unset($data[$key]['order_time']);
                unset($data[$key]['ticket_url']);
                
            }
            $data = $data[0];
            $this->to_back($data);
        }else {
            $this->to_back(60024);
        }
    }
}