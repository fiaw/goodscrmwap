<?php
namespace app\admin\controller;

use think\Request;
use think\Validate;
use app\admin\model\Customers;

class Order extends Base {
    
    protected $rules = [
        'company_name' => 'require',
        'company_short' => 'require',
        'contacts' => 'require',
        'email' => 'require|email',
        'cus_order_sn' => 'require',
        'require_time' => 'require'
    ];
    protected $message = [
        'company_name.require' => '公司名称不能为空',
        'company_short.require' => '简称不能为空',
        'contacts.require' => '联系人不能为空',
        'email.require' => 'E-Mail不是能为空',
        'email.email' => 'E-Mail格式不正确',
        'cus_order_sn.require' => '客户订单号不能为空',
        'require_time.require' => '交货日期不能为空'
    ];
    
    public function index(){
        $catename = '';
        $attr_val = [];
        if ($this->request->isPost()){
            $params = $this->request->param();
            
            if (isset($params['check_cate'])){
                $catename = $params['cate'];
            }
            foreach ($params['check_attr'] as $key => $v){
                if (isset($params['attr_val'][$key]) && !empty($params['attr_val'][$key])){
                    $attr_val[] = $params['attr_val'][$key];
                }
            }
        }
        $company_short = $this->request->param('company_short');
        $start_time = $this->request->param('start_time');
        $end_time = $this->request->param('end_time');
        $status = $this->request->param('status');
        $categroy_id = $this->request->param('categroy_id');
        $db = db('order o');
        if ($status == ''){
            $where = ['o.status' => ['neq','-1']];
        }else{
            $where = ['o.status' => intval($status)];
        }
        
        if ($company_short != ''){
            $where['o.company_short'] = ['like',"%{$company_short}%"];
            $where['o.company_name'] = ['like',"%{$company_short}%"];
        }
        $db->where($where);
        if ($start_time != '' && $end_time != ''){
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time.' 23:59:59');
            if ($start_time && $end_time){
                $db->where("o.create_time",'>=',$start_time);
                $db->where("o.create_time",'<=',$end_time);
            }
        }
        if (!$this->request->isMobile()){
            $db->field('o.*,g.*,o.id as oid,g.id as gid');
            $db->join('__ORDER_GOODS__ g','o.id=g.order_id');
            $flag = true;
            if (!empty($categroy_id)){
                $db->join('__GOODS__ gd','gd.goods_id=g.goods_id');
                $db->where(['gd.category_id' => $categroy_id]);
                $flag = false;
            }
    
            if (!empty($attr_val)){
                $attr_sql_or = '';
                foreach ($attr_val as $val){
                    $valArr = explode(',', $val);
                    foreach ($valArr as $str){
                        $attr_sql_or .= "t.attr_value='{$str}' OR ";
                    }
                }
                $attr_sql_or = mb_substr($attr_sql_or, 0,-4);
                if (!empty($attr_sql_or)){
                    if ($flag){
                        $flag = false;
                        $db->join('__GOODS__ gd','gd.goods_id=g.goods_id');
                        $db->join('__GOODS_ATTR_VAL__ t','gd.goods_id=t.goods_id');
                        $db->where($attr_sql_or);
                    }else{
                        $db->join('__GOODS_ATTR_VAL__ t','gd.goods_id=t.goods_id');
                        $db->where($attr_sql_or);
                    }
                }
            }
            
            if ($catename != ''){
                $sqlOR = '';
                $catename = explode(',', $catename);
                foreach ($catename as $str){
                    $sqlOR .= "gc.category_name='{$str}' OR ";
                }
                $sqlOR = mb_substr($sqlOR, 0,-4);
                if (!empty($sqlOR)){
                    if ($flag){
                        $db->join('__GOODS__ gd','gd.goods_id=g.goods_id');
                        $db->join('__GOODS_CATEGORY__ gc','gc.category_id=gd.category_id');
                        $db->where($sqlOR);
                    }else{
                        $db->join('__GOODS_CATEGORY__ gc','gc.category_id=gd.category_id');
                        $db->where($sqlOR);
                    }
                }
            }
        }else{
            $db->field('o.*,o.id as oid');
        }
        $db->order('o.create_time desc');
        $data = $db->paginate(config('PAGE_SIZE'), false, ['query' => $this->request->param() ]);
//      echo $db->getLastSql();exit;
        //获取分页显示
        $this->assign('current_page', $data->getCurrentPage());
        $this->assign('total_page', $data->lastPage());
        $this->assign('params', $this->request->query());
        $page = $data->render();
        $this->assign('page',$page);
        $this->assign('list',$data);
        
        $attr = getParams(19); //查询属性
        if (!empty($attr)){
            $attr = $attr['params_value'];
        }
        $this->assign('attr',$attr);
        
        if ($this->request->isMobile()) {
            $this->assign('title','订单管理');
            if ($this->request->isAjax()) {
                if (empty($data)) $this->success('ok','');
                return $this->fetch('load');
            }
        }else{
            $this->assign('title','订单列表');
        }

        $category = db('goods_category')->where(array('status' => 1))->select();
        $this->assign('category',$category);
        return $this->fetch();
    }
    
    public function nodeliery(){
        $company_short = $this->request->param('company_short');
        $start_time = $this->request->param('start_time');
        $end_time = $this->request->param('end_time');
        $status = $this->request->param('status');
        $categroy_id = $this->request->param('categroy_id');
        $db = db('order o');
        $db->field('o.*,g.*,o.id as oid,g.id as gid');
        if (empty($status)){
            //$where = ['o.status' => ['neq','-1']];
        }else{
            //$where = ['o.status' => intval($status)];
        }
        $where = ['o.status' => ['in',[1,5]]];
        if ($company_short != ''){
            $where['o.company_short'] = ['like',"%{$company_short}%"];
            $where['o.company_name'] = ['like',"%{$company_short}%"];
        }
        $db->where($where);
        if ($start_time != '' && $end_time != ''){
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time.' 23:59:59');
            if ($start_time && $end_time){
                $db->where("o.create_time",'>=',$start_time);
                $db->where("o.create_time",'<=',$end_time);
            }
        }
        $db->join('__ORDER_GOODS__ g','o.id=g.order_id');
        $db->order('o.id desc');
        if (!empty($categroy_id)){
            $db->join('__GOODS__ gd','gd.goods_id=g.goods_id');
            $db->where(['gd.category_id' => $categroy_id]);
        }
        $category = db('goods_category')->where(array('status' => 1))->select();
        $result = $db->paginate(config('PAGE_SIZE'), false, ['query' => $this->request->param() ]);
        $data = $result->all();
        foreach ($data as $key => $value){
            $category_id = db('goods')->where(['goods_id' => $value['goods_id']])->value('category_id');
            $data[$key]['category_name'] = '';
            foreach ($category as $val){
                if ($val['category_id'] == $category_id){
                    $data[$key]['category_name'] = $val['category_name'];
                    break;
                }
            }
        }
        $page = $result->render();
        $this->assign('page',$page);
        $this->assign('list',$data);
        $this->assign('title','未交货订单');
//      $this->assign('category',$category);
        return $this->fetch();
    }
    
    public function finish(){
    	$company_short = $this->request->param('company_short');
    	$start_time = $this->request->param('start_time');
    	$end_time = $this->request->param('end_time');
    	$status = $this->request->param('status');
    	$categroy_id = $this->request->param('categroy_id');
    	$db = db('order o');
    	$db->field('o.*,g.*,o.id as oid,g.id as gid');
    	if (empty($status)){
    		//$where = ['o.status' => ['neq','-1']];
    	}else{
    		//$where = ['o.status' => intval($status)];
    	}
    	$where = ['o.status' => 3];
    	if ($company_short != ''){
    		$where['o.company_short'] = ['like',"%{$company_short}%"];
    		$where['o.company_name'] = ['like',"%{$company_short}%"];
    	}
    	$db->where($where);
    	if ($start_time != '' && $end_time != ''){
    		$start_time = strtotime($start_time);
    		$end_time = strtotime($end_time.' 23:59:59');
    		if ($start_time && $end_time){
    			$db->where("o.create_time",'>=',$start_time);
    			$db->where("o.create_time",'<=',$end_time);
    		}
    	}
    	$db->join('__ORDER_GOODS__ g','o.id=g.order_id');
    	
    	if (!empty($categroy_id)){
    		$db->join('__GOODS__ gd','gd.goods_id=g.goods_id');
    		$db->where(['gd.category_id' => $categroy_id]);
    	}
    	$category = db('goods_category')->where(array('status' => 1))->select();
    	$db->order('o.id desc');
    	$result = $db->paginate(config('PAGE_SIZE'), false, ['query' => $this->request->param() ]);
    	$data = $result->all();
    	foreach ($data as $key => $value){
    		$category_id = db('goods')->where(['goods_id' => $value['goods_id']])->value('category_id');
    		$data[$key]['category_name'] = '';
    		foreach ($category as $val){
    			if ($val['category_id'] == $category_id){
    				$data[$key]['category_name'] = $val['category_name'];
    				break;
    			}
    		}
    	}
    	$page = $result->render();
    	$this->assign('page',$page);
    	$this->assign('list',$data);
    	$this->assign('title','完成订单');
    	//$this->assign('category',$category);
    	return $this->fetch();
    }
    
    public function cancel(){
        if ($this->request->isAjax()){
            $id = $this->request->param('id',0,'intval');
            if ($id <= 0) $this->error('参数错误');
            if (db('order')->where(['id' => $id])->setField('status',4)){
                $this->success('取消成功');   
            }
            $this->error('取消失败');
        }
    }
    
    public function delete(){
        if ($this->request->isAjax()){
            $id = $this->request->param('id',0,'intval');
            if ($id <= 0) $this->error('参数错误');
            if (db('order')->where(['id' => $id])->setField('status',-1)){
                $this->success('删除成功');
            }
            $this->error('删除失败');
        }
    }
    
    public function info(){
        $id = $this->request->param('id',0,'intval');
        if ($id <= 0) $this->error('参数错误');
        $order = db('order')->where(['id' => $id,'status' => ['neq','-1']])->find();
        if (empty($order)) $this->error('订单不存在');
        $goodsInfo = db('order_goods')->where(['order_id' => $order['id']])->order('goods_id asc')->select();
        $cus = db('customers')->where(['cus_id' => $order['cus_id']])->find();
        $this->assign('client',$cus);
        $order['attachment'] = json_decode($order['attachment'],true);
        $this->assign('data',$order);
        $this->assign('goodsList',$goodsInfo);
        $this->assign('page_l','');
        $this->assign('list',[]);
        $this->assign('title','订单详情');
        return $this->fetch();
    }
    
    public function add(){
        if ($this->request->isAjax()){
            $type = $this->request->param('type');
            if ($type == 'file'){
            	$file = $this->upload_file();
            	if (is_array($file) && !empty($file)){
            		$this->success('ok','',$file);
            	}else{
            		$this->error('error');
            	}
            	return;
            }
            $data = [
                'create_uid' => $this->userinfo['id'],
            	'con_id' => $this->request->post('con_id'),
                'cus_id' => $this->request->post('cus_id'),
                'order_sn' => $this->request->post('order_sn'),
                'cus_order_sn' => $this->request->post('cus_order_sn'),
                'company_name' => $this->request->post('company_name'),
                'company_short' => $this->request->post('company_short'),
                'fax' => $this->request->post('fax'),
                'email' => $this->request->post('email'),
                'contacts' => $this->request->post('contacts'),
                'order_remark' => $this->request->post('order_remark'),
                'require_time' => $this->request->post('require_time'),
                'status' => $type == 'confirm' ? 1 : 0,
                'create_time' => time(),
                'update_time' => time()
            ];
            $validate = new Validate($this->rules, $this->message);
            if (!$validate->check($data)){
                $this->error($validate->getError());
            }
            $data['require_time'] = strtotime($data['require_time']);
            $goodsInfo = $this->request->param('goods_info/a');
            if (empty($goodsInfo)){
                $this->error('请选择商品');
            }
            foreach ($goodsInfo as $val){
                if ($val['goods_number'] <= 0){
                    $this->error('下单数量不能小于1');
                }
                //$store_number = db('goods')->where(['goods_id' => $val['goods_id']])->value('store_number');
                //if ($store_number < $val['goods_number']){
                //	$this->error('“'.$val['goods_name'].'”下单数量不能大于库存量');
                //}
                //if ($val['send_num'] < 0){
                //    $this->error('已送数量不能小于0');
                //}
            }
            
            $ext = $this->request->param('ext/a');
            $oldfilename = $this->request->param('oldfilename/a');
            
            //$attachment = $this->upload_file();
            $attachment = isset($_POST['files']) ? $_POST['files'] : [];
            foreach ($attachment as $key => $name){
                $attachment[$key] = [
                    'ext' => $ext[$key],
                    'oldfilename' => $oldfilename[$key],
                    'path' => $name
                ];
            }
            $data['attachment'] = json_encode($attachment);
            $in = db('order')->where(['order_sn' => $data['order_sn']])->find();
            if (!empty($in)){
            	$data['order_sn'] = self::create_sn("SO", 'order');
            }
            $order_id = db('order')->insertGetId($data);
            if ($order_id){
            	$total_money = 0;
                foreach ($goodsInfo as $val){
                    
                    $goods_attr = db('goods_attr_val')->where(['goods_id' => $val['goods_id']])
                    ->field(['goods_attr_id','attr_name','attr_value'])->select();
                    $total_money += $val['shop_price']*$val['goods_number'];
                    db('order_goods')->insert([
                        'order_id' => $order_id,
                        'goods_id' => $val['goods_id'],
                        'goods_name' => $val['goods_name'],
                        'unit' => $val['unit'],
                        'goods_price' => $val['shop_price'],
                        'market_price' => $val['market_price'],
                        'goods_number' => $val['goods_number'],
                        'send_num' => $val['send_num'],
                        'goods_attr' => json_encode($goods_attr),
                        'remark' => $val['remark'],
                        'create_time' => time()
                    ]);
                }
                db('order')->where(['id' => $order_id])->setField('total_money',_formatMoney($total_money));
                
                if ($type == 'create'){
                    $this->success('保存成功',url('create',['id' => $order_id]));
                }else{
                    $this->success('保存成功',url('index'));
                }
            }else{
                $this->error('保存失败请重试');
            }
            return;
        }
        $assign = [
            'title' => '订单列表',
            'list'  => [],
            'page'  => '',
        ];
        $this->assign('order_sn',self::create_sn("SO", 'order'));
        $this->assign($assign);
        return $this->fetch();
        
    }

    public function confirm(){
    	$id = $this->request->param('id',0,'intval');
    	$route = $this->request->param('r');
    	if ($id <= 0) $this->error('参数错误');
    	$order = db('order')->where(['id' => $id,'status' => ['neq','-1']])->find();
    	if (empty($order)) $this->error('订单不存在');
    	if (!db('order')->where(['id' => $id])->setField('status',1)){
    		$this->error('确认失败');
    	}
    	if ($this->request->isAjax() && $this->request->isMobile()){
    	    $this->success('确认成功');
    	}
    	if($route == 'i'){
    	    if (!empty($_SERVER['HTTP_REFERER'])){
    	        $this->redirect($_SERVER['HTTP_REFERER']);
    	        return;
    	    }
    	}
    	$this->redirect(url('info',['id' => $id]));
    }
    
    public function setfinish(){
        $id = $this->request->param('id',0,'intval');
        $route = $this->request->param('r');
        if ($id <= 0) $this->error('参数错误');
        $order = db('order')->where(['id' => $id,'status' => ['neq','-1']])->find();
        if (empty($order)) $this->error('订单不存在');
        if (!db('order')->where(['id' => $id])->setField('status',3)){
            $this->error('操作失败');
        }
        if($route == 'i'){
            if (!empty($_SERVER['HTTP_REFERER'])){
                $this->redirect($_SERVER['HTTP_REFERER']);
                return;
            }
        }
        $this->success('操作成功');
    }
    
    protected $create_rule = [
    	'order_id' => 'require',
    	'order_sn' => 'require',
        'cus_order_sn' => 'require',
    	'po_sn' => 'require|checkPosn:1',
    	'supplier_id' => 'require',
    	'cus_phome' => 'require',
    	'transaction_type' => 'require',
    	'payment' => 'require',
    	'delivery_type' => 'require',
    	'delivery_company' => 'require',
    	'tax' => 'require',
    	'delivery_address' => 'require',		/* 		'receiver' => 'require',				'receivernum' => 'require', */		
    	'email' => 'require|email',
    	'contacts' => 'require',
    ];
    protected $create_message = [
    	'order_id.require' => '订单ID参数错误',
    	'order_sn.require' => '关联订单号错误',
        'cus_order_sn.require' => '客户订单号错误',
    	'po_sn.require' => 'PO号码不能为空',
    	'supplier_id.require' => '请选择供应商',
    	'contacts.require' => '联系人不能为空',
    	'email.require' => 'E-Mail不能为空',
    	'email.email' => 'E-Mail格式不正确',
    	'cus_phome.require' => '电话号码不能为空',
    	'transaction_type.require' => '请选择交易类别',
    	'payment.require' => '请选择付款条件',
    	'delivery_type.require' => '请选择交货方式',
    	'delivery_company.require' => '送货公司不能为空',
    	'tax.require' => '请选择税率',
    	'delivery_address.require' => '送货地址不能为空',				/* 'receiver.require' => '收货联系人不能为空',				'receivernum.require' => '收货人电话不能为空', */
    	'po_sn.checkPosn' => 'PO号码已存在请刷新'
    ];
    
    public function create_do(){
    	if ($this->request->isAjax()){
    		$type = $this->request->param('type');
    		$data = [
    				'admin_uid' => $this->userinfo['id'],
    				'order_id' => $this->request->post('order_id'),
    				'cus_id' => $this->request->post('cus_id'),
    				'po_sn' => $this->request->post('po_sn'),
    				'supplier_id' => $this->request->post('supplier_id'),
    				'cus_phome' => $this->request->post('cus_phome'),
    				'transaction_type' => $this->request->post('transaction_type'),
    				'payment' => $this->request->post('payment'),
    				'delivery_type' => $this->request->post('delivery_type'),
    				'delivery_company' => $this->request->post('delivery_company'),
    				'tax' => $this->request->post('tax'),
    				'delivery_address' => $this->request->post('delivery_address'),						                //added by wei on 2018-10-01 begin					'receiver' => $this->request->post('receiver'),					'receivernum' => $this->request->post('receivernum'),                    //end
    				'order_sn' => $this->request->post('order_sn'),
    		        'cus_order_sn' => $this->request->post('cus_order_sn'),
    				'fax' => $this->request->post('fax'),
    				'email' => $this->request->post('email'),
    				'contacts' => $this->request->post('contacts'),
    		        'receiver' => $this->request->post('receiver'),
    		        'receivernum' => $this->request->post('receivernum'),
    				'status' => $type == 'confirm' ? 1 : 0,
    				'remark' => $this->request->post('remark'),
    				'create_time' => time(),
    				'update_time' => time()
    		];
    		
    		$validate = new Validate($this->create_rule,$this->create_message);
    		$validate->extend('checkPosn',function($value,$rule,$data){
    			$find = db('purchase')->where(['po_sn' => $value])->find();
    			return empty($find);
    		});
    		if (!$validate->check($data)){
    			$this->error($validate->getError());
    		}
    		$goodsInfo = $this->request->post('goods_info/a');
    		if (empty($goodsInfo)){
    			$this->error('商品信息不能为空');
    		}
    		$purchseGoods = [];
    		$totalMoney = 0;
    		foreach ($goodsInfo as $key => $value){
    		    /*
    			$store_number = db('goods')->where(['goods_id' => $value['goods_id']])->value('store_number');
    			if ($store_number < $value['goods_number']){
    				$this->error('“'.$value['goods_name'].'”采购数量不能大于库存量');
    			}
    			$goods_price = db('order_goods')->where(['order_id' => $data['order_id'],'goods_id' => $value['goods_id']])->value('goods_price');
    			if ($goods_price < $value['shop_price']){
    				$this->error('“'.$value['goods_name'].'”采购单价不能高于关联订单价');
    			}
    			*/
    		    if ($value['purchase_number'] <= 0){
    		        $this->error('“'.$value['goods_name'].'”采购数量不能小于1');
    		    }
    		    if (_formatMoney($value['shop_price']) <= 0){
    		        $this->error('“'.$value['goods_name'].'”采购单价不能为0');
    		    }
    			$countMoney = _formatMoney($value['purchase_number']*$value['shop_price']);
    			$purchseGoods[] = [
    				'goods_id' => $value['goods_id'],
    				'goods_name' => $value['goods_name'],
    				'unit' => $value['unit'],
    				'goods_number' => $value['purchase_number'],
    				'goods_price' => $value['shop_price'],
    				'count_money' => $countMoney,
    				'goods_attr' => $value['goods_attr'],
    				'create_time' => time()
    			];
    			$totalMoney += $countMoney;
    		}
    		$data['total_money'] = _formatMoney($totalMoney);
    		$in = db('purchase')->where(['po_sn' => $data['po_sn']])->find();
    		if (!empty($in)){
    			$data['po_sn'] = self::create_sn('PO', 'purchase');
    		}
    		$purchase_id = db('purchase')->insertGetId($data);
    		if ($purchase_id){
    			db('order')->where(['id' => $data['order_id']])->update(['status' => 5,'is_create' => 1]);
    			foreach ($purchseGoods as $value){
    				$value['purchase_id'] = $purchase_id;
    				db('purchase_goods')->insert($value);
    			}
    			$this->success('保存采购单成功',url('purchase/info',['id' => $purchase_id]));
    		}else{
    			$this->error('保存采购单失败');
    		}
    	}
    }
    
    public function create(){
    	$id = $this->request->param('id',0,'intval');
    	$supplier_id = $this->request->param('supplier_id',0,'intval');
    	if ($id <= 0) $this->error('参数错误');
    	$order = db('order')->where(['id' => $id,'status' => ['neq','-1']])->find();
    	if (empty($order)) $this->error('订单不存在');
    	$goodsInfo = db('order_goods')->where(['order_id' => $order['id']])->select();
    	if (!empty($goodsInfo)){
    		foreach ($goodsInfo as $key => $value){
    			$cus_id = $order['cus_id'];
    			/*
    			$last_order_price = db('order o')->join('__ORDER_GOODS__ og','o.id=og.order_id')
    			->where(['o.cus_id' => $cus_id,'og.goods_id' => $value['goods_id']])
    			->where("o.status=2 OR o.status=3 OR o.status=6")->value('goods_price');
    			*/
    			$last_order_price = 0;
    			if ($this->request->isAjax()){
        			$last_order_price = db('purchase o')->join('__PURCHASE_GOODS__ og','o.id=og.purchase_id')
        			->where(['o.cus_id' => $cus_id,'og.goods_id' => $value['goods_id']])
        			->where("o.status!='-1'")->order('o.id desc')->value('goods_price');
    			}
    			$value['shop_price'] = _formatMoney($last_order_price);
    			
    			//$value['shop_price'] = $value['goods_price']; //实际价格
    			//$value['purchase_number'] = 0;
    			$value['purchase_number'] = $value['goods_number'];
    			$value['store_number'] = db('goods')->where(['goods_id' => $value['goods_id']])->value('store_number');
    			$value['totalMoney'] = _formatMoney($value['goods_price']*$value['purchase_number']);
    			$goodsInfo[$key] = json_encode($value);
    		}
    	}
    	if ($this->request->isAjax()){
    	    $this->success('','',$goodsInfo);
    	    return;
    	}
    	$order['goodsInfo'] = $goodsInfo;
    	$this->assign('data',$order);
    	
    	$client = db('customers')->where(['cus_id' => $order['cus_id']])->find();
    	$contacts = db('customers_contact')->where(['con_cus_id' => $client['cus_id']])->select();
    	$this->assign('client',$client);
    	$this->assign('contacts',$contacts);
    	$supplier = db('supplier')->where(['supplier_status' => ['neq','-1']])->select();
    	$this->assign('supplier',$supplier);
    	$trans_type = getParams(5);
    	if (!empty($trans_type)){
    		$trans_type = $trans_type['params_value'];
    	}
    	$this->assign('trans_type',$trans_type);
    	$payment = getParams(1);
    	if (!empty($payment)){
    		$payment = $payment['params_value'];
    	}
    	$this->assign('payment',$payment);
    	
    	$tax = getParams(17);
    	if (!empty($tax)){
    		$tax = $tax['params_value'];
    	}
    	$this->assign('tax',$tax);
    	$delivery_type = getParams(16);
    	if (!empty($delivery_type)){
    		$delivery_type = $delivery_type['params_value'];
    	}
    	$this->assign('delivery_type',$delivery_type);
    	
    	$remark = getTextParams(18);
    	$this->assign('remark',$remark);
    	
    	$this->assign('title','创建采购单');
    	$this->assign('po_sn',self::create_sn('PO', 'purchase'));
    	return $this->fetch();
    }
    
    public function edit(){
        if ($this->request->isAjax()){
            $type = $this->request->param('type');
            if ($type == 'file'){
            	$file = $this->upload_file();
            	if (is_array($file) && !empty($file)){
            		$this->success('ok','',$file);
            	}else{
            		$this->error('error');
            	}
            	return;
            }
            $data = [
                'id' => $this->request->post('id'),
            	'con_id' => $this->request->post('con_id'),
                'cus_id' => $this->request->post('cus_id'),
                //'order_sn' => $this->request->post('order_sn'),
                'cus_order_sn' => $this->request->post('cus_order_sn'),
                'company_name' => $this->request->post('company_name'),
                'company_short' => $this->request->post('company_short'),
                'fax' => $this->request->post('fax'),
                'email' => $this->request->post('email'),
                'contacts' => $this->request->post('contacts'),
                'order_remark' => $this->request->post('order_remark'),
                'require_time' => strtotime($this->request->post('require_time')),
                'status' => $type == 'confirm' ? 1 : 0,
                'update_time' => time()
            ];
            $validate = new Validate($this->rules, $this->message);
            if (!$validate->check($data)){
                $this->error($validate->getError());
            }
            $goodsInfo = $this->request->param('goods_info/a');
            if (empty($goodsInfo)){
                $this->error('请选择商品');
            }
            foreach ($goodsInfo as $val){
                if ($val['goods_number'] <= 0){
                    $this->error('下单数量不能小于1');
                }
                /*
                $store_number = db('goods')->where(['goods_id' => $val['goods_id']])->value('store_number');
                if ($store_number < $val['goods_number']){
                	$this->error('“'.$val['goods_name'].'”下单数量不能大于库存量');
                }
                */
                if ($val['send_num'] < 0){
                    //$this->error('已送数量不能小于0');
                }
            }
            $in = db('order_goods')->where(['order_id' => $data['id']])->field('id')->select();
            $ids = [];
            foreach ($in as $val){
                $ids[] = $val['id'];
            }
            $postIds = [];
            foreach ($goodsInfo as $value){
                if (isset($value['id']) && intval($value['id']) > 0){
                    $postIds[] = $value['id'];
                }
            }
            
            //新文件
            $ext = $this->request->param('ext/a');
            $oldfilename = $this->request->param('oldfilename/a');
            $oldfile = $this->request->param('oldfile/a');
            //$attachment = $this->upload_file();
            $attachment = isset($_POST['files']) ? $_POST['files'] : [];
            foreach ($attachment as $key => $name){
                $attachment[$key] = [
                    'ext' => $ext[$key],
                    'oldfilename' => $oldfilename[$key],
                    'path' => $name
                ];
            }
            $fileArr = [];
            if (!empty($oldfile)){
                $order_attachment = db('order')->where(['id' => $data['id'],'status' => ['neq','-1']])->value('attachment');
                $order_attachment = json_decode($order_attachment,true);
                foreach ($order_attachment as $v){
                    foreach ($oldfile as $k => $f){
                        if ($v['path'] == $f){
                            $fileArr[] = $v;
                        }
                    }
                }
            }
            $data['attachment'] = json_encode(array_merge($fileArr,$attachment));
            $affected = db('order')->update($data);
            if ($affected){
                $tempArr = array_count_values(array_merge($ids,$postIds));
                foreach ($tempArr as $key => $count){
                    if ($count == 1){
                        db('order_goods')->where(['id' => $key,'order_id' => $data['id']])->delete();
                    }
                }
                $total_money = 0;
                foreach ($goodsInfo as $val){
                    $goods_attr = db('goods_attr_val')->where(['goods_id' => $val['goods_id']])
                    ->field(['goods_attr_id','attr_name','attr_value'])->select();
                    $total_money += $val['shop_price']*$val['goods_number'];
                    if (!isset($val['id']) || intval($val['id']) <= 0){
                        db('order_goods')->insert([
                            'order_id' => $data['id'],
                            'goods_id' => $val['goods_id'],
                            'goods_name' => $val['goods_name'],
                            'unit' => $val['unit'],
                            'goods_price' => $val['shop_price'],
                            'market_price' => $val['market_price'],
                            'goods_number' => $val['goods_number'],
                            'send_num' => $val['send_num'],
                            'goods_attr' => json_encode($goods_attr),
                            'remark' => $val['remark'],
                            'create_time' => time()
                        ]);
                    }else{
                        db('order_goods')->where(['order_id' => $data['id'],'id' => intval($val['id'])])->update(
                            [
                                'goods_id' => $val['goods_id'],
                                'goods_name' => $val['goods_name'],
                                'unit' => $val['unit'],
                                'goods_price' => $val['shop_price'],
                                'market_price' => $val['market_price'],
                                'goods_number' => $val['goods_number'],
                                'send_num' => $val['send_num'],
                                'goods_attr' => json_encode($goods_attr),
                                'remark' => $val['remark'],
                        ]);
                    }
                }
                db('order')->where(['id' => $data['id']])->setField('total_money',_formatMoney($total_money));
                $this->success('保存成功');
            }else{
                $this->error('保存失败请重试');
            }
            return;
        }
        $id = $this->request->param('id',0,'intval');
        if ($id <= 0) $this->error('参数错误');
        $order = db('order')->where(['id' => $id,'status' => ['neq','-1']])->find();
        if (empty($order)) $this->error('订单不存在');
        $goodsInfo = db('order_goods')->where(['order_id' => $order['id']])->select();
        if (!empty($goodsInfo)){
            foreach ($goodsInfo as $key => $value){
                $value['shop_price'] = $value['goods_price']; //实际价格
                $value['store_number'] = db('goods')->where(['goods_id' => $value['goods_id']])->value('store_number');
                $value['show_input'] = false;
                $goodsInfo[$key] = json_encode($value);
            }
        }
        $order['goodsInfo'] = $goodsInfo;
        $order['attachment'] = json_decode($order['attachment'],true);
        $this->assign('data',$order);
        $this->assign('title','编辑订单');
        return $this->fetch();
    }
    
    public function search_company(){
        $order_ren = getParams(10); //获取跟单员
        if (!empty($order_ren)){
            $order_ren= $order_ren['params_value'];
        }
        $Customers = new Customers();
        $request = Request::instance();
        $query = $request->param(); // 分页查询传参数
        $status = $request->param('status'); // 状态查询
        $cus_short = $request->param('cus_short'); // 企业名称查询
        $get_order_ren = $request->param('order_ren'); // 企业名称查询
        $where = "status=1";
        if ($get_order_ren != ''){
            $where .= " and cus_order_ren='{$get_order_ren}'";
        }
        if ($cus_short != ''){
            $where .= " and cus_short like '%{$cus_short}%'";
        }
        $data = $Customers->where($where)->paginate(config('PAGE_SIZE'), false, ['query' => $query ]);
        // 获取分页显示
        $page = $data->render();
        if ($this->request->isMobile()) {
            if ($this->request->isAjax()) {
                if(count($data) == 0){
                    $this->success('ok','',$data);
                }
                return $this->fetch('load_cus');
            }
            array_unshift($order_ren, '');
        }
        $this->assign('page',$page);
        $this->assign('data', $data);
        $this->assign('empty', '<tr><td colspan="19" align="center">当前条件没有查到数据</td></tr>');
        $this->assign('title', '客户信息');
        $this->assign('current_page', $data->getCurrentPage());
        $this->assign('total_page', $data->lastPage());
        $this->assign('query', $this->request->query());
        $this->assign('order_ren_json',json_encode($order_ren));
        $this->assign('order_ren',$order_ren);
        return $this->fetch();
    }
    
    //成交记录
    public function history_record(){
    	$goods_id = $this->request->param('goods_id');
    	$order_id = $this->request->param('order_id');
    	$cus_id = $this->request->param('cus_id');
    	$result = db('order o')->join('__ORDER_GOODS__ g','o.id=g.order_id')
    	->where(['o.cus_id' => $cus_id,
    			 'o.id' => $order_id,
    			 //'g.goods_id' => $goods_id
    			
    	])->paginate(config('PAGE_SIZE'),false);
    	$this->assign('list',$result->all());
    	$this->assign('page',$result->render());
    	return $this->fetch();
    }
    
    public function get_goods(){
        $cate_lists = db('goods_category')->select();
        $this->assign('lists',$cate_lists);
        $before_time = strtotime(date('Y-m-d',strtotime("-0 year -3 month -0 day")));
        $cus_id = $this->request->param('cus_id',0,'intval');
        if (!$cus_id){
            $this->error('请选择客户');
        }
        $goods_name = $this->request->param('goods_name');
        $category_id = $this->request->param('category_id',0,'intval');
        
        $where = ['status' => ['neq','-1']];
        if ($goods_name != ''){
            $where['goods_name'] = ['like',"%$goods_name%"];
        }
        if ($category_id > 0) {
            $where['category_id'] = $category_id;
        }
        $result = db('goods')->where($where)->paginate(config('PAGE_SIZE'),false,['query' => $this->request->param()]);
        
        $lists = $result->all();
        
        foreach ($lists as $key => $value){
            $supplier = db('supplier')->where(['id' => $value['supplier_id']])->field('supplier_name,supplier_short')->find();
            $lists[$key]['supplier_name'] = $supplier['supplier_name'];
            $category = db('goods_category')->where(['category_id' => $value['category_id']])->field('category_name')->find();
            $lists[$key]['category_name'] = $category['category_name'];
            //$brand = db('goods_brand')->where(['brand_id' => $value['brand_id']])->find();
            //$lists[$key]['brand_name'] = $brand['brand_name'];
            $lists[$key]['brand_name'] = '';
            $lists[$key]['purchase_number'] = 0;
            $row = db('order o')->join('__ORDER_GOODS__ og','o.id=og.order_id')->where(['o.cus_id' => $cus_id,'og.goods_id' => $value['goods_id']])
            ->where("o.status=2 OR o.status=3")->order('o.create_time desc')->field('og.order_id,og.goods_price,og.create_time')->find();
            $lists[$key]['order_id'] = $row['order_id']?:0;
            $lists[$key]['last_price'] = $row['goods_price'];
            if ($row['goods_price']){
                $lists[$key]['shop_price'] = $row['goods_price'];
            }
            $lists[$key]['last_time'] = !empty($row['create_time']) ? date('Y-m-d',$row['create_time']) : '';
        }
        if ($this->request->isMobile() && $this->request->isAjax()){
        	$this->success('ok','',$lists);
        }
        $this->assign('current_page', $result->getCurrentPage());
        $this->assign('total_page', $result->lastPage());
        $this->assign('query', $this->request->query());
        $this->assign('data',$lists);
        $this->assign('page',$result->render());
        $this->assign('title', '选择商品');
        return $this->fetch();
    }
    
    
}