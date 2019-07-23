<?php
namespace src\memoboard\service;

	use \zil\factory\Database;
	use \zil\factory\BuildQuery;
	use \zil\factory\Session;
	use \zil\factory\Fileuploader;
	use \zil\factory\Filehandler;
	use \zil\factory\Logger;
	use \zil\factory\Mailer;
	use \zil\factory\Redirect;
	
	use \zil\security\Authentication;
	use \zil\security\Encryption;
	use \zil\security\Sanitize;



	use src\memoboard\config\Config;
	
	class UserEnumRoles{

		public function __construct(){

        }

        public function get(): array{

            return [
                'DIRECTOR' => 'DIRECTOR',
                'HOD' => 'HOD'
            ];
        }
	} 

?>