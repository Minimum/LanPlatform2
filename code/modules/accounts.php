<?php
    class AccountManager
    {
        const COOKIE_USERID = "userid";
        const COOKIE_USERSESSION = "usersession";
        
        public $localAccount = null;
        public $localUserId = "-1";
        public $localUserSession = "-1";
        
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
            
            $this->localUserId = isset($_COOKIE[self::COOKIE_USERID]) ? $_COOKIE[self::COOKIE_USERID] : "-1";
            $this->localUserSession = isset($_COOKIE[self::COOKIE_USERSESSION]) ? $_COOKIE[self::COOKIE_USERSESSION] : "-1";
        }
        
        public function reportOnline($account)
        {
            $this->conn->query("UPDATE lans_players SET last_active = UNIX_TIMESTAMP() WHERE id = \"".$this->conn->real_escape_string($account->id)."\";");
            
            return;
        }
        
        public function getOnlineList($minutes)
        {
            $seconds = 60 * $minutes;
            
            
        }
        
        public function loginAbuseCheck()
        {
            $abuse = false;
        
            $result = $this->conn->query("SELECT count(*) FROM lans_auth_log WHERE address=\"".$this->conn->real_escape_string($_SERVER['REMOTE_ADDR'])."\" AND success = \"0\" AND time > (UNIX_TIMESTAMP() - 900);");
            
            if($result->num_rows > 0)
            {
                $row = $result->fetch_array(MYSQLI_NUM);
                
                $abuse = $row[0] >= 5;
            }
            
            $result->close();
            
            return $abuse;
        }
        
        public function loginRecordAttempt($success, $data)
        {
            $successStorage = ($success) ? "1" : "0";
        
            $this->conn->query("INSERT INTO lans_auth_log (time,address,success,data) VALUES (UNIX_TIMESTAMP(),\"".$this->conn->real_escape_string($_SERVER['REMOTE_ADDR'])."\",\"".$this->conn->real_escape_string($successStorage)."\",\"".$this->conn->real_escape_string($data)."\");");
            
            return;
        }
        
        public function loadAdmin($account)
        {
            $result = $this->conn->query("SELECT root, flags FROM snet_admins WHERE id=\"".$this->conn->real_escape_string($account->id)."\" AND server=0;");
            
            if($result->num_rows > 0)
            {
                $row = $result->fetch_array(MYSQLI_NUM);
                
                $account->rootAccess = $row[0] == "1";
                $account->adminFlags = $row[1];
            }
            
            $result->close();
            
            return;
        }
        
        public function checkAdmin($account, $flag)
        {
            return ($account->type & 1 == 1 || $account->rootAccess || substr_count($account->adminFlags, "%".$flag."%") > 0);
        }
        
        public function checkAddress()
        {
            $result = $this->conn->query("SELECT a.id,a.username,a.type,a.universe,b.display_name FROM snet_accounts a JOIN lans_players b ON a.id = b.id WHERE a.id = (SELECT snid FROM snet_accounts_auths WHERE type = \"ip\" AND data = \"".$this->conn->real_escape_string($_SERVER['REMOTE_ADDR'])."\" LIMIT 1);");
            
            if($result->num_rows > 0)
            {
                $this->localAccount = $this->accountLoadMinimum($result);
            }
        
            $result->close();
            
            return ($this->localAccount != null);
        }
        
        public function checkSession()
        {
            if($this->loginAbuseCheck())
            {
                return false;
            }
        
            // TODO: complete this
        
            $account = null;
        
            $result = $this->conn->query("SELECT a.username,a.type,a.universe,b.display_name FROM snet_accounts a JOIN lans_players b ON a.id = b.id WHERE a.id = (SELECT snid FROM snet_accounts_auths WHERE type = \"session\" AND snid = \"".$this->conn->real_escape_string($this->localUserId)."\" AND data = \"".$this->conn->real_escape_string($this->localUserSession)."\" LIMIT 1);");
        
            if($result->num_rows > 0)
            {
                $this->localAccount = $this->accountLoadMinimum($result);
            }
            else
            {
                $this->loginRecordAttempt(false, "session");
            }
        
            $result->close();
            
            return;
        }
        
        public function checkUsername($user, $pass)
        {
            // Check if we have SHA512 support
            if(CRYPT_SHA512 == 1)
            {
                if($this->loginAbuseCheck())
                {
                    return false;
                }
            
                // Search database for username
                $result = $this->conn->query("SELECT a.id,a.username,a.type,a.universe,b.display_name,a.password,a.salt FROM snet_accounts a JOIN lans_players b ON a.id = b.id WHERE a.username = \"".$this->conn->real_escape_string($id)."\";");
            
                // Check if username exists
                if($result->num_rows > 0)
                {
                    // Obtain database info
                    $row = $result->fetch_array(MYSQLI_NUM);
            
                    $id = $row[0];
                    $realPass = $row[5];
                    $salt = $row[6];
                
                    // Check if password is correct
                    if(crypt($password, "$6$rounds=5000$".$salt) == $realPass)
                    {
                        $this->localAccount = $this->accountLoadMinimum($result);
                    }
                }
                else
                {
                    $this->loginRecordAttempt(false, "user");
                }
            }
            
            return ($this->localAccount != null);
        }
        
        public function checkApiSession($id, $key)
        {
            if($this->loginAbuseCheck())
            {
                return false;
            }
            
            $result = $this->conn->query("SELECT a.id,a.username,a.type,a.universe,b.display_name FROM snet_accounts a JOIN lans_players b ON a.id = b.id WHERE a.id = (SELECT snid FROM snet_accounts_auths WHERE type = \"api_key\" AND data = \"".$this->conn->real_escape_string($key)."\" AND snid = ANY (SELECT snid FROM snet_accounts_auths WHERE type = \"api_ip\" AND data = \"".$this->conn->real_escape_string($_SERVER['REMOTE_ADDR'])."\") LIMIT 1) LIMIT 1;");
        
            if($result->num_rows > 0)
            {
                $this->localAccount = $this->accountLoadMinimum($result);
            }
            else
            {
                $this->loginRecordAttempt(false, "api");
            }
            
            $result->close();
            
            return ($this->localAccount != null);
        }
       
        protected function accountLoadMinimum($result)
        {
            $account = new SnetAccount;
        
            $row = $result->fetch_array(MYSQLI_NUM);
        
            $account->id = $row[0];
            $account->username = $row[1];
            $account->type = $row[2];
            $account->universe = $row[3];
            $account->displayName = $row[4];
        
            return $account;
        }
        
        public function loginUsername($username, $password)
        {
            $success = false;
    
            // Check if we have SHA512 support
            if(CRYPT_SHA512 == 1)
            {
                if($this->loginAbuseCheck())
                {
                    return false;
                }
            
                // Search database for username
                $result = $this->conn->query("SELECT id,password,salt FROM snet_accounts WHERE username = \"".$this->conn->real_escape_string($username)."\";");
            
                // Check if username exists
                if($result->num_rows > 0)
                {
                    // Obtain database info
                    $row = $result->fetch_array(MYSQLI_NUM);
            
                    $id = $row[0];
                    $realPass = $row[1];
                    $salt = $row[2];
                
                    // Check if password is correct
                    if(crypt($password, "$6$rounds=5000$".$salt) == $realPass)
                    {
                        // Generate new session id
                        $seed = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXY0123456789";
                        $session = "";
                    
                        for($x = 0; $x < 47; $x++)
                        {
                            $session .= $seed[rand(0,61)];
                        }
                    
                        // Replace old session id with new one
                        $this->conn->query("DELETE FROM snet_accounts_auths WHERE snid = \"".$this->conn->real_escape_string($id)."\" AND type = \"session\";");
                    
                        $this->conn->query("INSERT INTO snet_accounts_auths SET (snid,type,data) VALUES (\"".$this->conn->real_escape_string($id)."\", \"session\", \"".$this->conn->real_escape_string($session)."\");");
                
                        // Give client new session info
                        setcookie("userid", $id);
                        setcookie("sessionid", $session);
                    
                        $success = true;
                    }
                }
                
                if($success)
                {
                    $this->loginRecordAttempt(true, "user: ".$username);
                }
                else
                {
                    $this->loginRecordAttempt(false, "user: ".$username);
                }
        
                // Free result resources
                $result->close();
            }
        
            return $success;
        }
        
        public function loginTicket($ticket)
        {
            if($this->loginAbuseCheck())
            {
                return false;
            }
        
            $result = $this->conn->query("SELECT a.id,a.username,a.type,a.universe,b.display_name FROM snet_accounts a JOIN lans_players b ON a.id = b.id WHERE a.id = (SELECT snid FROM snet_accounts_auths WHERE type = \"ticket\" AND data = \"".$this->conn->real_escape_string($ticket)."\" LIMIT 1);");
            
            if($result->num_rows > 0)
            {
                $this->localAccount = $this->accountLoadMinimum($result);
                
                // Add new address auth
                $this->conn->query("INSERT INTO snet_accounts_auths (snid,type,data) VALUES (\"".$this->conn->real_escape_string($this->localAccount->id)."\", \"ip\", \"".$this->conn->real_escape_string($_SERVER['REMOTE_ADDR'])."\");");
                
                // Remove one time ticket
                $this->conn->query("DELETE FROM snet_accounts_auths WHERE snid = \"".$this->conn->real_escape_string($this->localAccount->id)."\" AND type = \"ticket\";");
                
                $this->loginRecordAttempt(true, "ticket: ID#".$this->localAccount->id);
            }
            else
            {
                $this->loginRecordAttempt(false, "ticket");
            }
            
            $result->close();
            
            return ($this->localAccount != null);
        }
        
        public function printClientInfo()
        {
            // Start Account Section
            $output = "\n\"acct\": {";
            
            // Local Account
            if($this->localAccount != null)
            {
                $output .= "\n\"local\": ".$this->localAccount->toString();
            }
            
            // Active Accounts
            /*$result = $this->conn->query("SELECT id,display_name FROM lans_players WHERE last_active > (UNIX_TIMESTAMP() - 604800) LIMIT 500;");
    
            $accountOutput = "\"accounts\": {";
            
            $notFirstEntry = false;
        
            while($account = $result->fetch_array(MYSQLI_NUM))
            {
                if($notFirstEntry)
                {
                    $accountOutput .= ",";
                }
                else
                {
                    $notFirstEntry = true;
                }
            
                $accountOutput .= "\n\"".$account[0]."\": { \"id\": \"".$account[0]."\", \"name\": \"".$account[1]."\" }";
            }
        
            $accountOutput .= "\n}";
        
            $result->close();
            
            $output .= ",\n".$accountOutput;*/
            
            // End Account Section
            $output .= "\n}";
            
            return $output;
        }
        
        public function printClientUpdate($clientAccount, $lastUpdate)
        {
            $output = "\n\"acct\": {";
            $newTime = $lastUpdate;
    
            $result = $this->conn->query("SELECT a.id,b.display_name FROM snet_accounts a, lans_players b WHERE a.id = b.id AND a.id = ANY (SELECT user FROM lans_accountlogs WHERE id > \"".$this->conn->real_escape_string($lastUpdate)."\") LIMIT 50;");
            
            //$result = $this->conn->query("SELECT id,display_name FROM lans_players WHERE last_active > (UNIX_TIMESTAMP() - 604800) LIMIT 500;");
    
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
        
        public function changePassword($account, $password)
        {
            $seed = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXY0123456789";
            $salt = "";
        
            $throwOff = $account->id % 47;
        
            for($x = 0; $x < $throwOff; $x++)
            {
                rand(11, 97);
            }
        
            for($x = 0; $x < 7; $x++)
            {
                $salt .= $seed[rand(0,61)];
            }
    
            $encryptedPass = crypt($password, "$6$rounds=5000$".$salt);
        
            $account->password = $encryptedPass;
            $account->passwordSalt = $salt;
        
            return;
        }
        
        public function createApiKey($account)
        {
            $seed = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXY0123456789";
            $key = "";
            
            $throwOff = $account->id % 107;
        
            for($x = 0; $x < $throwOff; $x++)
            {
                rand(11, 97);
            }
        
            for($x = 0; $x < 64; $x++)
            {
                $key .= $seed[rand(0,61)];
            }
            
            for($x = 63; $x >= 0; $x--)
            {
                $symbol = $key[$x];
                $swapLocation = rand(0,63);
                
                $key[$x] = $key[$swapLocation];
                $key[$swapLocation] = $symbol;
            }
            
            $this->conn->query("INSERT INTO snet_accounts_auths (snid,type,data) VALUES (\"".$this->conn->real_escape_string($this->localAccount->id)."\",\"api_key\",\"".$this->conn->real_escape_string($key)."\");");
            
            return $key;
        }
        
        public function createApiAddress($account, $address)
        {
            $result = $this->conn->query("SELECT count(*) FROM snet_accounts_auths WHERE snid = \"".$this->conn->real_escape_string($this->localAccount->id)."\" AND type = \"api_ip\" AND data = \"".$this->conn->real_escape_string($address)."\";");
            
            if($result->num_rows > 0)
            {
                $row = $result->fetch_array(MYSQLI_NUM);
                
                if($row[0] == "0")
                {
                    
                }
            }
        }
    }
    
    
    
    function accountLogout($conn, $user, $session)
    {
    
    }
    
    function accountLoadFull($id)
    {
    
    }
    
    class SnetAccount
    {
        public $id = -1;
        public $username = "NONE";
        public $password = "";
        public $passwordSalt = "";
        public $type = -1;
        public $universe = -1;
        public $displayName = "NONE";
        public $rootAccess = false;
        public $adminFlags = "";
        
        public function hasAccess($flag)
        {
            return ($this->type == 1 || strpos($this->adminFlags, "%".$flag."%") === true);
        }
        
        public function toString()
        {
            // Start Object
            $output = "{\n";
            
            // Id
            $output .= "\"id\": \"".$this->id."\",\n";
            
            // Username
            $output .= "\"user\": \"".$this->username."\",\n";
            
            // Type
            $output .= "\"type\": \"".$this->type."\",\n";
            
            // Universe
            $output .= "\"universe\": \"".$this->universe."\",\n";
            
            // Display Name
            $output .= "\"displayname\": \"".$this->displayName."\"\n";
            
            // End Object
            $output .= "\n}";
            
            return $output;
        }
        
        
    }
?>