<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Content\MarkdownService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MarkdownPreviewController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, MarkdownService $markdown): Response
    {
        $validated = $request->validate([
            'markdown' => ['nullable', 'string'],
        ]);

        return response([
            'html' => $markdown->renderToHtml((string) ($validated['markdown'] ?? '')),
        ]);
    }
}
