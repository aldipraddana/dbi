<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FrontendController extends Controller
{
    public function generateGmail(Request $request)
    {
        if ($request->input('generate_gmail')) {
            $request->session()->put('generate_gmail', true);
            return redirect()->route('generate.gmail');
        }
        return view('frontend.generate-gmail');
    }
}