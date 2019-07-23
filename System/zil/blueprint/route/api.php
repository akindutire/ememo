<?php
namespace src\route;

use \zil\core\interfaces\Route;
use \zil\core\server\Resource;

/**
 *   Api Routes
 */

class Api implements Route{

	use \zil\core\facades\decorators\Route_D1;

		/**
		 * Api routes
		 *
		 * @return array
		 */
		public function route(): array{
			
			return [
				'get' 	=> 	$this->get(),
				'post'	=> 	$this->post(),
				'put' 	=> 	$this->put(),
				'delete'=>	$this->delete()
			];
		}

		private function get():array{

				return [];
		}

		private function post():array{

			return [];
		}
		
		private function put():array{

			return [];
		}
    
		private function delete():array{

			return [];
		}
	
}
	
?>