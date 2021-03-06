<?php

/**
 * ThinkPHP 简体中文语言包
 */
return array(
	'parmas_not_null'=>'必传参数不能为空',
	'parmas_null'=>'参数为空',
	'traceinfo_not_null'=>'traceinfo不能为空',
	'mobile_deviceid_not_match'=>'用户手机设备不一致',
	'token_not_null'=>'令牌不能为空',
	'token_has_expired'=>'token已失效',
	'sign_error'=>'签名错误',
	'success'=>'成功',
    
    //心跳上报
    'heart_mac_period_not_null'=>'mac地址不能为空',
    'heart_clientid_range_err'=>'clinetid不在有效范围内',
    'heart_mac_invalid'=>'mac地址非法',
    'heart_hotelid_invalid'=>'hotelid非法',
	//用户相关
	'user_not_exist' =>'用户不存在',
	'user_login_err'=>'用户登录失败',

	 //记录用户首次使用app
    'first_use_have_data'=>'该设备已记录首次使用数据',
    'first_use_push_err'=>'记录失败',
	//客户端类型
	'ctype_illegal'=>'类型标识错误',
	'cltype_insert_fail'=>'数据插入失败',
    //下载统计相关
    'download_source_error'=>'下载来源非法',
    'client_error'=>'客户端类型非法',
    'download_data_insert_error'=>'统计数据入库失败',
    'this_facility_have_download'=>'该设备已经下载安装过',
    
    //抽奖
    'box_not_set_award'=>'该机顶盒未设置奖项',
    'box_award_record_error'=>'机顶盒上报中奖信息失败',
    'have_not_this_box'=>'机顶盒不存在',
    'this_award_have_empty'=>'该奖项已经被抽空',
    'this_award_not_have_current'=>'该奖品未设置当前奖品剩余数量',


    //云平台PHP接口
    'down_hotel_infotype_error'=>'酒楼下载文件来源类型非法',
    'small_platform_hotel_error'=>'该酒楼为非正常状态',
    'small_platform_report_error'=>'上报数据入库失败',
    'small_platform_report_type_error'=>'上报数据类型错误',
    'hotel_not_set_small_plat_upgrade_version'=>'该酒楼未设置升级包',
    'have_not_this_small_plat_upgrade_war'=>'升级包不存在',
    'have_not_upgrade_sql'=>'无升级sql',
    'stas_time_illegal'=>'非标准时间戳格式',
    'stas_time_insert_fail'=>'存入redis失败',
    //客户端获取投屏酒楼距离接口
    'lat_illegal'=>'纬度值非法',
    'lng_illegal'=>'经度值非法',
    //用户收藏接口
    'deviceid_error'=>'用户设备号不可为空',
    'artid_error'=>'文章id非法',
    'artid_not_check'=>'文章id未审核通过',
    'addmycollection_insert_fail'=>'收藏插入失败',
    'addmycollection_update_fail'=>'收藏更新失败',
    //创富生活接口
    'hot_category_id_error'=>'分类id参数错误',
    'content_not_check_pass'=>'该文章不存在或未通过审核',
    'not_demand_content'=>'该内容不可点播',
	//专题组接口
	'special_group_not_exist'=>'专题组不存在',
    
    //运维客户端接口
    'option_user_not_exist'=>'登录失败,没有登录权限',
    'option_user_pwd_error'=>'登录失败,账号密码错误',
    'option_user_illegeal'=>'用户非法',
    'option_user_pro' =>'用户是否解决必选',
    'option_box_not_exists'=>'机顶盒mac不存在',
    'option_pla_not_exists'=>'小平台mac不存在',
    'option_reason_not_empty'=>'请选择维修记录或填写备注',
    'option_insert_fail'=>'插入记录失败',
    'option_error_report_not_exist'=>'该异常记录不存在',
    'option_error_hotel_not_exist'=>'该异常记录不包含该酒楼',
    'option_notallow_remark'=>'备注限制100字',
    //运维客户端接口1.1
    'option_user_role_empty'=>'该账号未设置运维角色',
    'option_user_role_error'=>'账号角色非法',
    'option_task_empty'=>'该任务不存在',
    'option_task_execuser_illegal'=>'该执行者无此权限',
    'option_boxid_not_null'=>'版位与是否解决为必填项',
    'option_task_state_error'=>'该任务为非处理中任务',
    'option_task_upload_pic_error'=>'上传照片数超过最大值',
    'option_task_record_error'=>'该版位维修记录已提交',
    'option_task_infocheck_error'=>'该任务信息检测已提交',
    'option_task_netmodify_error'=>'该任务网络改造已提交',
    'option_task_record_fail'=>'该版位维修记录提交失败',
    'option_task_submit_fail'=>'执行任务失败',
    'option_task_installl_error'=>'该任务安装流程已完成',
    'option_task_not_new_task'=>'该任务不是新任务', 
    'option_task_refuse_err'=>'任务拒绝失败',
    'option_task_appoint_err'=>'任务指派失败',
    'option_user_manage_city_err'=>'没有该城市的权限',
    'option_task_bind_error'=>'绑定机顶盒参数传递错误',
    'option_task_bind_mac_have'=>'该机顶盒mac地址已存在',
    'option_bind_mac_update_fail'=>'机顶盒mac更新失败',
    'option_user_role_null'=>'登录失败，没有登录权限',
    'option_task_type_empty'=>'任务类型错误',
    'option_task_type_changed'=>'任务类型不一致',
    'option_task_upload_img_nums_err'=>'上传照片数量错误',
    'option_task_bind_mac_repeat'=>'绑定MAC与机顶盒MAC相同',
    'option_ads_list_err'=>'由于正在下载历史数据，无法查看列表',
    'option_pro_empty'=>'该节目单不存在',
    'option_task_box_have_repaired'=>'该版位已经维修处理过,请勿重复操作',
    'option_task_have_completed'=>'该任务已经由#处理为完成状态',
    'option_task_box_have_completed'=>'该版位已经由#维修，请处理其它版位',
    'option_task_mobile_empty'=>'酒楼联系人电话不能为空',
    'option_task_mobile_illegal'=>'酒楼联系电话非法',
    //每日知享接口
    'daily_content_not_exist'=>'文章不存在',
    'daily_content_collection_err'=>'收藏失败',
    'daily_content_not_collection_err'=>'取消收藏失败',
    'daily_keywords_empty'=>'关键词为空',
    'daily_user_add_fail'=>'用户添加失败',
    'daily_user_ptype_notnull'=>'人群设定不可为空',
    'daily_user_update_fail'=>'用户更新失败',
    'daily_user_tel_illegal'=>'手机号输入有误',
    'daily_user_code_notnull'=>'验证码不可为空',
    'daily_user_code_illegal'=>'验证码错误或已过期',
    'daily_user_code_min'=>'一分钟内请勿重复获取验证码',
    'daily_code_send_fail'=>'验证码发送失败',
    //升级接口
    'version_device_type_err'=>'clientname错误',
    'program_hotel_not_exist'=>'该酒楼不存在',
    'program_hotel_have_delete'=>'该酒楼已删除',
    'program_hotel_abnormal'=>'该酒楼为非正常酒楼',
    'program_hotel_not_net_box'=>'该酒楼为非网络版',
    'program_hotel_box_empty'=>'该就楼下没有正常的机顶盒',
    'program_hotel_menu_empty'=>'该酒楼未设置节目单',
    'program_ads_num_empty'=>'该广告号不存在',
    'program_menu_update_donwstate_error'=>'酒楼下载节目单更新下载状态失败',
    'program_hotel_have_donw_success'=>'该酒楼已成功下载过',
    //餐厅端接口
    'dinner_reportlog_touping_fail'=>'餐厅端酒楼投屏日志上传失败',
    'dinner_mobile_error'=>'手机号输入有误',
    'dinner_user_code_min'=>'一分钟内请勿重复获取验证码',
    'dinner_user_code_illegal'=>'验证码错误或已过期',
    'dinner_invite_code_err'=>'您输入的邀请码不正确,请重新输入',
    'dinner_user_login_err'=>'登录失败',
    'dinner_hotel_rec_food_empty'=>'当前酒楼没有推荐菜，请联系酒楼维护人员添加',
    'dinner_hotel_empty'=>'邀请码对应酒楼不存在',
    'dinner_hotel_state_err'=>'邀请码对应的酒楼状态异常',
    'dinner_mobile_not_fit_invitecode'=>'该手机号与邀请码绑定手机号不一致',
    'dinner_invite_code_have_used'=>'该邀请码已经被使用,请更换',
    'dinner_please_input_your_invite_code'=>'您已经绑定邀请码，请输入正确邀请码',
    'dinner_hotel_adv_list_empty'=>'该酒楼没有宣传片，请联系酒楼维护人员添加',
    'dinner_hotel_room_empty'=>'该酒楼没有包间，请联系酒楼维护人员添加',
    'dinner_invite_id_illegal'=>'传参邀请id应为整数',

    'dinner_mobile_not_bind_code'=>'该手机号还未绑定酒楼邀请码',
    'dinner_customer_import_err'=>'通讯录导入失败',
    'dinner_customer_import_empty'=>'导入通讯录不能为空',
    'dinner_bind_invite_err'=>'用户绑定关系不存在',
    'dinner_bind_mobile_err'=>'绑定手机号与该手机号不一致',
    'dinner_customer_have_import'=>'该账号已经导入过通讯录',


    'dinner_customer_insert_fail'=>'添加客户失败请重试',
    'dinner_customer_already_exist'=>'该客户联系方式已经添加过',
    'dinner_customer_tel_repeat'=>'客户电话请勿重复',
    'dinner_customer_tel_empty'=>'客户两个电话不可都为空',
    'dinner_customer_tel1_exist'=>'手机号1已经存在',
    'dinner_customer_tel2_exist'=>'手机号2已经存在',
    'dinner_customer_id_empty'=>'客户id不可为空',
    'dinner_customer_empty'=>'该客户不存在',
    'dinner_customer_label_add_fail'=>'客户标签添加失败',
    'dinner_customer_update_fail'=>'客户更新失败',
    'dinner_customer_label_illegal'=>'客户标签id不可为空',
    'dinner_customer_label_not_exist'=>'客户标签id不存在',
    'dinner_customer_label_die_fail'=>'客户标签熄灭失败',
    'dinner_customer_consume_failed'=>'消费记录添加失败',
    'dinner_label_type_error'=>'点亮熄灭标签类型传参错误',
    'dinner_order_not_exist'=>'预订信息不存在',
    'dinner_cosumerecord_type_error'=>'添加消费记录类型传参错误',
    'dinner_customer_tel_notsame'=>'客户ID与电话得到ID不一致',

    'dinner_room_add_failed'=>'包间添加失败',
    'dinner_room_name_repeat'=>'包间名称已存在，请重新输入',
    'dinner_order_add_failed'=>'预订添加失败',
    'dinner_order_empty'=>'该预订信息不存在',
    'dinner_is_welcome_used'=>'该预订信息的欢迎词功能已经使用过',
    'dinner_is_recfood_used'=>'该预订信息的推荐菜功能已经使用过',
    'dinner_ticket_url_have_upload'=>'该预订信息的消费小票已经上传过',
    'dinner_service_update_failed'=>'更新失败',
    'dinner_service_ticket_url_empty'=>'请上传消费小票',
    'dinner_cannot_del_other_hotel_order'=>'不可删除其它酒楼的预订信息',
    'dinner_order_del_failed'=>'预订信息删除失败',
    'dinner_order_update_failed'=>'预订信息更新失败',
    'dinner_order_donot_belong_you'=>'该预订不属于你',
    'dinner_customer_mobile_err'=>'请输入正确的客户手机',
    'dinner_customer_have_exist'=>'导入失败，该用户已经存在',
    
    'box_not_exist'=>'该机顶盒不存在',
    'box_mac_error'=>'mac与机顶盒mac不一致',
    'box_device_token_report_error'=>'机顶盒device_token上报失败',
    'box_room_empty' =>'包间不存在',
    'box_device_token_empty'=>'机顶盒设备号为空',
    
    'hotel_attendant_empty'=>'酒楼工作人员mac为空',
    'not_in_rtb_ads_push_time'=>'不在推送时间范围内',
    'rtb_push_ads_list_empty'=>'推送RTB广告列表为空',
    'box_report_download_empty'=>'上报下载资源为空',
    'box_report_download_same'=>'上报下载资源和当前记录下载资源相同',
    'box_report_play_same'=>'上报播放资源和当前记录播放资源相同',

    'box_mac_not_empty'=>'机顶盒MAC不可为空',
    'box_mac_illegal'=>'机顶盒MAC不存在',
    'box_memory_state_illegal'=>'机顶盒内存状态上报值错误',
    'boxmem_data_insert_error'=>'数据入库失败',
    'boxmem_data_already_insert'=>'内存卡信息已经上报',
    'poly_ads_empty'=>'聚屏广告列表为空',
    'poly_hotel_not_normal'=>'酒楼不存在或者为非正常酒楼',
    'poly_box_empty'=>'该酒楼下无聚屏广告的盒子',
    'for_screen_failed'=>'投屏失败',
    'stop_screen_failed'=>'结束投屏失败',
    'option_task_pub_task_too_often'=>'您发布任务太过频繁，请10秒后再发',
    'forscreen_record_failed'=>'投屏记录失败',
    'forscreen_push_suggestion_failed'=>'反馈意见失败',
    'smallapp_turntable_log_failed'=>'日志添加失败',
    'smallapp_order_time_failed'=>'记录指令时间失败',
    'smallapp_user_exist'=>'用户已存在',
    'smallapp_user_add_failed'=>'用户添加失败',
    'small_app_add_failed'=>'新增失败',
    'small_app_del_failed'=>'删除失败',
    'small_app_breanlink_failed'=>'断开连接失败',
    'netty_box_empty' => '该机顶盒暂不支持投屏',
);