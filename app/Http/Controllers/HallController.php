<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class HallController extends Controller
{
    public function layoutBuilder()
    {
        return Inertia::render('admin/LayoutBuilder');
    }
    public function store(Request $request)
    {
        dd($request);

    }

}
