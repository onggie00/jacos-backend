<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php
/**
 * application/views/mhcu_tracker_view.php
 *
 * Halaman PUBLIK (tanpa login) untuk ditampilkan di layar proyektor saat sesi
 * pengisian MHCU serentak. Auto-refresh tiap 8 detik lewat endpoint publik
 * (agregat saja, TIDAK PERNAH menampilkan nama/npp/skor individu).
 *
 * v2.2 - Tambah switch tema dark/light (persist di localStorage).
 *
 * $logo_url (opsional, dari controller): kalau tidak dikirim, fallback ke
 * base_url('uploads/logo_labschool_cibubur_vertical.png').
 *
 * Kontrak response endpoint publik yang di-fetch:
 * {
 *   "periode": { "nama_periode": "...", "tanggal_mulai": "...", "tanggal_selesai": "..." },
 *   "ringkasan": { "target": 180, "sudah_mulai": 150, "selesai": 142 },
 *   "per_unit": [ { "label": "Guru SD", "selesai": 40, "total": 45 }, ... ],
 *   "per_tahap": [ { "tahap": 1, "label": "Instrument 1", "jumlah": 8 }, ... ],
 *   "last_updated": "2026-07-28 09:15:32"
 * }
 */
?>
<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tracker Pengisian MHCU</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        /* ========== DARK THEME (default) ========== */
        :root, [data-theme="dark"] {
            --bg: #0b1120;
            --bg-card: #141b2e;
            --bg-card-2: #171f36;
            --border: #232c45;
            --text-main: #f1f5f9;
            --text-dim: #8996b3;
            --track-bg: #1f2a44;
            --track-unit-bg: #1f2a44;
            --accent-1: #6366f1;
            --accent-2: #22d3ee;
            --accent-3: #34d399;
            --accent-4: #475569;
            --donut-track: #1f2a44;
            --donut-text: #f1f5f9;
            --donut-sub: #8996b3;
            --kartu-glow: rgba(99,102,241,0.18);
            --brand-glow: rgba(99,102,241,0.20);
            --brand-grad-start: rgba(99,102,241,0.16);
            --brand-grad-end: rgba(34,211,238,0.10);
            --body-grad-1: rgba(99,102,241,0.16);
            --body-grad-2: rgba(34,211,238,0.12);
            --angka-from: #ffffff;
            --angka-to: #c7d2fe;
            --section-title-color: #dbe4f5;
            --legend-dot-off: #334155;
            --toggle-bg: #334155;
            --toggle-knob: #f1f5f9;
            --switch-icon-opacity: 1;
        }

        /* ========== LIGHT THEME ========== */
        [data-theme="light"] {
            --bg: #f8fafc;
            --bg-card: #ffffff;
            --bg-card-2: #f1f5f9;
            --border: #e2e8f0;
            --text-main: #1e293b;
            --text-dim: #64748b;
            --track-bg: #e2e8f0;
            --track-unit-bg: #e2e8f0;
            --accent-1: #6366f1;
            --accent-2: #0891b2;
            --accent-3: #10b981;
            --accent-4: #94a3b8;
            --donut-track: #e2e8f0;
            --donut-text: #1e293b;
            --donut-sub: #64748b;
            --kartu-glow: rgba(99,102,241,0.08);
            --brand-glow: rgba(99,102,241,0.12);
            --brand-grad-start: rgba(99,102,241,0.10);
            --brand-grad-end: rgba(34,211,238,0.06);
            --body-grad-1: rgba(99,102,241,0.06);
            --body-grad-2: rgba(34,211,238,0.04);
            --angka-from: #1e293b;
            --angka-to: #6366f1;
            --section-title-color: #334155;
            --legend-dot-off: #cbd5e1;
            --toggle-bg: #cbd5e1;
            --toggle-knob: #ffffff;
            --switch-icon-opacity: 0.6;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background:
                radial-gradient(circle at 15% 0%, var(--body-grad-1), transparent 45%),
                radial-gradient(circle at 85% 15%, var(--body-grad-2), transparent 40%),
                var(--bg);
            color: var(--text-main);
            padding: 36px 44px;
            min-height: 100vh;
            transition: background 0.35s ease, color 0.35s ease;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 30px;
        }
        .header .brand { display: flex; align-items: center; gap: 16px; }
        .header .brand-icon {
            height: 58px; width: auto;
            padding: 6px 10px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--brand-grad-start), var(--brand-grad-end));
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 24px var(--brand-glow);
            transition: background 0.35s ease, box-shadow 0.35s ease;
        }
        .header .brand-icon img {
            height: 100%;
            width: auto;
            object-fit: contain;
            display: block;
        }
        .header h1 {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .header .periode-info {
            font-size: 16px;
            color: var(--text-dim);
            margin-top: 2px;
        }

        /* Toggle & controls area */
        .controls {
            text-align: right;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
        }
        .controls-row {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .live-badge {
            display: inline-flex; align-items: center; gap: 8px;
            font-size: 14px; color: var(--text-dim);
        }
        .live-dot {
            width: 9px; height: 9px; border-radius: 50%;
            background: var(--accent-3);
            box-shadow: 0 0 0 0 rgba(52,211,153,0.6);
            animation: pulse 1.8s infinite;
        }
        @keyframes pulse {
            0%   { box-shadow: 0 0 0 0 rgba(52,211,153,0.55); }
            70%  { box-shadow: 0 0 0 9px rgba(52,211,153,0); }
            100% { box-shadow: 0 0 0 0 rgba(52,211,153,0); }
        }
        #btn-refresh {
            padding: 10px 22px;
            font-size: 15px;
            font-weight: 600;
            background: linear-gradient(135deg, var(--accent-1), #4f46e5);
            color: #fff;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 6px 16px rgba(79,70,229,0.35);
        }
        #btn-refresh:active { transform: translateY(1px); }

        /* Theme switch */
        .theme-switch {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
        }
        .theme-switch .icon {
            font-size: 16px;
            transition: opacity 0.3s ease;
        }
        .theme-switch-track {
            width: 44px; height: 24px;
            border-radius: 12px;
            background: var(--toggle-bg);
            position: relative;
            transition: background 0.3s ease;
        }
        .theme-switch-knob {
            width: 18px; height: 18px;
            border-radius: 50%;
            background: var(--toggle-knob);
            position: absolute;
            top: 3px; left: 3px;
            transition: transform 0.3s ease, background 0.3s ease;
            box-shadow: 0 1px 4px rgba(0,0,0,0.2);
        }
        [data-theme="light"] .theme-switch-knob {
            transform: translateX(20px);
        }

        .top-grid {
            display: grid;
            grid-template-columns: 340px 1fr;
            gap: 22px;
            margin-bottom: 34px;
        }

        .donut-card {
            background: linear-gradient(160deg, var(--bg-card-2), var(--bg-card));
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 26px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: background 0.35s ease, border-color 0.35s ease;
        }
        .donut-card .donut-center-label {
            font-size: 13px; color: var(--text-dim); margin-top: 10px; text-align: center;
        }
        .donut-legend {
            display: flex; gap: 18px; margin-top: 14px; flex-wrap: wrap; justify-content: center;
        }
        .donut-legend span { font-size: 13px; color: var(--text-dim); display: flex; align-items: center; gap: 6px; }
        .legend-dot { width: 10px; height: 10px; border-radius: 3px; display: inline-block; }

        .kartu-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 18px;
        }
        .kartu {
            background: linear-gradient(150deg, var(--bg-card-2), var(--bg-card));
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 24px;
            position: relative;
            overflow: hidden;
            transition: background 0.35s ease, border-color 0.35s ease;
        }
        .kartu::after {
            content: '';
            position: absolute; top: -40%; right: -20%;
            width: 140px; height: 140px; border-radius: 50%;
            background: radial-gradient(circle, var(--kartu-glow), transparent 70%);
            transition: background 0.35s ease;
        }
        .kartu .angka {
            font-size: 52px;
            font-weight: 800;
            line-height: 1.1;
            background: linear-gradient(135deg, var(--angka-from), var(--angka-to));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .kartu .label {
            font-size: 15px;
            color: var(--text-dim);
            margin-top: 6px;
        }
        .kartu .sub {
            font-size: 13px;
            color: var(--accent-2);
            margin-top: 10px;
            font-weight: 600;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 16px;
            color: var(--section-title-color);
            display: flex; align-items: center; gap: 10px;
            transition: color 0.35s ease;
        }
        .section-title .bar { width: 5px; height: 20px; border-radius: 3px; background: linear-gradient(180deg, var(--accent-1), var(--accent-2)); }

        .unit-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
            margin-bottom: 36px;
        }
        .unit-item {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 16px 20px;
            transition: background 0.35s ease, border-color 0.35s ease;
        }
        .unit-item .unit-nama {
            font-size: 16px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
        }
        .unit-item .unit-nama .pct { color: var(--accent-2); font-weight: 700; }
        .unit-item .track {
            width: 100%;
            height: 10px;
            background: var(--track-unit-bg);
            border-radius: 6px;
            overflow: hidden;
            transition: background 0.35s ease;
        }
        .unit-item .fill {
            height: 100%;
            background: linear-gradient(90deg, var(--accent-1), var(--accent-2));
            transition: width 0.7s ease;
            border-radius: 6px;
        }

        .funnel-wrap {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px 28px 14px 28px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 14px;
            height: 200px;
            transition: background 0.35s ease, border-color 0.35s ease;
        }
        .funnel-bar-group {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            height: 100%;
        }
        .funnel-bar {
            width: 100%;
            max-width: 64px;
            border-radius: 8px 8px 0 0;
            background: linear-gradient(180deg, var(--accent-2), var(--accent-1));
            transition: height 0.7s ease;
            min-height: 4px;
        }
        .funnel-jumlah {
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 6px;
        }
        .funnel-label {
            font-size: 12px;
            color: var(--text-dim);
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="brand">
            <div class="brand-icon">
                <img src="<?php echo isset($logo_url) ? $logo_url : base_url('uploads/logo_labschool_cibubur_vertical.png'); ?>" alt="Logo Labschool Cibubur">
            </div>
            <div>
                <h1>Tracker Pengisian MHCU</h1>
                <div class="periode-info" id="periode-info">Memuat data periode...</div>
            </div>
        </div>
        <div class="controls">
            <div class="controls-row">
                <div class="live-badge"><span class="live-dot"></span><span id="last-updated">Memuat...</span></div>
                <div class="theme-switch" id="theme-switch" title="Ganti tema">
                    <span class="icon" id="theme-icon-dark">🌙</span>
                    <div class="theme-switch-track"><div class="theme-switch-knob"></div></div>
                    <span class="icon" id="theme-icon-light">☀️</span>
                </div>
            </div>
            <div><button id="btn-refresh" onclick="muatData()">🔄 Refresh Manual</button></div>
        </div>
    </div>

    <div class="top-grid">
        <div class="donut-card">
            <svg id="donut-svg" width="200" height="200" viewBox="0 0 200 200">
                <circle cx="100" cy="100" r="80" fill="none" stroke="var(--donut-track)" stroke-width="22"/>
                <circle id="donut-selesai" cx="100" cy="100" r="80" fill="none" stroke="url(#gradSelesai)"
                        stroke-width="22" stroke-linecap="round" transform="rotate(-90 100 100)"
                        stroke-dasharray="0 502" />
                <circle id="donut-mulai" cx="100" cy="100" r="80" fill="none" stroke="url(#gradMulai)"
                        stroke-width="22" stroke-linecap="round" transform="rotate(-90 100 100)"
                        stroke-dasharray="0 502" />
                <defs>
                    <linearGradient id="gradSelesai" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="var(--accent-3)"/>
                        <stop offset="100%" stop-color="var(--accent-2)"/>
                    </linearGradient>
                    <linearGradient id="gradMulai" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="var(--accent-1)"/>
                        <stop offset="100%" stop-color="#818cf8"/>
                    </linearGradient>
                </defs>
                <text x="100" y="94" text-anchor="middle" font-size="34" font-weight="800" fill="var(--donut-text)" id="donut-persen">0%</text>
                <text x="100" y="118" text-anchor="middle" font-size="12" fill="var(--donut-sub)">selesai mengisi</text>
            </svg>
            <div class="donut-legend">
                <span><span class="legend-dot" style="background:linear-gradient(135deg,var(--accent-3),var(--accent-2));"></span>Selesai</span>
                <span><span class="legend-dot" style="background:linear-gradient(135deg,var(--accent-1),#818cf8);"></span>Sedang Mengisi</span>
                <span><span class="legend-dot" style="background:var(--legend-dot-off);"></span>Belum Mulai</span>
            </div>
        </div>

        <div class="kartu-grid">
            <div class="kartu">
                <div class="angka" id="angka-target">-</div>
                <div class="label">Target Peserta</div>
            </div>
            <div class="kartu">
                <div class="angka" id="angka-mulai">-</div>
                <div class="label">Sudah Mulai Mengisi</div>
                <div class="sub" id="sub-mulai">-</div>
            </div>
            <div class="kartu">
                <div class="angka" id="angka-selesai">-</div>
                <div class="label">Selesai Mengisi</div>
                <div class="sub" id="sub-selesai">-</div>
            </div>
        </div>
    </div>

    <div class="section-title"><span class="bar"></span>Progress per Unit/Jenjang</div>
    <div class="unit-list" id="unit-list"></div>

    <div class="section-title"><span class="bar"></span>Jumlah Peserta per Tahap Instrument (yang sedang dikerjakan)</div>
    <div class="funnel-wrap" id="funnel-wrap"></div>

    <script>
        // ========== THEME TOGGLE ==========
        (function () {
            var root = document.documentElement;
            var stored = localStorage.getItem('mhcu_tracker_theme');
            if (stored === 'light' || stored === 'dark') {
                root.setAttribute('data-theme', stored);
            }
            document.getElementById('theme-switch').addEventListener('click', function () {
                var current = root.getAttribute('data-theme') || 'dark';
                var next = current === 'dark' ? 'light' : 'dark';
                root.setAttribute('data-theme', next);
                localStorage.setItem('mhcu_tracker_theme', next);
            });
        })();

        // ========== DATA FETCH ==========
        var TRACKER_API_URL = '/mhcu_tracker/tracker_publik';
        var POLLING_INTERVAL_MS = 8000;
        var DONUT_CIRCUMFERENCE = 2 * Math.PI * 80; // r=80

        function muatData() {
            fetch(TRACKER_API_URL)
                .then(function (res) { return res.json(); })
                .then(function (json) { renderData(json); })
                .catch(function (err) {
                    document.getElementById('last-updated').innerText = 'Gagal memuat data, coba refresh manual.';
                    console.error(err);
                });
        }

        function renderData(data) {
            var periode = data.periode || {};
            var ringkasan = data.ringkasan || { target: 0, sudah_mulai: 0, selesai: 0 };
            var perUnit = data.per_unit || [];
            var perTahap = data.per_tahap || [];

            document.getElementById('periode-info').innerText =
                (periode.nama_periode || '-') + ' (' + (periode.tanggal_mulai || '-') + ' s/d ' + (periode.tanggal_selesai || '-') + ')';

            var target = ringkasan.target || 0;
            var mulai = ringkasan.sudah_mulai || 0;
            var selesai = ringkasan.selesai || 0;
            var sedangMengisi = Math.max(mulai - selesai, 0);

            document.getElementById('angka-target').innerText = target;
            document.getElementById('angka-mulai').innerText = mulai;
            document.getElementById('angka-selesai').innerText = selesai;
            document.getElementById('sub-mulai').innerText = target > 0 ? Math.round((mulai / target) * 100) + '% dari target' : '-';
            document.getElementById('sub-selesai').innerText = target > 0 ? Math.round((selesai / target) * 100) + '% dari target' : '-';

            var persenSelesai = target > 0 ? (selesai / target) : 0;
            var persenMulai = target > 0 ? (sedangMengisi / target) : 0;

            document.getElementById('donut-persen').textContent = Math.round(persenSelesai * 100) + '%';
            document.getElementById('donut-selesai').setAttribute('stroke-dasharray',
                (persenSelesai * DONUT_CIRCUMFERENCE) + ' ' + DONUT_CIRCUMFERENCE);
            var mulaiEl = document.getElementById('donut-mulai');
            mulaiEl.setAttribute('stroke-dasharray', (persenMulai * DONUT_CIRCUMFERENCE) + ' ' + DONUT_CIRCUMFERENCE);
            mulaiEl.setAttribute('transform', 'rotate(' + (-90 + persenSelesai * 360) + ' 100 100)');

            var unitHtml = '';
            for (var i = 0; i < perUnit.length; i++) {
                var u = perUnit[i];
                var p = u.total > 0 ? Math.round((u.selesai / u.total) * 100) : 0;
                unitHtml += '<div class="unit-item">' +
                    '<div class="unit-nama"><span>' + u.label + '</span><span>' + u.selesai + '/' + u.total + ' <span class="pct">(' + p + '%)</span></span></div>' +
                    '<div class="track"><div class="fill" style="width:' + p + '%;"></div></div>' +
                    '</div>';
            }
            document.getElementById('unit-list').innerHTML = unitHtml;

            var maxJumlah = 1;
            for (var k = 0; k < perTahap.length; k++) { maxJumlah = Math.max(maxJumlah, perTahap[k].jumlah); }
            var funnelHtml = '';
            for (var j = 0; j < perTahap.length; j++) {
                var t = perTahap[j];
                var tinggiPersen = Math.round((t.jumlah / maxJumlah) * 100);
                funnelHtml += '<div class="funnel-bar-group">' +
                    '<div class="funnel-jumlah">' + t.jumlah + '</div>' +
                    '<div class="funnel-bar" style="height:' + Math.max(tinggiPersen, 3) + '%;"></div>' +
                    '<div class="funnel-label">Tahap ' + t.tahap + '</div>' +
                    '</div>';
            }
            document.getElementById('funnel-wrap').innerHTML = funnelHtml;

            document.getElementById('last-updated').innerText = 'Update: ' + (data.last_updated || '-');
        }

        muatData();
        setInterval(muatData, POLLING_INTERVAL_MS);
    </script>

</body>
</html>
