@extends('layouts.app')

@section('title', 'Upload Song - Rajdoot Nivedan Media')

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
                    <li class="breadcrumb-item active text-light" aria-current="page">Upload Song</li>
                </ol>
            </nav>
            <h1 class="h3 fw-extrabold text-light mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-music text-teal" style="font-size: 1.3rem;"></i>
                Upload Song &amp; Audio Management
            </h1>
            <p class="mb-0" style="color: #94a3b8; font-size: 0.9rem;">
                Upload your latest tracks with high-resolution 3000 &times; 3000 px cover artwork and MP3 audio files.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge px-3 py-2 rounded-pill fw-bold" style="background: rgba(0, 210, 170, 0.12); color: #00d2aa; border: 1px solid rgba(0, 210, 170, 0.3); font-size: 0.8rem;">
                <i class="fa-solid fa-compact-disc me-1"></i> {{ $user->songs->count() }} Track{{ $user->songs->count() === 1 ? '' : 's' }} in Catalog
            </span>
            <a href="{{ route('customer.dashboard') }}" class="btn btn-sm btn-dark-outline py-2 px-3 rounded-3 small">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Card 1: Upload Song Form -->
    <div class="exact-card mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3 pb-1">
            <div class="d-flex align-items-center gap-2">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(0, 210, 170, 0.12); display: flex; align-items: center; justify-content: center; color: #00d2aa;">
                    <i class="fa-solid fa-arrow-up-from-bracket fs-6"></i>
                </div>
                <h2 class="h6 fw-bold text-light mb-0" style="font-size: 0.95rem;">Upload New Track</h2>
            </div>
            <span class="badge rounded-pill px-3 py-1.5 small fw-bold" style="background: rgba(0, 210, 170, 0.1); color: #00d2aa; border: 1px solid rgba(0, 210, 170, 0.25);">
                <i class="fa-regular fa-copyright me-1"></i> ℗ 2026 Rajdoot Nivedan
            </span>
        </div>

        <form action="{{ route('customer.songs.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Row 1: Song Title & Singer -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label style="color: #94a3b8; font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 6px; display: block;">
                        SONG TITLE <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="title" class="form-control exact-input" placeholder="e.g. Dil Deewana" value="{{ old('title') }}" required>
                </div>
                <div class="col-md-6">
                    <label style="color: #94a3b8; font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 6px; display: block;">
                        SINGER <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="singer" class="form-control exact-input" placeholder="e.g. Arijit Singh, Shreya Ghoshal" value="{{ old('singer') }}" required>
                </div>
            </div>

            <!-- Row 2: Lyrics / Composer & Producer -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label style="color: #94a3b8; font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 6px; display: block;">
                        LYRICS / COMPOSER <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="composer" class="form-control exact-input" placeholder="e.g. Pritam Chakraborty" value="{{ old('composer') }}" required>
                </div>
                <div class="col-md-6">
                    <label style="color: #94a3b8; font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 6px; display: block;">
                        PRODUCER <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="producer" class="form-control exact-input" placeholder="e.g. Rajdoot Nivedan Media" value="{{ old('producer') }}" required>
                </div>
            </div>

            <!-- Row 3: Copyright / P-Line (Auto-Included) -->
            <div class="mb-3">
                <label style="color: #94a3b8; font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 6px; display: block;">
                    COPYRIGHT &mdash; P-LINE <span class="text-teal">(AUTOMATICALLY INCLUDED)</span>
                </label>
                <div class="position-relative">
                    <input type="text" name="copyright" class="form-control exact-input" value="℗ 2026 Rajdoot Nivedan" readonly style="background: rgba(0, 210, 170, 0.05); border-color: rgba(0, 210, 170, 0.35); color: #00d2aa; font-weight: 700; padding-right: 140px;">
                    <span class="position-absolute end-0 top-50 translate-middle-y me-3 badge rounded-pill px-2.5 py-1" style="background: rgba(0, 210, 170, 0.18); color: #00d2aa; font-size: 0.72rem; border: 1px solid rgba(0, 210, 170, 0.35);">
                        <i class="fa-solid fa-circle-check me-1"></i> Auto-Included
                    </span>
                </div>
                <small class="text-secondary mt-1 d-block" style="font-size: 0.72rem;">
                    Every newly uploaded track is automatically registered under: <strong class="text-light">℗ 2026 Rajdoot Nivedan</strong>
                </small>
            </div>

            <!-- Row 3: Cover Image (3000 x 3000 px) -->
            <div class="mb-3">
                <label style="color: #94a3b8; font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 6px; display: block;">
                    COVER IMAGE &mdash; 3000 &times; 3000 PX <span class="text-danger">*</span>
                </label>
                <div class="custom-file-select-box" onclick="document.getElementById('cover_image_input').click();">
                    <i class="fa-regular fa-image" style="color: #00d2aa; font-size: 1.2rem;"></i>
                    <span id="cover_file_label" style="color: #ffffff;">Choose 3000 &times; 3000 image (JPEG, PNG, WEBP)</span>
                </div>
                <input type="file" id="cover_image_input" name="cover_image" class="d-none" accept="image/jpeg,image/png,image/jpg,image/webp" required>
            </div>

            <!-- Row 4: MP3 Song File -->
            <div class="p-3 rounded-3 mb-3" style="background: rgba(255, 255, 255, 0.015); border: 1px solid rgba(255, 255, 255, 0.06);">
                <label style="color: #94a3b8; font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 6px; display: block;">
                    MP3 SONG FILE <span class="text-danger">*</span>
                </label>
                <div class="custom-file-select-box" onclick="document.getElementById('audio_file_input').click();">
                    <i class="fa-solid fa-music" style="color: #22d3ee; font-size: 1.2rem;"></i>
                    <span id="audio_file_label" style="color: #ffffff;">Choose MP3 audio file</span>
                </div>
                <input type="file" id="audio_file_input" name="audio_file" class="d-none" accept=".mp3,audio/mpeg,audio/mp3" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-exact-teal py-2.5 px-4">
                <i class="fa-solid fa-arrow-up-from-bracket me-1.5"></i> Upload song
            </button>
        </form>
    </div>

    <!-- Card 2: Uploaded Songs List -->
    <div class="exact-card mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3 pb-1">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-headphones text-teal" style="font-size: 0.95rem;"></i>
                <h3 class="h6 fw-bold text-light mb-0" style="font-size: 0.92rem;">
                    Your Uploaded Songs ({{ $user->songs->count() }})
                </h3>
            </div>
            <span class="small" style="color: #94a3b8; font-size: 0.8rem;">
                High-resolution audio streaming active
            </span>
        </div>

        @if($user->songs->count() === 0)
            <div class="text-center py-5">
                <div class="mb-3">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(0, 210, 170, 0.08); border: 1px dashed rgba(0, 210, 170, 0.3); display: inline-flex; align-items: center; justify-content: center; color: #00d2aa; font-size: 1.4rem;">
                        <i class="fa-solid fa-music"></i>
                    </div>
                </div>
                <h4 class="h6 fw-bold text-light mb-1">No songs uploaded yet</h4>
                <p class="small mb-0" style="color: #64748b; font-size: 0.86rem; max-width: 420px; margin: 0 auto;">
                    Use the form above to upload your first audio track with 3000 &times; 3000 px artwork.
                </p>
            </div>
        @else
            <div class="d-flex flex-column gap-3">
                @foreach($user->songs as $song)
                    <div class="p-3 rounded-3" style="background: #111726; border: 1px solid rgba(255, 255, 255, 0.06);">
                        <div class="row align-items-center g-3">
                            
                            <!-- Cover Thumbnail -->
                            <div class="col-auto">
                                <div class="position-relative" style="width: 64px; height: 64px; border-radius: 10px; overflow: hidden; border: 1px solid rgba(0, 210, 170, 0.3); background: #050810;">
                                    <img src="{{ $song->cover_image_url }}" alt="{{ $song->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    <button type="button" class="btn btn-sm position-absolute top-0 end-0 p-1 m-0.5 rounded-circle" style="background: rgba(0,0,0,0.7); color: #00d2aa; border: none; font-size: 0.65rem;" title="View 3000 × 3000 Cover Art" data-bs-toggle="modal" data-bs-target="#coverZoomModal{{ $song->id }}">
                                        <i class="fa-solid fa-magnifying-glass-plus"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Song Info -->
                            <div class="col-md-4">
                                <div class="fw-bold text-light text-truncate" style="font-size: 0.95rem;">{{ $song->title }}</div>
                                <div class="small text-truncate" style="color: #00d2aa; font-size: 0.82rem;">{{ $song->singer }}</div>
                                <div class="small text-truncate" style="color: #94a3b8; font-size: 0.75rem;">
                                    {{ $song->composer }} &middot; {{ $song->producer }}
                                </div>
                                <div class="small text-truncate mt-1" style="color: #00d2aa; font-size: 0.75rem; font-weight: 600;">
                                    <i class="fa-regular fa-copyright me-1"></i> {{ $song->copyright ?: '℗ 2026 Rajdoot Nivedan' }}
                                </div>
                            </div>

                            <!-- Audio Player -->
                            <div class="col-md-4">
                                <audio controls class="w-100" style="height: 36px; border-radius: 6px;" preload="metadata" src="{{ $song->audio_file_url }}"></audio>
                            </div>

                            <!-- Actions -->
                            <div class="col-12 col-md-auto ms-md-auto text-end">
                                <button type="button" class="btn btn-sm btn-dark-outline py-1.5 px-3 rounded-pill small fw-semibold text-info" style="border-color: rgba(56, 189, 248, 0.3);" data-bs-toggle="modal" data-bs-target="#editSongModal{{ $song->id }}">
                                    <i class="fa-regular fa-pen-to-square me-1"></i> Edit
                                </button>
                                <form action="{{ route('customer.songs.destroy', $song->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete &quot;{{ $song->title }}&quot;?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-dark-outline py-1.5 px-3 rounded-pill small fw-semibold text-danger border-danger border-opacity-40">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>

                    <!-- Modal: Cover Zoom -->
                    <div class="modal fade" id="coverZoomModal{{ $song->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(0,210,170,0.3); border-radius: 16px;">
                                <div class="modal-header border-secondary border-opacity-25">
                                    <h5 class="modal-title fw-bold text-light fs-6">3000 &times; 3000 px Cover: {{ $song->title }}</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4 text-center">
                                    <img src="{{ $song->cover_image_url }}" alt="{{ $song->title }}" class="img-fluid rounded mb-3" style="max-height: 440px; object-fit: contain;">
                                    <div>
                                        <a href="{{ $song->cover_image_url }}" download target="_blank" class="btn btn-exact-teal py-1.5 px-3 small">
                                            <i class="fa-solid fa-download me-1"></i> Download Artwork
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal: Edit Song -->
                    <div class="modal fade" id="editSongModal{{ $song->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px;">
                                <div class="modal-header border-secondary border-opacity-25">
                                    <h5 class="modal-title fw-bold text-light fs-6">
                                        <i class="fa-regular fa-pen-to-square text-teal me-2"></i>Edit Song: {{ $song->title }}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('customer.songs.update', $song->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body p-4 text-start">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label small" style="color: #94a3b8;">Song Title</label>
                                                <input type="text" name="title" class="form-control exact-input" value="{{ $song->title }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small" style="color: #94a3b8;">Singer</label>
                                                <input type="text" name="singer" class="form-control exact-input" value="{{ $song->singer }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small" style="color: #94a3b8;">Lyrics / Composer</label>
                                                <input type="text" name="composer" class="form-control exact-input" value="{{ $song->composer }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small" style="color: #94a3b8;">Producer</label>
                                                <input type="text" name="producer" class="form-control exact-input" value="{{ $song->producer }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small" style="color: #94a3b8;">Replace 3000 &times; 3000 px Cover Art (Optional)</label>
                                                <input type="file" name="cover_image" class="form-control exact-input" accept="image/jpeg,image/png,image/jpg,image/webp">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small" style="color: #94a3b8;">Replace MP3 Audio File (Optional)</label>
                                                <input type="file" name="audio_file" class="form-control exact-input" accept=".mp3,audio/mpeg,audio/mp3">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small" style="color: #94a3b8;">Copyright &mdash; P-Line <span class="text-teal">(Auto-Included)</span></label>
                                                <input type="text" name="copyright" class="form-control exact-input" value="{{ $song->copyright ?: '℗ 2026 Rajdoot Nivedan' }}" readonly style="background: rgba(0, 210, 170, 0.05); border-color: rgba(0, 210, 170, 0.35); color: #00d2aa; font-weight: 600;">
                                            </div>
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // File selector label updates
        const coverInput = document.getElementById('cover_image_input');
        const coverLabel = document.getElementById('cover_file_label');
        if (coverInput && coverLabel) {
            coverInput.addEventListener('change', function(e) {
                if (this.files && this.files[0]) {
                    coverLabel.innerHTML = `<strong class="text-light">${this.files[0].name}</strong> <span class="badge ms-2" style="background: rgba(0, 210, 170, 0.15); color: #00d2aa;">Ready</span>`;
                }
            });
        }

        const audioInput = document.getElementById('audio_file_input');
        const audioLabel = document.getElementById('audio_file_label');
        if (audioInput && audioLabel) {
            audioInput.addEventListener('change', function(e) {
                if (this.files && this.files[0]) {
                    const sizeMb = (this.files[0].size / (1024 * 1024)).toFixed(2);
                    audioLabel.innerHTML = `<strong class="text-light">${this.files[0].name}</strong> <span class="badge ms-2" style="background: rgba(34, 211, 238, 0.15); color: #22d3ee;">${sizeMb} MB</span>`;
                }
            });
        }
    });
</script>
@endsection
