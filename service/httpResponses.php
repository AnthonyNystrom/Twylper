<?php

        /**
	* Send a HTTP 200 response header.
	*/
	function ok($url = FALSE) {
		header('HTTP/1.0 200 OK');
		if ($url) {
			header('Location: '.$url);   
		}
	}
	
	/**
	 * Send a HTTP 201 response header.
	 */
	function created($url = FALSE) {
		header('HTTP/1.0 201 Created');
		if ($url) {
			header('Location: '.$url);   
		}
	}
	
	/**
	 * Send a HTTP 204 response header.
	 */
	function noContent() {
		header('HTTP/1.0 204 No Content');
	}
	
	/**
	 * Send a HTTP 400 response header.
	 */
	function badRequest() {
		header('HTTP/1.0 400 Bad Request');
	}
	
	/**
	 * Send a HTTP 401 response header.
	 */
	function unauthorized($realm = 'Twitreply') {
		header('WWW-Authenticate: Basic realm="'.$realm.'"');
		header('HTTP/1.0 401 Unauthorized');
	}
	
	/**
	 * Send a HTTP 404 response header.
	 */
	function notFound() {
		header('HTTP/1.0 404 Not Found');
	}
	
	/**
	 * Send a HTTP 405 response header.
	 */
	function methodNotAllowed($allowed = 'GET, HEAD') {
		header('HTTP/1.0 405 Method Not Allowed');
		header('Allow: '.$allowed);
	}
	
	/**
	 * Send a HTTP 406 response header.
	 */
	function notAcceptable() {
		header('HTTP/1.0 406 Not Acceptable');
	}
	
	/**
	 * Send a HTTP 411 response header.
	 */
	function lengthRequired() {
		header('HTTP/1.0 411 Length Required');
	}
	
	/**
	 * Send a HTTP 500 response header.
	 */
	function internalServerError() {
		header('HTTP/1.0 500 Internal Server Error');
	}
	
?>