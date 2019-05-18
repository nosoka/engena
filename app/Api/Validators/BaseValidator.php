<?php

namespace App\Api\Validators;

use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Routing\Helpers;

class BaseValidator
{
    use Helpers;

    /**
     * Data to validate
     * @var array
     */
    protected $data = [];

    /**
     * Validation Errors
     * @var \Illuminate\Support\MessageBag
     */
    protected $errors = [];

    /**
     * Validation rules
     * @var array
     */
    protected $rules = [];

    /**
     * custom message
     * @var array
     */
    protected $message = "Validation failed. Please fix the errors and retry.";

    public function __construct()
    {
        $this->request   = app('request');
        $this->validator = app('validator');
        $this->validate();
    }

    public function validate()
    {
        $data      = $this->data() ?: $this->request->all();
        $validator = $this->validator->make($data, $this->rules(), $this->errors());

        if ($validator->fails()) {
            throw new ResourceException($this->message(), $validator->errors());
        }

        return $this;
    }

    public function data()
    {
        return $this->data;
    }

    public function rules()
    {
        return $this->rules;
    }

    public function errors()
    {
        return $this->errors;
    }

    public function message()
    {
        return $this->message;
    }
}
