<?php

namespace DTApi\Repository;

use Validator;
use Illuminate\Database\Eloquent\Model;
use DTApi\Exceptions\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BaseRepository
{
    protected $model;
    protected $validationRules = [];

    public function __construct(Model $model = null)
    {
        $this->model = $model;
    }

    public function validatorAttributeNames()
    {
        return [];
    }

    public function getModel()
    {
        return $this->model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function with($array)
    {
        return $this->model->with($array);
    }

    public function findOrFail($id)
    {
        return $this->model->findOrFail($id);
    }

    public function findBySlug($slug)
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function query()
    {
        return $this->model->query();
    }

    public function instance(array $attributes = [])
    {
        return new $this->model($attributes);
    }

    public function paginate($perPage = null)
    {
        return $this->model->paginate($perPage);
    }

    public function where($key, $where)
    {
        return $this->model->where($key, $where);
    }

    public function validator(array $data = [], $rules = null, array $messages = [], array $customAttributes = [])
    {
        $rules = $rules ?? $this->validationRules;

        return Validator::make($data, $rules, $messages, $customAttributes);
    }

    public function validate(array $data = [], $rules = null, array $messages = [], array $customAttributes = [])
    {
        $validator = $this->validator($data, $rules, $messages, $customAttributes);
        
        if (!$this->_validate($validator)) {
            throw new ValidationException($validator);
        }
        
        return true;
    }

    public function create(array $data = [])
    {
        return $this->model->create($data);
    }

    public function update($id, array $data = [])
    {
        $instance = $this->findOrFail($id);
        $instance->update($data);
        
        return $instance;
    }

    public function delete($id)
    {
        $model = $this->findOrFail($id);
        $model->delete();
        
        return $model;
    }

    protected function _validate(\Illuminate\Validation\Validator $validator)
    {
        $attributeNames = $this->validatorAttributeNames();
        
        if (!empty($attributeNames)) {
            $validator->setAttributeNames($attributeNames);
        }

        return $validator->fails() ? false : true;
    }
}
