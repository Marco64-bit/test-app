<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getExternalLinks()
    {
        // Return the links requested in the assignment
        return response()->json([
            'message' => 'External links retrieved successfully.',
            'links' => [
                [
                    'title' => 'Elsewedy University',
                    'url' => 'https://sut.edu.eg/'
                ],
                [
                    'title' => 'Google',
                    'url' => 'https://www.google.com/'
                ]
            ]
        ]);
    }
}
