<?php
namespace src\memoboard\controller\api;

	use mysql_xdevapi\Exception;
    use src\memoboard\controller\Home;
    use src\memoboard\model\Memo;
    use src\memoboard\model\Memotemplate;
    use src\memoboard\model\Users;
    use src\memoboard\service\UserEnumRoles;
    use \zil\core\server\Param;
	use \zil\core\server\Response;

	use \zil\core\facades\helpers\Notifier;
	use \zil\core\facades\helpers\Navigator;


    use zil\core\tracer\ErrorTracer;
    use zil\factory\Session;
    use zil\security\Authentication;
    use zil\security\Encryption;
    use zil\security\Validation;


    class FormProcessor{

		use Notifier, Navigator;

		
		public function __construct(){
			
		}

		public function CreateUser(Param $param){
            try {
                $Validation = new Validation(['username', 'required'], ['password', 'required'], ['fullname', 'required']);

                if ($Validation->isPassed()) {

                    $n = new Users();

                    $n->name = strip_tags($param->form()->fullname);
                    $n->username = strip_tags($param->form()->username);
                    $n->password = strip_tags($param->form()->password);
                    $n->role = (new UserEnumRoles())->get()['HOD'];
                    $n->signature_path = (new Encryption())->authKey();

                    if($n->notExist($n->username, $n->name)){
                        if( $n->create() ) {
                            $data = ['msg' => "User Added", 'status' => true];

                        }else{
                            throw new \Exception("Operation failed! Retry");
                        }
                    }else{
                        throw new \Exception("Username or name already exist");
                    }

                } else {
                    throw new \Exception("Some field(s) are missing");
                }
            }catch(\Throwable $t){
                $data = $data = [ 'msg' => $t->getMessage(), 'status' => false ];
            }finally{
                echo Response::fromApi( $data, 200 );
            }

        }

        public function CreateMemo(Param $param){
            try {
                $Validation = new Validation(['itoID', 'required'], ['ref', 'required'], ['subject', 'required'], ['body', 'required']);

                if ($Validation->isPassed()) {

                    $m = new Memo();

                    $m->ifrom_ID = (new Users())->getUserId();
                    $m->ito_ID = $param->form()->itoID;
                    $m->subject = $param->form()->subject;
                    $m->message = $param->form()->body;
                    $m->ref = $param->form()->ref;


//                    if($m->messageNotExist($m->subject)){

                    if( $m->create() ) {
                            $data = ['msg' => "Memo Sent", 'status' => true];

                        }else{
                            throw new \Exception("Operation failed! Retry");
                        }

//                    }else{
//                        throw new \Exception("You already sent  exist");
//                    }

                } else {
                    throw new \Exception("Some field(s) are missing");
                }
            }catch(\Throwable $t){
                $data = $data = [ 'msg' => $t->getMessage(), 'status' => false ];
            }finally{
                echo Response::fromApi( $data, 200 );
            }

        }


        public function CreateMemoTemplate(Param $param){

            try {
                $Validation = new Validation(['templateName', 'required'], ['templateString', 'required']);

                if ($Validation->isPassed()) {

                    $m = new Memotemplate();

                    if(!$m->templateNameExists($param->form()->templateName)) {


                        $CMT = $m->createTemplate($param->form()->templateName, $param->form()->templateString);


                        if ($CMT) {
                            $data = ['msg' => "Template Created", 'status' => true];

                        } else {
                            throw new \Exception("Operation failed! Retry");
                        }

                    }else{
                        throw new \Exception("Template name already exist, choose another one");
                    }
                } else {
                    throw new \Exception("Some field(s) are missing");
                }
            }catch(\Throwable $t){
                $data = $data = [ 'msg' => $t->getMessage(), 'status' => false ];
            }finally{
                echo Response::fromApi( $data, 200 );
            }

        }

        public function ChangePassword(Param $param) {

		    try{
		        $Validation = new Validation( ['OldPass', 'required'], ['NewPass', 'required'], ['ConfirmNewPass', 'required'] );

		        if($Validation->isPassed()){

		            $U = new Users();

		            if( $U->isPasswordCorrect($param->form()->OldPass) ){
                        if( $param->form()->NewPass == $param->form()->ConfirmNewPass ){

                            if( $U->updatePassword($param->form()->NewPass) ){

                                $this->notification('Login to continue')->send(null);
                                Authentication::Destroy();
                                Session::delete('username');
                                $this->goTo('login');

                            }else{
                                throw new \Exception("Operation failed! Couldn't change password");
                            }
                        }else{
                            throw new \Exception("New Password doesn't match");
                        }
                    }else{
                        throw new \Exception("Password incorrect");
                    }
                }else{
		            throw new \Exception("Some fields are missing");
                }
            }catch (\Throwable $t){

                $this->clear()->notification($t->getMessage())->send('ERROR');
                $this->goBack();
            }
        }

    }

?>