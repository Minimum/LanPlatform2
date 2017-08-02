<?php
    include("../data.php");
    include("../modules/accounts.php");

    $dataConn = sqlConnect();
    
    if(!mysqli_connect_errno())
    {  
        if(isset($_COOKIE["sessionid"]) && isset($_COOKIE["userid"]))
        {
            $user = accountCheckLogin($dataConn, $_COOKIE["userid"], $_COOKIE["sessionid"]);
        }
    }
    else
    {
    
    }
    
?>