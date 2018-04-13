<?php

/**
 * Created by PhpStorm.
 * User: Houis
 * Date: 18/4/4
 * Time: 下午3:28
 */
class Timer extends NLF_Controller
{
    /**
     * 通知投票结果
     */
    public function voteResult(){
//            exit('x1');
        //消息模板ID
        $templateId = 'lfohcAMABZpB6__bK219Mv3_DMKpERoVqkGSCbr1RPs';
        $this->load->library('log_library');
        $this->load->library('wechat_lib');
        ignore_user_abort();//
        set_time_limit(0);//
        $second = strtotime(date('Y-m-d 10:00:00',strtotime('+1 day')))-time();
//        var_dump($second);
//        exit($second);
        do{
            $this->db->select("vl.create_time,wu.nickname,wu.head_img,vo.*,vl.description,vl.id as logId,wu.openId,wu.department,wu.position,vl.scene,vl.form_id");
            $this->db->from('wechat_user AS wu');
            $this->db->join("vote_log as vl", "wu.openId = vl.openId AND vl.is_del = 0", 'left');
            $this->db->join("vote_option as vo", "vo.id = vl.option_id", 'left');
            $this->db->join('vote as v','v.id = vl.vote_id','left');
            $this->db->where("v.date = '".date('Y-m-d')."'");
            $query = $this->db->get();
            $userLog = $query->result_array();
//            p($userLog);exit;
//            p($this->db->last_query());
            foreach ($userLog as $log){
                $postData = array(
                    "touser"=>$log['openId'],
                    "template_id"=>$templateId,
                    "page"=>"vote/vote",
                    "form_id"=>$log['form_id'],
                    "data"=>array(
                        "keyword1"=>array(
                            "value"=>date('Y-m-d')."玖晔午餐结果",
                        ),
                        "keyword2"=>array(
                            "value"=>"单选",
                        ),
                        "keyword3"=>array(
                            "value"=>$log['addition_option'],
                        ),
                    ),
                );
                $access = $this->wechat_lib->getAccessToken();
                $result = $this->wechat_lib->sendTemplateMsg($access,$postData);
//                p($result);
                if(is_array($result)){
                    $result = json_response($result);
                }
                log_message('debug',$result);
                $this->log_library->writeLog(date('Y-m-d').".txt",$result);
            }

            unset($query,$result,$access,$postData,$userLog);
            sleep($second);
        }while(true);
    }

    public function timerTest(){
        ignore_user_abort();//
        set_time_limit(0);//
        $second = 10;

        do{
            file_put_contents('timerLog.txt',date('Y-m-d H:i:s'));

           sleep($second);
        }while(true);
    }



    public function suanfa(){
        $a = ["1","2","3","20","4","60","9","15","11","8","9","7","10","-1"];

//        $a = $this->insertSort($a);
        $a = $this->insertSortDichotomy($a);
//        sort($a);
        p($a);
    }

    /***
     * @param $arr
     * @return mixed
     * 冒泡排序【重复比较两个相邻的元素，如果位置错误则对调位置，直至没有元素需要对换】
     * 分类 -------------- 内部比较排序
     * 数据结构 ---------- 数组
     * 最差时间复杂度 ---- O(n^2)
     * 最优时间复杂度 ---- 如果能在内部循环第一次运行时,使用一个旗标来表示有无需要交换的可能,可以把最优时间复杂度降低到O(n)
     * 平均时间复杂度 ---- O(n^2)
     * 所需辅助空间 ------ O(1)
     * 稳定性 ------------ 稳定
     */
    private function bubbleSort($arr){
        for ($i=0;$i<count($arr);$i++){
            for ($j=0;$j<count($arr)-1;$j++){
                if($arr[$j] > $arr[$j+1]){
                    $tmp = $arr[$j];
                    $arr[$j]=$arr[$j+1];
                    $arr[$j+1] = $tmp;
                }
            }
        }
        return $arr;
    }

    /**
     * @param $arr
     * @return mixed
     * 鸡尾酒排序【又叫定向冒泡排序，冒泡排序的一种改进，先向右排序出最大的那个，然后向左排序出最小的，周而复此】
     * 分类 -------------- 内部比较排序
     * 数据结构 ---------- 数组
     * 最差时间复杂度 ---- O(n^2)
     * 最优时间复杂度 ---- 如果序列在一开始已经大部分排序过的话,会接近O(n)
     * 平均时间复杂度 ---- O(n^2)
     * 所需辅助空间 ------ O(1)
     * 稳定性 ------------ 稳定
     */
    private function cocktailSort($arr){

        $left = 0;
        $right = count($arr)-1;
        while ($left < $right){
            for ($i=$left;$i<$right;$i++){
                if($arr[$i] > $arr[$i+1]){
                    $tmp = $arr[$i];
                    $arr[$i]=$arr[$i+1];
                    $arr[$i+1] = $tmp;
                }
            }
            $left++;
            for ($i=$right;$i>$left;$i--){
                if($arr[$i-1] > $arr[$i]){
                    $tmp = $arr[$i-1];
                    $arr[$i-1]=$arr[$i];
                    $arr[$i] = $tmp;
                }
            }
            $right--;
        }
        return $arr;
    }

    /**
     * @param $arr
     * @return mixed
     * 选择排序【找出第1/2/3/4/../n个最小(大)的值放到相应的位置，然后继续找出下一个未排序的最小（大）值】
     * 分类 -------------- 内部比较排序
     * 数据结构 ---------- 数组
     * 最差时间复杂度 ---- O(n^2)
     * 最优时间复杂度 ---- O(n^2)
     * 平均时间复杂度 ---- O(n^2)
     * 所需辅助空间 ------ O(1)
     * 稳定性 ------------ 不稳定
     */
    private function chooseSort($arr){
        for ($i=0;$i<count($arr);$i++){
            $min = $i;
            //因为是从i开始比较，所以i+1
            for ($j=$i+1;$j<count($arr);$j++){
                if($arr[$min]>$arr[$j]){
                    $min = $j;
                }
            }
            if($min != $i){
                $tmp = $arr[$i];
                $arr[$i] = $arr[$min];
                $arr[$min] = $tmp;
            }
        }
        return $arr;
    }

    /**
     * @param $arr
     * @return mixed
     * 选择排序【往左一个个比较】
     * 分类 ------------- 内部比较排序
     * 数据结构 ---------- 数组
     * 最差时间复杂度 ---- 最坏情况为输入序列是降序排列的,此时时间复杂度O(n^2)
     * 最优时间复杂度 ---- 最好情况为输入序列是升序排列的,此时时间复杂度O(n)
     * 平均时间复杂度 ---- O(n^2)
     * 所需辅助空间 ------ O(1)
     * 稳定性 ------------ 稳定
     */
    private function insertSort($arr){
        for ($i=1;$i<count($arr);$i++){
            $get = $arr[$i];
            $j = $i-1;
            while($j>=0 && $arr[$j]>$get){
                $arr[$j+1] = $arr[$j];
                $j--;
            }
            $arr[$j+1] = $get;
        }
        return $arr;
    }

    /**
     * @param $arr
     * @return mixed
     * 二分法选择排序
     * 分类 -------------- 内部比较排序
     * 数据结构 ---------- 数组
     * 最差时间复杂度 ---- O(n^2)
     * 最优时间复杂度 ---- O(nlogn)
     * 平均时间复杂度 ---- O(n^2)
     * 所需辅助空间 ------ O(1)
     * 稳定性 ------------ 稳定
     */
    private function insertSortDichotomy($arr){
        return $arr;
    }

    public function writeLog($fileId,$content){
        $this->load->library('log_library');
        $this->log_library->writeLog($fileId,$content);
    }

    public function getLog($fileId){
        $this->load->library('log_library');
        p($this->log_library->readLog($fileId));
    }
}