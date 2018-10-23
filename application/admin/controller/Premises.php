<?php

namespace app\admin\controller;

use app\admin\model\CustomersPremises;
use think\Db;
use think\Request;

class Premises extends Base
{
    public function _initialize()
    {
        // 是否有权限
        IS_ROOT([1,2])  ? true : $this->error('没有权限');
        return parent::_initialize(); // TODO: Change the autogenerated stub
    }

    //提交收货信息
    public function add_do() {
        $Request = Request::instance();
        if ($Request->isPost()) {
            $Premises = new CustomersPremises($_POST);
            $preid = $Request->param('pre_id');
            if (empty($preid)) {
                //新增
                // 过滤post数组中的非数据表字段数据
                $Premises->allowField(true)->save();
                $this->success('添加成功');
            } else {
                //更新
                // 过滤post数组中的非数据表字段数据
                //$Premises->allowField(true)->save($_POST,['pre_id' => $preid]);
                $pre_dist = isset($_POST['pre_dist']) ? $_POST['pre_dist'] : '';
                $pre_street = isset($_POST['pre_street']) ? $_POST['pre_street'] : '';
                Db::name('customers_premises')->where('pre_id', $preid)->update([
                    'pre_cus_id' => $_POST['pre_cus_id'],
                    'pre_log_id' => $_POST['pre_log_id'],
                    'pre_name' => $_POST['pre_name'],
                    'pre_phone' => $_POST['pre_phone'],
                    'pre_prov' => $_POST['pre_prov'],
                    'pre_city' => $_POST['pre_city'],
                    'pre_dist' => $pre_dist,
                    'pre_street' => $pre_street,
                    'pre_description' => $_POST['pre_description'],
                ]);
                $this->success('修改成功');
            }
        }
    }

    //选择默认联系人
    public function getContactName() {
        // 设定数据返回格式
        \think\Config::set("default_return_type","json");
        $Request = Request::instance();
        if ($Request->isPost()) {
            $cusid = $Request->param('cusid');
            if (empty($cusid)) {
                $this->error('参数错误！');
            }

            $customers = Db::name('customers')->where('cus_id', $cusid)->field('cus_con_id,cus_prov,cus_city,cus_dist,cus_street')->find();
            $contact = Db::name('customers_contact')->where('con_id', $customers['cus_con_id'])->field('con_id,con_name,con_mobile')->find();
            if ($contact) {
                $data = [
                    'shr'=>$contact['con_name'],
                    'tel'=>$contact['con_mobile'],
                    'prov'=>$customers['cus_prov'],
                    'city'=>$customers['cus_city'],
                    'dist'=>$customers['cus_dist'],
                    'street'=>$customers['cus_street'],
                ];
                $this->success('', '', $data);
            } else {
                $this->error('请检查是否已设定默认联系人！');
            }
        }
    }
}