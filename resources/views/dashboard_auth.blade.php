@extends('layouts.app')

@section('title', 'Dashboard Monitoring Produksi')

@section('content')
    <div class="glass-card shadow-sm border-0 mb-4 bg-white rounded-4 p-4">
    <div class="row g-3 align-items-center">
        <div class="col-12">
            <h4 class="fw-bold mb-0 ps-1">Dashboard Monitoring</h4>
        </div>
    </div>
</div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 rounded-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1">Total Produksi</p>
                            <h3 class="fw-bold mb-0">{{ number_format($totalProduction) }}</h3>
                        </div>
                        <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary">
                            <i data-lucide="package" size="24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 rounded-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1">Karyawan Aktif</p>
                            <h3 class="fw-bold mb-0">{{ $totalEmployees }}</h3>
                        </div>
                        <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success">
                            <i data-lucide="users" size="24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 rounded-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1">Total Ritase</p>
                            <h3 class="fw-bold mb-0">{{ number_format($totalRitase) }}</h3>
                        </div>
                        <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning">
                            <i data-lucide="truck" size="24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 rounded-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1">Shift Saat Ini</p>
                            <h3 class="fw-bold mb-0">Shift {{ $currentShift }}</h3>
                        </div>
                        <div class="rounded-3 p-3 bg-danger bg-opacity-10 text-danger">
                            <i data-lucide="clock" size="24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold mb-0">Visualisasi Produksi Harian</h4>
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                            <i data-lucide="calendar" size="14" class="me-1"></i> {{ date('d M Y') }}
                        </span>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="p-3 border rounded-4 bg-white h-100" style="min-height: 400px;">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3">
                                            <i data-lucide="trending-up" size="18"></i>
                                        </div>
                                        <h5 class="fw-bold m-0 small text-uppercase">Tren Produksi 7 Hari</h5>
                                    </div>
                                    <select id="filterTrend7" class="form-select form-select-sm border-0 shadow-none bg-light" style="width: auto; min-width: 130px;" onchange="filterTrend7Days(this.value)">
                                        <option value="all">Semua Pekerjaan</option>
                                        @foreach ($allJobTypes as $jt)
                                            <option value="{{ $jt }}">{{ $jt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="echartContainer" style="width: 100%; height: 320px;"></div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="p-3 border rounded-4 bg-white h-100" style="min-height: 400px;">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="p-2 bg-success bg-opacity-10 text-success rounded-3">
                                            <i data-lucide="factory" size="18"></i>
                                        </div>
                                        <h5 class="fw-bold m-0 small text-uppercase">Pencapaian Per Plant & Grup</h5>
                                    </div>
                                    <select id="filterPlantGroup" class="form-select form-select-sm border-0 shadow-none bg-light" style="width: auto; min-width: 130px;" onchange="filterPlantGroup(this.value)">
                                        <option value="all">Semua Pekerjaan</option>
                                        @foreach ($allJobTypes as $jt)
                                            <option value="{{ $jt }}">{{ $jt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div id="plantB" style="width: 100%; height: 160px;" class="border rounded-3"></div>
                                    </div>
                                    <div class="col-6">
                                        <div id="plantH" style="width: 100%; height: 160px;" class="border rounded-3"></div>
                                    </div>
                                    <div class="col-6">
                                        <div id="plantI" style="width: 100%; height: 160px;" class="border rounded-3"></div>
                                    </div>
                                    <div class="col-6">
                                        <div id="plantT" style="width: 100%; height: 160px;" class="border rounded-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 opacity-10">

                    <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
                        <div class="p-2 bg-warning bg-opacity-10 text-warning rounded-3">
                            <i data-lucide="trending-up" size="18"></i>
                        </div>
                        Grafik Tren Kinerja Per Orang (Individu)
                    </h5>
                    <div class="row g-3">
                        @foreach (['B', 'H', 'I', 'T'] as $p)
                            <div class="col-md-6 col-lg-6">
                                <div class="p-3 border rounded-4 bg-light shadow-sm">
                                    <div class="mb-3">
                                        <h6 class="fw-bold small text-center text-uppercase text-muted border-bottom pb-2">Plant {{ $p }}</h6>
                                        <select class="form-select form-select-sm border-0 shadow-none bg-white mt-2"
                                                onchange="applyIndividualFilter('job_{{ strtolower($p) }}', this.value)">
                                            @foreach ($jobTypesPerPlant[$p] ?? [] as $job)
                                                <option value="{{ $job }}" {{ ($selectedJobPerPlant[$p] ?? '') == $job ? 'selected' : '' }}>
                                                    {{ $job }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div id="perfPlant{{ $p }}" style="width: 100%; height: 300px;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="table-modern">
        <div class="p-3 border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Data Produksi Hari Ini</h5>
                <input type="text" class="form-control" id="searchInput" placeholder="Cari operator..."
                    style="width: 250px;" onkeyup="searchTable()">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover" id="productionTable">
                <thead>
                    <tr>
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
                    @forelse($receptions as $reception)
                        <tr>
                            <td><span class="badge bg-primary">{{ $reception->plant }}</span></td>
                            <td>{{ $reception->operator_name }}</td>
                            <td>{{ $reception->group }}</td>
                            <td>Shift {{ $reception->shift }}</td>
                            {{-- TAMPILKAN SCAN/STRIPPING/DLL DI SINI --}}
                            <td>{{ $reception->job_type }}</td>
                            <td>
                                <span
                                    class="badge bg-{{ $reception->status == 'Team Leader' ? 'success' : ($reception->status == 'Driver Forklift' ? 'warning' : 'info') }}">
                                    {{ $reception->status }}
                                </span>
                            </td>
                            <td>{{ $reception->ritase_result ?? '-' }}</td>
                            <td>{{ number_format($reception->production_count) }}</td>
                            <td>{{ $reception->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">Belum ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    var trendApiUrl = "{{ route('api.trend-data') }}";
    var trend7ApiUrl = "{{ route('api.trend-7days') }}";
    var plantGroupApiUrl = "{{ route('api.plant-group') }}";
    var chartColors = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#14b8a6','#f97316','#06b6d4','#84cc16'];
    var perfCharts = {};
    var mainChart = null;
    var plantCharts = {};

    function applyIndividualFilter(param, value) {
        // param = "job_b", value = "Scan"
        var plant = param.replace('job_', '').toUpperCase();

        fetch(trendApiUrl + '?plant=' + encodeURIComponent(plant) + '&job=' + encodeURIComponent(value))
            .then(function(res) { return res.json(); })
            .then(function(data) {
                var chart = perfCharts[plant];
                if (!chart) return;

                var names = Object.keys(data.series);
                var series = names.map(function(name, idx) {
                    return {
                        name: name,
                        type: 'line',
                        data: data.series[name],
                        smooth: true,
                        symbol: 'circle',
                        symbolSize: 6,
                        itemStyle: { color: chartColors[idx % chartColors.length] },
                        lineStyle: { width: 2 }
                    };
                });

                chart.setOption({
                    tooltip: { trigger: 'axis' },
                    legend: {
                        data: names,
                        bottom: 0,
                        textStyle: { fontSize: 10 },
                        type: 'scroll'
                    },
                    grid: { left: '5%', right: '5%', bottom: '18%', top: '8%', containLabel: true },
                    xAxis: {
                        type: 'category',
                        data: data.dates,
                        axisLabel: { fontSize: 9 },
                        axisLine: { lineStyle: { color: '#ccc' } }
                    },
                    yAxis: {
                        type: 'value',
                        axisLabel: { fontSize: 9 },
                        splitLine: { lineStyle: { type: 'dashed' } }
                    },
                    series: series
                }, true); // true = replace all, don't merge old series
            });
    }

    // --- FILTER FUNCTIONS ---
    function filterTrend7Days(job) {
        fetch(trend7ApiUrl + '?job=' + encodeURIComponent(job))
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (!mainChart) return;
                mainChart.setOption({
                    xAxis: { data: data.dates },
                    series: [{ data: data.totals }]
                });
            });
    }

    function filterPlantGroup(job) {
        fetch(plantGroupApiUrl + '?job=' + encodeURIComponent(job))
            .then(function(res) { return res.json(); })
            .then(function(data) {
                ['B','H','I','T'].forEach(function(p) {
                    var chart = plantCharts[p];
                    if (!chart) return;
                    var chartData = data[p] || {};
                    var keys = Object.keys(chartData);
                    var values = keys.map(function(k) { return chartData[k]; });

                    var sortedValues = [...values].sort((a, b) => b - a);
                    var maxVal = sortedValues[0];
                    var minVal = sortedValues[sortedValues.length - 1];

                    var seriesData = keys.map(function(key, index) {
                        var val = values[index];
                        var color = '#f59e0b';
                        if (val > 0) {
                            if (val === maxVal) color = '#10b981';
                            else if (val === minVal && values.length > 1) color = '#ef4444';
                        }
                        return { value: val, itemStyle: { color: color } };
                    });

                    chart.setOption({
                        title: { text: 'Plant ' + p, textStyle: { fontSize: 14 } },
                        grid: { left: '3%', right: '15%', bottom: '5%', top: '30%', containLabel: true },
                        tooltip: { trigger: 'axis' },
                        xAxis: { type: 'value', show: false },
                        yAxis: {
                            type: 'category',
                            data: keys,
                            inverse: true,
                            axisLine: { show: false },
                            axisTick: { show: false }
                        },
                        series: [{
                            type: 'bar',
                            data: seriesData,
                            barWidth: '60%',
                            label: { show: true, position: 'right', fontWeight: 'bold' },
                            itemStyle: { borderRadius: [0, 5, 5, 0] }
                        }]
                    }, true);
                });
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // 1. CHART UTAMA (TREN 7 HARI TOTAL)
        var mainContainer = document.getElementById('echartContainer');
        if (mainContainer) {
            mainChart = echarts.init(mainContainer);
            mainChart.setOption({
                tooltip: { trigger: 'axis' },
                grid: { left: '3%', right: '4%', bottom: '3%', top: '10%', containLabel: true },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: {!! json_encode($data7Days->pluck('date')->map(fn($d) => date('d M', strtotime($d)))) !!},
                    axisLine: { show: false },
                    axisTick: { show: false }
                },
                yAxis: {
                    type: 'value',
                    splitLine: { lineStyle: { type: 'dashed' } }
                },
                series: [{
                    name: 'Total Produksi',
                    type: 'line',
                    smooth: true,
                    symbol: 'circle',
                    symbolSize: 8,
                    data: {!! json_encode(array_map('intval', $data7Days->pluck('total')->toArray())) !!},
                    itemStyle: { color: '#0d6efd' },
                    lineStyle: { width: 3 },
                    areaStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: 'rgba(13, 110, 253, 0.4)' },
                            { offset: 1, color: 'rgba(13, 110, 253, 0.05)' }
                        ])
                    }
                }]
            });
        }

        // 2. PLANT CHARTS (BAR CHARTS FOR GROUP ACHIEVEMENT)
        function createPlantChart(id, title, data) {
            var chartDom = document.getElementById(id);
            if (!chartDom) return null;

            var chart = echarts.init(chartDom);
            var chartData = data || {};
            var keys = Object.keys(chartData);
            var values = Object.values(chartData);

            var sortedValues = [...values].sort((a, b) => b - a);
            var maxVal = sortedValues[0];
            var minVal = sortedValues[sortedValues.length - 1];

            var seriesData = keys.map((key, index) => {
                var val = values[index];
                var color = '#f59e0b'; // Default: Orange
                if (val > 0) {
                    if (val === maxVal) color = '#10b981'; // Green
                    else if (val === minVal && values.length > 1) color = '#ef4444'; // Red
                }
                return { value: val, itemStyle: { color: color } };
            });

            chart.setOption({
                title: { text: 'Plant ' + title, textStyle: { fontSize: 14 } },
                grid: { left: '3%', right: '15%', bottom: '5%', top: '30%', containLabel: true },
                tooltip: { trigger: 'axis' },
                xAxis: { type: 'value', show: false },
                yAxis: {
                    type: 'category',
                    data: keys,
                    inverse: true,
                    axisLine: { show: false },
                    axisTick: { show: false }
                },
                series: [{
                    type: 'bar',
                    data: seriesData,
                    barWidth: '60%',
                    label: { show: true, position: 'right', fontWeight: 'bold' },
                    itemStyle: { borderRadius: [0, 5, 5, 0] }
                }]
            });
            return chart;
        }

        var pB = createPlantChart('plantB', 'B', @json($dataPlantB));
        var pH = createPlantChart('plantH', 'H', @json($dataPlantH));
        var pI = createPlantChart('plantI', 'I', @json($dataPlantI));
        var pT = createPlantChart('plantT', 'T', @json($dataPlantT));
        plantCharts = { B: pB, H: pH, I: pI, T: pT };

        // 3. MULTI-LINE TREND CHARTS (PER JOB TYPE - MULTIPLE WORKERS)
        var colors = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#14b8a6','#f97316','#06b6d4','#84cc16'];
        var dates = {!! json_encode($trendDates) !!};
        var allTrendData = @json($trendSeriesPerPlant);

        ['B','H','I','T'].forEach(function(plant) {
            var chartDom = document.getElementById('perfPlant' + plant);
            if (!chartDom) return;

            var chart = echarts.init(chartDom);
            var plantData = allTrendData[plant] || {};
            var names = Object.keys(plantData);

            var series = names.map(function(name, idx) {
                return {
                    name: name,
                    type: 'line',
                    data: plantData[name],
                    smooth: true,
                    symbol: 'circle',
                    symbolSize: 6,
                    itemStyle: { color: colors[idx % colors.length] },
                    lineStyle: { width: 2 }
                };
            });

            chart.setOption({
                tooltip: { trigger: 'axis' },
                legend: {
                    data: names,
                    bottom: 0,
                    textStyle: { fontSize: 10 },
                    type: 'scroll'
                },
                grid: { left: '5%', right: '5%', bottom: '18%', top: '8%', containLabel: true },
                xAxis: {
                    type: 'category',
                    data: dates,
                    axisLabel: { fontSize: 9 },
                    axisLine: { lineStyle: { color: '#ccc' } }
                },
                yAxis: {
                    type: 'value',
                    axisLabel: { fontSize: 9 },
                    splitLine: { lineStyle: { type: 'dashed' } }
                },
                series: series
            });

            perfCharts[plant] = chart;
        });

        window.addEventListener('resize', function() {
            mainChart && mainChart.resize();
            Object.values(plantCharts).forEach(function(c) { c && c.resize(); });
            Object.values(perfCharts).forEach(function(c) { c && c.resize(); });
        });
    });

    function searchTable() {
        var input = document.getElementById('searchInput').value.toLowerCase();
        var rows = document.querySelectorAll('#productionTable tbody tr');
        rows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(input) ? '' : 'none';
        });
    }
</script>
@endpush
