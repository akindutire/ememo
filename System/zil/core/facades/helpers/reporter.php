<?php
namespace zil\core\facades\helpers;
use zil\core\config\Config;

    trait Reporter{
   
       public static function report(int $err_code){
        
        ob_end_clean();
        
        $r = (new Config())->getSysPath()."/core/facades/reports/{$err_code}/index.php";

        include_once ($r);

        unset($r);

        die();

        }
        
    }



?>