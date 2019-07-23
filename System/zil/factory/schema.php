<?php
/**
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\factory;

    use zil\core\scrapper\Info;
    use zil\core\tracer\ErrorTracer;
    use zil\core\writer\Model;
    use zil\core\directory\Tree;
    use zil\core\interfaces\Config;
    
   

    class Schema
    {

        private static $attrib = [];
       
        private static $struct = [ 'construct'=>[], 'destruct'=>[], 'table'=>'', 'haltModel'=>false ];
        private static $currentSchema = null;

    
        /**
         * Initialize Schema
         *
         * @param string $schema_name
         */
        public function __construct(string $schema_name)
        {
            if(empty($schema_name)){
                print("Undefined schema\n");
                exit();
            }

            self::$currentSchema    = $schema_name;
            self::$struct['table']  = $schema_name;

        }

        /**
         * Compile the last attribute of the schema, first could be last
         *
         * @return void
         */
        private function buildLastString(){


                try{
                    if(!empty(self::$attrib)){

                        $db = $this->getDatabaseParams();

                        $pdohandle = (new Database())->connect($db);
        
                        $table = self::$currentSchema;


                        /**
                         * Check if table exists
                         */
                        if($db['driver'] == 'mysql' || $db['driver'] == 'pgsql' || $db['driver'] == 'mssql' || $db['driver'] == 'oracle'){
                
                            $rs = $pdohandle->query("SELECT * FROM  INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = '{$table}' AND TABLE_TYPE='BASE TABLE' AND TABLE_SCHEMA='{$db['database']}'");
        
                        }else if($db['driver'] == 'sqlite'){
                            $rs = $pdohandle->query("SELECT * FROM pragma_table_info('$table')");
                        }
        
                        
                        $fn     =   rtrim( trim(self::$attrib['field_name']), ',');
                        $ft     =   rtrim( trim(self::$attrib['field_type']), ',');
                        $fi     =   rtrim( trim(self::$attrib['field_index']), ',');
                        $fnull  =   rtrim( trim(self::$attrib['field_nullable']), ',');
                        $fd     =   rtrim( trim(self::$attrib['field_default']), ',');
                        $fp     =   rtrim( trim(self::$attrib['field_position']), ',');


                        /**
                         * Organize query string
                         */
                        $attribs = rtrim(  trim("{$fn} {$ft} {$fi} {$fnull} {$fd} {$fp}")  , ',');
                        unset($fn, $ft, $fi, $fnull, $fd, $fp);

                        $constraint = null;
                        $query = null;

                        if($rs->rowCount() == 0){
                            $pdohandle->query("CREATE TABLE IF NOT EXISTS {$table}($attribs) ENGINE={$db['engine']}  CHARSET={$db['charset']}");
                        }else{
        
                            $col = self::$attrib['field_name'];


                            /**
                             * Check if column exists
                             */
                            if($db['driver'] == 'mysql' || $db['driver'] == 'pgsql' || $db['driver'] == 'mssql'){
        
                                $rs = $pdohandle->query("SELECT * FROM  INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='{$db['database']}' AND TABLE_NAME = '{$table}' AND COLUMN_NAME='{$col}'");
                            }else if($db['driver'] == 'sqlite'){
                                
                                $rs = $pdohandle->query("SELECT * FROM pragma_table_info('$table') WHERE name='$col'");
                            }else if($db['driver'] == 'oracle'){
                            
                                $rs = $pdohandle->query("SELECT * FROM  ALL_TAB_COLUMNS WHERE TABLE_NAME = '{$table}' COULUMN_NAME='$col'");
                            }
        
                            /**
                             * Execute appropriate query
                             */
                            if($rs->rowCount() == 0){
                                $query = "ALTER TABLE {$table} ADD $attribs";
                                
                            }else{
                                if($db['driver'] == 'mysql' || $db['driver'] == 'pgsql'){
                                    $query = "ALTER TABLE {$table} MODIFY COLUMN $attribs";
                                }else if($db['driver'] == 'mssql' || $db['driver'] == 'sqlite'){
                                    $query = "ALTER TABLE {$table} ALTER COLUMN $attribs";
                                }else if($db['driver'] == 'oracle'){
                                    $query = "ALTER TABLE {$table} MODIFY $attribs";
                                }
                            }

                            $pdohandle->query($query);

                            /**
                             * Create Index
                             */
                            if( isset(self::$attrib['field_spindex'])  && !empty(self::$attrib['field_spindex'])){
                                $i = self::$attrib['field_spindex'];
                                $pdohandle->query("CREATE INDEX zdx_{$i} ON {$table}($i)");
                            }

                            /**
                             * Create Constraint
                             */
                           
                            if( isset(self::$attrib['field_constraint']) && !empty(self::$attrib['field_constraint'])){
                                $constraint = rtrim(self::$attrib['field_constraint'] , ',');
                                $pdohandle->query("ALTER TABLE {$table} ADD {$constraint}");
                                
                            }

                        }
                    }

                }catch(\PDOException $t){
                    print("Query: {$query}\nConstraint: {$constraint}\n");
                    print($t->getMessage().' on line '.$t->getLine().' ('.$t->getFile().")\n");
                    exit();
                    
                }catch(\Throwable $t){
                    
                    print($t->getMessage().' on line '.$t->getLine().' ('.$t->getFile().")\n");

                }
              
            
        }

        /**
         * Retrive Config. of current app
         *
         * @return void
         */
        private function getConfig() : Config {
            try{
                    
                $AppDir     =   getcwd();
                $tree   =   json_decode(file_get_contents((new Info())->getSystemPath()."data/tree/.app.json"));

                $app_name = $tree->currentApp;

                if(!file_exists("{$AppDir}/src/{$app_name}/config/config.php"))
                    throw new \DomainException("Couldn't found config. file for {$app_name}");

                include_once("{$AppDir}/src/{$app_name}/config/config.php");
                
                $cfg = "src\\$app_name\\config\Config";

                return new $cfg();

            }catch(\DomainException $t){

                print($t->getMessage()."\n");
            }catch(\Throwable $t){
                print($t->getMessage()."\n");
            }
        }

        private function getDatabaseParams(){
            try {
                return ($this->getConfig())->getDatabaseParams();
            } catch (\Throwable $t){
                new ErrorTracer($t);
            }
        }

        /**
         * Wrapper of last attribute compiler and reset for next attribute
         *
         * @param string $attribute
         * @return Schema
         */
        public function build(string $attribute): Schema{
            try{

                $this->buildLastString();

                /**
                 * Reset attribute query components
                 */
                self::$attrib['field_name'] = $attribute;
                self::$attrib['field_type'] = '';
                self::$attrib['field_default'] = '';
                self::$attrib['field_position'] = '';
                self::$attrib['field_nullable'] = '';
                self::$attrib['field_index'] = '';
                self::$attrib['field_spindex'] = '';
                self::$attrib['field_constraint'] = '';

                return $this;
            }catch(\Throwable $t){
                print($t->getMessage().' on line '.$t->getLine().' ('.$t->getFile().")\n");
            }
            
        }

        /**
         * Set attribute as integer
         *
         * 
         * @return Schema
         */
        public function Integer(bool $unsigned = false): Schema{
            
            
            if(!$unsigned)
                self::$attrib['field_type'] = " INT,";
            else
                self::$attrib['field_type'] = " INT UNSIGNED ,";   
          
            return $this;
        }

        /**
         * Set attribute as real number
         *
         * 
         * @return Schema
         */
        public function Double(bool $unsigned = false): Schema{
            
            $db_driver = $this->getDatabaseParams()['driver'];

            $modf = null;
            if($unsigned)
                $modf = 'UNSIGNED';

            if($db_driver == 'mysql'){
                self::$attrib['field_type'] = " DOUBLE $modf ,";    
            }elseif ($db_driver == 'sqlite') {
                self::$attrib['field_type'] = " DOUB $modf ,";
            }elseif($db_driver == 'pgsql'){
                self::$attrib['field_type'] = " DOUBLE $modf ,";
            }
            
            return $this;
        }

        
        /**
         * Set attribute as real number
         *
         * 
         * @return Schema
         */
        public function Real(bool $unsigned = false): Schema{
            
            $db_driver = $this->getDatabaseParams()['driver'];

            $modf = null;
            if($unsigned)
                $modf = 'UNSIGNED';

            if($db_driver == 'mysql'){
                self::$attrib['field_type'] = " DOUBLE $modf ,";    
            }elseif ($db_driver == 'sqlite') {
                self::$attrib['field_type'] = " DOUB $modf ,";
            }elseif($db_driver == 'pgsql'){
                self::$attrib['field_type'] = " DOUBLE $modf ,";
            }
            
            return $this;
        }


        /**
         * Set attribute as boolean
         *
         *
         * @return Schema
         */
        public function Boolean(): Schema{
            
            $db_driver = $this->getDatabaseParams()['driver'];

            self::$attrib['field_type'] = " INT(1)  ,";    
          
            return $this;
        }

        /**
         * Set attribute default value
         *
         * @param string $text
         * @return Schema
         */
        public function Default(string $value): Schema{
            
            self::$attrib['field_default'] = rtrim( trim(self::$attrib['field_default']), ',');
            
            self::$attrib['field_default'] .= " DEFAULT '$value'  ,";    
          
            return $this;
        }

        /**
         * Set attribute comment
         *
         * @param string $text
         * @return Schema
         */
        public function Comment(string $comment): Schema{
            
            self::$attrib['field_default'] = rtrim( trim(self::$attrib['field_default']), ',');
           
            
            self::$attrib['field_default'] .= " COMMENT '$comment'  ,";    
          
            return $this;
        }

         /**
         * Set attribute after an attribute
         *
         * @param string $attribute
         * @return Schema
         */
        public function After(string $attribute): Schema{
            

            self::$attrib['field_position'] = " AFTER $attribute  ,";    
          
            return $this;
        }

         /**
         * Set attribute as first
         *
         * 
         * @return Schema
         */
        public function First(): Schema{
            

            self::$attrib['field_position'] = " FIRST,";    
          
            return $this;
        }

        /**
         * Set attribute must not be empty
         *
         * @return Schema
         */
        public function NotNull(): Schema{

            self::$attrib['field_nullable'] = "  NOT NULL,";    
          
            return $this;
        }


        /**
         * Set attribute as year
         *
         * @return Schema
         */
        public function Year(): Schema{
            
            
            self::$attrib['field_type'] = " YEAR ,";
            return $this;
        }


        /**
         * Set attribute as date
         *
         * @return Schema
         */
        public function Date(): Schema{
            
            
            self::$attrib['field_type'] = " DATE ,";
            return $this;
        }

        /**
         * Set attribute as datetime
         *
         * @return Schema
         */
        public function DateTime(): Schema{
            
            
            self::$attrib['field_type'] = " DATETIME() ,";
            return $this;
        }

        /**
         * Set attribute as time
         *
         * @return Schema
         */
        public function Time(): Schema{
            
            
            self::$attrib['field_type'] = " TIME ,";
            return $this;
        }

        /**
         * Set attribute as timestamp
         *
         * @return Schema
         */
        public function Timestamp(): Schema{
            
            
            self::$attrib['field_type'] = " TIMESTAMP ,";
            return $this;
        }

        /**
         * Set attribute as string
         *
         * @param integer $limit
         * @return Schema
         */
        public function String(int $limit = 255): Schema{

            
            self::$attrib['field_type'] = " VARCHAR($limit)  ,";    
            
            return $this;
        }

        /**
         * Set attribute as string
         *
         * @return Schema
         */
        public function Text(): Schema{


            self::$attrib['field_type'] = " TEXT  ,";

            return $this;
        }

        /**
         * Set attribute as Enumeration
         * @param string ...$list
         * @return Schema
         */
        public function Enum(string ...$enums): Schema{
            $e = null;
            foreach( $enums as $enum ){
                $e .= '\''.$enum.'\',';
            }

            $e = rtrim($e, ',');


            self::$attrib['field_type'] = " ENUM ( ".$e." ) ";

            return $this;
        }

        /**
         * Set attribute as character
         *
         * @param integer $limit
         * @return Schema
         */
        public function Char(int $limit = 1): Schema{

          
            self::$attrib['field_type'] = " CHAR($limit)  ,";    
            
            return $this;
        }

        /**
         * Set attribute as binary
         *
         * @param integer $limit
         * @return Schema
         */
        public function Binary(int $limit = 255): Schema{

            
            self::$attrib['field_type'] = " VARBINARY($limit)  ,";    
            
            return $this;
        }

        /**
         * Add Unique index to attribute
         *
         * @return Schema
         */
        public function Unique(): Schema{

            self::$attrib['field_index'] = " UNIQUE,";

            return $this;
        }

        /**
         * Add index to an attribute
         *
         * @param string $name
         * @return Schema
         */
        public function Index(string $name): Schema{

            self::$attrib['field_spindex'] = $name;
            return $this;
        }

        /**
         * Set attribute as primary key
         *
         * @return Schema
         */
        public function Primary(): Schema{

            self::$attrib['field_type'] = rtrim( trim(self::$attrib['field_type']), ',');
           
            self::$attrib['field_index'] = " PRIMARY KEY,";
            return $this;
        }

        /**
         * Set attribute to auto increment on new entries
         *
         * @return Schema
         */
        public function AutoIncrement(): Schema{

            $db = $this->getDatabaseParams();

            self::$attrib['field_type'] = rtrim( trim(self::$attrib['field_type']), ',');
            self::$attrib['field_index'] = rtrim( trim(self::$attrib['field_index']), ',');
            if($db['driver'] == 'sqlite'){
                self::$attrib['field_index'] .= " AUTOINCREMENT ,";
            }else{
                self::$attrib['field_index'] .= " AUTO_INCREMENT ,";
            }
            
            return $this;
        }

        /**
         * Add a foreign key index to attribute
         *
         * @param string $model
         * @param string $attribute
         * @return Schema
         */
        public function Foreign(string $model, string $attribute): Schema{

            $f = self::$attrib['field_name'];
            self::$attrib['field_constraint'] = "CONSTRAINT fk_{$f} FOREIGN KEY ($f) REFERENCES $model($attribute),";
            return $this;
        }



        /**
         * Destroy Attribute of a Schema
         *
         * @param string ...$attribute
         * @return void
         */
        public static function destroy(string ...$attribute){

            try{

                $schema = self::$currentSchema;

                $table = strtolower($schema);

                $db = (new self($schema))->getDatabaseParams();
                $pdohandle = (new Database())->connect($db);

                foreach($attribute as $attrib){
                     $pdohandle->query("ALTER $table DROP COLUMN $attrib");
                }
                return null;

            }catch(\DomainException $t){
                print($t->getMessage()."\n");
            }catch(\PDOException $t){
                print($t->getMessage()."\n");
            }catch(\Throwable $t){
                print($t->getMessage()."\n");
            }

        }

        /**
         * Destroy Schema
         *
         * @param boolean $destroyModel
         * @return void
         */
        public static function destroySchema(bool $destroyModel = true){

            try{
                $schema = self::$currentSchema;

                $table = $schema;

                $db = (new self($schema))->getDatabaseParams();
                $pdohandle = (new Database())->connect($db);

                print("destroying {$schema} schema...\n");

                $pdohandle->query("DROP TABLE IF EXISTS $table");
            
                if($destroyModel){
                    /**
                     * Halt Model Creation Ahead of Time
                     */
                    self::$struct['haltModel'] = true;

                    /**
                     *  Remove model Just in time
                     */
                    $model = ((new self($schema))->getConfig())->getAppName()."/model/{$schema}.php";

                    if(file_exists(getcwd().'/src/'.$model)){
                        print("destroying {$table} model...\n");
                        unlink(getcwd().'/'.$model);
                    
                    }
                }

                return null;

            }catch(\DomainException $t){
                print($t->getMessage()."\n");
            }catch(\PDOException $t){
                print($t->getMessage()."\n");
            }catch(\Throwable $t){
                print($t->getMessage()."\n");
            }
        }

        /**
         * Destroy Index
         *
         * @param string $name
         * @return void
         */
        public static function destroyIndex(string $name){

            try{
                $schema = self::$currentSchema;

                $table = strtolower($schema);

                $db = (new self($schema))->getDatabaseParams();
                $pdohandle = (new Database())->connect($db);

                print("destroying index on {$table} schema...\n");

                $db_driver = $db['driver'];

                if($db_driver == 'mysql'){
                    $pdohandle->query("ALTER {$table} DROP INDEX zdx_{$name}");
                }else if($db_driver == 'oracle'){
                    $pdohandle->query("DROP INDEX zdx_{$name}");
                }else if($db_driver == 'mssql'){
                    $pdohandle->query("DROP INDEX {$table}.zdx_{$name}");
                }

                
                return null;

            }catch(\DomainException $t){
                print($t->getMessage()."\n");
            }catch(\PDOException $t){
                print($t->getMessage()."\n");
            }catch(\Throwable $t){
                print($t->getMessage()."\n");
            }
        }


        /**
         * Migrate all db struct from stub(migration) and create(optional?) corresponding models
         *
         * @param Info $Info
         * @param boolean $save
         * @param boolean $rollback
         * @param boolean $all
         * @param string|null $migration_pointer
         * @return void
         */
        public static function migrate(Info $Info, bool $save = false, bool $rollback = false, bool $all = false, ?string $migration_pointer){
            try{

                /**
                 * App Meta Information 
                 */

                if( ($Info->getAppBase() !== null) && ($Info->getAppName() !== null) ){

                    $app_name   =   $Info->getAppName();
                    $app_base   =   $Info->getAppBase();

                    /**
                     * Extract App Tree
                     */
                    $tree = (new Info())->getTree();

                }else{
                    throw new \Exception("Error: Couldn't resolve app directories");
                }

                
                /**
                 *  Load Migrations
                 */
              
                if($all){
                    
                    $migration_files = scandir("{$app_base}/migration");
                    
                }else if(!is_null($migration_pointer)){

                    $migration_pointer = str_replace('.php', '', $migration_pointer).'.php';

                    $migration_files = [$migration_pointer];

                    if(!file_exists("{$app_base}/migration/$migration_pointer"))
                            throw new \DomainException("{$migration_pointer} not found in {$app_base}  migrations");

                    
                }else{
                    print("Undefined migration, specify a migration or use --all flag\n");

                }


                /**
                 * Iterate through migrations and generate appropriate models and schema
                 */
                foreach($migration_files as $migration_file){
                    
                    /**
                     * Migration Tag
                     */
                    $migration = str_replace('.php', '', $migration_file);
                    

                    /**
                     * Only process php file
                     */
                    $migration_file = "{$app_base}/migration/{$migration_file}";

                    if( is_file($migration_file) && pathinfo($migration_file, PATHINFO_EXTENSION) == 'php' ){

                        /**
                         * Skip executed migrations
                         */
                        if( isset($tree->apps->{$app_name}->exe_migration->{$migration}) ){
                            print("[SKIPPED] {$migration} migration\n");
                            continue;
                        }

                        include_once($migration_file);
                        $migration_content = file_get_contents($migration_file);

                        /**
                         * Extract Migration class
                         */
                        preg_match("/class[\s]+[\w]+/", $migration_content, $matches);

                            /** Migration class name is extracted*/
                            $m = preg_replace(
                                ['/[\s]+/','/class/'],
                                ['', ''],
                                $matches[0]
                            );

                            print("Migrating $m ...\n");

                            /**
                             * Normalize Migration class name
                             */
                            $mc = "src\\$app_name\migration\\".ucfirst($m);


                            /**
                             * Run Schema builds from Migration class.
                             * Schema is contained with the migration context
                             * Last build wont be reached due to late compilation such that 'n' build compiles at 'n+1' build stage
                             */
                            (new $mc())->set();

                            /**
                             * Run last build, The last build is part-of set operation
                             * */
                            (new self(self::$struct['table']))->buildLastString();

                            /**
                             * Run Schema destructives from Migration class
                             * Destructives are compiled Just-in-time
                             */
                            (new $mc())->drop();


                            /**
                             * Get Model Buffering Point
                             */
                            $application_buffering_point = $Info->getReadPoint();

                            if( !file_exists("{$application_buffering_point}model/Model.php"))
                                throw new \DomainException("Error: couldn't load template model");

                            $context = file_get_contents("{$application_buffering_point}model/Model.php", 'rb');

                            /** Get the model name
                             *  Find the model file
                             */
                            $model = self::$currentSchema;
                            if( file_exists("{$app_base}/model/{$model}.php")){
                               
                                $context = file_get_contents("{$app_base}/model/{$model}.php", 'rb');
                                preg_match_all("/(public|private|protected)[\s]+function[\s]+[\w]+[\s\S]+/", $context, $m);
                                
                                /**
                                 * Update Associate Model
                                 */
                                if(sizeof($m[0]) > 0)
                                    $context = preg_replace("/\#METHODS/", $m[0][0], $context);
        
                            }

                            /**
                             * Rewrite Model
                             */
                            $context = preg_replace(["/\#METHODS/", "/(\?>)+([\s\S]+)/"], [null, '?>' ], $context);
        


                            /** HaltModel flag are raised when scheme destructives exists and ran, else only schema builds
                             * were present, thus HaltModel flag not raised
                             */

                            if(!self::$struct['haltModel']){
                                
                                /**
                                 * Retrieve current column names of this schema
                                 */
                                $table = self::$struct['table'];


                                $db = (new self($table))->getDatabaseParams();
                                $pdohandle = (new Database())->connect($db);

                                
                                if($db['driver'] == 'mysql' || $db['driver'] == 'pgsql' || $db['driver'] == 'mssql'){
        
                                    $rs = $pdohandle->query("SELECT COLUMN_NAME FROM  INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='{$db['database']}' AND TABLE_NAME = '{$table}'");
                                }else if($db['driver'] == 'sqlite'){
                                    
                                    $rs = $pdohandle->query("SELECT name FROM pragma_table_info('$table')");
                                }else if($db['driver'] == 'oracle'){
                                
                                    $rs = $pdohandle->query("SELECT COULUMN_NAME FROM  ALL_TAB_COLUMNS WHERE TABLE_NAME = '{$table}'");
                                }

                                $ModelAttribute = [];
                                if($rs->rowCount() > 0){
                                    while( list($col) = $rs->fetch() ){
                                        array_push($ModelAttribute, $col);
                                    }
                                }
                                (new Model())->scaffold($Info, self::$struct['table'], $context, $ModelAttribute );

                            }
                            /**
                             * Reset Schema states
                             */
                            self::$currentSchema = null;
                            self::$struct = [ 'table'=>'', 'haltModel'=>false ];
                            self::$attrib = [];
                            $ModelAttribute = null;

                            /**
                             * Update Migration history
                             */

                            if(is_array($tree->apps->{$app_name}->exe_migration) )
                                $tree->apps->{$app_name}->exe_migration[$migration] = 'migrated';
                            else
                                $tree->apps->{$app_name}->exe_migration->{$migration} = 'migrated';


                            print("Migrated $mc\n\n");
                            unset($context);

                    }

                    if($save){

                        copy("{$app_base}/migration/{$migration_file}", "{$app_base}/migration/recent/{$migration_file}.php");
    
                    }
                    
                  
                }
                
         
                

            }catch(\PDOException $t){
                print($t->getMessage().' on line '.$t->getLine().' ('.$t->getFile().")\n");
            }catch(\DomainException $t){
                print($t->getMessage().' on line '.$t->getLine().' ('.$t->getFile().")\n");
            }catch(\LogicException $t){
                print($t->getMessage().' on line '.$t->getLine().' ('.$t->getFile().")\n");
            }catch(\Throwable $t){
                print($t->getMessage().' on line '.$t->getLine().' ('.$t->getFile().")\n");
            } finally{

                if(!is_null($tree))
                    (new Tree())->updateTree($Info, $tree);

                print("Migration closed");
            }
        }
        
    }
?>