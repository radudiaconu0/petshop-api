<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function index()
    {
        return File::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'uuid' => ['required'],
            'name' => ['required'],
            'path' => ['required'],
            'size' => ['required'],
            'type' => ['required'],
        ]);

        return File::create($data);
    }

    public function show(File $file)
    {
        return $file;
    }

    public function update(Request $request, File $file)
    {
        $data = $request->validate([
            'uuid' => ['required'],
            'name' => ['required'],
            'path' => ['required'],
            'size' => ['required'],
            'type' => ['required'],
        ]);

        $file->update($data);

        return $file;
    }

    public function destroy(File $file)
    {
        $file->delete();

        return response()->json();
    }
}
