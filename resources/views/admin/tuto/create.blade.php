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
                                            <button type="button" class="btn btn-primary mt-2" id="generate-image" style="display: none;">Generate Image with AI</button>
                                        </div>
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea name="description" id="description" required class="form-control" rows="5" placeholder="Describe your tutorial"></textarea>
                                            @error('description')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="category_id">Category</label>
                                            <select name="category_id" id="category_id" required class="form-control">
                                                <option value="" disabled selected>Select a category</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
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
                                            <div id="media-preview" class="media-preview mt-3"></div>
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

<style>
.media-preview {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
}
.media-preview-item {
    position: relative;
    width: 150px;
    height: 150px;
    overflow: hidden;
    border-radius: 8px;
    border: 2px solid #e9ecef;
    background-color: #f8f9fa;
}
.media-preview-item img, .media-preview-item video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.media-preview-item .remove-media {
    position: absolute;
    top: 5px;
    right: 5px;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 14px;
}
.form-control-file {
    padding: 8px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    background-color: #fff;
}
.form-group label {
    font-weight: 600;
    color: #2c3e50;
}
.form-control, .form-control-file, select.form-control {
    border-radius: 6px;
    transition: border-color 0.2s ease-in-out;
}
.form-control:focus, .form-control-file:focus {
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0,123,255,0.3);
}
.card {
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.card-header {
    background-color: #f8f9fa;
    border-bottom: none;
}
.card-title {
    color: #2c3e50;
    font-weight: 700;
}
#generate-image {
    background-color: #007bff;
    border: none;
    transition: background-color 0.2s ease-in-out;
}
#generate-image:hover {
    background-color: #0056b3;
}
#generate-image:disabled {
    background-color: #6c757d;
    cursor: not-allowed;
}
.error-message {
    color: #dc3545;
    font-size: 14px;
    margin-top: 10px;
    text-align: center;
}
</style>

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

// Update modal title preview and toggle generate button visibility
document.getElementById('title').addEventListener('input', function() {
    const titlePreview = document.getElementById('modal-title-preview');
    const generateBtn = document.getElementById('generate-image');
    titlePreview.textContent = this.value || 'Tutorial Title';
    generateBtn.style.display = this.value.trim() ? 'inline-block' : 'none';
});

// Handle media preview for file uploads
document.getElementById('media').addEventListener('change', function(e) {
    const previewContainer = document.getElementById('media-preview');
    const files = e.target.files;
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const imgUrl = URL.createObjectURL(file);
        if (!Array.from(previewContainer.querySelectorAll('img, video')).some(el => el.src === imgUrl)) {
            const previewItem = document.createElement('div');
            previewItem.className = 'media-preview-item';

            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = imgUrl;
                previewItem.appendChild(img);
            } else if (file.type === 'video/mp4') {
                const video = document.createElement('video');
                video.src = imgUrl;
                video.controls = true;
                previewItem.appendChild(video);
            }

            const removeButton = document.createElement('button');
            removeButton.className = 'remove-media';
            removeButton.innerHTML = '&times;';
            removeButton.onclick = function() {
                previewItem.remove();
            };
            previewItem.appendChild(removeButton);

            previewContainer.appendChild(previewItem);
        }
    }
});

// Handle AI image generation
document.getElementById('generate-image').addEventListener('click', async function() {
    const title = document.getElementById('title').value.trim();
    if (!title) {
        alert('Please enter a tutorial title to generate an image.');
        return;
    }

    const generateBtn = this;
    generateBtn.disabled = true;
    generateBtn.textContent = 'Generating...';

    const previewContainer = document.getElementById('media-preview');
    let errorMessage = previewContainer.querySelector('.error-message');
    if (errorMessage) errorMessage.remove();

    const loadingMessage = document.createElement('div');
    loadingMessage.className = 'media-preview-item';
    loadingMessage.style.cssText = 'display: flex; align-items: center; justify-content: center; color: #2c3e50;';
    loadingMessage.textContent = 'Generating image...';
    previewContainer.appendChild(loadingMessage);

    try {
        const prompt = encodeURIComponent(`A vibrant, eco-friendly illustration for a recycling tutorial titled: ${title}. Educational, colorful, and engaging.`);
        const apiUrl = `https://image.pollinations.ai/prompt/${prompt}?width=512&height=512&seed=${Math.floor(Math.random() * 10000)}&nologo=true`;

        const response = await fetch(apiUrl);
        if (!response.ok) throw new Error('Failed to generate image');

        const blob = await response.blob();
        const imgUrl = URL.createObjectURL(blob);

        loadingMessage.remove();

        if (!Array.from(previewContainer.querySelectorAll('img')).some(img => img.src === imgUrl)) {
            const previewItem = document.createElement('div');
            previewItem.className = 'media-preview-item';
            const img = document.createElement('img');
            img.src = imgUrl;
            img.alt = 'Generated AI Image';
            previewItem.appendChild(img);

            const removeButton = document.createElement('button');
            removeButton.className = 'remove-media';
            removeButton.innerHTML = '&times;';
            removeButton.onclick = function() {
                previewItem.remove();
            };
            previewItem.appendChild(removeButton);

            previewContainer.appendChild(previewItem);

            const dataTransfer = new DataTransfer();
            Array.from(document.getElementById('media').files).forEach(file => dataTransfer.items.add(file));
            const generatedFile = new File([blob], `ai-generated-${Date.now()}.jpg`, { type: 'image/jpeg' });
            dataTransfer.items.add(generatedFile);
            document.getElementById('media').files = dataTransfer.files;
        }

        generateBtn.textContent = 'Regenerate Image';
    } catch (error) {
        console.error('Error generating image:', error);
        loadingMessage.remove();
        errorMessage = document.createElement('div');
        errorMessage.className = 'error-message';
        errorMessage.textContent = 'Failed to generate image. Please try again.';
        previewContainer.appendChild(errorMessage);
    } finally {
        generateBtn.disabled = false;
        if (generateBtn.textContent !== 'Regenerate Image') {
            generateBtn.textContent = 'Generate Image with AI';
        }
    }
});
</script>
@endsection