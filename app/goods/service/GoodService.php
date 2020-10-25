<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2020-09-25 18:02:13
// |----------------------------------------------------------------------
// |LastEditTime : 2020-10-16 01:14:10
// |----------------------------------------------------------------------
// |LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Description  : 
// |----------------------------------------------------------------------
// |FilePath     : \www.thinkadmin.com\app\goods\service\GoodService.php
// |----------------------------------------------------------------------
// |Copyright (c) 2020 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace app\goods\service;

use think\admin\extend\DataExtend;
use think\admin\Service;

/**
 * 补单数据服务
 * Class GoodService
 * @package app\goods\service
 */
class GoodService extends Service
{
    /**
     * 获取店铺数据
     * @return array
     */
    public function getGoodsValue(string $table, string $value): array
    {
        $map = ['status' => 1];
        $query = $this->app->db->name($table);
        return $query->where($map)->order('sort desc,id desc')->column('id,'.$value);
    }

    /**
     * 获取店铺分类数据
     * @return array
     */
    public function getGoodsCats(string $pid, string $value): array
    {
        $map = ['pid' => $pid];
        $query = $this->app->db->name('GoodsCat');
        return $query->where($map)->order('sort desc,id desc')->column($value);
    }

    /**
     * 获取店铺分类数据
     * @return array
     */
    public function getJosnCats()
    {
        $cat = $this->getGoodsCats('0','id, cat_name');
        foreach ($cat as $key => $value)
        {
            $value['child'] = $this->getGoodsCats($cat[$key]['id'],'id, cat_name');
            foreach ($value['child'] as $kk => $vv)
            {
                $vv['child'] = $this->getGoodsCats($value['child'][$kk]['id'],'id, cat_name');
                $value['child'][$kk] = $vv;
            }
            $cats['option'][$key] = $value;
        }
        return json_encode($cats,JSON_UNESCAPED_UNICODE);
    }

    /**
     * 获取指定分类的父类ID
     * @return array
     */
    public function getParentAttr(string $cat_id) : array
    {
        $query = $this->app->db->name('GoodsCat');
        return $query->field('pid, cat_name')->where(['id' => $cat_id])->find();
    }

    /**
     * 重组选择的商品分类
     * @return string
     */
    public function makeGoodsCats(string $cat_id) : string
    {
        $cat_3 = $this->getParentAttr($cat_id);
        $cat_2 = $this->getParentAttr($cat_3['pid']);
        $cat_1 = $this->getParentAttr($cat_2['pid']);
        return $cat_1['cat_name'] . "<font>&gt;</font>" . $cat_2['cat_name'] ."<font>&gt;</font>" . $cat_3['cat_name'];
    }

    /**
     * 获取指定分类的属性类型
     * @return string
     */
    public function getCatsTypeID(string $cat_id) : string
    {
        $query = $this->app->db->name('GoodsCat');
        return $query->where(['id' => $cat_id])->value('type_id');
    }

    /**
     * 获取店铺类型属性
     * @return array
     */
    public function getGoodsAttrValue(string $cat_id): array
    {
        $type_id = $this->getCatsTypeID($cat_id);
        $query = $this->app->db->name('GoodsAttr')->where(['type_id'=>$type_id])->column('id, attr_type, attr_name, attr_values');
        foreach ($query as $key => $value) {
            $value['attr_values'] = explode(',',$value['attr_values']);
            // foreach ($value['attr_values'] as $kk => $vv){
            //     if (strpos($vv,'|')) {
            //         $value['attr_values']['name'][$kk]  = substr($vv,0,strpos($vv, '|'));
            //         $value['attr_values']['code'][$kk]   = trim(strrchr($vv, '|'),'|');
            //         unset($value['attr_values'][$kk]);
            //     }
            // }
            // dump($value);
            $attrs[$key] = $value;
        }
        return $attrs;
    }
    
    /**
     * 获取最后一条记录的ID编号
     */
    public function getLastID(string $table = 'GoodsAttr') : int
    {
        $query = $this->app->db->name($table);
        $id = $query->order('id desc')->value('id');
        return isset($id) ? $id : 0;
    }

    public function getColorCode(string $value) : string
    {
        $color = [
            '白色' => '#FFFFFF',
            '红色' => '#FF0000',
            '绿色' => '#00FF00',
            '蓝色' => '#0000FF',
            '青色' => '#00FFFF',
            '黄色' => '#FFFF00',
            '黑色' => '#000000',
            '棕色' => '#A67D3D',
            '橙色' => '#FF7F00',
            '灰色' => '#666666',
            '粉色' => '#FFB6C1',
            '褐色' => '#855b00',
        ];
        return !isset($color[$value]) ? "" : $color[$value];
    }
    /**
     * 最大分类级别
     * @return integer
     */
    public function getCateLevel(): int
    {
        return 3;
    }
}