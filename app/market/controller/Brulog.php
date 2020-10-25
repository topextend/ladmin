<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2020-09-27 12:04:44
// |----------------------------------------------------------------------
// |LastEditTime : 2020-09-27 12:24:29
// |----------------------------------------------------------------------
// |LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Description  : 
// |----------------------------------------------------------------------
// |FilePath     : \www.thinkadmin.com\app\market\controller\Brulog.php
// |----------------------------------------------------------------------
// |Copyright (c) 2020 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace app\market\controller;

use think\admin\Controller;

/**
 * 日志管理
 * Class Brulog
 * @package app\market\controller
 */
class Brulog extends Controller
{
    /**
     * 绑定数据表
     * @var string
     */
    private $table = 'MarketBrushLog';

    /**
     * 日志列表
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $this->title = '资金日志';
        $field = 'a.log_id, a.account_log, a.account, a.create_at, b.shop_name';
        $query = $this->_query($this->table)->alias('a')->field($field)->join('market_brush_shops b','a.shop_id=b.id');
        $query->order('create_at desc,log_id desc')->page();
    }

    /**
     * 删除日志
     * @auth true
     * @throws \think\db\exception\DbException
     */
    public function remove()
    {
        $this->_applyFormToken();
        $this->_delete($this->table);
    }
}