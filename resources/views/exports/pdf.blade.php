<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Produksi - {{ $today->format('d/m/Y') }}</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 20px;
            background: #f8fafc;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            border-radius: 12px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #64748b;
            font-size: 14px;
        }
        .table-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #f8fafc;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #1e293b;
            border-bottom: 1px solid #e5e7eb;
        }
        td {
            padding: 15px;
            border-bottom: 1px solid #f1f5f9;
        }
        tr:last-child td {
            border-bottom: none;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
        }
        .badge-primary { background: #dbeafe; color: #1e40af; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fed7aa; color: #92400e; }
        .badge-info { background: #e0e7ff; color: #3730a3; }
        .shift-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 6px;
        }
        .shift-1 { background: #10b981; }
        .shift-2 { background: #f59e0b; }
        .shift-3 { background: #8b5cf6; }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #64748b;
            font-size: 12px;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏭 Laporan Produksi Harian</h1>
        <p>{{ $today->format('l, d F Y') }}</p>
    </div>

    @php
        $totalProduction = $receptions->sum('production_count');
        $totalEmployees = $receptions->unique('operator_name')->count();
        $totalRitase = $receptions->where('status', 'Driver Forklift')->sum('ritase_result');
    @endphp

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ number_format($totalProduction) }}</div>
            <div class="stat-label">Total Produksi</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $totalEmployees }}</div>
            <div class="stat-label">Jumlah Karyawan</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ number_format($totalRitase) }}</div>
            <div class="stat-label">Total Ritase Forklift</div>
        </div>
    </div>

    <div class="table-container">
        @if($receptions->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Plant</th>
                        <th>Operator</th>
                        <th>Group</th>
                        <th>Shift</th>
                        <th>Pekerjaan</th>
                        <th>Status</th>
                        <th>Ritase</th>
                        <th>Produksi</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($receptions as $reception)
                        <tr>
                            <td>{{ $reception->check_in->format('H:i') }}</td>
                            <td><span class="badge badge-primary">{{ $reception->plant }}</span></td>
                            <td>{{ $reception->operator_name }}</td>
                            <td>{{ $reception->group }}</td>
                            <td>
                                <span class="shift-indicator shift-{{ $reception->shift }}"></span>
                                Shift {{ $reception->shift }}
                            </td>
                            <td>{{ $reception->job_type }}</td>
                            <td>
                                <span class="badge {{ $reception->status == 'Team Leader' ? 'badge-success' : ($reception->status == 'Driver Forklift' ? 'badge-warning' : 'badge-info') }}">
                                    {{ $reception->status }}
                                </span>
                            </td>
                            <td>{{ $reception->ritase_result ?? '-' }}</td>
                            <td>{{ number_format($reception->production_count) }}</td>
                            <td>{{ $reception->notes ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                <p>Tidak ada data produksi untuk hari ini</p>
            </div>
        @endif
    </div>

    <div class="footer">
        <p>Laporan ini dihasilkan pada {{ now()->format('d/m/Y H:i:s') }} dari Sistem Monitoring Produksi</p>
    </div>
</body>
</html>
