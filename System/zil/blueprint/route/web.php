<?php
namespace src\route;

use \zil\core\interfaces\Route;
use \zil\core\server\Resource;
/**
 *   Web Routes
 */

class Web implements Route{

	use \zil\core\facades\decorators\Route_D1;

		/**
		 * Web routes, routes from browser
		 *
		 * @return array
		 */
		public function route(): array{

			return [
				'get' 	=> 	$this->get(),
				'post'	=> 	$this->post()
			];

		}


		private function get(): array{

			return [];
		}

		private function post(): array{

			return [];
		}
	
}
	
?>