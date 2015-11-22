<?php
namespace Gnx\LaravelToolkit\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Exception\HttpResponseException;
use Gnx\LaravelToolkit\Utils\ResponseManager;
use Response;

class AppBaseController extends Controller
{
	public function validateRequest($request, $rules) {
		$validator = $this->getValidationFactory()->make($request->all(), $rules);

		if($validator->fails()) {
            $message = array();

			foreach($validator->errors()->getMessages() as $field => $errorMsg) {
                $message[] = $errorMsg[0];
			}

            $message = implode('|', $message);

			throw new HttpResponseException(Response::json(ResponseManager::error($message)));
		}

        return parent::validateRequest($request, $rules);
	}

	public function throwRecordNotFoundException($message)
	{
		throw new HttpResponseException(Response::json(ResponseManager::error($message)));
	}
}