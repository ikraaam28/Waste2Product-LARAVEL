@extends('layouts.admin')

@section('title', 'Publications')

@section('content')
  <div class="container">
    <div class="page-inner">
      <div class="page-header">
        <h3 class="fw-bold mb-3">Publications</h3>
        <ul class="breadcrumbs mb-3">
          <li class="nav-home"><a href="{{ url('admin') }}"><i class="icon-home"></i></a></li>
          <li class="separator"><i class="icon-arrow-right"></i></li>
          <li class="nav-item"><a href="{{ route('admin.publications.index') }}">Publications</a></li>
        </ul>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <div class="card-title">Publications List</div>
            </div>
            <div class="card-body">
              @if (session('success'))
                <div class="alert alert-success">
                  {{ session('success') }}
                </div>
              @endif
              <div class="table-responsive">
                <table class="table table-striped align-middle">
                  <thead>
                    <tr>
                      <th>Image</th>
                      <th>Author</th>
                      <th>Title</th>
                      <th>Category</th>
                      <th>Content</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($publications as $publication)
                      <tr>
                        <td>
                          @if ($publication->image && Storage::disk('public')->exists($publication->image))
                            <img src="{{ Storage::url($publication->image) }}" alt="Publication Image" style="max-width: 100px; max-height: 100px;">
                          @else
                            <span>No Image</span>
                          @endif
                        </td>
                        <td>{{ $publication->user ? $publication->user->first_name . ' ' . $publication->user->last_name : 'Deleted User' }}</td>
                        <td>{{ $publication->titre }}</td>
                        <td>{{ $publication->categorie }}</td>
                        <td>{{ $publication->contenu }}</td>
                        <td>
                          <!-- Delete Icon (Triggers Modal) -->
                          <button 
                            type="button" 
                            class="btn btn-outline-danger btn-sm" 
                            data-bs-toggle="modal" 
                            data-bs-target="#deleteModal{{ $publication->id }}">
                            <i class="fas fa-trash-alt"></i>
                          </button>

                          <!-- Modern Delete Confirmation Modal -->
                          <div class="modal fade" id="deleteModal{{ $publication->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $publication->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                                <div class="modal-header bg-light border-0">
                                  <h5 class="modal-title fw-bold" id="deleteModalLabel{{ $publication->id }}">Confirm Deletion</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                  <p class="mb-2 text-muted">Are you sure you want to delete this publication?</p>
                                  <p class="fw-bold text-dark mb-0">{{ $publication->titre }}</p>
                                </div>
                                <div class="modal-footer border-0 bg-light">
                                  <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                  <form action="{{ route('admin.publications.destroy', $publication->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger rounded-pill px-4">Delete</button>
                                  </form>
                                </div>
                              </div>
                            </div>
                          </div>
                          <!-- End Modal -->
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              {{ $publications->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection