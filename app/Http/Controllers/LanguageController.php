<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function __invoke(Request $request, string $language)
    {
        $blog = $request->session()->get('blog');

        if (! in_array($language, $blog->languages)) {
            abort(404);
        }

        $request->session()->put('language', $language);

        return redirect('feed');
    }
}
