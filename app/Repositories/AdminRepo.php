<?php
namespace App\Repositories;

use App\Models\Movie;

class AdminRepo
{
    private $movieModel;

    public function __construct()
    {
        $this->movieModel = new Movie();

    }

    public function createMovie($request): void
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'duration'     => 'required|integer',
            'description'  => 'required|string',
            'release_date' => 'required|date',
            'poster'       => 'nullable|image|max:2048',
        ]);

        $posterPath = null;

        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store(
                'posters',
                'public'
            );
        }

        $this->movieModel->create([
            'title'        => $request->title,
            'duration'     => $request->duration,
            'description'  => $request->description,
            'release_date' => $request->release_date,
            'poster'       => $posterPath,
        ]);
    }

}
