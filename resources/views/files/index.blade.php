@extends('layouts.app')

@section('content')
    <style>
        .file-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
        }

        .file-item {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            text-align: center;
            word-wrap: break-word;
            overflow: hidden;
        }

        .file-item img {
            max-width: 100px;
            max-height: 100px;
            display: block;
            margin: 0 auto 10px;
            cursor: pointer;
        }

        .file-item-name {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
        }
    </style>


    <!-- Main Content -->
    <div class="container mt-4">
        <!-- Breadcrumb Navigation -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('files.index') }}">Home</a></li>
                @php
                    $path = '';
                    $segments = explode('/', $currentFolder);
                @endphp
                @foreach ($segments as $index => $segment)
                    @php $path .= ($path ? '/' : '') . $segment; @endphp
                    <li class="breadcrumb-item {{ $index === count($segments) - 1 ? 'active' : '' }}">
                        @if ($index === count($segments) - 1)
                            {{ $segment }}
                        @else
                            <a href="{{ route('folders.view', ['folder' => $path]) }}">{{ $segment }}</a>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>

        <!-- Search Bar -->
        <div class="mb-4">
            <form action="{{ route('files.index') }}" method="GET" class="d-flex">
                <input type="hidden" name="folder" value="{{ $currentFolder }}">
                <input type="text" name="search" class="form-control me-2" placeholder="Search files..."
                    value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>

        <!-- Folders -->
        <h2>Folders</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Folder Name</th>
                    <th>Items</th>
                    <th>Size</th>
                    <th>Last Modified</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($folders as $folder)
                    <tr>
                        <td>{{ $folder['name'] }}</td>
                        <td>{{ $folder['itemCount'] }}</td>
                        <td>{{ number_format($folder['size'] / 1024, 2) }} KB</td>
                        <td>{{ $folder['lastModified'] }}</td>
                        <td>
                            <a href="{{ route('folders.view', ['folder' => str_replace("uploads/{$userId}/", '', $folder['path'])]) }}"
                                class="btn btn-sm btn-info">Open</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No folders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- View Toggle -->
        <div class="mb-4 d-flex justify-content-end">
            <button class="btn btn-outline-primary btn-sm me-2" id="listViewButton">List View</button>
            <button class="btn btn-outline-primary btn-sm" id="gridViewButton">Grid View</button>
        </div>

        <!-- Folder Contents -->
        <div id="fileViewContainer">
            <!-- Default to List View -->
            <div id="listView" class="d-block">
                <!-- Files -->
                <h2>Files</h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>
                                <a
                                    href="{{ route('files.index', ['folder' => $currentFolder, 'sort' => 'name', 'direction' => $sort === 'name' && $direction === 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}">
                                    File Name
                                    @if ($sort === 'name')
                                        <span>{{ $direction === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a
                                    href="{{ route('files.index', ['folder' => $currentFolder, 'sort' => 'lastModified', 'direction' => $sort === 'lastModified' && $direction === 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}">
                                    Upload Date
                                    @if ($sort === 'lastModified')
                                        <span>{{ $direction === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a
                                    href="{{ route('files.index', ['folder' => $currentFolder, 'sort' => 'size', 'direction' => $sort === 'size' && $direction === 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}">
                                    File Size
                                    @if ($sort === 'size')
                                        <span>{{ $direction === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($files as $file)
                            <tr>
                                <td>{{ $file['name'] }}</td>
                                <td>{{ date('Y-m-d H:i:s', $file['lastModified']) }}</td>
                                <td>{{ number_format($file['size'] / 1024, 2) }} KB</td>
                                <td>
                                    <a href="{{ route('files.download', ['file' => $file['name'], 'folder' => $currentFolder]) }}"
                                        class="btn btn-success btn-sm">Download</a>
                                    <form
                                        action="{{ route('files.delete', ['file' => $file['name'], 'folder' => $currentFolder]) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                    @if (in_array($file['type'], ['jpg', 'jpeg', 'png', 'gif']))
                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#imagePreviewModal"
                                            onclick="showImage('{{ $file['url'] }}')">Preview</button>
                                    @elseif($file['type'] === 'pdf')
                                        <a href="{{ $file['url'] }}" target="_blank"
                                            class="btn btn-warning btn-sm">Preview PDF</a>
                                    @else
                                        <span class="text-muted">No Preview Available</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No files found matching your search.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Grid View -->
            <div id="gridView" class="d-none">
                <h2>Files</h2>
                <div class="row">
                    <div class="col">
                        <a
                            href="{{ route('files.index', ['folder' => $currentFolder, 'sort' => 'name', 'direction' => $sort === 'name' && $direction === 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}">
                            File Name
                            @if ($sort === 'name')
                                <span>{{ $direction === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </div>
                    <div class="col">
                        <a
                            href="{{ route('files.index', ['folder' => $currentFolder, 'sort' => 'lastModified', 'direction' => $sort === 'lastModified' && $direction === 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}">
                            Upload Date
                            @if ($sort === 'lastModified')
                                <span>{{ $direction === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </div>
                    <div class="col">
                        <a
                            href="{{ route('files.index', ['folder' => $currentFolder, 'sort' => 'size', 'direction' => $sort === 'size' && $direction === 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}">
                            File Size
                            @if ($sort === 'size')
                                <span>{{ $direction === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </div>
                </div>
                <div class="file-grid">
                    @forelse($files as $file)
                        <div class="file-item">
                            @if (in_array(strtolower($file['type']), ['jpg', 'jpeg', 'png', 'gif']))
                                <img src="{{ $file['url'] }}" alt="{{ $file['name'] }}" class="preview-image"
                                    data-bs-toggle="modal" data-bs-target="#imagePreviewModal"
                                    onclick="showImage('{{ $file['url'] }}')">
                            @elseif(strtolower($file['type']) === 'pdf')
                                <span>PDF</span>
                            @else
                                <span>{{ $file['type'] }}</span>
                            @endif
                            <span class="file-item-name">{{ $file['name'] }}</span>
                            <a href="{{ route('files.download', ['file' => $file['name'], 'folder' => $currentFolder]) }}"
                                class="btn btn-success btn-sm mt-2">Download</a>
                            <form
                                action="{{ route('files.delete', ['file' => $file['name'], 'folder' => $currentFolder]) }}"
                                method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm mt-2">Delete</button>
                            </form>
                        </div>
                    @empty
                        <div class="text-center">No files found matching your search.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imagePreviewLabel">Image Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="previewImage" src="" alt="Preview" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
    <!-- File Upload Modal -->
    <div class="modal fade" id="uploadFileModal" tabindex="-1" aria-labelledby="uploadFileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('files.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="current_folder" value="{{ $currentFolder }}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadFileModalLabel">Upload Files</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="files" class="form-label">Choose Files</label>
                            <input type="file" class="form-control" id="files" name="files[]" multiple required>
                            <small class="text-muted">Hold Ctrl (Cmd on Mac) to select multiple files.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Upload Files</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Create Folder Modal -->
    <div class="modal fade" id="createFolderModal" tabindex="-1" aria-labelledby="createFolderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('folders.create') }}" method="POST">
                    @csrf
                    <input type="hidden" name="current_folder" value="{{ $currentFolder }}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createFolderModalLabel">Create Folder</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="folder_name" class="form-label">Folder Name</label>
                            <input type="text" class="form-control" id="folder_name" name="folder_name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Create Folder</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        const listViewButton = document.getElementById('listViewButton');
        const gridViewButton = document.getElementById('gridViewButton');
        const listView = document.getElementById('listView');
        const gridView = document.getElementById('gridView');

        listViewButton.addEventListener('click', () => {
            listView.classList.remove('d-none');
            gridView.classList.add('d-none');
        });

        gridViewButton.addEventListener('click', () => {
            gridView.classList.remove('d-none');
            listView.classList.add('d-none');
        });

        function showImage(url) {
            document.getElementById('previewImage').src = url;
        }
    </script>
@endsection
