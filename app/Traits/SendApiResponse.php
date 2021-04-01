<?php

namespace App\Traits;

trait SendApiResponse {
    public function sendApiResponse ($status, $message, $data = null, $errors = null) {
        return response()->json([
            'message' => $message,
            'data' => $data,
            'errors' => $errors
        ], $status);
    }
}