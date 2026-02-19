@extends('admin.layout.layout')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Upload New Video Feed
                <a href="{{ route('admin.videos.index') }}" class="btn btn-danger float-end">Back</a>
            </h4>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.videos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Only Rudraksha" required>
                </div>

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
                    <div class="col-md-6 mb-3">
                        <label>Upload Video (MP4) - Max 20MB</label>
                        <input type="file" name="video" class="form-control" accept="video/*" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Upload Poster Image (Thumbnail)</label>
                        <input type="file" name="image" class="form-control" accept="image/*" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Status</label> <br>
                        <input type="checkbox" name="status" checked style="width: 20px; height: 20px;"> Active
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Upload Video</button>
            </form>
        </div>
    </div>
@endsection
