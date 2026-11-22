<?php
class HttpException extends Exception
{
    protected $status = 500;

    public function get_status()
    {
        return $this->status;
    }
}

class ValidationException extends HttpException { protected $status = 400; }

class UnauthorizedException extends HttpException { protected $status = 401; }

class ForbiddenException extends HttpException { protected $status = 403; }

class NotFoundException extends HttpException { protected $status = 404; }

class ExternalServiceException extends HttpException { protected $status = 503; }
