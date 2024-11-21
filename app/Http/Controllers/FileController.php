<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    // Display files and folders
    public function index(Request $request)
{
    $currentFolder = $request->get('folder', ''); // Current folder path or root
    $userId = auth()->id(); // Authenticated user ID

    // Fetch folder metadata
    $folders = collect(Storage::directories("uploads/{$userId}/{$currentFolder}"))
        ->map(function ($folder) {
            $files = Storage::allFiles($folder); // Get all files in the folder
            $size = collect($files)->sum(function ($file) {
                return Storage::size($file);
            });
            $lastModified = collect($files)->max(function ($file) {
                return Storage::lastModified($file);
            });

            return [
                'name' => basename($folder),
                'path' => $folder,
                'itemCount' => count(Storage::files($folder)) + count(Storage::directories($folder)), // Number of items
                'size' => $size,
                'lastModified' => $lastModified ? date('Y-m-d H:i:s', $lastModified) : 'N/A',
            ];
        });

    // Fetch files metadata
    $files = collect(Storage::files("uploads/{$userId}/{$currentFolder}"))
        ->map(function ($file) {
            return [
                'name' => basename($file),
                'path' => $file,
                'url' => Storage::url($file),
                'size' => Storage::size($file),
                'lastModified' => Storage::lastModified($file),
                'type' => strtolower(pathinfo($file, PATHINFO_EXTENSION)),
            ];
        });

    // Filter by search query if provided
    if ($request->has('search') && $request->get('search') !== '') {
        $search = strtolower($request->get('search'));
        $files = $files->filter(function ($file) use ($search) {
            return str_contains(strtolower($file['name']), $search);
        });
    }

    // Handle sorting
    $sort = $request->get('sort', 'name'); // Default sort by name
    $direction = $request->get('direction', 'asc'); // Default direction is ascending
    $files = $files->sortBy($sort, SORT_REGULAR, $direction === 'desc');

    return view('files.index', compact('folders', 'files', 'currentFolder', 'userId', 'sort', 'direction'));
}


    // Upload a file
    public function upload(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|max:5120', // Each file must not exceed 5MB
            'current_folder' => 'nullable|string',
        ]);
    
        $userId = auth()->id(); // Get authenticated user ID
        $currentFolder = $request->input('current_folder', ''); // Get current folder or root
    
        // Process each uploaded file
        foreach ($request->file('files', []) as $file) {
            $file->store("uploads/{$userId}/{$currentFolder}", 'public'); // Store each file
        }
    
        return redirect()->route('files.index', ['folder' => $currentFolder])
            ->with('success', 'Files uploaded successfully!');
    }
    

    // Download a file
    public function download($file)
    {
        $currentFolder = request()->get('folder', ''); // Current folder path
        $userId = auth()->id(); // Get user ID
        $filePath = "uploads/{$userId}/{$currentFolder}/{$file}";

        if (Storage::exists($filePath)) {
            return Storage::download($filePath);
        }

        return redirect()->route('files.index', ['folder' => $currentFolder])
            ->with('error', 'File not found!');
    }

    // Delete a file
    public function delete($file)
    {
        $currentFolder = request()->get('folder', ''); // Current folder path
        $userId = auth()->id(); // Get user ID
        $filePath = "uploads/{$userId}/{$currentFolder}/{$file}";

        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
            return redirect()->route('files.index', ['folder' => $currentFolder])
                ->with('success', 'File deleted successfully!');
        }

        return redirect()->route('files.index', ['folder' => $currentFolder])
            ->with('error', 'File not found!');
    }

    // Create a new folder
    public function createFolder(Request $request)
    {
        $request->validate([
            'folder_name' => 'required|string|max:255', // Folder name validation
            'current_folder' => 'nullable|string', // Current folder path
        ]);

        $userId = auth()->id(); // Get user ID
        $currentFolder = $request->input('current_folder', ''); // Get current folder or root
        $folderName = $request->input('folder_name'); // New folder name

        // Build the full folder path
        $folderPath = "uploads/{$userId}/{$currentFolder}/{$folderName}";
        Storage::makeDirectory($folderPath); // Create the folder

        return redirect()->route('files.index', ['folder' => $currentFolder])
            ->with('success', "Folder '{$folderName}' created successfully!");
    }

    // Navigate to a folder
    public function viewFolder($folder = '')
    {
        return redirect()->route('files.index', ['folder' => $folder]);
    }
    
}

