<?php

namespace App\Http\Controllers;

use App\Models\Evolution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;
class EvolutionController extends Controller
{

    public function downloadPdf(Evolution $record): \Illuminate\Http\Response
    {
        $htmlContent = $record->ai_suggestion;

        return Pdf::loadHTML($htmlContent)->download('sugestao-da-ia.pdf');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Evolution $evolution)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Evolution $evolution)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Evolution $evolution)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Evolution $evolution)
    {
        //
    }
}
