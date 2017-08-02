{
<?php
    include("../data.php");
    include("../modules/accounts.php");
    include("../modules/chat.php");
    include("../modules/jukebox.php");
    include("../modules/news.php");
    
    header('Content-type: application/json');
    
    $accountTime = (isset($_GET["acct"])) ? $_GET["acct"] : 0;
    $newsTime = (isset($_GET["news"])) ? $_GET["news"] : 0;
    $chatTime = (isset($_GET["chat"])) ? $_GET["chat"] : 0;
    $jukeboxTime = (isset($_GET["jukebox"])) ? $_GET["jukebox"] : 0;
    $messageTime = (isset($_GET["msg"])) ? $_GET["msg"] : 0;
    
    $dataConn = sqlConnect();
    
    $result = $dataConn->query("SELECT id,event,data FROM lans_webevents WHERE id > \"".$dataConn->real_escape_string($messageTime)."\" ORDER BY id ASC LIMIT 50;");
    
    $lastMsg = 0;
    
    print "\"msgs\": [";
    
    while($msg = $result->fetch_array(MYSQLI_NUM))
    {
        if($lastMsg != 0)
        {
            print ",";
        }
    
        $lastMsg = $msg[0];
        
        print "\n{ \"name\": ".json_encode($msg[1]).", \"data\": ".json_encode($msg[2])." }";
    }
    
    $result->close();
    
    if($lastMsg == 0)
    {
        $result = $dataConn->query("SELECT id FROM lans_webevents ORDER BY id DESC LIMIT 1;");
        
        if($result->num_rows > 0)
        {
            $msg = $result->fetch_array(MYSQLI_NUM);
            
            $lastMsg = $msg[0];
        }
        
        $result->close();
    }
    
    print "\n],\n\"last\": \"".$lastMsg."\"\n";
    
    //$accountManager = new AccountManager($dataConn);
    //$newsManager = new NewsManager($dataConn);
    
    //$accountManager->checkAddress();
    
    
    
    // Accounts
    //print $accountManager->printClientUpdate($accountManager->localAccount, $accountTime);
    
    // News
    // print $newsManager->printClientUpdate($accountManager->localAccount, $newsTime);
    
    // Chat
    // print chatUpdateClient($dataConn, $account, $chatTime);
    
    // Jukebox
    // print jukeboxUpdateClient($dataConn, $account, $jukeboxTime);
?>
}