{
<?php
    include("../data.php");
    include("../modules/accounts.php");
    include("../modules/games.php");
    include("../modules/news.php");
    include("../modules/lanaccounts.php");
    
    header('Content-type: application/json');
    
    $dataConn = sqlConnect();
    
    $accountManager = new AccountManager($dataConn);
    $gameManager = new GameManager($dataConn);
    $guestManager = new LanAccountManager($dataConn);
    $newsManager = new NewsManager($dataConn);
    
    $accountManager->checkAddress();
    
    // Account
    print $accountManager->printClientInfo();
    
    print ",";
    
    // Lan Accounts
    print $guestManager->printAccountInfo();
    
    print ",";
    
    // Games
    print $gameManager->printGames($accountManager->localAccount);
    
    print ",";
    
    // News
    print $newsManager->printClientInit();
    
    print ",";
    
    // Messages
    $result = $dataConn->query("SELECT id FROM lans_webevents ORDER BY id DESC LIMIT 1;");
    
    $lastMsg = 0;
    
    if($result->num_rows > 0)
    {
        $msg = $result->fetch_array(MYSQLI_NUM);
        
        $lastMsg = $msg[0];
    }
    
    $result->close();
    
    print "\n\"msg\": \"".$lastMsg."\"\n";
    
?>
}