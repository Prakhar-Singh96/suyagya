@extends('admin.layout.layout')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Video Feeds
            <a href="{{ route('admin.videos.create') }}" class="btn btn-primary float-end">Add Video</a>
        </h4>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Poster</th>
                    <th>Title</th>
                    <th>Video</th>
                    <th>Link</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($videos as $video)
                <tr>
                    <td>{{ $video->id }}</td>
                    <td>
                        <img src="{{ asset($video->image) }}" style="width: 60px; height: 80px; object-fit: cover;">
                    </td>
                    <td>{{ $video->title }}</td>
                    <td>
                        <a href="{{ asset($video->video) }}" target="_blank" class="btn btn-sm btn-info">View Video</a>
                    </td>
                    <td><a href="{{ $video->link }}" target="_blank">Open Link</a></td>
                    <td>{{ $video->status == 1 ? 'Active' : 'Inactive' }}</td>
                    <td>
                        <a href="{{ route('admin.videos.edit', $video->id) }}" class="btn btn-success btn-sm">Edit</a>

                        <form action="{{ route('admin.videos.destroy', $video->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
