<?php
namespace src\memoboard\migration;


    use zil\factory\Schema;
    use zil\core\interfaces\Migration;
	
	class Users implements Migration{
        /**
         * Attributes to be created
         *
         * @return void
         */
        public function set(){

            /**
             * New Schema or Connect to existing schema
             */
                
			$schema = new Schema('Users');
            $schema->build('id')->Primary()->Integer()->AutoIncrement();
            $schema->build('role')->String()->NotNull();
            $schema->build('username')->String()->Unique()->NotNull();
            $schema->build('password')->String();
            $schema->build('created_at')->Timestamp();

            
        }

        /**
         * Attributes to be dropped
         *
         * @return void
         */
        public function drop(){
          

        }

      

    }
   

?>