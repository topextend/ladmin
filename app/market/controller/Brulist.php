<?php
// -----------------------------------------------------------------------
// |Author       : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Date         : 2020-09-25 19:07:38
// |----------------------------------------------------------------------
// |LastEditTime : 2020-09-29 15:06:19
// |----------------------------------------------------------------------
// |LastEditors  : Jarmin <edshop@qq.com>
// |----------------------------------------------------------------------
// |Description  : 
// |----------------------------------------------------------------------
// |FilePath     : \www.thinkadmin.com\app\market\controller\Brulist.php
// |----------------------------------------------------------------------
// |Copyright (c) 2020 http://www.ladmin.cn   All rights reserved. 
// -----------------------------------------------------------------------
namespace app\market\controller;

use app\market\service\BrushService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use think\admin\Controller;

/**
 * 补单数据管理
 * Class Brulist
 * @package app\market\controller
 */
class Brulist extends Controller
{
    /**
     * 绑定数据表
     * @var string
     */
    private $table = 'MarketBrushList';

    /**
     * 补单列表
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $this->title = '补单记录';    
        $this->type = trim(input('type', 'ta'), 't');    
        $field = 'a.shop_id, a.create_at, b.shop_name, b.shop_type, count(a.id) as conuts, SUM(a.user_bkge) as user_bkge,';
        $field .= 'SUM(a.order_price) as order_price, SUM(a.express_price) as express_price,SUM(a.order_price+a.pay_bkge) as capital,';
        $field .= 'SUM(a.user_bkge+a.express_price-a.pay_bkge-a.pay_price) as profit, SUM(a.user_bkge+a.order_price+a.express_price) as total,';
        $field .= 'SUM(a.pay_price) as fee';
        $query = $this->_query($this->table)->alias('a')->field($field)->dateBetween('a.create_at#create_at')->order('a.create_at desc');
        $query->like('b.shop_name#shop_name')->join('market_brush_shops b','a.shop_id=b.id')->group('a.shop_id,a.create_at');
        // 分页排序处理
        if (input('output') === 'json') {
            $result = $query->page(true, false);
            $this->success('获取数据列表成功', $result);
        } else {
            $query->page();
        }
    }
    
    /**
     * 明细列表
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function detail()
    {
        $this->title = '明细列表';
        $map = [['a.shop_id', '=', input('shop_id')],['a.create_at','like', input('create_at').'%']];
        $field = 'a.id, a.shop_id, a.order_sn, a.order_price, a.user_name, a.user_phone, ';
        $field .= 'a.user_bkge, a.express_price, a.create_at, b.shop_name, b.shop_type';
        $query = $this->_query($this->table)->alias('a')->field($field)->join('market_brush_shops b','a.shop_id=b.id');
        $query->dateBetween('a.create_at')->like('a.user_name#user_name, a.user_phone#user_phone')->where($map);
        // 列表排序并显示
        $query->order('a.id desc')->page();
    }
    
    /**
     * 导出表格
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        // 设置工作表标题名称
        $worksheet->setTitle('补单明细');
        $account = $this->app->db->name('market_brush_shops')->where(['shop_name'=>input('shop_name')])->value('account');
        // 设置单元格内容
        $worksheet->setCellValueByColumnAndRow(1, 1, $account?'补单明细('. $account . '元)':'补单明细');
        $worksheet->setCellValueByColumnAndRow(1, 2, '日期');
        $worksheet->setCellValueByColumnAndRow(2, 2, '店铺');
        $worksheet->setCellValueByColumnAndRow(3, 2, '订单');
        $worksheet->setCellValueByColumnAndRow(4, 2, '姓名');
        $worksheet->setCellValueByColumnAndRow(5, 2, '手机');
        $worksheet->setCellValueByColumnAndRow(6, 2, '金额');
        $worksheet->setCellValueByColumnAndRow(7, 2, '佣金');
        $worksheet->setCellValueByColumnAndRow(8, 2, '空包');
        $worksheet->setCellValueByColumnAndRow(9, 2, '备注');
        // 合并单元格
        $worksheet->mergeCells('A1:I1');
        $styleArray = [
            'font'      => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,],
        ];
        // 设置单元格样式
        $worksheet->getStyle('A1')->applyFromArray($styleArray)->getFont()->setSize(18);
        $worksheet->getStyle('A2:I2')->applyFromArray($styleArray)->getFont()->setSize(12);
        // 设置单元格宽度
        $worksheet->getColumnDimension('A')->setWidth(15);
        $worksheet->getColumnDimension('B')->setWidth(30);
        $worksheet->getColumnDimension('C')->setWidth(30);
        $worksheet->getColumnDimension('D')->setWidth(15);
        $worksheet->getColumnDimension('E')->setWidth(15);
        $worksheet->getColumnDimension('F')->setWidth(15);
        $worksheet->getColumnDimension('G')->setWidth(15);
        $worksheet->getColumnDimension('H')->setWidth(15);
        $worksheet->getColumnDimension('I')->setWidth(15);
        // 查询数据
        $map = ['id','>','0'];
        $filename = "所有店铺";
        if (!empty(input('shop_name')))
        {
            $shop_id = $this->app->db->name('market_brush_shops')->where(['shop_name'=>input('shop_name')])->value('id');
            $map = ['shop_id','=',$shop_id];
            $filename = input('shop_name');
        }
        if (!empty(input('create_at')))
        {
            $filename .= '_' . str_replace(' - ', '_', input('create_at'));
            if (strstr(input('create_at')," - "))
            {
                $time = str_replace(' - ', ',', input('create_at'));
                $time = explode(',',$time);
                $map = [$map, ['create_at','between', $time]];
            }
            else
            {
                $map = [$map, ['create_at','=',input('create_at')]];
            }
        }
        $list = $this->app->db->name($this->table)->where([$map])->order('create_at asc,id desc')->select()->toArray();
        $len = count($list);
        $j = 0;
        for ($i=0; $i < $len; $i++)
        {
            $j = $i + 3; //从表格第3行开始
            $worksheet->setCellValueByColumnAndRow(1, $j, format_datetime($list[$i]['create_at'],'Y-m-d'));
            $worksheet->setCellValueByColumnAndRow(2, $j, $this->app->db->name('market_brush_shops')->where(['id'=>$list[$i]['shop_id']])->value('shop_name'));
            $worksheet->setCellValueByColumnAndRow(3, $j, $list[$i]['order_sn']);
            $worksheet->setCellValueByColumnAndRow(4, $j, $list[$i]['user_name']);
            $worksheet->setCellValueByColumnAndRow(5, $j, $list[$i]['user_phone']);
            $worksheet->setCellValueByColumnAndRow(6, $j, $list[$i]['order_price']);
            $worksheet->setCellValueByColumnAndRow(7, $j, $list[$i]['user_bkge']);
            $worksheet->setCellValueByColumnAndRow(8, $j, $list[$i]['express_price']);
            $worksheet->setCellValueByColumnAndRow(9, $j, '');
        }
        $worksheet->mergeCells('A'.($len+3).':E'.($len+3));
        $worksheet->setCellValueByColumnAndRow(1, $len+3, '合计');
        $worksheet->setCellValueByColumnAndRow(6, $len+3, '=SUM(F3:F'.($len+2).')');
        $worksheet->setCellValueByColumnAndRow(7, $len+3, '=SUM(G3:G'.($len+2).')');
        $worksheet->setCellValueByColumnAndRow(8, $len+3, '=SUM(H3:H'.($len+2).')');
        $worksheet->setCellValueByColumnAndRow(9, $len+3, '=SUM(F'.($len+3).':H'.($len+3).')');
        $styleArrayBody = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '666666'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $total_jzInfo = $len + 3;
        //添加所有边框/居中
        $worksheet->getStyle('A1:I'.$total_jzInfo)->applyFromArray($styleArrayBody);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition:attachment;filename={$filename}.xlsx");
        header('Cache-Control: max-age=0');//禁止缓存
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
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
        $this->shop_id = input('shop_id', 0);
    }
    
    /**
     * 表单数据处理
     * @param array $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function _form_filter(array &$data)
    {
        if ($this->request->isGet()) {
            $this->shops = BrushService::instance()->getShopsList('id, shop_type, shop_name');
        }  elseif ($this->request->isPost()) {
            if (empty(input('id')))
            {
                if (empty($data['shop_id']))   $this->error('请选择店铺!');
                if (empty($data['order_str'])) $this->error('请输入订单信息!');
                $orders = explode("\r\n", $data['order_str']);
                foreach ($orders as $value)
                {
                    strstr($value, '+') ? $order = explode("+", $value) : $order = explode("\t", $value);
                    $shop = $this->app->db->name('MarketBrushShops')->field('shop_type, account, bkge, express_price')->where(['id' => $data['shop_id']])->find();
                    if ($shop['shop_type'] == 1)
                    {
                        if (strstr($order[0], '-') == false) $this->error('订单号格式不正确!');
                        if (strlen($order[0])      != 22)    $this->error('订单号格式不正确!');
                        if (strlen($order[2])      != 11)    $this->error('手机号格式不正确!');
                        if (!is_numeric($order[3]))          $this->error('订单金额格式不正确!');
                        if (!is_numeric($order[4]))          $this->error('佣金金额格式不正确!');
                        if (!empty($order[0]) && !empty($order[1]) && !empty($order[2]) && !empty($order[3]) && !empty($order[4]))
                        {
                            $list = [
                                'shop_id'     => $data['shop_id'],
                                'order_sn'    => $order[0],
                                'user_name'   => $order[1],
                                'user_phone'  => $order[2],
                                'order_price' => $order[3],
                                'pay_bkge'    => $order[4],
                                'create_at'   => BrushService::PddOrder2Date($order[0]),
                            ];
                            empty($order[5]) ? $list['user_bkge']     = $shop['bkge']           : $list['user_bkge']     = $order[5];
                            empty($order[6]) ? $list['express_price'] = $shop['express_price']  : $list['express_price'] = $order[6];
                            empty($order[7]) ? $list['pay_price']     = '1.6'                   : $list['pay_price']     = $order[7];
                            $log['shop_id']     = $data['shop_id'];
                            $log['account_log'] = "消费" . $list['order_sn'] . "订单金额" . $list['order_price'] . ",佣金" . $list['user_bkge'] . ",空包费用" . $list['express_price'];
                            $log['account']     = $shop['account'] - $list['order_price'] - $list['user_bkge'] - $list['express_price'];
                            // if ($log['account'] < 0) $this->error("店铺资金不足，请充值");
                            data_save($this->table, $list, 'order_sn');
                            $count = $this->app->db->name('MarketBrushLog')->whereLike('account_log', '%'.$list['order_sn'].'%')->count();
                            if ($count == 0) {
                                // 更新店铺资金
                                $this->app->db->name('MarketBrushShops')->where(['id' => $data['shop_id']])->update(['account' => $log['account']]);
                                // 记录资金日志
                                $this->app->db->name('MarketBrushLog')->save($log);
                            }
                        } else {
                            $this->error('订单信息格式不正确!');
                        }
                    } else {
                        $this->error('淘宝及京东未完善!');
                    }
                }
                unset($data['shop_id']);
                unset($data['order_str']);
            }
        }
    }

    /**
     * 添加订单信息
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
     * 编辑订单信息
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit()
    {
        $this->title = '编辑订单信息';
        $this->_applyFormToken();
        $this->_form($this->table, 'detail_form');
    }

    /**
     * 删除订单信息
     * @auth true
     * @throws \think\db\exception\DbException
     */
    public function remove()
    {
        $this->_applyFormToken();
        $this->_delete($this->table);
    }
}