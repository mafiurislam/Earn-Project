@extends('layouts.app')

@section('title', 'Copyright Claim Remove Links - Rajdoot Nivedan Media')

@section('content')
<div class="dashboard-page-wrapper">

    <!-- Breadcrumb & Page Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item">
                        <a href="{{ route('customer.dashboard') }}" class="text-decoration-none text-teal">
                            <i class="fa-solid fa-gauge me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-light" aria-current="page">Copyright Claim Remove Links</li>
                </ol>
            </nav>
            <h1 class="h3 fw-extrabold text-light mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-link text-teal" style="font-size: 1.3rem;"></i>
                Copyright Claim Remove Links
            </h1>
            <p class="mb-0" style="color: #94a3b8; font-size: 0.9rem;">
                Upload, update, and manage your verified copyright claim removal links. Published links appear live on the Home Page slots.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge px-3 py-2 rounded-pill fw-bold" style="background: rgba(0, 210, 170, 0.12); color: #00d2aa; border: 1px solid rgba(0, 210, 170, 0.3); font-size: 0.8rem;">
                <i class="fa-solid fa-circle-check me-1"></i> {{ $user->copyrightClaimLinks->count() }} Link{{ $user->copyrightClaimLinks->count() === 1 ? '' : 's' }} Uploaded
            </span>
            <a href="{{ route('customer.dashboard') }}" class="btn btn-sm btn-dark-outline py-2 px-3 rounded-3 small">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Card 1: Add New Copyright Claim Link -->
    <div class="exact-card mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="d-flex align-items-center gap-2">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(0, 210, 170, 0.12); display: flex; align-items: center; justify-content: center; color: #00d2aa;">
                    <i class="fa-solid fa-plus fs-6"></i>
                </div>
                <h2 class="h6 fw-bold text-light mb-0" style="font-size: 0.95rem;">Upload New Copyright Claim Link</h2>
            </div>
            <span class="small" style="color: #94a3b8; font-size: 0.78rem;">
                Slot assigned automatically (1&ndash;10)
            </span>
        </div>

        <form action="{{ route('customer.copyright_links.store') }}" method="POST">
            @csrf
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold" style="color: #94a3b8; font-size: 0.72rem; letter-spacing: 0.5px; text-transform: uppercase;">
                        TITLE (E.G. SONG / CLAIM TITLE)
                    </label>
                    <input type="text" name="title" class="form-control exact-input" placeholder="Title (e.g. song name)" value="{{ old('title') }}">
                </div>
                <div class="col-md-5">
                    <label class="form-label small fw-semibold" style="color: #94a3b8; font-size: 0.72rem; letter-spacing: 0.5px; text-transform: uppercase;">
                        TARGET LINK URL <span class="text-danger">*</span>
                    </label>
                    <input type="url" name="url" class="form-control exact-input" placeholder="https://..." value="{{ old('url') }}" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-exact-teal w-100 justify-content-center py-2.5">
                        <i class="fa-solid fa-plus me-1"></i> Save
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Card 2: Your Active Copyright Claim Links List -->
    <div class="exact-card mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3 pb-1">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-list-check text-teal" style="font-size: 0.95rem;"></i>
                <h3 class="h6 fw-bold text-light mb-0" style="font-size: 0.92rem;">
                    Your Published Links ({{ $user->copyrightClaimLinks->count() }})
                </h3>
            </div>
            <a href="{{ route('home') }}" target="_blank" class="small text-decoration-none text-teal">
                View on Home Page <i class="fa-solid fa-arrow-up-right-from-square ms-1" style="font-size: 0.75rem;"></i>
            </a>
        </div>

        @if($user->copyrightClaimLinks->count() === 0)
            <div class="text-center py-5">
                <div class="mb-3">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(0, 210, 170, 0.08); border: 1px dashed rgba(0, 210, 170, 0.3); display: inline-flex; align-items: center; justify-content: center; color: #00d2aa; font-size: 1.4rem;">
                        <i class="fa-solid fa-link-slash"></i>
                    </div>
                </div>
                <h4 class="h6 fw-bold text-light mb-1">No copyright claim links uploaded yet</h4>
                <p class="small mb-0" style="color: #64748b; font-size: 0.86rem; max-width: 420px; margin: 0 auto;">
                    Use the form above to add your claim removal links. Once added, they will be assigned a slot and published live on the Home Page.
                </p>
            </div>
        @else
            <div class="d-flex flex-column gap-2 pt-2 border-top border-secondary border-opacity-25">
                @foreach($user->copyrightClaimLinks as $link)
                    <div class="p-3 rounded-3 d-flex align-items-center justify-content-between gap-3" style="background: #111726; border: 1px solid rgba(255, 255, 255, 0.06);">
                        <div class="d-flex align-items-center gap-3 overflow-hidden" style="min-width: 0;">
                            <div class="flex-shrink-0 d-flex align-items-center justify-content-center fw-bold" style="width: 34px; height: 34px; border-radius: 50%; background: rgba(0, 210, 170, 0.12); color: #00d2aa; font-size: 0.85rem; border: 1px solid rgba(0, 210, 170, 0.25);">
                                {{ $link->slot_number }}
                            </div>
                            <div class="overflow-hidden" style="min-width: 0;">
                                <div class="fw-bold text-light text-truncate" style="font-size: 0.92rem;">
                                    {{ $link->display_title }}
                                </div>
                                <div class="text-truncate">
                                    <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" class="small text-decoration-none text-truncate d-inline-block" style="color: #38bdf8 !important; font-size: 0.8rem;">
                                        <i class="fa-solid fa-arrow-up-right-from-square me-1" style="font-size: 0.7rem;"></i>{{ $link->url }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                            <span class="badge d-none d-sm-inline-block px-2.5 py-1 rounded-pill small" style="background: rgba(34, 211, 238, 0.1); color: #22d3ee; border: 1px solid rgba(34, 211, 238, 0.2); font-size: 0.72rem;">
                                Slot #{{ $link->slot_number }}
                            </span>
                            <button type="button" class="btn btn-sm btn-dark-outline p-1.5 px-2.5 rounded-pill text-info border-info border-opacity-30" title="Edit Link" data-bs-toggle="modal" data-bs-target="#editLinkModal{{ $link->id }}">
                                <i class="fa-regular fa-pen-to-square me-1"></i> Edit
                            </button>
                            <form action="{{ route('customer.copyright_links.destroy', $link->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete link from Slot #{{ $link->slot_number }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-dark-outline p-1.5 px-2.5 rounded-pill text-danger border-danger border-opacity-30" title="Delete Link">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Modal: Edit Link -->
                    <div class="modal fade" id="editLinkModal{{ $link->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px;">
                                <div class="modal-header border-secondary border-opacity-25">
                                    <h5 class="modal-title fw-bold text-light fs-6">
                                        <i class="fa-regular fa-pen-to-square text-teal me-2"></i>Edit Link (Slot #{{ $link->slot_number }})
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('customer.copyright_links.update', $link->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold" style="color: #94a3b8;">Title (e.g. song name)</label>
                                            <input type="text" name="title" class="form-control exact-input" value="{{ $link->title }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold" style="color: #94a3b8;">Target Link (URL) <span class="text-danger">*</span></label>
                                            <input type="url" name="url" class="form-control exact-input" value="{{ $link->url }}" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-secondary border-opacity-25">
                                        <button type="button" class="btn btn-dark-outline py-1.5 px-3 small" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-exact-teal py-1.5 px-3">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
