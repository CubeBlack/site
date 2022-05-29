<?php 
class Openarena{
    static function hook(){
        $con = con();
        $sRequest = serialize($_REQUEST);
        $sql = "INSERT INTO `hook` SET
        `request` = '$sRequest'";
        $resp = $con->prepare($sql);
        $resp->execute();
    }
}