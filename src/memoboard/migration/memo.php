<?php
namespace src\memoboard\migration;


    use zil\factory\Schema;
    use zil\core\interfaces\Migration;
	
	class Memo implements Migration{
        /**
         * Attributes to be created
         *
         * @return void
         */
        public function set(){

            /**
             * New Schema or Connect to existing schema
             */
                
			$schema = new Schema('Memo');
            $schema->build('id')->Primary()->Integer()->AutoIncrement();
            $schema->build('ifrom_ID')->Integer()->Foreign('users', 'id');
            $schema->build('ito_ID')->Integer()->Foreign('users', 'id');
            $schema->build('message')->String();
            $schema->build('user_is_read_receipt')->Boolean()->Default(0);

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