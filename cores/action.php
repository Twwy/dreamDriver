<?php

// +----------------------------------------------------------------------+
// | Warning: Design By Everyone's Dreams                                 |
// +----------------------------------------------------------------------+
// | FileName: action.php                                                 |
// +----------------------------------------------------------------------+
// | Version: 1.0                                                         |
// +----------------------------------------------------------------------+
// | Author: Twwy                                                         |
// | Email: twwwwy@gmail.com                                              |
// +----------------------------------------------------------------------+

router('action\/user\.login',function(){require('./actions/user/login.php');});
router('action\/user\.exit',function(){require('./actions/user/exit.php');});
router('action\/user\.reg',function(){require('./actions/user/reg.php');});
router('action\/user\.passwd',function(){require('./actions/user/passwd.php');});

router('action\/job\.add',function(){require('./actions/job/add.php');});
router('action\/job\.list',function(){require('./actions/job/list.php');});

router('action\/problem\.add',function(){require('./actions/problem/add.php');});

router('action\/solution\.add',function(){require('./actions/solution/add.php');});
router('action\/solution\.modify',function(){require('./actions/solution/modify.php');});
router('action\/solution\.remove',function(){require('./actions/solution/remove.php');});


?>