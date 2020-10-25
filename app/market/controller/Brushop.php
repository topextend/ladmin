<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2020-09-25 16:58:58
// |----------------------------------------------------------------------
// |LastEditTime : 2020-09-27 12:28:48
// |----------------------------------------------------------------------
// |LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Description  : 
// |----------------------------------------------------------------------
// |FilePath     : \www.thinkadmin.com\app\market\controller\Brushop.php
// |----------------------------------------------------------------------
// |Copyright (c) 2020 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace app\market\controller;

use app\market\service\BrushService;
use think\admin\Controller;

/**
 * 店铺数据管理
 * Class Brushop
 * @package app\market\controller
 */
class Brushop extends Controller
{
    /**
     * 绑定数据表
     * @var string
     */
    private $table = 'MarketBrushShops';

    /**
     * 店铺列表
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $this->title = '店铺列表';
        $query = $this->_query($this->table);
        $query->like('shop_name,shop_type,create_at')->equal('status');
        // 列表排序并显示
        $query->order('sort desc,id desc')->page();
    }
    
    /**
     * 数据列表处理
     * @param array $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function _page_filter(array &$data)
    {
        $this->shops = BrushService::instance()->getShopsList();
    }
    
    /**
     * 添加店铺
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add()
    {
        $this->title = '添加店铺';
        $this->_applyFormToken();
        $this->_form($this->table, 'form');
    }

    /**
     * 编辑店铺
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit()
    {
        $this->title = '编辑店铺';
        $this->_applyFormToken();
        $this->_form($this->table, 'form');
    }
    
    /**
     * 店铺充值
     * @auth true
     * @return void
     */
    public function deposit()
    {
        $this->title = '资金充值';
        $this->_applyFormToken();
        $this->_form($this->table, 'deposit');
    }

    /**
     * 表单数据处理
     * @param array $vo
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function _form_filter(&$vo)
    {
        if ($this->request->isPost()){
            if (!empty($vo['account']))
            {
                if ($vo['account'] == "0.00") $this->error("请输入要充值的金额");
                $account = $this->app->db->name($this->table)->where(['id' => $vo['id']])->value('account');
                $log['shop_id']     = $vo['id'];
                $log['account_log'] = strstr($vo['account'],'-') ? $vo['account'] : "+" . $vo['account'];
                $log['account']     = $account + $vo['account'];
                $vo['account']      = $account + $vo['account'];
                $this->app->db->name('market_brush_log')->save($log);
                
            }
        }
    }

    /**
     * 修改店铺状态
     * @auth true
     * @throws \think\db\exception\DbException
     */
    public function state()
    {
        $this->_save($this->table, $this->_vali([
            'status.in:0,1'  => '状态值范围异常！',
            'status.require' => '状态值不能为空！',
        ]));
    }

    /**
     * 删除店铺
     * @auth true
     * @throws \think\db\exception\DbException
     */
    public function remove()
    {
        $this->_applyFormToken();
        $this->_delete($this->table);
    }
}
