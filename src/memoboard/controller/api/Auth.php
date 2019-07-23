<?php
namespace src\memoboard\controller\api;

	use mysql_xdevapi\Exception;
    use src\memoboard\model\Users;
    use \zil\core\server\Param;
	use \zil\core\server\Response;
	use \zil\core\facades\helpers\Notifier;
	use \zil\core\facades\helpers\Navigator;

	use src\memoboard\config\Config;
    use zil\factory\Session;
    use zil\security\Authentication;
    use zil\security\Encryption;
    use zil\security\Validation;


    class Auth{

		use Notifier, Navigator;

		
		public function __construct(){
			
		}

		public function LoginUser(Param $param)
        {
            try {
                $Validation = new Validation(['username', 'required'], ['password', 'required']);

                if ($Validation->isPassed()) {

                    if( (new Users())->validate($param->form()->username, $param->form()->password) === true){

                        $AuthCert = (new Encryption())->authKey();
                        (new Session())->build('username', $param->form()->username)->build('AUTH_CERT', $AuthCert, true);
                        $data = ['msg' => "Login Successful", 'status' => true];

                    }else{
                            throw new \Exception("Login Credentials Invalid");
                        }

                } else {
                    throw new \Exception("Login failed! some field(s) are missing");
                }
            }catch(\Throwable $t){
                $data = $data = [ 'msg' => $t->getMessage(), 'status' => false ];
            }finally{
                echo Response::fromApi( $data, 200 );
            }
		}
				
	}

?>