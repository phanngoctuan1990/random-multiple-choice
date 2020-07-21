<?php

namespace App\Http\Controllers;

use App\Services\GenerateDocumentService;
use App\Http\Requests\GenerateMultipleChoicesRequest;

class GenerateDocumentController extends Controller
{
    private $generateDocumentService;

    /**
     * GenerateDocumentController construct.
     *
     * @param GenerateDocumentService $generateDocumentService generateDocumentService
     *
     * @return void
     */
    public function __construct(GenerateDocumentService $generateDocumentService)
    {
        $this->generateDocumentService = $generateDocumentService;
    }


    /**
     * Generate multiple choices
     *
     * @param GenerateMultipleChoicesRequest $request request
     *
     * @return mixed
     */
    public function generateMultipleChoices(GenerateMultipleChoicesRequest $request)
    {
        return $this->generateDocumentService
            ->setRequest($request->all())
            ->generateMultipleChoices();
    }
}
