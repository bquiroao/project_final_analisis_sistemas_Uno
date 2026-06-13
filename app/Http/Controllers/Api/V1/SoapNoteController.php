<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SoapNote;
use Illuminate\Http\Request;

class SoapNoteController extends Controller
{
    public function store(Request $request)
    {
        $soapNote = SoapNote::create([
            'patient_name' => $request->patient_name,
            'subjective' => $request->subjective,
            'objective' => $request->objective,
            'assessment' => $request->assessment,
            'plan' => $request->plan,
        ]);

        return response()->json([
            'message' => 'SOAP note created successfully',
            'data' => $soapNote
        ], 201);
    }
    public function index()
{
    return response()->json(
        SoapNote::all()
    );
}

public function show($id)
{
    return response()->json(
        SoapNote::findOrFail($id)
    );
}
}
