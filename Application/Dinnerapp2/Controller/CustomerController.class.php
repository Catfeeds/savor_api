<?php
/**
 * @desc 餐厅端2.0-客户信息管理
 * @author baiyutao
 * @date  20171219
 */
namespace Dinnerapp2\Controller;
use Think\Controller;
use \Common\Controller\BaseController as BaseController;
class CustomerController extends BaseController{
    /**
     * 构造函数
     */
    function _init_() {
        switch(ACTION_NAME) {
            case 'addCustomer':
                $this->is_verify = 1;
                $this->valid_fields = array(
                    'invite_id'     =>1001,
                    'mobile'        =>1001,
                    'usermobile'    =>1001,
                    'name'          =>1001,
                );
                break;
            case 'editCustomer':
                $this->is_verify = 1;
                $this->valid_fields = array(
                    'invite_id'     =>1001,
                    'mobile'        =>1001,
                    'usermobile'    =>1001,
                    'name'          =>1001,
                    'customer_id'   =>1001,
                );
                break;

            case 'addConsumeRecord':
                $this->is_verify = 1;
                $this->valid_fields = array(
                    'invite_id'     =>1001,
                    'mobile'        =>1001,
                    'customer_id'    =>1001,
                );
                break;
            case 'getCustomerHistory':
                $this->is_verify = 1;
                $this->valid_fields = array(
                    'invite_id'     =>1001,
                    'mobile'        =>1001,
                );
                break;
            case 'getCustomerBaseInfo':
                $this->is_verify = 1;
                $this->valid_fields = array(
                    'invite_id'     =>1001,
                    'mobile'        =>1001,
                );
                break;
            case 'getConsumeRecordTopList':
                $this->is_verify = 1;
                $this->valid_fields = array(
                    'invite_id'     =>1001,
                    'mobile'        =>1001,
                );
                break;
            case 'importInfo':
                $this->is_verify = 1;
                $this->valid_fields = array('invite_id'=>1001,'mobile'=>1001,'book_info'=>1001);
                break;
            default:
                 break;
        }
        parent::_init_();
        $this->vcode_valid_time =  600;
    }
    /**
     * @desc 导入通讯录
     */
    public function  importInfo(){
        $invite_id = $this->params['invite_id'];
        $mobile   = $this->params['mobile'];    //用户手机号
        //$hotel_id = $this->params['hotel_id'];  //酒楼id
        $book_info= $this->params['book_info']; //通讯录列表
    
        if(!check_mobile($mobile)){
            $this->to_back('60002');
        }
        $m_hotel_invite_code = new \Common\Model\HotelInviteCodeModel();
        $where = array();
        $where['id'] = $invite_id;
        $where['state'] = 1;
        $where['flag'] = '0';
        $invite_info = $m_hotel_invite_code->getOne('bind_mobile', $where);
        if(empty($invite_id)){
            $this->to_back(60018);
        }
        if($invite_info['bind_mobile'] != $mobile){
            $this->to_back(60019);
        }
        $m_dinner_customer = new \Common\Model\DinnerCustomerModel();
        $where = array();
        $where['invite_id'] = $invite_id;
        $where['flag']      =0;
        $customer_nums = $m_dinner_customer->countNums($where);
        if(!empty($customer_nums)){
            $this->to_back(60020);
        }
        
        $book_info = str_replace('\\', '', $book_info);
        $book_info =  json_decode($book_info,true);
        $m_hotel_invite_code = new \Common\Model\HotelInviteCodeModel();
        $fields = 'id';
        $where = array();
        $where['bind_mobile'] = $mobile;
        $where['state'] = 1;
        $where['flag']  = 0;
        $info = $m_hotel_invite_code->getOne($fields, $where);
        if(empty($info)){
            $this->to_back(60015);
        }
        $flag = 0;
        if(!empty($book_info)){
            //print_r($book_info);exit;
            foreach($book_info as $key=>$v){
                $where = '';
                if(!empty($v['mobile'])){//第一个手机号不为空
                    $where .= " (mobile='".$v['mobile']."'";
                }
                if(!empty($v['mobile1'])){//第二个手机号不为空
                    if(empty($where)){
                        $where .=" (mobile1='".$v['mobile']."'";
                    }else{
                        $where .=" or  mobile1='".$v['mobile']."'";
                    }
                }
                if(!empty($where)){
                    $where .=") and invite_id=$invite_id";
                    $nums = $m_dinner_customer->countNums($where);
                    
                    if(!empty($nums)){
                        continue;
                    }
                }else {
                     $v['invite_id'];
                     $m_dinner_customer->add($v); 
                }
                $flag ++;  
            }
            if($flag){
                $this->to_back(10000);
            }else {
                $this->to_back(60016);
            }
        }else {
            $this->to_back(60017);
        }
    
    }

    public function getCustomerHistory(){
        $invite_id = $this->params['invite_id'];
        $mobile   = $this->params['mobile'];    //销售手机号
        $m_hotel_invite_code = new \Common\Model\HotelInviteCodeModel();
        $where = array();
        $where['id'] = $invite_id;
        $where['state'] = 1;
        $where['flag'] = '0';
        $invite_info = $m_hotel_invite_code->getOne('bind_mobile', $where);
        if(empty($invite_id)){
            $this->to_back(60018);
        }
        if($invite_info['bind_mobile'] != $mobile){
            $this->to_back(60019);
        }

        $field = 'sct.name username,sa.create_time,sct.mobile,sct.mobile1,sa.type';
        $map = array();
        $map['sct.flag'] = 0;
        $map['sa.invite_id'] = $invite_id;
        $order = 'sa.create_time desc';
        $limit = 20;
        $m_dinner_customer_log = new \Common\Model\DinnerActionLogModel();
        $res_info = $m_dinner_customer_log->getLatestCusInfo($field, $where, $order, $limit);
        $now = time();
        foreach($res_info as $ra=>$rk) {
            $mobile_ar = array_merge($rk['mobile'], $rk['mobile1']);
            $res_info[$ra]['usermobile'] = current($mobile_ar);
            //判断时间
            $ltime = strtotime($rk['create_time']);
            $diff = ($now-$ltime);
            if($diff< 3600) {
                $dp = floor($diff/60).'分钟';

            }else if ($diff >= 3600 && $diff <= 86400) {
                $hour = floor($diff/3600);
                $min = floor($diff%3600/60);
                $dp = $hour.'小时'.$min.'分钟';
            }else if ($diff > 86400) {
                $day = floor($diff/86400);
                $hour = floor($diff%86400/3600);
                $dp = $day.'天'.$hour.'小时';
            }
            $res_info[$ra][create_time] = $dp;
            if($rk['type'] == 1) {
                $res_info[$ra]['type'] = '新增';
            }
            if($rk['type'] == 2) {
                $res_info[$ra]['type'] = '修改';
            }
            if($rk['type'] == 3) {
                $res_info[$ra]['type'] = '查看';
            }
            if($rk['type'] == 4) {
                $res_info[$ra]['type'] = '预定';
            }
        }
    }

    public function getCustomerBaseInfo(){
        $customer_id  = $this->params['customer_id'];
        $m_dinner_cus = new \Common\Model\DinnerCustomerModel();
        $field = 'name username,mobile usermobile,mobile1 usermobile1
        ,sex,birthday,birthplace,face_url,consume_ability ,remark';
        $cus_info = $m_dinner_cus->getOneRow($field, $customer_id);
        $abi_num = $cus_info['consume_ability'];
        $config_abi = C('CONSUME_ABILITY');
        $cus_info['consume_ability'] = $config_abi[$abi_num];
        if($cus_info['sex'] == 1) {
            $cus_info['sex'] = '男';
        } else {
            $cus_info['sex'] = '女';
        }
        //获取标签

        $m_customer_lab = new \Common\Model\DinnerCustomerLabelModel();
        $map = array();
        $map['customer_id'] = $customer_id;
        $map['flag'] = 0;
        $field = 'scl.label_id,sdl.NAME label_name';
        $label_info = $m_customer_lab->getLabelNameByCid($field, $map);
        if($label_info) {
            $cus_info['label'] = $label_info;
        }else {
            $cus_info['label'] = array();
        }
        $data['list'] = $cus_info;
        $this->to_back($data);
    }

    /*
     * 上拉消费记录
     */
    public function getConRecUpList(){
        $max_id  = $this->params['max_id'];
        $mp = array();
        $mp['flag'] = 0;
        $order = 'id asc';
        $limit = 10;
        if($max_id) {
            $mp['id'] = array('gt',$max_id);
        } else {

        }
    }
    /*
     * 下拉消费记录
     */
    public function getConRecTopList(){
        //取最新的并按倒序排 1下拉2上拉
        $ptype = $this->params['type'];
        $invite_id = $this->params['invite_id'];
        $mobile   = $this->params['mobile'];    //销售手机号
        $m_hotel_invite_code = new \Common\Model\HotelInviteCodeModel();
        $where = array();
        $where['id'] = $invite_id;
        $where['state'] = 1;
        $where['flag'] = '0';
        $invite_info = $m_hotel_invite_code->getOne('bind_mobile', $where);
        if(empty($invite_id)){
            $this->to_back(60018);
        }
        if($invite_info['bind_mobile'] != $mobile){
            $this->to_back(60019);
        }
        $limit = 10;
        $mp = array();
        $mp['scr.invite_id'] = $invite_id;
        $mp['scr.flag'] = 0;
        $mp['scr.customer_id'] = $this->params['customer_id'];
        $order = 'scr.id desc';
        if($ptype == 1) {
            $m_dinner_consume = new \Common\Model\DinnerConRecModel();
            $max_id  = $this->params['max_id'];
            if($max_id) {
                $mp['id'] = array('gt',$max_id);
            }
        }
        if($ptype == 2) {
            $min_id  = $this->params['min_id'];
            $mp['id'] = array('lt',$min_id);
        }
        $field = ' scr.order_id,  scr.create_time,scr.recipt, sdo.room_type,sdo.room_id,sdo.hotel_id ';
        $consume_arr = $m_dinner_consume->getConsumeList($field, $mp, $order, $limit);
        if($consume_arr) {
            $roomModel = new \Common\Model\RoomModel();
            $m_dinner_room = new \Common\Model\DinnerRoomModel();
            $count = 0;
            foreach($consume_arr as $ck=>$cv) {
                if(!empty($cv['order_id'])) {
                    if($cv['room_type'] == 1) {
                        //酒楼包间
                        $field = 'name rname';
                        $ro['hotel_id'] = $cv['hotel_id'];
                        $ro['id'] = $cv['room_id'];
                        $room_info = $roomModel->getOne($field, $ro);
                        $cv['room_name'] = $room_info['rname'];
                    }
                    if($cv['room_type'] == 2) {
                        $field = 'name rname';
                        $ro['hotel_id'] = $cv['hotel_id'];
                        $ro['id'] = $cv['room_id'];
                        $room_info = $m_dinner_room->getOne($field, $ro);
                        $cv['room_name'] = $room_info['rname'];
                    }
                }else {
                    $cv['room_name'] = '';
                }
                $count++;
            }
        }
        $data['list'] = $consume_arr;
        $data[min_id] = $consume_arr[$count-1][order_id];
        $data[max_id] = $consume_arr[0][order_id];
        $this->to_back($data);
    }

    public function addConsumeRecord() {
        //type 1:单个只截图2.多个信息填写
        $ptype  = empty($this->params['type'])?1:$this->params['type'];
        $invite_id = $this->params['invite_id'];
        $mobile   = $this->params['mobile'];    //销售手机号
        $m_hotel_invite_code = new \Common\Model\HotelInviteCodeModel();
        $where = array();
        $where['id'] = $invite_id;
        $where['state'] = 1;
        $where['flag'] = '0';
        $invite_info = $m_hotel_invite_code->getOne('bind_mobile', $where);
        if(empty($invite_id)){
            $this->to_back(60018);
        }
        if($invite_info['bind_mobile'] != $mobile){
            $this->to_back(60019);
        }
        $cus = array();
        $cus['customer_id']  = $this->params['customer_id'];
        $cus['invite_id']  = $invite_id;
        $cus['flag'] = 0;
        //判断客户id存在
        $m_dinner_cus = new \Common\Model\DinnerCustomerModel();
        $cus_num = $m_dinner_cus->countNums($cus);
        $recipt = empty($this->params['recipt'])?0:$this->params['recipt'];
        $recipt_arr = parse_url($recipt);
        $save['recipt']  = $recipt_arr['path'];
        $save['invite_id'] = $invite_id;
        if($cus_num > 0) {
            $save['customer_id']  = $cus['customer_id'];
            if ($ptype == 1) {
                $m_di_consume = new \Common\Model\DinnerConRecModel();
                $bool = $m_di_consume->addData($save);
                if($bool) {
                    $this->to_back(10000);
                } else {
                    $this->to_back(60113);
                }

            }
            if ($ptype == 2) {
                //usermobile填写
                $cus['id']         = $this->params['orderid'];
                //判断预订id是否存在
                $m_dinner_order = new \Common\Model\DinnerOrderModel();
                $field = 'id';
                $or_res = $m_dinner_order->getOne($field, $cus);
                if($or_res) {
                    $save['order_id']  = $cus['id'];
                    $save['name']  = empty($this->params['name'])?0:$this->params['name'];
                    $usermobile    = empty($this->params['usermobile'])?'':$this->params['usermobile'];
                    //判断手机号是否在表中存在
                    $m_dinner_customer = new \Common\Model\DinnerCustomerModel();
                    $mp = array();
                    $mp['mobile'] = $usermobile;
                    $mp['mobile1'] = $usermobile;
                    $mp['_logic'] = 'or';
                    $map['_complex'] = $mp;
                    $field = 'id';
                    $cus_info = $m_dinner_customer->getOne($field,$map);
                    if($cus_info) {
                        $save['customer_id'] = $cus_info['id'];
                        $bool = $m_dinner_order->addInfo($save);
                        if($bool) {
                            $this->to_back(10000);
                        } else {
                            $this->to_back(60113);
                        }
                    }else {
                        //添加客户表
                        $map = array();
                        $map['name'] = $save['name'];
                        $map['invite_id'] = $invite_id;
                        $map['mobile'] = $usermobile;
                        $insid = $m_dinner_customer->addData($save);
                        if($insid) {
                            //加lg日志
                            $m_dinner_customer_log = new \Common\Model\DinnerActionLogModel();
                            $log_arr['action_id'] = $insid;
                            $log_arr['type'] = 1;
                            $log_arr['invite_id'] = $invite_id;
                            $m_dinner_customer_log->addData($log_arr);
                            $save['customer_id'] = $insid;
                            $bool = $m_dinner_order->addInfo($save);
                            if($bool) {
                                $this->to_back(10000);
                            } else {
                                $this->to_back(60113);
                            }
                        } else {
                            $this->to_back(60113);
                        }

                    }
                } else{
                    $this->to_back(60114);
                }

            }
        } else {
            $this->to_back(60017);
        }
    }

    public function editCustomer() {


        $mobile = $this->params['mobile'];
        //验证管理人手机格式
        if(!check_mobile($mobile)){
            $this->to_back(60002);
        }
        $invite_id = $this->params['invite_id'];
        if(!is_numeric($invite_id)) {
            $this->to_back(60100);
        }
        //客户手机
        $usermobile_str = $this->params['usermobile'];
        $usermobile_str = str_replace('\\','',$usermobile_str);
        $usermobile_arr = json_decode($usermobile_str, true);
        $tel_a = empty($usermobile_arr[0])?'':$usermobile_arr[0];
        $tel_b = empty($usermobile_arr[1])?'':$usermobile_arr[1];
        if (empty($tel_a) && empty($tel_b)) {
            $this->to_back(60104);
        }
        if ($tel_a == $tel_b) {
            $this->to_back(60103);
        }
        //验证手机格式
        foreach ($usermobile_arr as $uv ) {
            if(!empty($uv) &&!check_mobile($uv)){
                $this->to_back(60002);
            }
        }
        $m_hotel_invite_code = new \Common\Model\HotelInviteCodeModel();
        $where = array();
        $where['id'] = $invite_id;
        $where['state'] = 1;
        $where['flag'] = '0';
        $invite_info = $m_hotel_invite_code->getOne('bind_mobile', $where);
        if(empty($invite_id)){
            $this->to_back(60018);
        }
        if($invite_info['bind_mobile'] != $mobile){
            $this->to_back(60019);
        }

        //判断用户名是否存在
        $username    = empty($this->params['name'])?'':$this->params['name'];
        $m_dinner_customer = new \Common\Model\DinnerCustomerModel();
        $save['name']                = $username;
        $save['sex']                = empty($this->params['sex'])?1:$this->params['sex'];
        $save['birthplace']         = empty($this->params['birthplace'])?'':$this->params['birthplace'];
        $save['birthday']           = empty($this->params['birthday'])?'':$this->params['birthday'];
        $save['invite_id']          = $invite_id;
        $save['consume_ability']    = empty($this->params['consume_ability'])?0:$this->params['consume_ability'];
        $save['bill_info']    = empty($this->params['bill_info'])?'':$this->params['bill_info'];
        $save['remark']    = empty($this->params['remark'])?'':$this->params['remark'];
        $save['flag']               = 0;
        $fimg = empty($this->params['face_url'])?'':$this->params['face_url'];
        $save['face_url'] = '';
        if($fimg){
            $face_arr = parse_url($fimg);
            $save['face_url'] = $face_arr['path'];
        }
        $c_id  = empty($this->params['customer_id'])?0:$this->params['customer_id'];
        $map = array();
        $map['invite_id'] = $invite_id;
        $map['flag'] = 0;
        $save['update_time'] = date("Y-m-d H:i:s");
        if($c_id) {
            $field = '*';
            $mop['id'] = $c_id;
            $mop['invite_id'] = $invite_id;
            $mop['flag'] = 0;
            $cus_info = $m_dinner_customer->getOne($field, $mop);
            if($cus_info) {
                $mobile = $cus_info['mobile'];
                $mobile1 = $cus_info['mobile1'];
                //没更新改过
                $map = array();
                if($mobile == $tel_a || $mobile1 == $tel_b) {
                    $map['id'] = $c_id;
                    $bool = $m_dinner_customer->saveData($save, $map);
                }else {
                    $field = 'id,mobile,mobile1';
                    if(empty($tel_a)) {
                        $mp = array();
                        $mp['mobile'] = $tel_b;
                        $mp['mobile1'] = $tel_b;
                        $mp['_logic'] = 'or';
                        $map['_complex'] = $mp;
                        $d_res = $m_dinner_customer->countNums($map);
                        if($d_res) {
                            $this->to_back(60106);
                        }
                        $save['mobile1'] = $tel_b;
                    }
                    if(empty($tel_b)) {
                        $mp = array();
                        $mp['mobile'] = $tel_a;
                        $mp['mobile1'] = $tel_a;
                        $mp['_logic'] = 'or';
                        $map['_complex'] = $mp;
                        $d_res = $m_dinner_customer->countNums($map);
                        if($d_res) {
                            $this->to_back(60105);
                        }
                        $save['mobile'] = $tel_a;
                    }
                    if(!empty($tel_a) && !empty($tel_b)) {
                        $field = 'id,mobile,mobile1';
                        $map['_string'] = " (mobile like '".$tel_a."') or
        (mobile1 like '".$tel_a."') or (mobile like '".$tel_b."')
         or (mobile1 like '".$tel_b."') ";
                        $d_res = $m_dinner_customer->getData($field, $map);
                        if($d_res) {
                            $mobile = $d_res[0]['mobile'];
                            $mobil1 = $d_res[0]['mobile1'];
                            if($tel_a == $mobil1 || $tel_a == $mobile) {
                                $this->to_back(60105);
                            }
                            if($tel_b == $mobil1 || $tel_b == $mobile) {
                                $this->to_back(60106);
                            }
                        }
                        $save['mobile'] = $tel_a;
                        $save['mobile1'] = $tel_b;
                    }
                    var_export($save);
                    var_export($map);
                    $rp['id'] = $c_id;
                    $bool = $m_dinner_customer->saveData($save, $rp);
                }
                if($bool) {
                    $m_dinner_customer_log = new \Common\Model\DinnerActionLogModel();
                    $log_arr['action_id'] = $c_id;
                    $log_arr['type'] = 2;
                    $log_arr['invite_id'] = $invite_id;
                    $m_dinner_customer_log->addData($log_arr);
                    $m_dinner_customer_log->addData($log_arr);
                    $this->to_back(10000);
                } else {
                    $this->to_back(60110);
                }
            } else {
                $this->to_back(60108);
            }

        } else {
            $this->to_back(60107);
        }


    }

    public function addCustomer() {
        //type 1增加 2修改
        $mobile = $this->params['mobile'];
        //验证管理人手机格式
        if(!check_mobile($mobile)){
            $this->to_back(60002);
        }
        $invite_id = $this->params['invite_id'];
        if(!is_numeric($invite_id)) {
            $this->to_back(60100);
        }
        //客户手机
        $usermobile_str = $this->params['usermobile'];
        $usermobile_str = str_replace('\\','',$usermobile_str);
        $usermobile_arr = json_decode($usermobile_str, true);
        $tel_a = empty($usermobile_arr[0])?'':$usermobile_arr[0];
        $tel_b = empty($usermobile_arr[1])?'':$usermobile_arr[1];
        if (empty($tel_a) && empty($tel_b)) {
            $this->to_back(60104);
        }
        if ($tel_a == $tel_b) {
            $this->to_back(60103);
        }

        //验证手机格式
        foreach ($usermobile_arr as $uv ) {
            if(!empty($uv) &&!check_mobile($uv)){
                $this->to_back(60002);
            }
        }
        $m_hotel_invite_code = new \Common\Model\HotelInviteCodeModel();
        $where = array();
        $where['id'] = $invite_id;
        $where['state'] = 1;
        $where['flag'] = '0';
        $invite_info = $m_hotel_invite_code->getOne('bind_mobile', $where);
        if(empty($invite_id)){
            $this->to_back(60018);
        }
        if($invite_info['bind_mobile'] != $mobile){
            $this->to_back(60019);
        }

        //判断用户名是否存在
        $username    = empty($this->params['name'])?'':$this->params['name'];
        $m_dinner_customer = new \Common\Model\DinnerCustomerModel();
        $save['name']                = $username;
        $save['sex']                = empty($this->params['sex'])?1:$this->params['sex'];
        $save['birthplace']         = empty($this->params['birthplace'])?'':$this->params['birthplace'];
        $save['birthday']           = empty($this->params['birthday'])?'':$this->params['birthday'];
        $save['invite_id']          = $invite_id;
        $save['consume_ability']    = empty($this->params['consume_ability'])?0:$this->params['consume_ability'];
        $save['bill_info']    = empty($this->params['bill_info'])?'':$this->params['bill_info'];
        $save['remark']    = empty($this->params['remark'])?'':$this->params['remark'];
        $save['flag']               = 0;
        $fimg = empty($this->params['face_url'])?'':$this->params['face_url'];
        $save['face_url'] = '';
        if($fimg){
            $face_arr = parse_url($fimg);
            $save['face_url'] = $face_arr['path'];
        }

        $map = array();
        $map['invite_id'] = $invite_id;
        $map['flag'] = 0;
        if(empty($tel_a)) {
            $mp = array();
            $mp['mobile'] = $tel_b;
            $mp['mobile1'] = $tel_b;
            $mp['_logic'] = 'or';
            $map['_complex'] = $mp;
            $d_res = $m_dinner_customer->countNums($map);
            if($d_res) {
                $this->to_back(60106);
            }
            $save['mobile1'] = $tel_b;
        }
        if(empty($tel_b)) {

            $mp = array();
            $mp['mobile'] = $tel_a;
            $mp['mobile1'] = $tel_a;
            $mp['_logic'] = 'or';
            $map['_complex'] = $mp;
            $d_res = $m_dinner_customer->countNums($map);
            if($d_res) {
                $this->to_back(60105);
            }
            $save['mobile'] = $tel_a;
        }
        if(!empty($tel_a) && !empty($tel_b)) {
            $field = 'id,mobile,mobile1';
            $map['_string'] = " (mobile like '".$tel_a."') or
        (mobile1 like '".$tel_a."') or (mobile like '".$tel_b."')
         or (mobile1 like '".$tel_b."') ";
            $d_res = $m_dinner_customer->getData($field, $map);
            if($d_res) {
                $mobile = $d_res[0]['mobile'];
                $mobil1 = $d_res[0]['mobile1'];
                if($tel_a == $mobil1 || $tel_a == $mobile) {
                    $this->to_back(60105);
                }
                if($tel_b == $mobil1 || $tel_b == $mobile) {
                    $this->to_back(60106);
                }
            }
            $save['mobile'] = $tel_a;
            $save['mobile1'] = $tel_b;
        }
        $insid = $m_dinner_customer->addData($save);
        if($insid) {
            $m_dinner_customer_log = new \Common\Model\DinnerActionLogModel();
            $log_arr['action_id'] = $insid;
            $log_arr['type'] = 1;
            $log_arr['invite_id'] = $invite_id;
            $m_dinner_customer_log->addData($log_arr);
            $this->to_back(10000);
        } else {
            $this->to_back(60101);
        }
    }

}