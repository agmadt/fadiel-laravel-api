<?php

namespace App\Exceptions;

use Exception;

class NotFoundApiException extends Exception
{
    public $message;

    public function __construct($message = 'Not found')
    {
        $this->message = $message;
    }

    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        //
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()->json([
            'message' => $this->message
        ], 404);
    }
}
