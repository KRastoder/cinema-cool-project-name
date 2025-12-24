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

    public function index()
    {
        $movies = $this->adminRepo->allMovies();
        return Inertia::render('admin/Index', [
            'movies' => $movies,
        ]);
    }

    public function createMoviesForm(): Response
    {
        return Inertia::render('admin/MovieCreateForm');
    }

    public function store(Request $request)
    {
        $this->adminRepo->createMovie($request);
        return redirect()->route('admin.index');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'duration'     => 'nullable|string',
            'description'  => 'nullable|string',
            'poster'       => 'nullable|image|max:2048',
            'release_date' => 'nullable|date',
        ]);

        // Pass the validated data array to the repository
        $this->adminRepo->updateMovie($id, $validated);

        return redirect()->back();
    }

    public function destroy($id)
    {
        $this->adminRepo->deleteMovie($id);

        return redirect()->back();
    }
}
