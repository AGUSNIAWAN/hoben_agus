{{--
    ====================================================================
    REUSABLE INLINE UPLOAD PANEL COMPONENT
    ====================================================================
    Usage: @include('dashboard.partials.upload-panel', [
        'panelId'    => 'upload-sales',           // unique DOM id
        'title'      => 'Upload Data Sales',       // heading text
        'subtitle'   => 'Deskripsi singkat',       // sub-heading
        'icon'       => 'ti-chart-bar',             // Tabler icon class
        'iconColor'  => '#F59E0B',                 // icon accent color
        'moduleId'   => 2,                          // excel_module_id default
        'accept'     => '.xlsx,.xls,.csv',         // accepted mime
        'noteText'   => 'catatan default',         // placeholder note
    ])
    ====================================================================
--}}

@php
    $panelId   = $panelId   ?? 'upload-generic-' . rand(1000,9999);
    $title     = $title     ?? 'Upload File';
    $subtitle  = $subtitle  ?? 'Upload file Excel / CSV untuk disimpan & dianalisis.';
    $icon      = $icon      ?? 'ti-upload';
    $iconColor = $iconColor ?? '#F59E0B';
    $moduleId  = $moduleId  ?? ($modules->first()?->id ?? 1);
    $accept    = $accept    ?? '.xlsx,.xls,.csv';
    $noteText  = $noteText  ?? '';
@endphp

<div id="{{ $panelId }}-wrap" style="display:none; margin-top:16px;">
    <div style="background: linear-gradient(135deg, {{ $iconColor }}10 0%, transparent 100%);
                border: 1px solid {{ $iconColor }}30; border-radius: 14px; padding: 18px 20px;">

        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; flex-wrap:wrap; gap:8px;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:36px; height:36px; border-radius:10px; background:{{ $iconColor }}20;
                            display:flex; align-items:center; justify-content:center; color:{{ $iconColor }}; font-size:1.1rem; flex-shrink:0;">
                    <i class="ti {{ $icon }}"></i>
                </div>
                <div>
                    <p style="margin:0; font-weight:800; font-size:0.92rem; color:var(--text-main);">{{ $title }}</p>
                    <p style="margin:0; font-size:0.76rem; color:var(--text-muted);">{{ $subtitle }}</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('{{ $panelId }}-wrap').style.display='none'"
                    style="background:none; border:none; color:var(--text-muted); cursor:pointer; font-size:1.1rem; padding:4px;">
                <i class="ti ti-x"></i>
            </button>
        </div>

        <form action="{{ route('submissions.store') }}" method="POST" enctype="multipart/form-data"
              id="{{ $panelId }}-form"
              onsubmit="handleInlineUploadSubmit(this, '{{ $panelId }}')">
            @csrf
            <input type="hidden" name="excel_module_id" id="{{ $panelId }}-module" value="{{ $moduleId }}">
            <input type="hidden" name="notes" id="{{ $panelId }}-notes" value="{{ $noteText }}">

            <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                {{-- Drop Zone --}}
                <label for="{{ $panelId }}-file"
                       id="{{ $panelId }}-zone"
                       style="flex:1; min-width:200px; display:flex; align-items:center; gap:10px; padding:12px 16px;
                              border:2px dashed {{ $iconColor }}50; border-radius:10px; cursor:pointer;
                              background:{{ $iconColor }}08; transition:all 0.2s;"
                       onmouseover="this.style.borderColor='{{ $iconColor }}'"
                       onmouseout="this.style.borderColor='{{ $iconColor }}50'">
                    <i class="ti ti-cloud-upload" style="font-size:1.5rem; color:{{ $iconColor }};"></i>
                    <div>
                        <p id="{{ $panelId }}-label" style="margin:0; font-size:0.84rem; font-weight:600; color:var(--text-body);">
                            Klik atau drag file ke sini
                        </p>
                        <p style="margin:0; font-size:0.72rem; color:var(--text-muted);">{{ $accept }} • Maks 15 MB</p>
                    </div>
                    <input type="file" id="{{ $panelId }}-file" name="file"
                           accept="{{ $accept }}" style="display:none;" required
                           onchange="onInlineFileChosen(this, '{{ $panelId }}', '{{ $iconColor }}')">
                </label>

                {{-- Catatan --}}
                <input type="text" id="{{ $panelId }}-note-input"
                       placeholder="Catatan (opsional)..."
                       style="padding:10px 14px; background:var(--bg-subtle); border:1px solid var(--border-color);
                              border-radius:10px; color:var(--text-main); font-size:0.82rem; min-width:160px; flex:0 0 auto;"
                       oninput="document.getElementById('{{ $panelId }}-notes').value=this.value">

                {{-- Submit --}}
                <button type="submit" id="{{ $panelId }}-btn"
                        style="display:none; padding:10px 18px; background:{{ $iconColor }};
                               border:none; border-radius:10px; color:#fff; font-weight:700;
                               font-size:0.84rem; cursor:pointer; white-space:nowrap; flex-shrink:0;
                               display:none; align-items:center; gap:6px;">
                    <i class="ti ti-send"></i> Kirim
                </button>
            </div>

            {{-- Progress / Feedback --}}
            <div id="{{ $panelId }}-feedback" style="display:none; margin-top:10px; padding:10px 14px;
                 border-radius:8px; font-size:0.82rem; font-weight:600;"></div>
        </form>
    </div>
</div>

