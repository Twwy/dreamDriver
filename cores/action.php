<?php

///////////////////////////////
//Design By Everyone's Dreams//
///////////////////////////////

router('action\/user\.reg',function(){require('./actions/user/reg.php');});
router('action\/user\.login',function(){require('./actions/user/login.php');});
router('action\/user\.passwd',function(){require('./actions/user/passwd.php');});

router('action\/job\.add',function(){require('./actions/job/add.php');});
router('action\/job\.problem\.add',function(){require('./actions/job/problem-add.php');});



?>