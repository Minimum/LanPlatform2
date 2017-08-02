{
<?php
    include("../data.php");
    include("../modules/accounts.php");
    include("../modules/doge.php");

    header('Content-type: application/json');
    
    $dataConn = sqlConnect();
    
    $accountManager = new AccountManager($dataConn);
    $dogeManager = new DogeManager();
    
    $code = "none";
    $cookieMode = false;
    
    if(isset($_POST["code"]))
    {
        $code = $_POST["code"];
    }
    else if(isset($_COOKIE["dogecode"]))
    {
        $cookieMode = true;
        $code = $_COOKIE["dogecode"];
    }
    
    print "\"success\": ";
    
    if($accountManager->checkAddress())
    {
        // Log submission
        if(!$cookieMode)
        {
            $dataConn->query("INSERT INTO lans_dogelog (user, phrase) VALUES (\"".$dataConn->real_escape_string($accountManager->localAccount->id)."\", \"".$dataConn->real_escape_string($code)."\");");
        }
    
        if(strtolower(trim($code)) == "chair")
        {
            setcookie("dogecode", "chair");
        
            dogeInit($dogeManager);
        
            print "\"1\",".$dogeManager->printInfo();
        }
        else
        {
            print "\"0\"";
        }
    }
    else
    {
        print "\"2\"";
    }
    
    
?>
}