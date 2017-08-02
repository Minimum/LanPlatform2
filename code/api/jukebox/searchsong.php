{
<?php
    include("../../data.php");
    include("../../modules/jukebox.php");

    header('Content-type: application/json');
    
    $dataConn = sqlConnect();
    $jukeboxManager = new JukeboxManager($dataConn);
    
    $searchPhrase = (isset($_POST["search"])) ? $_POST["search"] : "";
    $searchOffset = (isset($_POST["offset"])) ? intval($_POST["offset"]) : 0;
    
    $songList = $jukeboxManager->searchSong($searchPhrase, $searchOffset);
    
    // Print Songs
    print "\"songs\": [";
    
    for($x=0; $x < 50; $x++)
    {
        if(isset($songList[$x]))
        {
            if($x > 0)
            {
                print ",";
            }
            
            print "\n".$songList[$x]->toString();
        }
        else
        {
            break;
        }
    }
    
    print "\n]\n";
?>
}