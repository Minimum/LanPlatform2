<?php
    class LanAccountManager
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
        
        function printAccountInfo()
        {
            // Start Lan Account Section
            $output = "\n\"lana\": {";
            
            $result = $this->conn->query("SELECT a.id,a.name,a.password,a.steamid,a.checkout,b.display_name FROM lans_guests a LEFT JOIN (lans_players b) ON (a.checkout = b.id);");
            
            $accounts = array();
            
            while($data = $result->fetch_array(MYSQLI_NUM))
            {
                $account = new LanAccount;
                
                $account->id = $data[0];
                $account->name = $data[1];
                $account->password = $data[2];
                $account->steamid = $data[3];
                $account->checkout = $data[4];
                $account->checkoutName = ($data[5] == null) ? "" : $data[5];
                
                $accounts[$data[0]] = $account;
            }
            
            $result->close();
            
            $result = $this->conn->query("SELECT a.account,b.id FROM lans_guests_games a, lans_library b WHERE a.game = b.id ORDER BY b.id ASC;");
            
            while($data = $result->fetch_array(MYSQLI_NUM))
            {
                if(isset($accounts[$data[0]]))
                {
                    $accounts[$data[0]]->games[$accounts[$data[0]]->numGames] = $data[1];
                    
                    $accounts[$data[0]]->numGames++;
                }
            }
            
            $result->close();
            
            // Start Account Array
            $output .= "\n\"accounts\": [";
            
            $numAccounts = 0;
            
            foreach($accounts as $account)
            {
                if($numAccounts != 0)
                {
                    $output .= ",";
                }
                
                $output .= "\n".$account->toString();
                
                $numAccounts++;
            }
            
            // End Account Array
            $output .= "\n]";
            
            // End Lan Account Section
            $output .= "\n}";
            
            return $output;
        }
        
        function checkoutAccount($user, $accountId)
        {
            $success = false;
            
            // Check if user has already checked out an account
            $result = $this->conn->query("SELECT id FROM lans_guests WHERE checkout = \"".$this->conn->real_escape_string($user->id)."\" LIMIT 1;");
            
            if($result->num_rows < 1)
            {
                $result->close();
            
                // Check if account has already been checked out
                $result = $this->conn->query("SELECT id FROM lans_guests WHERE id = \"".$this->conn->real_escape_string($accountId)."\" AND checkout = 0 LIMIT 1;");
                
                if($result->num_rows > 0)
                {
                    // Checkout account
                    $this->conn->query("UPDATE lans_guests SET checkout=\"".$this->conn->real_escape_string($user->id)."\" WHERE id = \"".$this->conn->real_escape_string($accountId)."\";");
                    
                    $displayName = str_replace("|", "", $user->displayName);
                    
                    // Create message
                    $this->conn->query("INSERT INTO lans_webevents (event,data) VALUES (\"lanAcctCheckout\", \"".$this->conn->real_escape_string($accountId)."|".$this->conn->real_escape_string($user->id)."|".$this->conn->real_escape_string($displayName)."\");");
                    
                    $success = true;
                }
            }
            
            $result->close();
            
            return $success;
        }
        
        function checkinAccount($user, $accountId)
        {
            // Check if account is checked out by this user
            $result = $this->conn->query("SELECT id FROM lans_guests WHERE id = \"".$this->conn->real_escape_string($accountId)."\" AND checkout = \"".$this->conn->real_escape_string($user->id)."\" LIMIT 1;");
            
            if($result->num_rows > 0)
            {
                // Checkin account
                $this->conn->query("UPDATE lans_guests SET checkout=\"0\" WHERE id = \"".$this->conn->real_escape_string($accountId)."\";");
                
                // Create message
                $this->conn->query("INSERT INTO lans_webevents (event,data) VALUES (\"lanAcctCheckin\", \"".$this->conn->real_escape_string($accountId)."\");");
            }
            
            $result->close();
            
            return;
        }
        
    }
    
    class LanAccount
    {
        public $id = -1;
        public $name = "";
        public $password = "";
        public $steamid = "";
        public $checkout = 0;
        public $checkoutName = "";
        public $numGames = 0;
        public $games = array();
        
        public function toString()
        {
            // Start Object
            $output = "{\n";
            
            // Id
            $output .= "\"id\": ".json_encode($this->id).",\n";
            
            // Name
            $output .= "\"name\": ".json_encode($this->name).",\n";
            
            // Password
            $output .= "\"password\": ".json_encode($this->password).",\n";
            
            // SteamID
            $output .= "\"steamid\": ".json_encode($this->steamid).",\n";
            
            // Checkout Status
            $output .= "\"checkout\": ".json_encode($this->checkout).",\n";
            
            // Checkout Name
            $output .= "\"checkoutName\": ".json_encode($this->checkoutName).",\n";
            
            // Start Games
            $output .= "\"games\":\n[";
            
            for($x = 0; $x < $this->numGames; $x++)
            {
                if($x != 0)
                {
                    $output .= ", ";
                }
                
                $output .= "\"".$this->games[$x]."\"";
            }
            
            // End Games
            $output .= "\n]";
            
            // End Object
            $output .= "\n}";
            
            return $output;
        }
    }
?>