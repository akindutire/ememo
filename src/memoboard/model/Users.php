<?php
namespace src\memoboard\model;
    use src\config\Config;
    use zil\core\tracer\ErrorTracer;
    use zil\factory\Logger;
    use \zil\factory\Model;
    use zil\factory\Session;
    use zil\security\Encryption;

    class Users
    {

        use Model;

        public $id = null;
        public $name = null;
        public $signature_path = null;
        public $role = null;
        public $username = null;
        public $password = null;
        public $created_at = null;


        public static $table = 'users';
        public static $key = '';

        private function baseCondition() : array {
            return ['username', Session::get('username')];
        }

        public function sendMemo(string $from, string $to, string $message): bool
        {

        }

        public function readMemo(int $memoID): void
        {

        }

        public function validate(string $username, string $password) : bool {

            if( $this->filter('id')->where( ['username', $username], ['password', $password] )->count() == 1 )
                return true;
            else
                return false;
        }

        public function isPasswordCorrect(string $password) : bool {
            try{

                if( $this->where( ['password', $password], ['id', $this->getUserId() ] )->count() == 1 )
                    return true;
                else
                    return false;

            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }

        public function updatePassword(string $new_pass) : bool {
            try{
                $this->password = $new_pass;
                $AffectedRows = $this->where( ['id', $this->getUserId()] )->update();

                if($AffectedRows == 1)
                    return true;
                else
                    return false;

            }catch (\Throwable $t){
                new ErrorTracer($t);
            }
        }

        public function notExist(string $username, string $fname) : bool{
            try{

                if( $this->filter('id')->where( ['username', $username, 'OR'], ['name', $fname] )->count() == 0 )
                    return true;
                else
                    return false;

            }catch(\throwable $t){
                new ErrorTracer($t);
            }
        }
        public function getRole(): string
        {
            try {

                $role = $this->filter('role')->where(['username', Session::get('username')])->get()->role;
                return $role;

            } catch (\Throwable $t) {
                new ErrorTracer($t);
            }
        }

        public function getUserId(): int
        {
            try {

                $id = $this->filter('id')->where( $this->baseCondition() )->get()->id;
                return $id;

            } catch (\Throwable $t) {
                new ErrorTracer($t);
            }
        }

        /**
         * @param int $userId
         * @return object
         */
        public function getAnyUser(int $userId) : object{
            try{
                return $this->all()->where( ['id', $userId] )->first();
            }catch (\Throwable $t){
                new ErrorTracer($t);
            }
        }

        /**
         * Get Details of Currently Active User
         * @return object
         */
        public function getDetails(): object {
            try{

                return $this->all()->where( $this->baseCondition() )->first();

            }  catch (\Throwable $t){
                new ErrorTracer($t);
            }
        }

        public function checkAndAssignDigitalSignature(): bool
        {
            try {

                $dsp = $this->filter('signature_path')->where(['username', Session::get('username')])->get()->signature_path;
                if (empty($dsp) || is_null($dsp) || strlen($dsp) < 64) {
                    $signature = (new Encryption())->authKey();
                    $this->signature_path = $signature;

                    if ($this->update() == 1)
                        return true;
                    else
                        throw new \Exception("Unable to verify your signature, kindly refresh your browser tab");
                } else {
                    return true;
                }

            } catch (\Throwable $t) {

            }
        }

        public function getAllUsersNameWithId(): array {
            try {

                $all = $this->filter('id', 'name')->where( ['username', '<>', Session::get('username')] )->get('VERBOSE');

                return $all;

            } catch (\Throwable $t){
                new ErrorTracer($t);
            }
        }
	}
?>