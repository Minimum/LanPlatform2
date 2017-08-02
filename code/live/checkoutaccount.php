{
<?php
    include("../data.php");
    include("../modules/accounts.php");
    include("../modules/lanaccounts.php");
    
    header('Content-type: application/json');
    
    $lanAccount = (isset($_POST["acct"])) ? $_POST["acct"] : 0;
    
    $dataConn = sqlConnect();
    
    $accountManager = new AccountManager($dataConn);
    $lanManager = new LanAccountManager($dataConn);
    
    $accountManager->checkAddress();
    
    if($accountManager->localAccount != null)
    {
        if($lanManager->checkoutAccount($accountManager->localAccount, $lanAccount))
        {
            print "\"success\": \"1\"\n";
        }
        else
        {
            print "\"success\": \"0\"\n";
        }
    }
    else
    {
        print "\"success\": \"0\"\n";
    }
?>
}