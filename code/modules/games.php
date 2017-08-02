<?php
    class GameManager
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
        
        public function printGames($userAccount)
        {
            // Start Game Section
            $output = "\n\"game\": {";    
        
            $result = $this->conn->query("SELECT id,name FROM lans_library;");
            
            // Start Game Info Array
            $output .= "\n\"games\": [\n";
            
            if($result->num_rows > 0)
            {
                $numGames = 0;
            
                while($data = $result->fetch_array(MYSQLI_NUM))
                {
                    if($numGames != 0)
                    {
                        $output .= ",";
                    }
                
                    // Start Object
                    $output .= "{\n";
                    
                    // Id
                    $output .= "\"id\": \"".$data[0]."\",\n";
            
                    // Name
                    $output .= "\"name\": \"".$data[1]."\"";
                    
                    // End Object
                    $output .= "\n}";
                    
                    $numGames++;
                }
            }
            
            // End GameInfo Array
            $output .= "\n]";
            
            // End Game Section
            $output .= "\n}";
            
            return $output;
        }

        public function printGameInfo($userAccount)
        {
            // Start Game Section
            $output = "\n\"game\": {";
            
            $result = $this->conn->query("SELECT a.id as game, b.id as section,a.name,a.steamid,b.title,b.content FROM lans_games a, lans_games_info b WHERE a.id = b.game order by b.id asc;");
            
            $games = array();
            
            while($data = $result->fetch_array(MYSQLI_NUM))
            {
                $game = null;
            
                if(!isset($games[$data[0]]))
                {
                    $game = new GameInfo;
                    
                    $game->id = $data[0];
                    $game->name = $data[2];
                    $game->steamid = $data[3];
                
                    $games[$data[0]] = $game;
                }
                else
                {
                    $game = $games[$data[0]];
                }
                
                $section = new GameInfoSection;
                
                $section->id = $data[1];
                $section->title = $data[4];
                $section->content = $data[5];
                
                $game->sections[$data[1]] = $section;
            }
            
            $result->close();
            
            // Start GameInfo Array
            $output .= "\n\"games\": [";
            
            $numGames = 0;
            
            // Write GameInfos
            foreach($games as $game)
            {
                if($numGames != 0)
                {
                    $output .= ",";
                }
                
                $output .= "\n".$game->toString();
                
                $numGames++;
            }
            
            // End GameInfo Array
            $output .= "\n]";
            
            // End Game Section
            $output .= "\n}";
            
            return $output;
        }
    }
    
    class GameInfo
    {
        public $id = 0;
        public $name = "";
        public $steamid = 0;
        public $sections = array();
        
        public function toString()
        {
            // Start Object
            $output = "{\n";
            
            // Id
            $output .= "\"id\": \"".$this->id."\",\n";
            
            // Name
            $output .= "\"name\": \"".$this->name."\",\n";
            
            // SteamId
            $output .= "\"steamid\": \"".$this->steamid."\",\n";
            
            // Start Sections
            $output .= "\"sections\":\n[";
            
            $numSections = 0;
            
            foreach($this->sections as $section)
            {
                if($numSections != 0)
                {
                    $output .= ",";
                }
                
                $output .= "\n".$section->toString();
                
                $numSections++;
            }
            
            // End Sections
            $output .= "\n]";
            
            // End Object
            $output .= "\n}";
            
            return $output;
        }
    }
    
    class GameInfoSection
    {
        public $id = 0;
        public $title = "";
        public $content = "";
        
        public function toString()
        {
            // Start Object
            $output = "{\n";
            
            // Id
            $output .= "\"id\": \"".$this->id."\",\n";
            
            // Title
            $output .= "\"title\": \"".$this->title."\",\n";
            
            // Content
            $output .= "\"content\": ".json_encode($this->content)."\n";
            
            // End Object
            $output .= "}";
            
            return $output;
        }
    }
?>