<?php
    class JukeboxManager
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
        
        function searchSong($search, $offset)
        {
            $result = null;
            $songs = array();
            $searchStr = trim($search);
        
            if($searchStr != "")
            {
                $result = $this->conn->query("SELECT id,title,artist,album,genre,duration,volume,location FROM lans_jukebox_songs WHERE title LIKE \"%".$this->conn->real_escape_string($search)."%\" OR artist LIKE \"%".$this->conn->real_escape_string($search)."%\" OR album LIKE \"%".$this->conn->real_escape_string($search)."%\" OR genre LIKE \"%".$this->conn->real_escape_string($search)."%\" LIMIT ".$this->conn->real_escape_string(strval($offset)).",50;");
            }
            else
            {
                $result = $this->conn->query("SELECT id,title,artist,album,genre,duration,volume,location FROM lans_jukebox_songs LIMIT ".$this->conn->real_escape_string(strval($offset)).",50;");
            }
            
            $songNum = 0;
            
            if($result->num_rows > 0)
            {
                while($data = $result->fetch_array(MYSQLI_NUM))
                {
                    $song = new Song();
                
                    $song->id = $data[0];
                    $song->title = $data[1];
                    $song->artist = $data[2];
                    $song->album = $data[3];
                    $song->genre = $data[4];
                    $song->duration = $data[5];
                    $song->volume = $data[6];
                    $song->location = $data[7];
                
                    $songs[$songNum] = $song;
                
                    $songNum++;
                }
            }
            
            return $songs;
        }
    }
    
    class Song
    {
        public $id = -1;
        public $title = "";
        public $artist = "";
        public $album = "";
        public $genre = "";
        public $duration = 0;
        public $volume = 0;
        public $location = "";
        
        public function toString()
        {
            // Start Object
            $output = "{\n";
            
            // Id
            $output .= "\"id\": \"".$this->id."\",\n";
            
            // Title
            $output .= "\"title\": \"".$this->title."\",\n";
            
            // Artist
            $output .= "\"artist\": \"".$this->artist."\",\n";
            
            // Album
            $output .= "\"album\": \"".$this->album."\",\n";
            
            // Genre
            $output .= "\"genre\": \"".$this->genre."\",\n";
            
            // Duration
            $output .= "\"duration\": \"".$this->duration."\",\n";
            
            // Volume
            $output .= "\"volume\": \"".$this->volume."\",\n";

            // Location
            $output .= "\"location\": \"".$this->location."\"";
            
            // End Object
            $output .= "\n}";
            
            return $output;
        }
    }
?>