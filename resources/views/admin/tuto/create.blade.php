@extends('layouts.admin')

@section('title', 'Create Tutorial')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Create Tutorial</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="{{ url('admin') }}"><i class="icon-home"></i></a>
                </li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="{{ route('admin.tutos.index') }}">Tutorials</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item">Create</li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Share Your Recycling Idea</div>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        <div class="table-responsive">
                            <form action="{{ route('admin.tutos.store') }}" method="POST" enctype="multipart/form-data" id="tuto-form">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title">Title</label>
                                            <input type="text" name="title" id="title" required class="form-control" placeholder="Enter the tutorial title">
                                            @error('title')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea name="description" id="description" required class="form-control" rows="5" placeholder="Describe your tutorial"></textarea>
                                            @error('description')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="category">Category</label>
                                            <select name="category" id="category" required class="form-control">
                                                <option value="" disabled selected>Select a category</option>
                                                <option value="plastic">Plastic</option>
                                                <option value="wood">Wood</option>
                                                <option value="paper">Paper</option>
                                                <option value="metal">Metal</option>
                                                <option value="glass">Glass</option>
                                                <option value="other">Other</option>
                                            </select>
                                            @error('category')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Steps</label>
                                            <div id="steps-container">
                                                <div class="input-group mb-3 step-group">
                                                    <input type="text" name="steps[]" class="form-control" placeholder="Step 1" required>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-danger remove-step">Remove</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-primary mt-2" id="add-step">Add Step</button>
                                            @error('steps.*')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="media">Media (Images or Videos)</label>
                                            <input type="file" name="media[]" id="media" class="form-control-file" multiple accept="image/*,video/mp4">
                                            <small class="form-text text-muted">Select images or videos (MP4). Maximum size: 10 MB per file.</small>
                                            @error('media.*')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="is_published">Publication Status</label>
                                            <select name="is_published" id="is_published" class="form-control">
                                                <option value="1">Published</option>
                                                <option value="0">Unpublished</option>
                                            </select>
                                            @error('is_published')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="admin_notes">Admin Notes</label>
                                            <textarea name="admin_notes" id="admin_notes" class="form-control" rows="3" placeholder="Internal notes (not visible to users)"></textarea>
                                            @error('admin_notes')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-action">
                        <!-- Submit Button with Modal Trigger -->
                        <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createModal">Create Tutorial</button>
                        <a href="{{ route('admin.tutos.index') }}" class="btn btn-danger rounded-pill px-4">Cancel</a>
                    </div>

                    <!-- Modern Create Confirmation Modal -->
                    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                                <div class="modal-header bg-light border-0">
                                    <h5 class="modal-title fw-bold" id="createModalLabel">Confirm Creation</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <p class="mb-2 text-muted">Are you sure you want to create this tutorial?</p>
                                    <p class="fw-bold text-dark mb-0" id="modal-title-preview">Tutorial Title</p>
                                </div>
                                <div class="modal-footer border-0 bg-light">
                                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" form="tuto-form" class="btn btn-success rounded-pill px-4">Confirm</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Modal -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('add-step').addEventListener('click', function() {
    const container = document.getElementById('steps-container');
    const stepCount = container.children.length + 1;
    const newStep = document.createElement('div');
    newStep.className = 'input-group mb-3 step-group';
    newStep.innerHTML = `
        <input type="text" name="steps[]" class="form-control" placeholder="Step ${stepCount}" required>
        <div class="input-group-append">
            <button type="button" class="btn btn-danger remove-step">Remove</button>
        </div>
    `;
    container.appendChild(newStep);
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-step')) {
        const container = document.getElementById('steps-container');
        if (container.children.length > 1) {
            e.target.closest('.step-group').remove();
        }
    }
});

// Update modal title preview when input changes
document.getElementById('title').addEventListener('input', function() {
    const titlePreview = document.getElementById('modal-title-preview');
    titlePreview.textContent = this.value || 'Tutorial Title';
});
</script>
@endsection