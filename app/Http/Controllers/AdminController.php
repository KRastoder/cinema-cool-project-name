<?php
namespace App\Http\Controllers;

use Inertia\Inertia;

class AdminController extends Controller
{
    public function createMoviesForm()
    {
        return Inertia::render('admin/MovieCreateForm');
    }
}