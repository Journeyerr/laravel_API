<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class VerificationCodesController extends Controller
{
    /**
     * @param array $middleware
     */
    public function store()
    {
        return $this->response->array(['test_message' => 'store verification code']);
    }
}
