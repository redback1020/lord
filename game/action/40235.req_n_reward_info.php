<?php
$cmd = 4;
$code = 236;
$data = array(
    'errno' => 0,
    'error' => '',
    'is_finish' => 0,
    'reward_list' => array(),  //n局送乐券的信息列表
);
$uid = $user['uid'];
$data['reward_list'] = $this->model->getNRewardList($uid,$user['vercode']);
$res = sendToFd($fd, $cmd, $code, $data);


end:{}
