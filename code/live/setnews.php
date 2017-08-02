{
<?php
    include("../data.php");
    include("../modules/accounts.php");
    include("../modules/news.php");
    
    header('Content-type: application/json');
    
    $newsPost = (isset($_POST["news"])) ? $_POST["news"] : (isset($_GET["news"]) ? $_GET["news"] : 0);
    
    $dataConn = sqlConnect();
    
    $accountManager = new AccountManager($dataConn);
    $newsManager = new NewsManager($dataConn);
    
    $accountManager->checkAddress();
    
    if($accountManager->localAccount != null)
    {
        if($newsManager->setNews($newsPost, $accountManager->localAccount))
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