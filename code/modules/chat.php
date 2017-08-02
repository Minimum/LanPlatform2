<?php
    function chatUpdateClient($conn, $account, $lastUpdate)
    {
        $output = "\n\"chat\": {";
        $newTime = $lastUpdate;
    
        // Get Data
        if($account != null)
        {
            $result = $conn->query("SELECT id,author,room,message FROM lans_chat WHERE (room = 0 OR room = ANY (SELECT room FROM lans_chatguests WHERE guest = \"".$conn->real_escape_string($account->id)."\")) AND id > \"".$conn->real_escape_string($lastUpdate)."\" LIMIT 50;");
        }
        else
        {
            $result = $conn->query("SELECT id,author,room,message FROM lans_chat WHERE room = 0 AND id > \"".$conn->real_escape_string($lastUpdate)."\" LIMIT 50;");
        }
        
        // Output Messages
        $messageOutput = "\"messages\": [";
        
        while($message = $result->fetch_array(MYSQLI_NUM))
        {
            if($newTime != $lastUpdate)
            {
                $messageOutput .= ",";
            }
            
            $messageOutput .= "\n{ \"id\": \"".$message[0]."\", \"author\": \"".$message[1]."\", \"room\": \"".$message[2]."\", \"text\": \"".$message[3]."\" }";
            
            $newTime = ($newTime < $message[0]) ? $message[0] : $newTime;
        }
        
        $messageOutput .= "\n]";
        
        $result->close();
        
        // Output Properties
        $output .= "\n\"time\": \"".$newTime."\"";     // Timing
        $output .= ",\n".$messageOutput;               // Messages
        
        $output .= "\n}";
        
        return $output;
    }
    
    function chatReceiveMessage($conn, $account, $room, $message)
    {
        $formattedMessage = rtrim($message);
    
        if($account != null && strlen($formattedMessage) > 0)
        {
            $result = $conn->query("SELECT id FROM lans_chatguests WHERE room = \"".$conn->real_escape_string($room)."\" AND guest = \"".$conn->real_escape_string($account->id)."\" LIMIT 1;");
            
            if($result->num_rows > 0)
            {
                $conn->query("INSERT INTO lans_chat (time,author,room,message) VALUES (unix_timestamp(), \"".$conn->real_escape_string($account->id)."\", \"".$conn->real_escape_string($room)."\", \"".$conn->real_escape_string($message)."\");");
            }
            
            $result->close();
        }
    
        return;
    }
?>