<?php

namespace Biltorvet\Model;

class ApiResponse
{
    /**
     * @var int
     */
    private $status;

    /**
     * @var array
     */
    private $errors;

    /**
     * @var mixed
     */
    private $result;

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param  int $status
     * @return ApiResponse
     */
    public function setStatus(int $status): ApiResponse
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param  array $errors
     * @return ApiResponse
     */
    public function setErrors(array $errors): ApiResponse
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param  mixed $result
     * @return ApiResponse
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }
}
