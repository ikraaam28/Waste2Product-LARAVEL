@extends('layouts.admin')
@section('title', 'Edit Partner')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header mb-4 d-flex justify-content-between align-items-center">
            <h3 class="fw-bold mb-0">Edit Partner</h3>
            <a href="{{ route('admin.partners.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.partners.update', $partner) }}" method="POST" onsubmit="return mergeCustomFields()">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <!-- Name -->
                        <div class="col-md-6">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $partner->name) }}" required>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $partner->email) }}">
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $partner->phone) }}">
                        </div>

                        <!-- Type -->
                        <div class="col-md-6">
                            <label for="type" class="form-label">Type</label>
                            <select id="type" name="type" class="form-select" onchange="checkCustom(this, 'customType')">
                                <option value="">Select a type</option>
                                @php
                                    $types = ['Client','Supplier','Partner'];
                                @endphp
                                @foreach($types as $type)
                                    <option value="{{ $type }}" {{ old('type', $partner->type) === $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                                <option value="Other" {{ !in_array(old('type', $partner->type), $types) ? 'selected' : '' }}>Other (custom)</option>
                            </select>
                            <input type="text" id="customType" class="form-control mt-2 {{ !in_array(old('type', $partner->type), $types) ? '' : 'd-none' }}" 
                                   placeholder="Enter custom type" value="{{ !in_array(old('type', $partner->type), $types) ? old('type', $partner->type) : '' }}">
                        </div>

                        <!-- Address -->
                        <div class="col-md-12">
                            <label for="address" class="form-label">Address</label>
                            <select id="addressSelect" name="address" class="form-select" onchange="checkCustom(this, 'customAddress')">
                                <option value="">Select Address</option>
                                @php
                                    $addresses = ['Tunis Center','La Marsa','Ariana','Sfax'];
                                @endphp
                                @foreach($addresses as $addr)
                                    <option value="{{ $addr }}" {{ old('address', $partner->address) === $addr ? 'selected' : '' }}>{{ $addr }}</option>
                                @endforeach
                                <option value="Other" {{ !in_array(old('address', $partner->address), $addresses) ? 'selected' : '' }}>Other (custom)</option>
                            </select>
                            <input type="text" id="customAddress" class="form-control mt-2 {{ !in_array(old('address', $partner->address), $addresses) ? '' : 'd-none' }}" 
                                   placeholder="Enter custom address" value="{{ !in_array(old('address', $partner->address), $addresses) ? old('address', $partner->address) : '' }}">
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Update Partner</button>
                        <a href="{{ route('admin.partners.index') }}" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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

function mergeCustomFields() {
    const form = document.querySelector('form');

    // Type
    const typeSelect = document.getElementById('type');
    const customType = document.getElementById('customType');
    let typeValue = typeSelect.value === 'Other' ? customType.value.trim() : typeSelect.value;

    let oldTypeInput = form.querySelector('input[name="type"]');
    if (oldTypeInput) oldTypeInput.remove();

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

    return true;
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
</style>
@endsection
