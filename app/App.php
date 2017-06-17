<?php 


class App {

	static function bootstrap(){
		
		Config::bootstrap();

		DB::bootstrap();

		Router::bootstrap();

		Session::bootstrap();
		
	}
}