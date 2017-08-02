{
<?php
    include("../data.php");
    include("../modules/accounts.php");

    header('Content-type: application/json');
    
    $dataConn = sqlConnect();
    
    $accountManager = new AccountManager($dataConn);
    
    $loginSuccess = false;
    
    if(isset($_POST["type"]))
    {
        $loginType = strtolower($_POST["type"]);    
    
        if($loginType == "ticket")
        {
            if(isset($_POST["ticket"]))
            {
                if($accountManager->loginTicket($_POST["ticket"]))
                {
                    $loginSuccess = true;
                
                    print "\n\"success\": 1,\n\"acct\": ".$accountManager->localAccount->toString();
                }
            }
        }
    }
    
    if(!$loginSuccess)
    {
        print "\n\"success\": 0";
    }
    
?>
}