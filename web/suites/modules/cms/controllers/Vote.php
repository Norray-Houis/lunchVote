<?php

/**
 * Created by PhpStorm.
 * User: Houis
 * Date: 18/3/27
 * Time: 上午11:37
 */
class Vote extends NLF_Controller
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        require_once(getcwd(). '/suites/config/configurations.php');
        $this->session->set_userdata('set_id', 8);
    }

    /**
     * 发起点餐
     */
    public function form(){
        if(date('w') >= 5){
            $endDay = date('Y-m-d',strtotime('+1 week last monday'));
        }else{
            $endDay = date('Y-m-d',strtotime('+1 day'));
        }
        $channel = '发起点餐';
        $array = compact('endDay','channel');
        load_view("vote/form", $array);
    }



    /**
     * 投票列表
     */
    public function listing(){

        //读取投票数据
        $this->load->model('vote_mdl');
        $voteList = $this->vote_mdl->getListByDesc();

        $channel = '点餐列表';
        $array = compact('channel','voteList');
//        p($array);exit;
        load_view('vote/listing',$array);
    }

    public function details($voteId){
        $this->load->model('vote_mdl');
        $userLog = $this->vote_mdl->getVoteLog($voteId);
        $statistics = $this->vote_mdl->getVoteStatistics($voteId);

        $this->load->model('vote_option_mdl');
        $option = $this->vote_option_mdl->getOpptionByVoteId($voteId);

        $channel = '点餐详情';
        $array = compact('userLog','channel','statistics','option','voteId');
//        p($array);exit;
        load_view('vote/details',$array);
    }




    /**
     * @param bool $nickName
     */
    public function user($nickName = FALSE){
        $this->load->model('wechat_user_mdl');
        $userList = $this->wechat_user_mdl->getUserList($nickName);
        $channel = "用户管理";
        $array = compact('userList','channel');
        load_view('vote/user',$array);
    }

    /**
     *  修改用户投票权限操作
     */
    public function changeAccess(){
        $openId = $this->input->post('openId');
        $access = $this->input->post('access');
        $data = array(
            "vote_access"=>$access,
            'openId' => $openId,
        );
        $this->load->model('wechat_user_mdl');
        $this->wechat_user_mdl->updateUser($openId,$data);

        json_response('0','success');
    }



    /**
     * vote统一动作处理方法
     * 方法的多态性
     */
    public function actionByWechat(){
        $action = $this->input->post('action');
        $this->load->model('vote_log_mdl');
        $this->load->model('vote_option_mdl');
        $this->load->model('vote_mdl');
        $this->load->model('wechat_user_mdl');

        $this->load->library('vote_library');
        $this->load->library('wechat_lib');
        if(!$action) json_response(1,'miss param action');

        switch ($action){
            case 'saveUserChoose':
                //查询用户是否已经投票，没有则创建一条记录

//                $optionId = $this->input->post('optionId');
                $query = $this->db->query("SELECT * FROM tbl_vote_option WHERE `option` = '".$this->input->post('option')."' AND `addition` = '".$this->input->post('addition')."'");
                $option = $query->row_array();
                $optionId = $option['id'];

                $openId = $this->input->post('openId');
                $voteId = $this->input->post('voteId');
                $formId = $this->input->post('formId');

                $log = $this->vote_log_mdl->getUserLogById($openId,$voteId);
                if(!$log){
                    //第一次投票创建记录
                    $this->vote_log_mdl->addLog($openId,$voteId,$optionId,0,$formId);
                    //累加选项统计栏
                    $this->vote_option_mdl->optionNumAddNum($optionId);

                    $updateVote = array(
                        'update_time' => date('Y-m-d H:i:s'),
                        'total' => count($this->vote_log_mdl->getLogByVoteId($voteId)),
                    );
                    $this->vote_mdl->updateVote($voteId,$updateVote);

                }
                else{
                    //更改投票
                    if($log['option_id'] == $optionId && $log['is_del'] == 0){
                        goto end;
                    }
                    if($log['is_del'] == 0){
                        //旧记录-1
                        $this->vote_option_mdl->optionNumRedict($log['option_id']);
                    }
                    $logData = array(
                        'update_time' => date('Y-m-d H:i:s'),
                        'scene'=>'0',
                        'is_del'=>'0',
                        'option_id'=>$optionId,
                        'old_option_id'=>$log['option_id'],
                        'description'=>'',
                        'form_id'=>$formId,
                    );
                    $this->vote_log_mdl->updateLog($log['id'],$logData);

                    //新纪录+1
                    $this->vote_option_mdl->optionNumAddNum($optionId);

                    $updateVote = array(
                        'update_time' => date('Y-m-d H:i:s'),
                        'total' => count($this->vote_log_mdl->getLogByVoteId($voteId)),
                    );
                    $this->vote_mdl->updateVote($voteId,$updateVote);
                }

                end:
                //重新获取数据喂给小程序
                $vote = $this->vote_mdl->getVoteDetailsById($voteId);
                $statistics = $this->vote_mdl->getVoteStatistics($vote['id'],1);
                $statistics['optionTotal'] = $this->vote_library->number2chinese(count($statistics['optionStatistics']));
                $statistics['additionTotal'] = $this->vote_library->number2chinese(count($statistics['additionStatistics']));
                $log = $this->vote_log_mdl->getLogByVoteIdAndOpenId($this->input->post('openId'),$vote['id']);

                $data = compact('vote','statistics','log');
                json_response(0,'success',$data);
                break;
            case 'getVoteAndOpenId': //微信小程序端渲染页面的时候获取投票数据

                $date = date('Y-m-d');
                if(date('H:i:s') < '14:00:00'){
                    $vote = $this->vote_mdl->getVoteDetailsByDate($date);
                }else{
                    $query = $this->db->query("SELECT * FROM tbl_vote WHERE date > '$date' ORDER BY date ASC LIMIT 1");
                    $vote = $query->row_array();
                }
                if(!$vote){
                    $vote = $this->vote_mdl->getNesestVote();
                }



                //解密用户信息
                $encryptedData = $this->wechat_lib->decodeEncryptedData($this->input->post('code'),$this->input->post('iv'),$this->input->post('encryptedData'));
                $openId = $encryptedData['data']['openId'];

                //查询用户权限，没有用户则创建用户
                $user = $this->wechat_user_mdl->getWechatUserByOpenId($openId);
                if(!$user){
                    $this->wechat_user_mdl->addUser($encryptedData['data']);
                    $voteAccess = 0;
                }else{
                    $voteAccess = $user['vote_access'];
                }

                //获取该用户该投票记录
                $log = $this->vote_log_mdl->getLogByVoteIdAndOpenId($openId,$vote['id']);
                $statistics = $this->vote_mdl->getVoteStatistics($vote['id'],1);
                $statistics['optionTotal'] = $this->vote_library->number2chinese(count($statistics['optionStatistics']));
                $statistics['additionTotal'] = $this->vote_library->number2chinese(count($statistics['additionStatistics']));

                $data = compact('vote','statistics','option','openId','voteAccess','log');

                json_response(0,'success',$data);
                break;
            case 'getVote':
                $date = date('Y-m-d');
                if(date('H:i:s') < '14:00:00'){
                    $vote = $this->vote_mdl->getVoteDetailsByDate($date);
                }else{
                    $query = $this->db->query("SELECT * FROM tbl_vote WHERE date > '$date' ORDER BY date ASC LIMIT 1");
                    $vote = $query->row_array();
                }

                if(!$vote){
                    $vote = $this->vote_mdl->getNesestVote();
                }

                $statistics = $this->vote_mdl->getVoteStatistics($vote['id'],1);
                $statistics['optionTotal'] = $this->vote_library->number2chinese(count($statistics['optionStatistics']));
                $statistics['additionTotal'] = $this->vote_library->number2chinese(count($statistics['additionStatistics']));
                $log = $this->vote_log_mdl->getLogByVoteIdAndOpenId($this->input->post('openId'),$vote['id']);
                $user = $this->wechat_user_mdl->getWechatUserByOpenId($this->input->post('openId'));
                $voteAccess = $user['vote_access'];
                $data = compact('vote','statistics','log','voteAccess');
                json_response(0,'success',$data);
                break;
        }
    }

    public function actionByAdmin(){
        $action = $this->input->post('action');
        if(!$action) throw new Exception('missing param action');

        switch ($action){
            case 'changeAccess':  //修改用户投票权限
                $openId = $this->input->post('openId');
                $access = $this->input->post('access');
                $data = array(
                    "vote_access"=>$access,
                    'openId' => $openId,
                );
                $this->load->model('wechat_user_mdl');
                $this->wechat_user_mdl->updateUser($openId,$data);

                json_response('0','success');
                break;
            case 'saveVote':
                //创建投票记录
//                p(sprintf("%.2f",100));exit;
//                p($this->input->post());exit;
                $this->load->model('vote_mdl');

                $vote = $this->vote_mdl->getVoteDetailsByDate($this->input->post('date'));

                if($vote)   json_response(1,$this->input->post('date').'日期的投票已经存在');

                $postData = $this->input->post();
                if(isset($postData['action']))  unset($postData['action']);
                $voteId = $this->vote_mdl->create($postData);

                //生成投票选项
                $this->load->library('vote_library');
                $optionArr = $this->vote_library->makeOptionArr($voteId,$this->input->post('option'),$this->input->post('addition_option'));
                $this->load->model('vote_option_mdl');
                $this->vote_option_mdl->addOption($optionArr);

                json_response(0, 'success');
                break;
            case 'saveLogDescription'://修改用户投票记录说明
                $this->load->model('vote_log_mdl');
                $data = array(
                    'description' => $this->input->post('description')
                );
                $this->vote_log_mdl->updateLog($this->input->post('logId'),$data);
                json_response('0','success');
                break;
            case 'cacnleOption'://取消菜单
                $this->load->model('vote_option_mdl');
                $data = array(
                    'is_del' => 1,
                );
                $option = $this->vote_option_mdl->updateOption($this->input->post('optionId'),$data);
                $this->db->query("UPDATE tbl_vote_log SET is_del = 1 WHERE option_id = ".$this->input->post('optionId'));

                //更新投票数量
                $this->load->model('vote_mdl');
                $this->load->model('vote_log_mdl');
                $updateVote = array(
                    'update_time' => date('Y-m-d H:i:s'),
                    'total' => count($this->vote_log_mdl->getLogByVoteId($option['voteId']))
                );
                $this->vote_mdl->updateVote($option['voteId'],$updateVote);
                json_response('0','success');
                break;
            case 'changeOption'://变更菜单
                $this->load->model('vote_option_mdl');
                $data = array(
                    'is_del' => 1,
                );
                $option = $this->vote_option_mdl->updateOption($this->input->post('optionId'),$data);

                //更新投票数量
                $this->load->model('vote_mdl');

                $this->load->model('vote_log_mdl');
                $updateVote = array(
                    'update_time' => date('Y-m-d H:i:s'),
                    'total' => count($this->vote_log_mdl->getLogByVoteId($option['voteId']))
                );
                $this->vote_mdl->updateVote($option['voteId'],$updateVote);


                $optionData[] = array(
                    'voteId' => $option['voteId'],
                    'option' => $this->input->post('option'),
                    'addition' => $this->input->post('addition'),
                    'num' => 0,
                    'addition_option' => $this->input->post('option')." ".$this->input->post('addition'),
                    'is_replace' => $option['id'],
                    'create_time' => date('Y-m-d H:i:s')
                );
                $this->vote_option_mdl->addOption($optionData);
                json_response('0','success');
                break;
            case 'getUserLog'://获取用户投票
                $this->load->model('vote_mdl');
                $userLog = $this->vote_mdl->getVoteLog2($this->input->post('voteId'));
                $array = array(
                    'code' => 0,
                    'msg' => 'success',
                    'data'=>$userLog
                );
                exit(json_encode($array));
                break;
            case 'saveUserLog'://后台代用户投票
//                p($this->input->post());
                $this->load->model('vote_log_mdl');
                $this->load->model('vote_option_mdl');
                $this->load->model('vote_mdl');
                $voteId = $this->input->post('voteId');
                $optionId = $this->input->post('optionId');
                foreach ($this->input->post('openId') as $v){
                    $log = $this->vote_log_mdl->getUserLogById($v,$voteId);
                    if(!$log){
                        //第一次投票创建记录
                        $this->vote_log_mdl->addLog($v,$this->input->post('voteId'),$this->input->post('optionId'),1);
                        //累加选项统计栏
                        $this->vote_option_mdl->optionNumAddNum($this->input->post('optionId'));

                        $updateVote = array(
                            'update_time' => date('Y-m-d H:i:s'),
                            'total' => count($this->vote_log_mdl->getLogByVoteId($voteId))
                        );
                        $this->vote_mdl->updateVote($voteId,$updateVote);
                    }
                    else{
                        //更改投票
                        if($log['option_id'] == $optionId && $log['is_del'] == 0){
                            continue;
                        }

                        if($log['is_del'] == 0){
                            //旧记录-1
                            $this->vote_option_mdl->optionNumRedict($log['option_id']);
                        }

                        $logData = array(
                            'update_time' => date('Y-m-d H:i:s'),
                            'scene'=>'1',
                            'is_del'=>'0',
                            'option_id'=>$optionId,
                            'old_option_id'=>$log['option_id'],
                            'description'=>null,
                        );
                        $this->vote_log_mdl->updateLog($log['id'],$logData);
//                        $this->vote_log_mdl->updateLogOption($log['id'],$this->input->post('optionId'),1);

                        //新纪录+1
                        $this->vote_option_mdl->optionNumAddNum($optionId);
                        $updateVote = array(
                            'total' => count($this->vote_log_mdl->getLogByVoteId($voteId)),
                            'update_time' => date('Y-m-d H:i:s'),
                        );
                        $this->vote_mdl->updateVote($voteId,$updateVote);
                    }
                }
                json_response(0,"success");
                break;
            case 'saveUserMsg':
                $this->load->model('wechat_user_mdl');
                $user = $this->wechat_user_mdl->getWechatUserByOpenId($this->input->post('openId'));
                $user['remark'] = $this->input->post('remark');
                $user['position'] = $this->input->post('position');
                $user['department'] = $this->input->post('department');
                $user['telephone'] = $this->input->post('telephone');
                $this->wechat_user_mdl->updateUser($user['openId'],$user);
                json_response(0,"success");
                break;
            case 'removeLog':
                $this->load->model('vote_log_mdl');
                $this->load->model('vote_option_mdl');
                $this->load->model('vote_mdl');
                $data = array(
                    'is_del' => 1,
                );
                $log = $this->vote_log_mdl->updateLog($this->input->post('logId'),$data);
                $this->vote_option_mdl->optionNumRedict($log['option_id']);

                $updateVote = array(
                    'total' => count($this->vote_log_mdl->getLogByVoteId($log['vote_id'])),
                    'update_time' => date('Y-m-d H:i:s'),
                );
                $this->vote_mdl->updateVote($log['vote_id'],$updateVote);
                json_response(0,'success');
                break;
        }
    }


    /**
     * @param $voteId
     */
    public function exportVoteDetails($voteId,$date)
    {
        $this->load->model('vote_mdl');
        $this->load->model('vote_option_mdl');
//        $this->load->model();
        $statistics = $this->vote_mdl->getVoteStatistics($voteId);

        $headData = array(array(
            'value'=>'导出日期时间： '.date('Y-m-d H:i:s'),
            'colspan'=>3,
        ));
        $emptyArray = [];
        $title = ["出餐日期"];
        $data = [$date];
        $additionTotal = $optionTotal = 0;
        foreach ($statistics['optionStatistics'] as $v){
            $title[] = $v['option'];
            $data[] = $v['total'];
            $optionTotal+=$v['total'];
        }
        $title[] = '小计';
        $data[] = $optionTotal;
        foreach ($statistics['additionStatistics'] as $v){
            $title[] = $v['addition'];
            $data[] = $v['total'];
            $additionTotal+=$v['total'];
        }
        $title[] = '小计';
        $data[] = $additionTotal;
        $query = $this->db->query('SELECT
                                        wu.nickname,
                                        IF(vl.old_option_id >0,\'更换\',\'取消\') AS type,
                                        vl.update_time,
                                        vo1.addition_option as afterOption,
                                        IFNULL(vo2.addition_option,\'-\') as beforeOption
                                    FROM
                                        tbl_vote_log AS vl
                                        LEFT JOIN tbl_wechat_user AS wu ON wu.openId = vl.openId
                                        LEFT JOIN tbl_vote_option AS vo1 ON vo1.id = vl.option_id
                                        LEFT JOIN tbl_vote_option AS vo2 ON vo2.id = vl.old_option_id
                                    WHERE
                                        (vl.is_del = 1
                                        OR vl.old_option_id != 0)
                                        AND vl.vote_id = '.$voteId);

        $option = $query->result_array();
        $changeAndCancleHead = array(array('value'=>'变更取消', 'colspan'=>7,'align'=>'center'));
        $changeAndCancleTitle = array('出餐日期','用户','类型',array('value'=>'操作时间','colspan'=>2),'变更前','变更后');
        $total =  $cancleTotal = $changeTotal = 0;

        $daliyData = compact('headData','emptyArray','title','data','changeAndCancleHead','changeAndCancleTitle');
        foreach ($option as $k){
            $changeAndCancleData = array(
                $date,
                $k['nickname'],
                $k['type'],
                array("value"=>$k['update_time'],'colspan'=>2),
                $k['beforeOption'],
                $k['afterOption']
            );

            $daliyData = array_merge($daliyData,array($changeAndCancleData));

            $total++;
            if($k['type'] == '取消'){
                $cancleTotal++;
            }
            if($k['type'] == '更换'){
                $changeTotal++;
            }
        }


        $daliyData[] = array(
            '总计：',
            array('value'=>$total,'colspan'=>6)
        );

        $daliyData[] = array(
            '取消总计：',
            array('value'=>$cancleTotal,'colspan'=>6)
        );

        $daliyData[] = array(
            '变更总计：',
            array('value'=>$changeTotal,'colspan'=>6)
        );

        $this->load->library('excel_library');


        $this->excel_library->createSheet('worksheet1');
        $this->excel_library->setSheetData($daliyData);


        $this->excel_library->export('玖晔午餐报表');
    }

    public function export(){
        load_view('vote/export');
    }

    public function exportExcel(){
        $startTime = $this->input->post('startTime');
        $endTime = $this->input->post('endTime');
        $query = $this->db->query("SELECT vo.option,vo.addition,vo.num,v.date,v.single_cost
                                   FROM tbl_vote_option AS vo 
                                   LEFT JOIN tbl_vote AS v ON v.id = vo.voteId 
                                   WHERE v.date BETWEEN '$startTime' AND DATE_ADD('$endTime',INTERVAL 1 DAY) 
                                   ORDER BY v.date ASC
                                 ");
        $result = $query->result_array();

        $this->load->library('vote_library');

        $dailyData = $this->vote_library->formaReportDailyData($result);
        $monthlyData = $this->vote_library->formaReportMonthlyData($result);

        $this->load->library('excel_library');
        $this->excel_library->createSheet('日报');
        $this->excel_library->setDailySheetData($dailyData);
        $this->excel_library->createSheet('月报');
        $this->excel_library->setMonthlySheetData($monthlyData);
        $this->excel_library->export('玖晔午餐报表');
//        p($monthlyData);

    }


}