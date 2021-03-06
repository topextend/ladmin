<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2020-09-29 20:37:34
// |----------------------------------------------------------------------
// |LastEditTime : 2020-10-15 22:45:54
// |----------------------------------------------------------------------
// |LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Description  : 
// |----------------------------------------------------------------------
// |FilePath     : \www.thinkadmin.com\app\goods\controller\Lists.php
// |----------------------------------------------------------------------
// |Copyright (c) 2020 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace app\goods\controller;

use app\goods\service\GoodService;
use think\admin\Controller;

/**
 * 商品数据管理
 * Class Lists
 * @package app\goods\controller
 */
class Lists extends Controller
{
    /**
     * 绑定数据表
     * @var string
     */
    private $table = 'GoodsList';

    /**
     * 商品列表
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $this->title = '商品列表';
        $query = $this->_query($this->table);
        $query->equal('status');
        // 列表排序并显示
        $query->order('sort desc,goods_id desc')->page();
    }

    /**
     * 添加商品
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function category()
    {
        $this->title = '选择商品分类';
        $this->_applyFormToken();
        $this->_form($this->table, 'category');
    }

    /**
     * 添加商品
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add()
    {
        $this->title = '发布商品';
        $this->_applyFormToken();
        $this->_form($this->table, 'form');
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
        if ($this->request->isGet()) {
            $this->cats             = GoodService::instance()->getJosnCats();
            if (!empty(input('cat_id')))
            {
                $this->goods_brand      = GoodService::instance()->getGoodsValue('GoodsBrand','brand_name');
                $this->goods_whouses    = GoodService::instance()->getGoodsValue('GoodsWarehouse','whouse_name');
                $this->select_cats      = GoodService::instance()->makeGoodsCats(input('cat_id'));
                $this->goods_attr       = GoodService::instance()->getGoodsAttrValue(input('cat_id'));
                // dump($this->goods_attr);
            }
        } elseif ($this->request->isPost()) {
            dump($vo);die;
        }
    }
    
    /**
     * 编辑商品
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit()
    {
        $this->title = '编辑商品';
        $this->_applyFormToken();
        $this->_form($this->table, 'form');
    }
    
    /**
     * 修改商品状态
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
     * 删除商品
     * @auth true
     * @throws \think\db\exception\DbException
     */
    public function remove()
    {
        $this->_applyFormToken();
        $this->_delete($this->table);
    }
}