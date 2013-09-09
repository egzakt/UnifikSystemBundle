<?php

namespace Egzakt\SystemBundle\Lib;

use Doctrine\Common\Collections\ArrayCollection;

class DeletableResponse
{

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $message;

    /**
     * @var ArrayCollection
     */
    private $errors;

    const STATUS_FAIL = 'fail';
    const STATUS_SUCCESS = 'success';

    public function __construct($status, $message, ArrayCollection $errors = null)
    {
        $this->status = $status;
        $this->message = $message;
        if ( null == $errors ) {
            $errors = new ArrayCollection();
        }
        $this->errors = $errors;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return ArrayCollection
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $output =  array(
          'status' => $this->getStatus(),
          'message' => $this->getMessage(),
        );
        if ( $this->isFail() ) {
            $output['errors'] = $this->getErrors()->toArray();
        }

        return $output;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->getStatus() == DeletableResponse::STATUS_SUCCESS;
    }

    /**
     * @return bool
     */
    public function isFail()
    {
        return $this->getStatus() == DeletableResponse::STATUS_FAIL;
    }

}
