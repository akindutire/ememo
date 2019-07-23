<?php
/**
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\core;

use zil\core\interfaces\ApplicationManager as AppManagerIf;
use zil\core\scrapper\Info;
use zil\core\directory\Manager as App_Dir_Manager;
use zil\core\directory\Tree;
use zil\core\writer\Init as Initializer_Writer;
use zil\core\writer\Config as Configuration_Writer;
use zil\core\writer\Composer as Composer_Writer;
use zil\core\writer\Controller as Controller_Writer;
use zil\core\writer\View as View_Writer;
use zil\core\writer\Route as Route_Writer;

use zil\core\writer\Ht;

use zil\factory\Filehandler;


    class ApplicationManager implements AppManagerIf
    {
    
        private $appName    = null;
       
        /**
         * Constructor
         */
        public function __construct(){ }

        /**
         * Switch between apps
         *
         * @param string $app_name
         * @return void
         */
        public function useApp(string $app_name):  void{

            $tree = json_decode(file_get_contents((new Info())->getSystemPath()."data/tree/.app.json"));

            if(isset($tree->apps->{$app_name})){
    
                $tree->currentApp = $app_name;
                file_put_contents((new Info())->getSystemPath()."data/tree/.app.json",json_encode($tree,JSON_PRETTY_PRINT));
                print "Switched to {$app_name}\n";

            }else{
                print "Couldn't use {$app_name}, {$app_name} not found\n";
            }
            
        }

        /**
         * List installed apps
         *
         * @return void
         */
        public function showApps(){

            try{
                $tree = json_decode(file_get_contents((new Info())->getSystemPath()."data/tree/.app.json"));
                foreach($tree->apps as $app){
                    yield $app->name."\n";
                }  
            }catch(\Throwable | \ClosedGeneratorException $t){
                print($t->getMessage().' on line '.$t->getLine().' ('.$t->getFile().")\n");
            }
        }

        /**
         * Register a newly installed app
         *
         * @param boolean $is_root
         * @return void
         */
        private function registerApp(bool $is_root ){
            
            $Info = new Info();
            $Info->getProgressMessage("registerApp");
            (new Tree())->createAppTree($Info, $is_root);
        }

        /**
         * Install App
         */
        public function createApp(Info $Info){

            try{

                if(!empty((string)$Info->getAppName()) && !is_null($Info->getAppDir())){
                    $AppDir = $Info->getAppDir();
                    $app_base = $Info->getAppBase();
                    $framework_path =   $Info->getSystemPath();
                        
                }else{
                    throw new \Exception("Error: Couldn't resolve app directories");
                }

                if( is_null(@($Info->getTree())->apps->{$Info->getAppName()}) ) {

                    $tree = json_decode(file_get_contents((new Info())->getSystemPath()."data/tree/.app.json"));

                    $tree->currentApp = '';
                    file_put_contents((new Info())->getSystemPath()."data/tree/.app.json",json_encode($tree,JSON_PRETTY_PRINT));
                    
                    (new Filehandler())->createDir("$app_base");
                    (new App_Dir_Manager())->validateDirectoryListing($Info);
                    
                    $init = new Initializer_Writer();
                    $init->create($Info);
                
                    (new Ht())->create($Info, $init->_isRoot);
                    $this->registerApp($init->_isRoot);
                
                    new Configuration_Writer($Info);
                    new Composer_Writer($Info);
                    new Route_Writer($Info);

                    (new Controller_Writer())->create($Info);
                    (new View_Writer())->create($Info, 'index', 'home');
                   
                    $Info->getProgressMessage("createApp");
                    
                    $this->useApp($Info->getAppName());
                    
                }else{
                    print "Error: {$Info->getAppName()} already existing\n";
                }
            }catch(\Throwable $e){
                print $e->getMessage()."\n";
            }finally{
                print "App creation closed\n";
            }
        }

        /**
         * Release App to Production Mode
         *
         * @param Info $Info
         * @return void
         */
        public function setProdMode(Info $Info){
            try{
                
                $tree = json_decode(file_get_contents((new Info())->getSystemPath()."data/tree/.app.json"));

                $tree->apps->{$Info->getAppName()}->prod = true;

                file_put_contents((new Info())->getSystemPath()."data/tree/.app.json",json_encode($tree,JSON_PRETTY_PRINT));    
                print($Info->getAppName()." is in production mode\n");
            }catch(\Throwable $e){
                print $e->getMessage();
            }

        }

        /**
         * Release App to Development Mode
         *
         * @param Info $Info
         * @return void
         */
        public function setDevMode(Info $Info){
            try{
                $tree = json_decode(file_get_contents((new Info())->getSystemPath()."data/tree/.app.json"));

                $tree->apps->{$Info->getAppName()}->prod = false;
                file_put_contents((new Info())->getSystemPath()."data/tree/.app.json",json_encode($tree,JSON_PRETTY_PRINT));    
               
                print($Info->getAppName()." is in development mode\n");
            }catch(\Throwable $e){
                print $e->getMessage();
            }

        }

        /**
         * Uninstall App
         *
         * @param Info $Info
         * @return void
         */
        private function deleteAppFiles(Info $Info){

            try{
                echo "destroying app init...\n";

                $AppDir = $Info->getAppDir();
                
                $app_name = $Info->getAppName();
                
                $tree = $Info->getTree();
                
                $filehandler = new Filehandler;        

                if($tree->apps->{$app_name}->root == true){
                    
                    (new Initializer_Writer())->destroy($Info, null);
                    (new Ht())->destroy($Info);
                    
                }else{
                
                    $filehandler->removeDir("{$AppDir}{$app_name}");
                }

                print "destroying app src logic...\n";


                $filehandler->removeDir("{$AppDir}src/{$app_name}/");                

                return null;
            }catch(\Throwable $e){
                print $e->getMessage();
            }finally{
                $tree = $Info->getTree();
                unset($tree->apps->{$app_name});
                file_put_contents($Info->getSystemPath()."data/tree/.app.json",json_encode($tree,JSON_PRETTY_PRINT));
                unset($tree);
                print "Main destruction closed\n";
            }
        }


        /**
         * Remove app from app list while uninstalling
         *
         * @param Info $Info
         * @return boolean
         */
        private function unregisterApp(Info $Info) : bool {
            
            $tree = $Info->getTree();
            $app_name = $Info->getAppName();
            
            if(is_null(@$tree->apps->{$app_name})){
                
                echo "Error: {$app_name} not recognised";
                return false;
            }else{

                $this->deleteAppFiles($Info);
                return true;
            }
        }

        /**
         * Grand destroy for uninstall
         *
         * @param Info $Info
         * @param string|null $app_name
         * @return void
         */
        public function destroy(Info $Info, ?string $app_name) : bool{
            
            try{
                $tree = json_decode(file_get_contents($Info->getSystemPath()."data/tree/.app.json"));

                $tree->currentApp = '';
                file_put_contents($Info->getSystemPath()."data/tree/.app.json",json_encode($tree,JSON_PRETTY_PRINT));    

                $this->unregisterApp($Info);            
                
                return true;
            }catch(\Throwable $e){
                print $e->getMessage();
            }
        }

        /**
         * Switch Out of app
         *
         * @param string $app_name
         * @return void
         */
        public function exitApp(string $app_name){
            try{
                $tree = json_decode(file_get_contents((new Info())->getSystemPath()."data/tree/.app.json"));

                $tree->currentApp = '';
                file_put_contents((new Info())->getSystemPath()."data/tree/.app.json",json_encode($tree,JSON_PRETTY_PRINT));    
            }catch(\Throwable $e){
                print $e->getMessage();
            }
        }


    }
