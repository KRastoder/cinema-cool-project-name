<?php
namespace App\Http\Controllers;

use App\Repositories\AdminRepo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    private $adminRepo;
    public function __construct()
    {
        $this->adminRepo = new AdminRepo();
    }
    public function createMoviesForm(): Response
    {
        return Inertia::render('admin/MovieCreateForm');
    }
    public function updateMovies(Request $request)
    {
        if (isset($request)) {

            $this->adminRepo->createMovie($request);
        }
        return redirect()->back();
    }
}
