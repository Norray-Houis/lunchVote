<?php

/**
 * Created by PhpStorm.
 * User: dev_001
 * Date: 18/4/3
 * Time: 下午6:22
 */
class Vote_library
{
    private $ci;
    public function __construct()
    {
        $this->ci = &get_instance();
    }


    /**
     * @param $voteId  投票Id
     * @param $option  选项字符串
     * @param $additionOption  附加选项字符串
     * @return array   选项与附加组合的数组
     * 生成选项数组
     */
    public function makeOptionArr($voteId,$option,$additionOption){
        $optionArr = array_filter(explode("\n",$option));
        $additionArr = array_filter(explode("\n",$additionOption));
        $result = [];
        for ($i=0;$i<count($optionArr);$i++){
            for ($j=0;$j<count($additionArr);$j++){
                $result[] = array(
                    "voteId" =>$voteId,
                    "option" => $optionArr[$i],
                    "addition" => $additionArr[$j],
                    "addition_option" => str_replace(array("\r\n", "\r", "\n"),'',$optionArr[$i])." ".str_replace(array("\r\n", "\r", "\n"),'',$additionArr[$j]),
                    "num" => 0,
                    "is_show"=>1,
                );
            }
        }

        $result[] = array(
            "voteId" =>$voteId,
            "option" => "粥",
            "addition" => "无",
            "addition_option"=>"粥 无",
            "num" => 0,
            "is_show"=>0,
        );

        return $result;
    }

    public function getMothlyData($date){
        $firstDay = date('Y-m-01',strtotime($date));
        $lastDay = date('Y-m-d',strtotime("$firstDay +1 month -1 day"));

        $monthlyData = [];
        $query = $this->ci->db->query("SELECT v.date,vo.*,sum(vo.num) as total FROM tbl_vote AS v LEFT JOIN tbl_vote_option AS vo ON vo.voteId = v.id AND vo.is_del = 0 WHERE v.date >= '$firstDay' AND v.date <= '$lastDay' GROUP BY vo.voteId,vo.option");

        $result = $query->result_array();
        $total = [array('value'=>'共计','colspan'=>2,'align'=>'center'),"A"=>0,"B"=>0,"C"=>0,"D"=>0,"E"=>0,"粥"=>0,'小计1'=>0,'无'=>0,'配汤'=>0,'配柠檬茶'=>0,'小计2'=>0];
        foreach ($result as $k){


            if(!isset($monthlyData[$k['date']])){
                $monthlyData[$k['date']] = array(
                    "所属月份"=>date('Ym',strtotime($date)),
                    "出餐日期"=>$k['date'],
                    "A"=>0,
                    "B"=>0,
                    "C"=>0,
                    "D"=>0,
                    "E"=>0,
                    "粥"=>0,
                    "小计1"=>0,
                    '无'=>0,
                    '汤'=>0,
                    '饮料'=>0,
                    "小计2"=>0,
                );
            }

            $monthlyData[$k['date']][mb_substr($k['option'],0,1)] = $k['total'];

            $total[mb_substr($k['option'],0,1)]+= $k['total'];
        }

        $query = $this->ci->db->query("SELECT v.date,vo.*,sum(vo.num) as total FROM tbl_vote AS v LEFT JOIN tbl_vote_option AS vo ON vo.voteId = v.id AND vo.is_del = 0 WHERE v.date >= '$firstDay' AND v.date <= '$lastDay' GROUP BY vo.voteId,vo.addition");

        $result = $query->result_array();

//        foreach ($result as $k){
//            $monthlyData[$k['date']][str_replace(array("\r\n", "\r", "\n"),'',$k['addition'])] = $k['total'];
//            $total[str_replace(array("\r\n", "\r", "\n"),'',$k['addition'])]+= $k['total'];
//        }
//        $monthlyData[] = $total;
//        foreach ($monthlyData as $k => $v){
//            $monthlyData[$k]['小计1'] = $v['A']+$v['B']+$v['C']+$v['D']+$v['E']+$v['粥'];
//            $monthlyData[$k]['小计2'] = $v['无']+$v['配汤']+$v['配柠檬茶'];
//        }

        return $monthlyData;
    }

    function number2chinese($num,$mode = false,$sim = true){
        if(!is_numeric($num)) return '含有非数字非小数点字符！';
        $char    = $sim ? array('零','一','二','三','四','五','六','七','八','九')
            : array('零','壹','贰','叁','肆','伍','陆','柒','捌','玖');
        $unit    = $sim ? array('','十','百','千','','万','亿','兆')
            : array('','拾','佰','仟','','萬','億','兆');
        $retval  = $mode;
        //小数部分
        if(strpos($num, '.')){
            list($num,$dec) = explode('.', $num);
            $dec = strval(round($dec,2));
            if($mode){
                $retval .= "{$char[$dec['0']]}角{$char[$dec['1']]}分";
            }else{
                for($i = 0,$c = strlen($dec);$i < $c;$i++) {
                    $retval .= $char[$dec[$i]];
                }
            }
        }
        //整数部分
        $str = $mode ? strrev(intval($num)) : strrev($num);
        for($i = 0,$c = strlen($str);$i < $c;$i++) {
            $out[$i] = $char[$str[$i]];
            if($mode){
                $out[$i] .= $str[$i] != '0'? $unit[$i%4] : '';
                if($i>1 and $str[$i]+$str[$i-1] == 0){
                    $out[$i] = '';
                }
                if($i%4 == 0){
                    $out[$i] .= $unit[4+floor($i/4)];
                }
            }
        }
        $retval = join('',array_reverse($out)) . $retval;
        return $retval;
    }

    /**
     * @param $data
     * @return array
     * 格式化报表每日订购数据
     */
    public function formaReportDailyData($data){
        $resultData = array();
        foreach ($data as $v){
            if(!isset($resultData[$v['date']])){
                $resultData[$v['date']] = array(
                    'date'=> date('Y.m.d',strtotime($v['date'])),
                    'option'=>array(),
                    'totalMoney'=>0
                );
            }

            $resultData[$v['date']]['totalMoney'] += ($v['num']*$v['single_cost']);

            if(!isset($resultData[$v['date']]['option'][$v['option']])){
                $resultData[$v['date']]['option'][$v['option']] = array(
                    'title'=>$v['option'],
                    'num'=>0,
                    'soup'=>0,
                    'drink'=>0,
                    'totalMoney'=>0
                );
            }

            $resultData[$v['date']]['option'][$v['option']]['num'] += $v['num'];
            $resultData[$v['date']]['option'][$v['option']]['totalMoney'] += ($v['num']*$v['single_cost']);

            if(stristr($v['addition'],'汤')){
                $resultData[$v['date']]['option'][$v['option']]['soup'] += $v['num'];
            }
            if(stristr($v['addition'],'饮料')){
                $resultData[$v['date']]['option'][$v['option']]['drink'] += $v['num'];
            }


        }

        return $resultData;
    }



    public function formaReportMonthlyData($data){
        $resultData = array();
        $monthTotalMoney = 0;
        foreach ($data as $v){
            if(!isset($resultData[$v['date']])){
                $resultData[$v['date']] = array(
                    'date'=> date('Y.m.d',strtotime($v['date'])),
                    "",
                    'num'=>0,
                    'totalMoney'=>0,

                );
            }
            $v['totalMoney'] = ($v['num']*$v['single_cost']);
            $resultData[$v['date']]['num'] += $v['num'];
            $resultData[$v['date']]['totalMoney'] += $v['totalMoney'];
            $monthTotalMoney+= $v['totalMoney'];
        }
        $resultData[] = $monthTotalMoney;
        return $resultData;
    }
}