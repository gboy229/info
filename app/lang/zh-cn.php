<?php


return [
    //**************************全局**************************

    '_param_error_'            => '参数错误！',
    '_operation_fail_'            => '操作失败！',
    '_operation_success_'         => '操作成功！',
    '_valid_access_'              => '没有权限',

    //**************************admin模块**************************

    'username_not_exist' => '用户名不能为空',
    'username_error_length' => '用户名长度为6-16位之间',
    'password_not_exist' => '密码不能为空',
    'password_error_length' => '密码长度为6-16位之间',
    'admin_user_not_exist' => '账号不存在',
    'password_checked_error' => '登陆密码验证失败',
    'code_not_exist' => '请输入验证码',
    'code_checked_error' => '验证码不正确',
    'no_login' => '尚未登录或已过期',
    //团队管理
    'del_admin_user_success' => '删除团队成员成功',
    'del_admin_user_error' => '删除团队成员失败',
    'del_default_admin_user_error' => '超级管理员不能删除',
    'update_admin_user_success' => '设置团队成员成功',
    'update_admin_group_success' => '设置角色成功',
    'rules_admin_group_require' => '请选择权限',
    //删除日志
    'del_log_success' => '删除日志成功',

    'cache_upload_success' => '缓存更新完毕',
    'upload_success' => '更新成功',
    'upload_error' => '更新失败',
    'no_promission_delete' => '备份文件删除失败，请检查权限！',
    'delete_backups_success' => '备份文件删除成功！',
    'backup_not_exist_success' => '备份目录不存在或不可写，请检查后重试！',
    'backup_set_error' => '初始化失败，备份文件创建失败！',
    'backup_error' => '备份出错！',
    'init_ok' => '初始化完成！',
    'backup_damage' => '备份文件可能已经损坏，请检查！',
    'restore_data_error' => '还原数据出错！',
    'restore_data_success' => '还原完成！',
    'add_address_success' => '添加成功',
    'edit_address_success' => '编辑成功',
    'delete_region_success' => '指定删除成功',
    'edit_sort_success' => '排序修改成功',
    'region_not_exist' => '名称不能为空',
    'edit_region_success' => '名称修改成功',
    'no_promission_operate' => '您没有权限进行此操作',
    'record_no_exist' => '要更改的记录不存在',
    'theme_empty' => '主题标识不能为空',
    'theme_no_exist' => '主题不存在',
    'start_upload' => '准备完毕开始升级',
    'versions_upload_success' => '版本更新完毕',
    'versions_uploading' => '正在更新',
    'download_again' => '文件不完整，请重新下载',
    'upload_file_no_exist' => '文件不存在,升级失败',
    'optimize_data_success' => '数据表优化完成！',
    'optimize_data_error' => '数据表优化出错请重试！',
    'optimize_data_empty' => '请指定要优化的表！',
    'repair_data_success' => '数据表修复完成！',
    'repair_data_error' => '数据表修复出错请重试！',
    'repair_data_empty' => '请指定要修复的表！',
    'relogin' => '操作成功,请重新登录',
    'head_portrait_save_error' => '头像数据保存失败',
    'illegal_image_upload' => '请勿上传非法图片',
    'head_protrait_data_error' => '头像数据异常',
    'original_password_empty' => '原密码必须填写',
    'original_password_error' => '原密码不正确，请重新填写',
    'superior_area_no_exist' => '上级地区不存在',

    'role_name_require' => '角色名不能为空',
    'role_description_require' => '权限描述不能为空',
    'user_name_exist' => '帐号名称已经存在！',
    'password_not_standard' => '密码不符合规范',
    'plugin_name_require' => '插件名称不能为空',
    'plugin_name_not_unique' => '插件名称已存在',
    'plugin_id_require' => '插件标识不能为空',
    'plugin_id_not_unique' => '插件标识已存在',


    //**************************站点模块**************************

    'class_name_require'     => '请输入分类名称',
    'parent_class_not_exist'    => '父分类不存在',
    'sort_require'              => '排序必须为数字',
    'category_not_exist'      => '分类不存在',
    'article_name_require'      => '请输入文章名称',
    'article_classify_require'  => '请选择文章分类',
    'title_require'             => '请输入标题',
    'name_require'              => '请输入名称',


    //**************************附件模块**************************

    'upload_success' 		=> '上传成功',
    'delete_not_exist' 		=> '请指定要删除的图片',
    'delete_success' 		=> '图片删除成功',
    'no_promission_upload'	=> '没有上传权限',
    'file_upload_empty'		=> '没有上传任何文件',
    'catalog_empty'			=> '目录不能为空',


    //**************************广告模块**************************

    'ads_name_require'			=> '广告名称不能为空',
    'ads_position_name_require'		=> '广告位名称不能为空',
    'ads_thumb_require'		=> '广告图不能为空',


    //**************************商城模块**************************


    'goods_category_id_require'			=> '请选择上级分类',
    'goods_category_name_require'			=> '请输入分类名称',
    'goods_brand_name_require'      => '品牌名称不能为空',
    'goods_spec_name_require'       => '请输入规格名称',
    'goods_spec_value_empty'        => '规格属性值不能为空',

    'goods_goods_not_exist'         => '商品不存在',

    'goods_goods_name_empty'        => '商品名称不能为空',
    'goods_category_id_empty'       => '商品分类必须选择',


    //**************************会员模块**************************

    '_not_login_'		            	=> '请先登录',
    'login_username_empty'		        => '请输入用户名',
    'login_password_empty'		        => '请输入登录密码',
    'username_not_exist'		        => '用户名或密码错误',
    'user_ban_login'		            => '此用户已冻结，不能登录',
    'member_group_name_require'			=> '请输入等级名称',
    'username_length_require'			=> '用户名长度在3-15个字符',
    'username_exist'					=> '该用户名已被注册',
    'email_format_exist'				=> '该邮箱已被注册',
    'mobile_format_error'				=> '手机号码格式不正确',
    'mobile_format_exist'				=> '该手机号码已被注册',
    'username_disable_keyword'			=> '用户名含有被禁用的关键字',
    'password_length_require'			=> '密码长度为3-16个字符',
    'group_id_require'      			=> '请选择所属分组',
    'captcha_error'      			    => '验证码不正确或过期了',


    //**************************微信模块**************************

    'menu_title_require'		    	=> '请输入菜单标题',
    'menu_data_require'	    	    	=> '菜单数据不能为空',


    //**************************通知模块**************************

    'upload_message_success' 	=> '更新通知模板完成',
    'no_found_config_file' 		=> '没有找到驱动配置文件',
    'config_operate_error' 		=> '配置操作失败',
    'uninstall_success' 		=> '卸载成功',


    //**************************支付模块**************************

    'pay_enabled_success' 	    => '设置支付方式成功',
    'pay_enabled_error' 		=> '设置支付方式失败',
    'pay_uninstall_success' 	=> '卸载支付方式成功',
    'pay_uninstall_error'   	=> '卸载支付方式失败',
    'order_pay_success' 	    => '订单支付成功',
    'no_promission_view'    	=> '抱歉，您无法查看此订单',
    'order_paid' 			    => '该订单已支付',
    'pay_set_error' 		    => '支付请求创建失败',
    'pay_code_require'		    => '支付方式不存在',
    'pay_name_require'		    => '支付名称必须存在',
    'config_require'		    => '支付配置必须存在',


    //**************************插件**************************

    'plugin_not_exist' 	                    => '插件不存在',
    'plugin_no_found_config_file' 	        => '插件找不到配置文件',
    'plugin_install_success' 	            => '插件安装成功',
    'plugin_repeat_install' 	            => '插件重复安装',
    'plugin_info_error' 	                => '插件信息缺失',
    'plugin_pre_install_error' 	            => '插件预安装失败',
    'plugin_not_install' 	                => '插件未安装',
    'plugin_uninstall_fail' 	            => '插件卸载失败',
    'plugin_update_success' 	            => '插件更新成功',


    //**************************模块**************************

    'module_not_exist' 	                    => '模块不存在',
    'module_domain_require' 	            => '请输入二级域名',
    'module_update_success' 	            => '插件更新成功',



    //**************************订单模块**************************

    'order_sn_not_null'                     => '订单号不能为空',
    'order_not_empty'				        => '主订单号不能为空',
    'order_sn_error'                        => '订单号有误',
    'order_sn_already_exist'                => '订单号已存在',
    'order_not_exist'                       => '该订单不存在',
    'order_submit_success'                  => '订单提交成功',
    'order_submit_error'                    => '订单提交失败',
    'goods_not_exist' 	                    => '商品不存在',
    'module_domain_require' 	            => '请输入二级域名',
    'module_update_success' 	            => '插件更新成功',
    'shopping_cart_empty'			        => '购物车已为空',
    'shipping_address_empty'	        	=> '请选择收货地址',
    'invoice_head_empty'		        	=> '请填写发票抬头',
    'pay_way_empty'				        	=> '请选择支付方式',
    'order_goods_empty'			        	=> '请选择订单商品',
    'order_log_empty'				        => '订单日志信息不能为空',
    'parent_order_sn_not_exist'		        => '主订单信息不存在',
    'order_require'					        => '主订单号必须存在',
    'order_sub_require'				        => '子订单号必须存在',
    'child_order_sn_empty'			        => '子订单号不能为空',

    'pay_deal_sn_empty' 		        	=> '请填写支付交易号',
    'pay_success'     				        => '确认付款成功',


    // ---------- 待操作状态
    'wait_pay'            => '待付款',
    'wait_confirm'        => '待确认',
    'wait_delivery'       => '待发货',
    'wait_completion'     => '待确认完成',

    'cancellation_order_success'   	=> '订单作废操作成功',
    'order_create_success'     		=> '订单创建成功',
    'operate_type_empty'			=> '订单操作类型不能为空',
    'pay_type_require'				=> '订单支付类型不能为空',
    'pay_type_error'				=> '订单支付类型有误',
    'pay_status_require'			=> '支付状态必须为布尔值',
    'confirm_status'				=> '确认状态必须为布尔值',
    'delivery_status_require'		=> '发货状态必须为布尔值',
    'finish_status_require'			=> '完成状态必须为布尔值',
    'sku_amount_require'			=> '商品总额不能为空',
    'sku_amount_currency'			=> '商品总额有误',
    'delivery_amount_currency'		=> '配送总额只能为实数(保留两位小数)',
    'balance_amount_currency'		=> '余额付款总额只能为实数(保留两位小数)',
    'address_detail_require'		=> '收货人详细地址不能为空',
    'type_not_empty' 				=> '操作类型不能为空',
    'operator_type_not_null'		=> '操作者类型不能为空',
    'operator_type_numbre'			=> '操作者类型为大于零的正整数',
    'user_id_not_empty'				=> '操作者ID不能为空',
    'user_name_not_null'			=> '操作者名称不能为空',
    'user_id_require'     			=> '操作者ID为大于零的正整数',
    'order_parame_empty'			=> '订单参数不能为空',
    'order_logistics_not_exist'		=> '订单物流信息不存在',
    'order_log_empty'				=> '订单日志信息不能为空',
    'parent_order_sn_not_exist'		=> '主订单信息不存在',
    'order_require'					=> '主订单号必须存在',
    'order_sub_require'				=> '子订单号必须存在',
    'child_order_sn_empty'			=> '子订单号不能为空',
    'order_no_pay'     				=> '该订单未支付',
    'order_dont_operate'			=> '该订单不能执行该操作',
    'order_goods_not_exist'			=> '该订单商品不存在',
    'cancel_order_success'     		=> '取消订单成功',
    'delete_order_error'			=> '删除订单失败',
    'order_goods_empty'				=> '请选择订单商品',
    'order_not_pay_status'     		=> '抱歉，该订单当前不是支付状态',
    'no_promission_view'     		=> '抱歉，您无法查看此订单',
    'dont_edit_order_amount'		=> '当前状态不能修改订单应付总额',
    'submit_parameters_error'     	=> '提交参数有误',
    'express_identifying_no_exist' 	=> '物流标识图片不存在',
    'deliver_identify_not_exist'	=> '物流标识不存在',
    'logistics_no_msg'				=> '物流单暂无结果',
    'logistics_name_not_empty'		=> '物流名称不能为空',
    'logistics_sn_not_exist'		=> '物流单号不能为空',
    'logistics_identifi_not_empty'	=> '物流标识不能为空',
    'logistics_name_exist'			=> '物流名称已存在',
    'logistics_insurance_amount_empty'=> '请填写物流保价金额',
    'logistics_id_empty'			=> '物流ID不能为空',
    'logistics_id_require'			=> '物流ID必须为正整数',
    'logistics_empty'				=> '请选择配送物流',
    'logistics_not_exist'			=> '该物流不存在',
    'delete_logistics_error'		=> '删除物流失败',
    'delete_logistics_id_empty'		=> '要删除的物流ID不能为空',
    'clearing_goods_no_exist'     	=> '结算商品不存在',
    'pay_deal_sn_empty' 			=> '请填写支付交易号',
    'pay_success'     				=> '确认付款成功',
    'record_no_exist'     			=> '该记录不存在',
    'parameter_empty'     			=> '参数不能为空',
    'delete_parame_error'			=> '要删除的参数有误',
    'merchant_id_empty'				=> '商家ID不能为空',
    'area_not_exist'				=> '地区不存在',
    'request_parame_error'			=> '请求的参数有误！',
    'edit_field_empty'				=> '要更改的字段不能为空',
    'edit_value_empty'				=> '要更改的值不能为空',
    'shipment_sn_id_not_exist'		=> '发货单ID不能为空',
    'waybill_sn_not_exist'			=> '运单号不存在',
    'waybill_sn_empty'				=> '运单号不能为空',
    'inquire_error'					=> '查询失败，请稍候重试',
    'connector_error'				=> '接口出现异常',
    'buy_number_require'			=> '购买数量必须为正整数',
    'goods_not_exist'				=> '该购物车商品不存在',
    'delete_parame_empty'			=> '要删除的参数不能为空',
    'shopping_cart_empty'			=> '购物车已为空',
    'refund_money_require'			=> '退款金额必须大于0',
    'return_cause_empty'			=> '请选择退货原因',
    'repeat_submit'					=> '您已申请售后，请勿重复提交',
    'operate_type_error'			=> '操作类型有误',
    'operate_record_not_exist'		=> '要操作的记录不存在',
    'record_ban_operate'			=> '该记录禁止该操作',
    'shipping_address_empty'		=> '请选择收货地址',
    'invoice_head_empty'			=> '请填写发票抬头',
    'pay_way_empty'					=> '请选择支付方式',
    'pay_ebanks_error'				=> '选择的支付网银有误',
    'model_id_require'				=> '发货单模版ID必须为正整数',
    'no_found_data'					=> '未查询到相关数据',
    'insure_money_require'			=> '保价金额必须为数字',
    'sort_require'					=> '排序必须为正整数',

    'thsi_operator_a'				=> '当前操作配送中',
    'thsi_operator_b'				=> '当前操作配送失败',
    'thsi_operator_c'				=> '当前操作配送成功',
    'thsi_operator_d'				=> '当前操作待配送',
    'buyer_id_not_null'				=> '买家ID不能为空',
    'buyer_id_error'				=> '买家ID有误',
    'amount_require'				=> '退款总额只能为实数(保留两位小数)',
    'sku_id_not_null'				=> '产品ID不能为空',
    'sku_id_require'				=> '产品ID必须是正整数',
    'buy_nums_require'				=> '购买数量不能为空',
    'buy_nums_number'				=> '购买数量必须是正整数',
    'content_require' 				=> '发货单模版内容不能为空',
    'msg_require'					=> '订单跟踪内容不能为空',

    'name_empty'					=> '运费模板名称不能为空',
    'template_type_error'			=> '请选择运费模板计费类型',
    'template_empty'				=> '地区模板信息不能为空',
    'delivery_template_not_exist' 	=> '运费模板不存在或者未开启',
    'delivery_default_cannot_delete' => '默认运费模板不能删除',



    //**************************购物车模块**************************

    'clearing_goods_no_exist' 	            => '结算商品不存在',
    'goods_stock_insufficient' 	            => '商品库存不足',








];

?>