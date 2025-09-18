<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Response;

class AdminPageController extends Controller
{
    // Deprecated: external Kaiadmin pages no longer served from filesystem
    protected function getAllowedPages(): array
    {
        return [];
    }

    /**
     * Serve a Kaiadmin template page with asset URLs rewritten to Laravel public paths.
     */
    public function show(Request $request, string $slug = 'index')
    {
        $allowedPages = $this->getAllowedPages();

        // Normalize slug (remove trailing slashes)
        $normalizedSlug = trim($slug, '/');
        if ($normalizedSlug === '') {
            $normalizedSlug = 'index';
        }

        abort(404);

        // Not used anymore
        return Response::make('', 404);
    }
}


