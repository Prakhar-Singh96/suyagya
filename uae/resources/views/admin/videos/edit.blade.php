@extends('admin.layout.layout')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Edit Video Feed
                <a href="{{ route('admin.videos.index') }}" class="btn btn-danger float-end">Back</a>
            </h4>
        </div>
        <div class="card-body">
            {{-- Form Start --}}
            <form action="{{ route('admin.videos.update', $video->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT') {{-- Update Request ke liye zaroori hai --}}

                {{-- 1. Title --}}
                <div class="mb-3">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $video->title) }}"
                        required>
                    @error('title')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- 2. Link --}}
                <div class="mb-3">
                    <label>Redirect Link (Product/Category URL)</label>
                    <input type="text" name="link" class="form-control"
                        placeholder="e.g. product/ebony-bracelet or category/rudraksha"
                        value="{{ old('link', $video->link ?? '') }}" required>
                    <small class="text-muted">Enter internal path (e.g. <code>category/rudraksha</code>) or full
                        URL.</small>
                    @error('link')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="row">
                    {{-- 3. Video Upload --}}
                    <div class="col-md-6 mb-3">
                        <label>Upload New Video (Optional)</label>
                        <input type="file" name="video" class="form-control" accept="video/*">
                        <small class="text-muted">Leave empty to keep current video.</small>
                        @error('video')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                        {{-- Show Current Video --}}
                        @if ($video->video)
                            <div class="mt-3 p-2 border rounded bg-light">
                                <label class="d-block mb-1 fw-bold small">Current Video:</label>
                                <video width="200" height="120" controls class="rounded">
                                    <source src="{{ asset($video->video) }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        @endif
                    </div>

                    {{-- 4. Image Upload --}}
                    <div class="col-md-6 mb-3">
                        <label>Upload New Poster Image (Optional)</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted">Leave empty to keep current poster.</small>
                        @error('image')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                        {{-- Show Current Image --}}
                        @if ($video->image)
                            <div class="mt-3 p-2 border rounded bg-light">
                                <label class="d-block mb-1 fw-bold small">Current Poster:</label>
                                <img src="{{ asset($video->image) }}" alt="Poster" class="img-thumbnail"
                                    style="width: 120px; height: 120px; object-fit: cover;">
                            </div>
                        @endif
                    </div>
                </div>

                {{-- 5. Sort Order & Status --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" class="form-control"
                            value="{{ old('sort_order', $video->sort_order) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Status</label> <br>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" id="statusSwitch"
                                {{ $video->status == 1 ? 'checked' : '' }} style="width: 40px; height: 20px;">
                            <label class="form-check-label ms-2 mt-1" for="statusSwitch">Active</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Video</button>
            </form>
        </div>
    </div>
@endsection
