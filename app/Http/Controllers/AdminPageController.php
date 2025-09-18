<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Response;

class AdminPageController extends Controller
{
    /**
     * Whitelisted Kaiadmin pages mapped to their file paths in the template folder.
     * Keys are slugs used in the URL; values are relative paths within kaiadmin-lite-1.2.0.
     *
     * @return array<string,string>
     */
    protected function getAllowedPages(): array
    {
        return [
            'index' => 'index.html',
            'widgets' => 'widgets.html',
            'icon-menu' => 'icon-menu.html',
            'sidebar-style-2' => 'sidebar-style-2.html',
            'starter-template' => 'starter-template.html',
            'components/avatars' => 'components/avatars.html',
            'components/buttons' => 'components/buttons.html',
            'components/gridsystem' => 'components/gridsystem.html',
            'components/notifications' => 'components/notifications.html',
            'components/panels' => 'components/panels.html',
            'components/simple-line-icons' => 'components/simple-line-icons.html',
            'components/sweetalert' => 'components/sweetalert.html',
            'components/typography' => 'components/typography.html',
            'components/font-awesome-icons' => 'components/font-awesome-icons.html',
            'forms/forms' => 'forms/forms.html',
            'tables/tables' => 'tables/tables.html',
            'tables/datatables' => 'tables/datatables.html',
            'charts/charts' => 'charts/charts.html',
            'charts/sparkline' => 'charts/sparkline.html',
            'maps/googlemaps' => 'maps/googlemaps.html',
            'maps/jsvectormap' => 'maps/jsvectormap.html',
        ];
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

        if (!array_key_exists($normalizedSlug, $allowedPages)) {
            abort(404);
        }

        $templateRoot = realpath(base_path('..' . DIRECTORY_SEPARATOR . 'kaiadmin-lite-1.2.0'));
        if ($templateRoot === false) {
            abort(500, 'Template root not found');
        }

        $relativePath = $allowedPages[$normalizedSlug];
        $fullPath = $templateRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);

        if (!is_file($fullPath)) {
            abort(404);
        }

        $html = file_get_contents($fullPath);
        if ($html === false) {
            abort(500, 'Failed to read template');
        }

        // Rewrite asset paths: any '../' levels + assets/ → /vendor/kaiadmin
        $publicVendor = url('vendor/kaiadmin');
        $html = preg_replace('#(?:\.+/)*assets/#i', $publicVendor . '/', $html);

        // Rewrite internal page links to Laravel routes under /admin/pages
        // 1) href to index.html (with optional ../ prefixes) -> /admin
        $html = preg_replace('#href=[\"\'](?:(?:\.+/)+|(?:\./)*)?index\.html([\#\?][^\"\']*)?[\"\']#i', 'href="' . url('admin') . '$1"', $html);

        // 2) href to other .html pages that are not external or assets/js/css
        // Skip protocols (http, https, mailto, tel), anchors (#), javascript:
        // Convert e.g. href="components/avatars.html" to /admin/pages/components/avatars
        $html = preg_replace_callback(
            '#href=[\"\'](?!https?:|mailto:|tel:|javascript:|\#)(?!' . preg_quote($publicVendor, '#') . ')([^\"\']+?)\.html([\#\?][^\"\']*)?[\"\']#i',
            function ($m) {
                $path = $m[1];
                $suffix = isset($m[2]) ? $m[2] : '';
                // Normalize ./ and ../ that might appear from nested links
                $path = preg_replace('#^\./#', '', $path);
                $path = preg_replace('#^(?:\.+/)+#', '', $path); // strip leading ../ segments
                $path = preg_replace('#^/+#', '', $path);
                // Map to /admin/pages/{path}
                $url = url('admin/pages/' . $path) . $suffix;
                return 'href="' . $url . '"';
            },
            $html
        );

        return Response::make($html);
    }
}


