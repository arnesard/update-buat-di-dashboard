@forelse($overtimes as $ot)
@php
    $normalizedName = trim(strtoupper($ot->employee_name));
    $displayId = $employeeMap[$normalizedName] ?? null;
    $isApproved = $ot->status == 'approved';
    
    $start = \Carbon\Carbon::parse($ot->start_time);
    $end = \Carbon\Carbon::parse($ot->end_time);
    if ($end->lt($start)) $end->addDay();
    $gross = $start->diffInHours($end);
    $totalJam = min(7, $gross);
@endphp
<tr class="overtime-row" id="row-{{ $ot->id }}" style="border-bottom: 1px solid var(--slate-100);">
    <td class="py-3">
        <div class="d-flex flex-column">
            <span class="fw-bold text-slate-900">{{ $ot->employee_name }}</span>
            @if($displayId)
            <span class="text-xs text-primary fw-semibold">{{ $displayId }}</span>
            @endif
        </div>
    </td>
    <td class="py-3 text-sm">
        <div class="fw-medium text-slate-800">{{ $ot->overtime_date->format('d M Y') }}</div>
    </td>
    <td class="py-3">
        <div class="fw-bold text-slate-900">{{ $totalJam }} Jam</div>
        <div class="text-xs text-muted">{{ \Carbon\Carbon::parse($ot->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($ot->end_time)->format('H:i') }}</div>
    </td>
    <td class="py-3">
        <div class="text-sm text-slate-600">{{ $ot->reason }}</div>
    </td>
    <td class="py-3 text-end no-print">
        <div class="d-flex gap-1 justify-content-end action-buttons-container">
            <button type="button" class="btn btn-sm btn-primary text-white py-1 px-2 border-0 shadow-sm rounded-3" 
                    onclick="openEditModal({{ $ot->id }}, '{{ addslashes($ot->employee_name) }}', '{{ $ot->overtime_date->format('Y-m-d') }}', '{{ \Carbon\Carbon::parse($ot->start_time)->format('H:i') }}', '{{ \Carbon\Carbon::parse($ot->end_time)->format('H:i') }}', '{{ addslashes($ot->reason) }}')" 
                    title="Edit">
                <i data-lucide="edit" size="14"></i>
            </button>
            <form action="{{ route('overtime.destroy', $ot) }}" method="POST" class="ajax-action-form" data-row-id="{{ $ot->id }}" data-action-type="delete" onsubmit="return confirm('Hapus data lembur ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-secondary text-white py-1 px-2 border-0 shadow-sm rounded-3" title="Hapus">
                    <i data-lucide="trash-2" size="14"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="5" class="text-center py-5 text-slate-400">
        <i data-lucide="inbox" class="opacity-25 mb-2" size="32"></i>
        <p class="mb-0">Belum ada pengajuan lembur</p>
    </td>
</tr>
@endforelse
