<?php

/**
 * Created by Xmh.
 * User: wang fuqiang
 * Date: 2018/10/9
 * Time: 15:32
 * tp5.0 phpexecel导出
 */
class PhpExcel
{
    //简单的
    public function test()
    {
        vendor("PHPExcel.PHPExcel");
        Loader::import('PHPExcel.PHPExcel');
        Loader::import('PHPExcel.PHPExcel.IOFactory.PHPExcel_IOFactory');
        Loader::import('PHPExcel.PHPExcel.Reader.Excel5');

        $path = dirname(__FILE__); //找到当前脚本所在路径
        $PHPExcel = new \PHPExcel(); //实例化PHPExcel类，类似于在桌面上新建一个Excel表格
        $PHPSheet = $PHPExcel->getActiveSheet(); //获得当前活动sheet的操作对象
        $PHPSheet->setTitle('demo'); //给当前活动sheet设置名称
        $PHPSheet->setCellValue('A1','姓名')->setCellValue('B1','分数');//给当前活动sheet填充数据，数据填充是按顺序一行一行填充的，假如想给A1留空，可以直接setCellValue(‘A1’,’’);
        $PHPSheet->setCellValue('A2','张三')->setCellValue('B2','50');
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,'Excel2007');//按照指定格式生成Excel文件，‘Excel2007’表示生成2007版本的xlsx，‘Excel5’表示生成2003版本Excel文件
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//告诉浏览器输出07Excel文件
        //header('Content-Type:application/vnd.ms-excel');//告诉浏览器将要输出Excel03版本文件
        header('Content-Disposition: attachment;filename="01simple.xlsx"');//告诉浏览器输出浏览器名称
        header('Cache-Control: max-age=0');//禁止缓存
        $PHPWriter->save("php://output");
    }


    //复杂的
    //$cellName['显示的值','显示的值','合并的单元格','单元格宽度','对齐方式']
    //
    public function index()
    {
        $title="这个是测试的标题";
        $cellName=[
            0=>['id','id',0,12,'CENTER'],
            1=>['state','订单状态',0,12,'left'],
            2=>['type','订单类型',0,12,'LEFT'],
            3=>['price','支付额',0,12,'LEFT'],
            4=>['member','会员',0,12,'LEFT'],
            5=>['store','店铺',0,12,'LEFT'],
            6=>['order_name','订单名称',0,12,'LEFT'],
            7=>['num','商品数量',0,12,'LEFT'],
            8=>['data','日期',0,12,'LEFT'],
        ];

        $data=[
            0=>['id'=>1,'state'=>'代付款','type'=>'普通订单','price'=>'48.01','member'=>'王大','store'=>'北京','order_name'=>'娃哈哈','num'=>'1523','data'=>'2018-10-09']
        ];

        $this->exportOrderExcel($title,$cellName,$data);

    }


    //导出
    //$title="这个是测试的标题";
    //$cellName=[
    //0=>['id','id',0,12,'CENTER'],
    //1=>['state','订单状态',0,12,'left'],
    //2=>['type','订单类型',0,12,'LEFT'],
    //3=>['price','支付额',0,12,'LEFT'],
    //4=>['member','会员',0,12,'LEFT'],
    //5=>['store','店铺',0,12,'LEFT'],
    //6=>['order_name','订单名称',0,12,'LEFT'],
    //7=>['num','商品数量',0,12,'LEFT'],
    //8=>['data','日期',0,12,'LEFT'],
    //];
    //
    //$data=[
    //0=>['id'=>1,'state'=>'代付款','type'=>'普通订单','price'=>'48.01','member'=>'王大','store'=>'北京','order_name'=>'娃哈哈','num'=>'1523','data'=>'2018-10-09']
    //];

    public function exportOrderExcel($title,$cellName,$data)
    {
        //引入核心文件
        vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new \PHPExcel();
        //定义配置
        $topNumber = 2;//表头有几行占用
        $xlsTitle = iconv('utf-8', 'gb2312', $title);//文件名称
        $fileName = $title.date('_YmdHis');//文件名称
        $cellKey = array(
            'A','B','C','D','E','F','G','H','I','J','K','L','M',
            'N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
            'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM',
            'AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ'
        );

        //写在处理的前面（了解表格基本知识，已测试）
//     $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);//所有单元格（行）默认高度
//     $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);//所有单元格（列）默认宽度
//     $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);//设置行高度
//     $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);//设置列宽度
//     $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);//设置文字大小
//     $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);//设置是否加粗
//     $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);// 设置文字颜色
//     $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//设置文字居左（HORIZONTAL_LEFT，默认值）中（HORIZONTAL_CENTER）右（HORIZONTAL_RIGHT）
//     $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
//     $objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);//设置填充颜色
//     $objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('FF7F24');//设置填充颜色

        //处理表头标题
        $objPHPExcel->getActiveSheet()->mergeCells('A1:'.$cellKey[count($cellName)-1].'1');//合并单元格（如果要拆分单元格是需要先合并再拆分的，否则程序会报错）
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1',$title);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

        //处理表头
        foreach ($cellName as $k=>$v)
        {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellKey[$k].$topNumber, $v[1]);//设置表头数据
            $objPHPExcel->getActiveSheet()->freezePane($cellKey[$k].($topNumber+1));//冻结窗口
            $objPHPExcel->getActiveSheet()->getStyle($cellKey[$k].$topNumber)->getFont()->setBold(true);//设置是否加粗
            $objPHPExcel->getActiveSheet()->getStyle($cellKey[$k].$topNumber)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
            if($v[3] > 0)//大于0表示需要设置宽度
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($cellKey[$k])->setWidth($v[3]);//设置列宽度
            }
        }

        //处理数据
        foreach ($data as $k=>$v)
        {
            foreach ($cellName as $k1=>$v1)
            {
                $objPHPExcel->getActiveSheet()->setCellValue($cellKey[$k1].($k+1+$topNumber), $v[$v1[0]]);
                if($v['end'] > 0)
                {
                    if($v1[2] == 1)//这里表示合并单元格
                    {
                        $objPHPExcel->getActiveSheet()->mergeCells($cellKey[$k1].$v['start'].':'.$cellKey[$k1].$v['end']);
                        $objPHPExcel->getActiveSheet()->getStyle($cellKey[$k1].$v['start'])->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    }
                }
                if($v1[4] != "" && in_array($v1[4], array("LEFT","CENTER","RIGHT")))
                {
                    $v1[4] = eval('return PHPExcel_Style_Alignment::HORIZONTAL_'.$v1[4].';');
                    //这里也可以直接传常量定义的值，即left,center,right；小写的strtolower
                    $objPHPExcel->getActiveSheet()->getStyle($cellKey[$k1].($k+1+$topNumber))->getAlignment()->setHorizontal($v1[4]);
                }
            }
        }
        //导出execl
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

}