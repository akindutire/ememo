<?php
namespace src\memoboard\service;

    use \zil\core\interfaces\Guard as GuardInterface;
    use \zil\core\interfaces\Param;
    use zil\factory\Redirect;
    use \zil\factory\Session;

	
	class GuardL implements GuardInterface {

        public function __construct(){ }

        public function validate(Param $p): \Closure
        {

            if (!is_null(Session::get('username')) && strlen(Session::getEncoded('AUTH_CERT')) > 64) {

                    return function () {
                    };
                } else {

                    return function () {
                        new Redirect('login');
                    };

                }

        }
	} 

?>
