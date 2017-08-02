<?php
    function sqlConnect()
    {
        $dataConn = new mysqli("localhost", "username", "password", "gslans");
        
        return $dataConn;
    }
?>