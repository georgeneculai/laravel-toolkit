<?php
namespace Gnx\LaravelToolkit\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Exception\HttpResponseException;
use Response;
use Schema;

use Gnx\LaravelToolkit\Utils\ResponseManager;

class AppBaseController extends BaseController
{
    protected $apiModel = '';
    protected $apiModelInstance = null;

    public function __construct() {
        if (!$this->apiModel || !class_exists($this->apiModel)) {
            throw new HttpResponseException(Response::json(ResponseManager::error('The API model is not set or does not exist')));
        }
    }

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

    /**
     * Get the class name of the API model
     *
     * @return string
     */
    public function getApiModel() {
        return $this->apiModel;
    }

    /**
     * Get an instance of the API model
     *
     * @return \Gnx\LaravelToolkit\Models\Revisionable
     */
    public function getApiModelInstance() {
        if (!$this->apiModelInstance) {
            $model = $this->apiModel;
            $this->apiModelInstance = new $model;
        }

        return $this->apiModelInstance;
    }

    /**
     * Get a listing of the records.
     *
     * @return Response
     */
    public function index(Request $request) {
        $model = $this->getApiModelInstance();
        $modelClass = $this->getApiModel();

        $query = call_user_func(array($modelClass, 'query'));

        $columns = Schema::getColumnListing($model->getTable());

        foreach($columns as $column) {
            if($request->has($column)) {
                $query->where($column, $request->get($column));
            }
        }

        $result = $query->get();

        return Response::json(ResponseManager::result($result->toArray(), "List retrieved successfully."));
    }

    /**
     * Show the form for creating a new record.
     *
     * @return Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created record in storage.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function store(Request $request) {
        $modelClass = $this->getApiModel();
        $rules = $modelClass::$rules;
        if(sizeof($rules) > 0) {
            $this->validateRequest($request, $rules);
        }

        $input = $request->all();

        $record = call_user_func_array(array($modelClass, 'create'), array($input));

        return Response::json(ResponseManager::result($record->toArray(), "Record saved successfully."));
    }

    /**
     * Display the specified record.
     *
     * @param  int  $id
     * @param Request $request
     *
     * @return Response
     */
    public function show($id) {
        $modelClass = $this->getApiModel();
        $record = call_user_func_array(array($modelClass, 'find'), array($id));

        if(empty($record)) {
            $this->throwRecordNotFoundException("Record not found");
        }

        return Response::json(ResponseManager::result($record->toArray(), "Record retrieved successfully."));
    }

    /**
     * Show the form for editing the specified record.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified record in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request) {
        $modelClass = $this->getApiModel();
        $record = call_user_func_array(array($modelClass, 'find'), array($id));

        if(empty($record)) {
            $this->throwRecordNotFoundException("Record not found");
        }

        $rules = $modelClass::$rules;

        if(sizeof($rules) > 0) {
            $this->validateRequest($request, $rules);
        }

        $input = $request->all();

        $record->fill($input);
        $record->save();

        return Response::json(ResponseManager::result($record->toArray(), "Record updated successfully."));
    }

    /**
     * Remove the specified record from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        $modelClass = $this->getApiModel();
        $record = call_user_func_array(array($modelClass, 'find'), array($id));

        if(empty($record)) {
            $this->throwRecordNotFoundException("Record not found");
        }

        $record->delete();

        return Response::json(ResponseManager::result($id, "Record deleted successfully."));
    }
}