<?php
    namespace src\migration;


    use zil\factory\Schema;
    use zil\core\interfaces\Migration;
	
	class User implements Migration{

        /**
         * Attributes to be created
         *
         * @return void
         */
        public function set(){

            /**
             * New Schema or Connect to existing schema
             */
                
            $schema = new Schema('user');

            $schema->build('id')->Primary()->Integer()->AutoIncrement();
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