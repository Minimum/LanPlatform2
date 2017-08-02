<?php
    include("../../data.php");
    include("../../modules/accounts.php");

    header('Content-type: application/json');
    
    $dataConn = sqlConnect();
    
    $accountManager = new AccountManager($dataConn);
    
    if(isset($_POST["api_id"]) && isset($_POST["api_key"]))
    {
        if($accountManager->checkApiSession($_POST["api_id"], $_POST["api_key"]))
        {
            // Load admin info
            $accountManager->loadAdmin($accountManager->localAccount);
        
            if($accountManager->checkAdmin($accountManager->localAccount, "setaccount"))
            {
                
            }
        }
    }
?>