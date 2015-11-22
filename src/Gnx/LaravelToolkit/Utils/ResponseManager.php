<?php
namespace Gnx\LaravelToolkit\Utils;

/**
 *
 * ResponseManager class to generate response for result or error in following pattern
 *
 *      success - true / false
 *      message - string message
 *      data - it can be anything
 *
 * Class ResponseManager
 *
 */
class ResponseManager
{
	/**
	 * Generates result response object
	 *
	 * @param mixed  $data
	 * @param string $message
	 *
	 * @return array
	 */
	public static function result($data, $message) {
		$result = array();
		$result['success'] = true;
		$result['message'] = $message;
		$result['data'] = $data;

		return $result;
	}

	/**
	 * Generates error response object
	 *
	 * @param int    $errorCode
	 * @param string $message
	 * @param mixed  $data
	 *
	 * @return array
	 */
	public static function error($message, $data = array()) {
		$error = array();
		$error['success'] = false;
		$error['message'] = $message;
		if(!empty($data))
			$error['data'] = $data;

		return $error;
	}
}