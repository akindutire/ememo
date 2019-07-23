<?php
namespace zil\core\writer;

use zil\core\interfaces\Writer;
use zil\core\scrapper\Info;

use zil\factory\Filehandler;

    class Composer  implements Writer
    {

        public function __construct(Info $Info){
            
            try{
                if( ($Info->getAppBase() !== null) && ($Info->getAppName() !== null) ){
                    $app_base = $Info->getAppBase();
                    $app_name = $Info->getAppName();
                }else{
                    throw new \Exception("Error: Couldn't resolve app directories");
                }

                $AppDir = $Info->getAppDir();
                
                if(!file_exists("{$AppDir}composer.json")) {

                    $Info->getprogressMessage("createComposer");
                    $context    =    json_encode(["autoload"=> ["psr-4"=> ["src\\"=>"src"] ] ],JSON_PRETTY_PRINT);

                    (new Filehandler())->createFile("{$AppDir}composer.json", $context);
                }

                return "{$AppDir}composer.json";
            }catch(\Exception $e){
                
                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }catch(\Throwable $e){

                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }
        }

        public function destroy(Info $Info, string $name){
            try{

            }catch(\Exception $e){
                
                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }catch(\Throwable $e){

            }finally{
                print "destruction closed";
            }
        }

    }
?>