{{-- Graphiques du tableau de bord : $chart (labels, values) et $statusCounts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function () {
    var css = getComputedStyle(document.body);
    var accent = css.getPropertyValue('--accent').trim() || '#7c3aed';
    var accent2 = css.getPropertyValue('--accent-2').trim() || '#d946ef';
    var muted = css.getPropertyValue('--muted').trim() || '#888';
    var border = css.getPropertyValue('--border').trim() || '#ddd';
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.color = muted;

    var rev = document.getElementById('revChart');
    if (rev) {
        var ctx = rev.getContext('2d');
        var grad = ctx.createLinearGradient(0, 0, 0, 280);
        grad.addColorStop(0, accent + '66'); grad.addColorStop(1, accent + '00');
        new Chart(rev, {
            type: 'line',
            data: { labels: @json($chart['labels']), datasets: [{ label: 'Chiffre d\'affaires (FCFA)', data: @json($chart['values']), borderColor: accent, backgroundColor: grad, fill: true, tension: .38, pointRadius: 3, pointBackgroundColor: accent, borderWidth: 3 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } },
                scales: { x: { grid: { display: false } }, y: { beginAtZero: true, grid: { color: border }, ticks: { callback: function (v) { return v >= 1000 ? (v / 1000) + 'k' : v; } } } } }
        });
    }

    var st = document.getElementById('statusChart');
    if (st) {
        var labels = @json(collect(\App\Models\Order::STATUSES)->values());
        var keys = @json(collect(\App\Models\Order::STATUSES)->keys());
        var counts = @json($statusCounts);
        var colors = { pending: '#f59e0b', confirmed: '#3b82f6', assigned: '#8b5cf6', delivering: '#06b6d4', delivered: '#10b981', cancelled: '#ef4444', problem: '#f97316' };
        new Chart(st, {
            type: 'doughnut',
            data: { labels: labels, datasets: [{ data: keys.map(function (k) { return counts[k] || 0; }), backgroundColor: keys.map(function (k) { return colors[k]; }), borderWidth: 0 }] },
            options: { responsive: true, maintainAspectRatio: false, cutout: '68%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true } } } }
        });
    }
})();
</script>
