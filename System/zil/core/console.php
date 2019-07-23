<?php
/**
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\core;

    use zil\App;
    use zil\core\scrapper\Info;
    use zil\core\writer\View;
    use zil\core\writer\Service;
    use zil\factory\Schema;
    use zil\core\writer\Migration;
    use zil\core\writer\Controller;
        
    
    class Console{


        public function run(array $command, int $command_count, string $cwd){

            try{

                    $AppManager = new ApplicationManager();

                    
                    $command_string = preg_replace('/[\s]+/', ' ', implode(' ', $command));
                    
                    $command = explode(' ', $command_string);
                    unset($command[0], $command_string);
                    
                    $command = array_values($command);

                    
                    function help(){

                        $mask = "%-20.27s \t %-30.60s\n";
                            printf($mask, "use app <name>", "Switch app");
                            printf($mask, "show --apps", "Show all apps");
                            printf($mask, "show --current", "Show current working app\n");
                            
                            printf($mask, "app <name>", "Create app\n");

                            printf($mask, "service <name>", "Create service");
                            printf($mask, "controller <name>", "Create controller");
                            printf($mask, "api <name>", "Create api\n");

                            printf($mask, "view <name> -c <name>", "Create view");
                            printf($mask, "-c <name>", "Attach view to a controlller\n");
                            
                            

                            printf($mask, "migration <name>", "Create a new migration");
                            printf($mask, "-model <name>", "Create and point to a model\n");
                            
                            printf($mask, "migrate <name>", "Migrate <name>");
                            printf($mask, "--all", "Migrate all from new migration");
                            // printf($mask, "--save", "Migrate and Save migration");
                            // printf($mask, "--rollback", "Rollback migration and migrate\n");
                            
                            printf($mask, "destroy",  "Destroy app\n");
                            printf($mask, "serve", "Start php development server");
                            printf($mask, "prod", "Activate production mode");
                            printf($mask, "dev", "Activate development mode");
                            
                
                    }
                
                    if($command_count == 0){
                
                        help();
                        exit();
                    }
                
                    if($command_count == 1 || $command[0] == '-h' || $command[0] == 'h' || $command[0] == '?' || $command[0] == '-help' || $command[0] == 'help' ){
                    
                        help();
                        exit();
                    }




                    if( $command_count > 1){

                        if( $command[0] != 'app' ) {
                            /**
                             * Inform app currently working on
                             **/
                            printf("\n\033[1;32m---CURRENT NODE-APP:%s Begins Task----\033[0m\n\n", (new Info())->getCurrentApp());
                        }


                        if( $command[0] == 'use'){
                            
                            
                            $name = strpos($command[1],'-') == false ? $command[1] : null;
                            if( !is_null($name) )
                                $AppManager->useApp($name);
                            else
                                print("Error: Undefined app name");
                
                            exit();
                        }
                
                        if( $command[0] == 'exit'){
                
                            
                            $name = strpos($command[1],'-') == false ? $command[1] : null;
                            if( !is_null($name) )
                                $AppManager->useApp($name);
                            else
                                print("Error: Undefined app name");
                
                            exit();
                        }
                
                        if( $command[0] == 'show'){
                            
                            $command = strpos($command[1],'-') == false ? strtolower($command[1]) : null;
                            if( $command == '--apps' ){
                
                                foreach($AppManager->showApps() as $app_name){
                                    print $app_name;
                                }
                            
                            }else if($command == '--current'){
                                
                                print (new Info())->getCurrentApp()."\n";
                
                            }else if($command == '?' ){
                
                                $mask = "%-20.27s \t %-30.60s\n";
                                printf($mask, "--apps", "List all apps");
                                printf($mask, "--current", "List current working app");
                            
                            }else{
                                print("Invalid command: check show ?");
                        
                            }
                
                            exit();
                        }
                
                        if( $command[0] == 'app'){
                            
                            $command_found = true;
                
                            $name = strpos($command[1],'-') == false ? $command[1] : null;
                            if( !is_null($name) ){
                                
                                $Info = new Info();
                                $Info->setAppDir($cwd);
                                $Info->setAppName($name);
                
                            $AppManager->createApp($Info);
                
                            }else{
                                print("Error: Undefined app name");
                            }
                
                            exit();
                        }
                
                        if( $command[0] == 'api'){
                
                            $apiName = strpos($command[1],'-') == false ? $command[1] : null;
                            $appPointer = null;
                
                            
                            $appPointer = (new Info())->getCurrentApp();
                
                            if(strlen($appPointer) > 0){
                                
                                $Info = new Info();
                                $Info->setAppDir($cwd);
                                $Info->setAppName($appPointer);
                
                                (new Controller())->create($Info, 'api/'.$apiName, 'Api' );

                            }else{
                                print "No app in use, try use <name>";
                            }
                
                            exit();
                        }
                
                    
                        if( $command[0] == 'controller' || $command[0] == '-c'){
                
                            $controllerName = strpos($command[1],'-') == false ? $command[1] : null;
                            $appPointer = null;
                            $viewName = null;
                
                            foreach($command as $k => $args){
                                
                                if( ($args == "-v" || $args == "-view")  && isset($command[$k+1]) ){
                                    $viewName = $command[$k+1];
                                }else  if($args == "?"){
                
                                    $mask = "%-20.27s \t %-30.60s\n";
                                    printf($mask, "-v <name>", "Create controller and a view");
                                    exit();
                                }
                            }
                            
                
                            $appPointer = (new Info())->getCurrentApp();
                
                            if(strlen($appPointer) > 0){
                                
                                $Info = new Info();
                                $Info->setAppDir($cwd);
                                $Info->setAppName($appPointer);
                
                                if(!is_null($viewName))
                                    (new Controller())->createEx($Info, $controllerName, $viewName );
                                else
                                    (new Controller())->createEx($Info, $controllerName);
                            }else{
                                print "No app in use, try use <name>";
                            }
                
                            exit();
                        }
                
                        if( $command[0] == 'view' || $command[0] == '-v'){
                
                            $viewName = strpos($command[1],'-') == false ? $command[1] : null;
                
                            foreach($command as $k => $args){
                                
                                
                                if( ($args == "-c" || $args == "-controller") && isset($command[$k+1]) )
                                    $controllerName = $command[$k+1];
                
                            }
                            
                        
                
                            $appPointer = (new Info())->getCurrentApp();
                
                            if(strlen($appPointer) > 0){
                                $Info = new Info();
                                
                                $Info->setAppDir($cwd);
                                $Info->setAppName($appPointer);
                                
                                if(!isset($controllerName)){
                                    (new View())->createWithoutConveyor($Info, $viewName );
                                }else{
                                    (new View())->create($Info, $viewName, $controllerName );
                                }

                            }else{
                                print "No app in use, try use <name>";
                            }
                
                            exit();
                        }
                
                        if( $command[0] == 'service' || $command[0] == '-s'){
                
                            $serviceName = strpos($command[1],'-') == false ? $command[1] : null;
                            $appPointer = null;
                            
                            
                            $appPointer = (new Info())->getCurrentApp();
                
                            if(strlen($appPointer) > 0){
                                
                                $Info = new Info();
                                $Info->setAppDir($cwd);
                                $Info->setAppName($appPointer);
                
                                if($serviceName !== null){
                                    $response = (new Service())->create($Info, $serviceName );
                                    if($response === 0  )
                                        print "Error: Unable to create service";
                                }else{
                                    print "Error: Undefined service name";
                                }
                            }else{
                                print "No app in use, try use <name>";
                            }
                
                            exit();
                        }
                
                        if( $command[0] == 'migrate'){
                
                            $appPointer = null;
                            
                            $Info = new Info();
                            
                            $appPointer = $Info->getCurrentApp();
                
                            if(!empty($appPointer)){
                
                                $Info->setAppDir($cwd);
                                $Info->setAppName($appPointer);
                
                            
                                $save = false;
                                
                                // if(in_array('--save', $command))
                                //     $save = true;
                
                                $rollback = false;
                                
                                // if(in_array('--rollback', $command))
                                // $rollback = true;
                
                                $all = false;
                                $migration_pointer = null;
                                if(in_array('--all', $command))
                                    $all = true;
                                else
                                    $migration_pointer = isset($command[1]) && strpos($command[1], '-') == 0 ? $command[1] : null;
                
                                
                                Schema::migrate($Info, $save, $rollback, $all, $migration_pointer);
                
                            }else{
                                print "No app in use, try use <name>";
                            }
                            exit();
                        }
                        
                        if( $command[0] == 'migration'){
                
                            if(!isset($command[1])){
                                print "Error: Undefined migration name";
                                exit();
                            }
                
                            $migrationName = strpos($command[1],'-') == false ? $command[1] : null;
                
                            $appPointer = null;
                            $modelName = null;            
                            unset($command[0], $command[1]);
                
                            foreach($command as $k => $args){
                            
                                if($args == '-model')
                                    $modelName = isset($command[$k++]) ? $command[$k++] : null;               
                            }
                            
                            $appPointer = (new Info())->getCurrentApp();
                
                            if(strlen($appPointer) > 0){
                                
                                $Info = new Info();
                                $Info->setAppDir($cwd);
                                $Info->setAppName($appPointer);
                
                                if($migrationName !== null){
                                
                                    $response = (new Migration())->create($Info, $migrationName, $modelName );
                                
                                    if($response === 0  )
                                        print "Error: Unable to create migration";
                                }else{
                                    print "Error: Undefined migration";
                                }
                            }else{
                                print "No app in use, try use <name>";
                            }
                
                            exit();
                        }
                
                        if( $command[0] == 'prod'){
                
                            exit();
                        }
                
                        if( $command[0] == 'destroy'){
                
                            $appPointer = null;
                            $cli_dir = $cwd;
                
                            $commandV = $command;
                            $commandValue = strtolower(@$commandV[2]) ?? null;
                
                            $command = strtolower($command[1]);
                            
                            $appPointer = (new Info())->getCurrentApp();
                
                            if(strlen($appPointer) > 0){
                
                                $Info = new Info();
                                $Info->setAppDir($cli_dir);
                                $Info->setAppName($appPointer);
                
                                
                                if( $command == '--app'){
                
                                    if(strlen($appPointer) > 0)
                                        $AppManager->destroy($Info, null);
                                    else
                                        print "No app in use, try use <name>";
                
                                }else if($command == '-controller' || $command == '-c'){
                                

                                    if(isset($commandValue)){
                                        $controllerName = $commandValue;
                
                                        (new Controller())->destroy($Info, $controllerName);
                                    }else{
                                        print "Error: Undefined Controller";
                                    }
                
                                }else if($command == '-api'){
                                
                                    if(isset($commandValue)){
                                        $apiName = 'api/'.$commandValue;
                
                                        (new Controller())->destroy($Info, $apiName);
                                    }else{
                                        print "Error: Undefined api";
                                    }
                                
                                }else if($command == '-service' || $command == '-s'){
                                    if(isset($commandValue)){
                                        $serviceName = $commandValue;
                                        (new Service())->destroy($Info, $serviceName);
                                    }else{
                                        print "Error: Undefined service";
                                    }
                                }else if($command == '-view' || $command == '-v'){
                    
                                    if(isset($commandValue)){
                                        $viewName = $commandValue;
                
                                        unset($commandV[1], $commandV[2]);
                
                                        $commandV = array_values($commandV);
                                        if(@$commandV[1] == '-controller' || @$commandV[1] == '-c'){
                                            $hostController = $commandV[2];
                                            (new View())->destroy($Info, $viewName, $hostController);
                                        }else{
                                            (new View())->destroy($Info, $viewName, '');
                                        }
                                    }else{
                                        print "Error: Undefined view";
                                    }
                                }else if($command == '?'){
                
                                    $mask = "%-20.27s \t %-30.60s\n";
                                    printf($mask, "--app", "Destroy app");
                                    printf($mask, "-service <name>", "Destroy service");
                                    printf($mask, "-controller <name>", "Destroy controller and its views");
                                    printf($mask, "-view <name>", "Destroy view");
                                    printf($mask, "-view <name> -c <name>", "Destroy view and its conveyor");
                                    printf($mask, "-api <name>", "Destroy api");
                                    
                                }
                
                            }else{
                                print "No app in use, try use <name>";
                            }
                
                            exit();
                        }
                        
                        if( $command[0] == 'serve'){
                            $port = 5000;
                            if(isset($command[1])){
                                if($command[1] == '-port'){
                                    if(isset($command[2]))
                                        $port = intval($command[2]);
                                }
                            }
                
                            print "\n***Development server starts on port $port\n\n";
                            shell_exec("php -S 127.0.0.1:$port");
                            
                
                        }else if( $command[0] == 'prod'){
                                    
                        
                            $appPointer = null;
                
                            $appPointer = (new Info())->getCurrentApp();
                
                            if(strlen($appPointer) > 0){
                                
                                $Info = new Info();
                                $Info->setAppDir($cwd);
                                $Info->setAppName($appPointer);
                
                                $AppManager->setProdMode($Info);
                
                            }else{
                                print "No app in use, try use <name>";
                            }
                
                            exit();
                        }else if( $command[0] == 'dev'){
                                    
                        
                            $appPointer = null;
                
                            $appPointer = (new Info())->getCurrentApp();
                
                            if(strlen($appPointer) > 0){
                                
                                $Info = new Info();
                                $Info->setAppDir($cwd);
                                $Info->setAppName($appPointer);
                
                                $AppManager->setDevMode($Info);
                
                            }else{
                                print "No app in use, try use <name>";
                            }
                
                            exit();
                        }else{
                            print "Invalid command, try -h for help\n";
                            exit();
                        }
                        
                    
                    }else{
                        help();
                    }
            }catch(\CompileError $t){
                print($t->getMessage());
            }catch(\InvalidArgumentException $t){
                print($t->getMessage());
            }catch(\LengthException $t){
                print($t->getMessage());
            }catch(\Throwable $t){
                print($t->getMessage().' on line '.$t->getLine().' ('.$t->getFile().")\n");
            }

        }
    }

?>