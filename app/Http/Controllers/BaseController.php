<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    public function sendResponse($result, $message) : JsonResponse{
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message,
            ];
        return response()->json($response, 200);
    }

    public function sendError($error, $errorMessage=[], $code = 400): JsonResponse{
        $response = [
            'success' => false,
            'data' => $error,
            'message' => $errorMessage,
        ];
        if(!empty($errorMessage)){
            $response['data'] = $errorMessage;
        };
                
        return response()->json($response, $code);
    }
        
}
