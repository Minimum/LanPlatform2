<?php
    include("../data.php");
    include("../modules/accounts.php");
    include("../modules/chat.php");

    $dataConn = sqlConnect();
    
    $account = accountCheckSession($dataConn, $_COOKIE["userid"], $_COOKIE["usersession"]);
    
    chatReceiveMessage($dataConn, $account, $_POST["room"], $_POST["message"]);
?>