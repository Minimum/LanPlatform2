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
        $lanManager->checkinAccount($accountManager->localAccount, $lanAccount);
    }
?>
}