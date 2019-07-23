<?php
namespace src\memoboard\service;

	use src\memoboard\model\Users;
    use src\model\User;
    use zil\core\tracer\ErrorTracer;


    class UserInfoScrapper{

		public function __construct(){
            /** Assign digital signature if doesn't exist*/

            (new Users())->checkAndAssignDigitalSignature();
        }

        public function scrap() :array {
            return [
                'Role' => (new Users())->getRole()
            ];
        }

        public function getDetails():object {
		    try {
                return (new Users())->getDetails();
            }catch (\Throwable $t){
		        new ErrorTracer($t);
            }
        }

        public function getAnyUser(int $user_id) : object {
		    try{
		        return (new Users())->getAnyUser($user_id);
            }catch (\Throwable $t){
		        new ErrorTracer($t);
            }
        }
	} 

?>