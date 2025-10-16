@extends('layouts.admin')
@section('title', 'Add Partner')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header mb-4">
            <h3 class="fw-bold mb-1">Add a Partner</h3>
            <ul class="breadcrumbs mb-0">
                <li class="nav-home">
                    <a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a>
                </li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="{{ route('admin.partners.index') }}">Partners</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Add</a></li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <div class="card-title">Partner Form</div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.partners.store') }}" method="POST" onsubmit="return mergeCustomFields()">
                            @csrf
                            <div class="row g-3">
                                <!-- Name -->
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}">
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}">
                                </div>

                                <!-- Type -->
                                <div class="col-md-6">
                                    <label for="type" class="form-label">Type</label>
                                    <select id="type" name="type" class="form-select" onchange="checkCustom(this, 'customType')">
                                        <option value="">Select a type</option>
                                        <option value="Client">Client</option>
                                        <option value="Supplier">Supplier</option>
                                        <option value="Partner">Partner</option>
                                        <option value="Other">Other (custom)</option>
                                    </select>
                                    <input type="text" id="customType" class="form-control mt-2 d-none" placeholder="Enter custom type">
                                </div>

                                <!-- Address -->
                                <div class="col-md-12">
                                    <label for="address" class="form-label">Address</label>
                                    <select id="addressSelect" name="address" class="form-select" onchange="checkCustom(this, 'customAddress')">
                                        <option value="">Select an address</option>
                                        <option value="Tunis Center">Tunis Center</option>
                                        <option value="La Marsa">La Marsa</option>
                                        <option value="Ariana">Ariana</option>
                                        <option value="Sfax">Sfax</option>
                                        <option value="Other">Other (custom)</option>
                                    </select>
                                    <input type="text" id="customAddress" class="form-control mt-2 d-none" placeholder="Enter custom address">
                                </div>
                            </div>

                            <div class="mt-4 d-flex justify-content-end gap-2">
                                <button type="submit" class="btn btn-success me-2"><i class="fas fa-plus"></i> Add Partner</button>
                                <a href="{{ route('admin.partners.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script to handle custom fields -->
<script>
function checkCustom(select, inputId) {
    const input = document.getElementById(inputId);
    if (select.value === 'Other') {
        input.classList.remove('d-none');
        input.focus();
    } else {
        input.classList.add('d-none');
        input.value = '';
    }
}

// Merge custom fields before submit
function mergeCustomFields() {
    const form = document.querySelector('form');

    // Type
    const typeSelect = document.getElementById('type');
    const customType = document.getElementById('customType');
    let typeValue = typeSelect.value === 'Other' ? customType.value.trim() : typeSelect.value;

    // Supprimer l'ancien input si existant
    let oldTypeInput = form.querySelector('input[name="type"]');
    if (oldTypeInput) oldTypeInput.remove();

    // Créer un champ caché avec la valeur finale
    const typeInput = document.createElement('input');
    typeInput.type = 'hidden';
    typeInput.name = 'type';
    typeInput.value = typeValue;
    form.appendChild(typeInput);

    // Address
    const addressSelect = document.getElementById('addressSelect');
    const customAddress = document.getElementById('customAddress');
    let addressValue = addressSelect.value === 'Other' ? customAddress.value.trim() : addressSelect.value;

    let oldAddressInput = form.querySelector('input[name="address"]');
    if (oldAddressInput) oldAddressInput.remove();

    const addressInput = document.createElement('input');
    addressInput.type = 'hidden';
    addressInput.name = 'address';
    addressInput.value = addressValue;
    form.appendChild(addressInput);

    return true; // allow submit
}

</script>

<style>
.card-header {
    font-size: 1.2rem;
    font-weight: 600;
}
.form-label {
    font-weight: 500;
}
.form-control, .form-select {
    border-radius: 0.4rem;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.075);
}
.btn-success {
    background-color: #28a745;
    border-color: #28a745;
}
.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
}
</style>
@endsection
