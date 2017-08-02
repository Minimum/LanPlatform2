<?php
    class NewsManager
    {
        protected $conn = null;
    
        function __construct()
        {
            $a = func_get_args();
            $i = func_num_args();
            
            if (method_exists($this,$f='__construct'.$i))
            {
                call_user_func_array(array($this,$f),$a);
            } 
        }
        
        function __construct1($sqlConn)
        {
            $this->conn = $sqlConn;
        }
        
        function setNews($newsId, $account)
        {
            $success = false;
        
            if($account->hasAccess("webnews"))
            {
                $this->conn->query("UPDATE lans_websettings SET data=\"".$this->conn->real_escape_string($newsId)."\" WHERE name = \"homeNews\";");
                
                $this->conn->query("INSERT INTO lans_webevents (event, data) VALUES (\"newsChange\", \"".$this->conn->real_escape_string($newsId)."\");");
                
                $success = true;
            }
            
            return $success;
        }
        
        function printClientInit()
        {
            $output = "\n\"news\": {";
            
            $result = $this->conn->query("SELECT data FROM lans_websettings WHERE name = \"homeNews\";");
            
            if($result->num_rows > 0)
            {
                $data = $result->fetch_array(MYSQLI_NUM);
                
                $output .= "\n\"homeNews\": \"".$data[0]."\"";
            }
            else
            {
                $output .= "\n\"homeNews\": \"0\"";
            }
            
            $result->close();
            
            $output .= "\n}";
            
            return $output;
        }
        
        function printClientUpdate($account, $lastUpdate)
        {
            $output = "\n\"news\": {";
            $newTime = $lastUpdate;
            
            // News Post
            
            // Guest Accounts
    
            $result = $this->conn->query("SELECT a.id,b.display_name FROM snet_accounts a, lans_players b WHERE a.id = b.id AND a.id = ANY (SELECT user FROM lans_accountlogs WHERE id > \"".$this->conn->real_escape_string($lastUpdate)."\") LIMIT 50;");
    
            $accountOutput = "\"accounts\": [";
        
            while($account = $result->fetch_array(MYSQLI_NUM))
            {
                if($newTime != $lastUpdate)
                {
                    $accountOutput .= ",";
                }
            
                $accountOutput .= "\n{ \"id\": \"".$account[0]."\", \"name\": \"".$account[1]."\" }";
            
                $newTime = ($newTime < $account[0]) ? $account[0] : $newTime;
            }
        
            $accountOutput .= "\n]";
        
            $result->close();
        
            // Output Properties
            $output .= "\n\"time\": \"".$newTime."\"";     // Timing
            $output .= ",\n".$accountOutput;               // Accounts
        
            $output .= "\n}";
        
            return $output;
        }
    }

    function newsUpdateClient($conn, $account, $lastUpdate)
    {
        $output = "";
        
        if($account != null)
        {
            
        }
        else
        {
        
        }
        
        return $output;
    }
?>