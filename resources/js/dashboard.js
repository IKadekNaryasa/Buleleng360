import "leaflet/dist/leaflet.css";
import L from "leaflet";

const BULELENG_CENTER = [-8.1509, 115.0489];
const DEFAULT_ZOOM = 10.0;
let dashboardData = [];
let kecamatanById = new Map();
let selectedKecamatanId = null;
let kecamatanBoundaryLayers = new Map();

const map = L.map("map", { zoomControl: true }).setView(
    BULELENG_CENTER,
    DEFAULT_ZOOM,
);

map.createPane("kecamatan-boundaries");
map.getPane("kecamatan-boundaries").style.zIndex = 410;
map.createPane("desa-boundaries");
map.getPane("desa-boundaries").style.zIndex = 420;

L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution:
        '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    maxZoom: 19,
    noWrap: false,
}).addTo(map);

const activeLayer = L.layerGroup().addTo(map);
const desaBoundaryLayer = L.layerGroup();
const kecamatanBoundaryLayer = L.layerGroup();
const colors = {
    ormas: "#2563eb",
    partai: "#dc2626",
    agama: "#facc15",
    konflik: "#22c55e",
};
const chartColors = [
    "#2563eb",
    "#dc2626",
    "#16a34a",
    "#f59e0b",
    "#9333ea",
    "#0891b2",
    "#db2777",
    "#65a30d",
    "#ea580c",
];

const statusColors = {
    terjadi: "#dc2626",
    ditangani: "#f59e0b",
    selesai: "#16a34a",
};

// Membuat badge HTML berwarna sesuai status konflik (terjadi/ditangani/selesai)
function statusBadge(status) {
    const key = String(status ?? "").toLowerCase();
    const color = statusColors[key] ?? "#64748b";
    const label = status ?? "-";

    return `<span style="background:${color}1a;color:${color};border:1px solid ${color}66" class="rounded px-1.5 py-0.5 text-[0.65rem] font-semibold capitalize">${escapeHtml(label)}</span>`;
}

// Meng-escape karakter HTML berbahaya agar aman disisipkan ke innerHTML
function escapeHtml(value) {
    return String(value ?? "-").replace(
        /[&<>'"]/g,
        (character) =>
            ({
                "&": "&amp;",
                "<": "&lt;",
                ">": "&gt;",
                "'": "&#039;",
                '"': "&quot;",
            })[character],
    );
}

// Memformat angka ke format ribuan gaya Indonesia (contoh: 1.000)
function formatNumber(value) {
    return new Intl.NumberFormat("id-ID").format(value ?? 0);
}

// Memformat angka menjadi string persentase dengan 3 desimal
function formatPercentage(value) {
    return `${value.toLocaleString("id-ID", { minimumFractionDigits: 3, maximumFractionDigits: 3 })}%`;
}

// Menghitung path SVG "irisan pie" berdasarkan nilai, total, dan sudut awal
function pieSectorPath(value, total, startAngle) {
    const endAngle = startAngle + (value / total) * Math.PI * 2;
    const center = 80;
    const radius = 54;
    const start = [
        center + radius * Math.cos(startAngle),
        center + radius * Math.sin(startAngle),
    ];
    const end = [
        center + radius * Math.cos(endAngle),
        center + radius * Math.sin(endAngle),
    ];
    const largeArc = endAngle - startAngle > Math.PI ? 1 : 0;

    return `M ${center} ${center} L ${start[0]} ${start[1]} A ${radius} ${radius} 0 ${largeArc} 1 ${end[0]} ${end[1]} Z`;
}

// Merender satu diagram pie (ormas/partai/agama) beserta legend-nya ke elemen [data-chart]
function renderPieChart(category) {
    const chart = document.querySelector(`[data-chart="${category}"]`);

    if (!chart || !dashboardData.length) {
        return;
    }

    let entries;

    if (category === "agama") {
        const religionTotals = new Map();

        dashboardData.forEach((kecamatan) => {
            Object.entries(kecamatan.agama).forEach(([agama, jumlah]) => {
                religionTotals.set(
                    agama,
                    (religionTotals.get(agama) ?? 0) + Number(jumlah),
                );
            });
        });

        entries = Array.from(religionTotals, ([agama, value], index) => ({
            label: agama.replaceAll("_", " "),
            value,
            color: chartColors[index % chartColors.length],
        })).filter((entry) => entry.value > 0);
    } else {
        entries = dashboardData
            .map((kecamatan, index) => ({
                ...kecamatan,
                value: kecamatan[category].length,
                color: chartColors[index % chartColors.length],
            }))
            .filter((entry) => entry.value > 0);

        if (category === "partai" && entries.length === 1) {
            entries[0].color = "#16a34a";
        }
    }
    const total = entries.reduce((sum, entry) => sum + entry.value, 0);
    const totalPopulation =
        category === "agama"
            ? dashboardData.reduce(
                  (sum, kecamatan) => sum + Number(kecamatan.total_penduduk),
                  0,
              )
            : 0;

    if (!total) {
        chart.innerHTML =
            '<div class="dashboard-empty-state text-xs text-slate-500">Belum ada data diagram.</div>';
        return;
    }

    let angle = -Math.PI / 2;
    const sectors = entries.map((entry) => {
        const percentage = (entry.value / total) * 100;
        const sector = {
            ...entry,
            percentage,
            startAngle: angle,
            path: pieSectorPath(entry.value, total, angle),
        };
        angle += (entry.value / total) * Math.PI * 2;
        return sector;
    });
    const svgSectors =
        sectors.length === 1
            ? `<circle cx="80" cy="80" r="54" fill="${sectors[0].color}" />`
            : sectors
                  .map((entry) => {
                      return `<path d="${entry.path}" fill="${entry.color}" />`;
                  })
                  .join("");
    const legend = sectors
        .map((entry) => {
            const detail =
                category === "agama"
                    ? `${formatNumber(entry.value)} (${formatPercentage(totalPopulation ? (entry.value / totalPopulation) * 100 : 0)})`
                    : `${formatNumber(entry.value)} ${category} (${formatPercentage(entry.percentage)})`;
            const label = entry.label ?? entry.kode;

            return `<div class="dashboard-chart-legend-item"><span style="background:${entry.color}"></span><div><b>${label}:</b> ${detail}</div></div>`;
        })
        .join("");
    const title =
        category === "agama"
            ? "Sebaran agama Buleleng"
            : `${category === "ormas" ? "Ormas" : "Partai"} per kecamatan`;

    chart.innerHTML = `<div class="dashboard-chart-body"><svg class="dashboard-pie" viewBox="0 0 160 160" role="img" aria-label="${title}">${svgSectors}</svg><div class="dashboard-chart-legend">${legend}</div></div>`;
}

// Memanggil renderPieChart untuk semua kategori (ormas, partai, agama) sekaligus
function renderAllCharts() {
    ["ormas", "partai", "agama"].forEach(renderPieChart);
}

// Menghitung dan menampilkan total angka (ormas/partai/agama/konflik) di header tiap panel
function renderHeaderTotals() {
    ["ormas", "partai", "agama", "konflik"].forEach((category) => {
        const total =
            category === "agama"
                ? (dashboardData[0]?.total_agama ?? 0)
                : dashboardData.reduce(
                      (sum, kecamatan) => sum + kecamatan[category].length,
                      0,
                  );
        const counter = document.querySelector(
            `[data-total-count="${category}"]`,
        );

        if (counter) {
            counter.textContent = `${formatNumber(total)} ${category}`;
        }
    });
}

// Mengosongkan layer marker aktif di peta dan mengembalikan warna batas kecamatan ke default
function clearMapLayer() {
    openSpiderfy = null;
    activeLayer.clearLayers();
    restoreDefaultBoundaryColors();
}

// Memvalidasi struktur objek GeoJSON boundary sebelum dipakai (mencegah error render)
function safeGeoJsonBoundary(boundary) {
    if (!boundary || typeof boundary !== "object") {
        return null;
    }

    if (
        typeof boundary.type !== "string" ||
        !Array.isArray(boundary.coordinates)
    ) {
        return null;
    }

    return boundary;
}

// Menggambar seluruh batas wilayah desa (dari semua kecamatan) ke layer desaBoundaryLayer
function renderDesaBoundaries() {
    desaBoundaryLayer.clearLayers();

    const features = dashboardData.flatMap((kecamatan) =>
        kecamatan.desa.flatMap((desa) => {
            const geometry = safeGeoJsonBoundary(desa.geojson_boundary);

            return geometry
                ? [
                      {
                          type: "Feature",
                          properties: { nama: desa.nama },
                          geometry,
                      },
                  ]
                : [];
        }),
    );

    L.geoJSON(
        { type: "FeatureCollection", features },
        {
            style: {
                color: "#38bdf8",
                fillColor: "#0e7490",
                fillOpacity: 0.3,
                opacity: 1,
                weight: 2,
            },
            pane: "desa-boundaries",
            onEachFeature: (feature, layer) => {
                layer.options.className = `desa-boundary-${feature.properties.nama.toLowerCase().replaceAll(" ", "-")}`;
                layer.bindTooltip(escapeHtml(feature.properties.nama), {
                    direction: "center",
                    className: "desa-boundary-tooltip",
                });
            },
        },
    ).addTo(desaBoundaryLayer);
}

// Menggambar batas wilayah tiap kecamatan dengan warna default (dari chartColors) dan menyimpan referensinya
function renderKecamatanBoundaries() {
    kecamatanBoundaryLayer.clearLayers();
    kecamatanBoundaryLayers.clear();

    dashboardData.forEach((kecamatan, index) => {
        const geometry = safeGeoJsonBoundary(kecamatan.geojson_boundary);

        if (!geometry) {
            return;
        }

        const kecamatanColor = chartColors[index % chartColors.length];

        const geoJsonLayer = L.geoJSON(
            {
                type: "Feature",
                properties: { nama: kecamatan.nama },
                geometry,
            },
            {
                style: {
                    color: kecamatanColor,
                    fillColor: kecamatanColor,
                    fillOpacity: 0.5,
                    opacity: 0.9,
                    weight: 3,
                },
                pane: "kecamatan-boundaries",
                onEachFeature: (feature, layer) => {
                    layer.bindTooltip(escapeHtml(feature.properties.nama), {
                        direction: "center",
                        className: "kecamatan-boundary-tooltip",
                    });
                },
            },
        ).addTo(kecamatanBoundaryLayer);

        kecamatanBoundaryLayers.set(kecamatan.nama, {
            layer: geoJsonLayer,
            defaultColor: kecamatanColor,
        });
    });
}

// Menampilkan atau menyembunyikan layer batas wilayah (desa/kecamatan) di peta
function toggleBoundaryLayer(category, isVisible) {
    const layer =
        category === "desa" ? desaBoundaryLayer : kecamatanBoundaryLayer;

    if (isVisible) {
        layer.addTo(map);
    } else {
        map.removeLayer(layer);
    }
}

// Menentukan warna berdasarkan tingkat keparahan jumlah konflik (merah/kuning/hijau)
function konflikSeverityColor(count) {
    if (count > 3) return "#dc2626"; // merah
    if (count >= 1) return "#facc15"; // kuning
    return "#22c55e"; // hijau
}

// Mewarnai ulang seluruh polygon kecamatan sesuai jumlah konflik di masing-masing wilayah
function applyKonflikBoundaryColors() {
    kecamatanBoundaryLayers.forEach(({ layer }, nama) => {
        const kecamatan = dashboardData.find((item) => item.nama === nama);
        const count = kecamatan ? kecamatan.konflik.length : 0;
        const color = konflikSeverityColor(count);

        layer.setStyle({
            color,
            fillColor: color,
        });
    });
}

// Mengembalikan warna polygon kecamatan ke warna default masing-masing
function restoreDefaultBoundaryColors() {
    kecamatanBoundaryLayers.forEach(({ layer, defaultColor }) => {
        layer.setStyle({
            color: defaultColor,
            fillColor: defaultColor,
        });
    });
}

// Menggerakkan (fly-to) peta menuju koordinat kecamatan yang dipilih
function focusKecamatan(kecamatanId) {
    const kecamatan = kecamatanById.get(kecamatanId);

    if (!kecamatan) {
        return;
    }

    map.flyTo([kecamatan.lat, kecamatan.long], Math.max(map.getZoom(), 11), {
        duration: 0.7,
    });
}

// Mengambil kategori layer yang sedang aktif/dicentang (ormas/partai/agama/konflik)
function activeCategory() {
    return document.querySelector("[data-layer-toggle]:checked")?.dataset
        .layerToggle;
}

// Menandai kecamatan yang dipilih (highlight tombol) lalu menampilkan data sesuai layer aktif atau fokus peta saja
function selectKecamatan(kecamatanId) {
    selectedKecamatanId = kecamatanId;
    document.querySelectorAll("[data-kecamatan-id]").forEach((button) => {
        button.classList.toggle(
            "bg-cyan-400/30",
            button.dataset.kecamatanId === kecamatanId,
        );
        button.classList.toggle(
            "text-white",
            button.dataset.kecamatanId === kecamatanId,
        );
    });
    const category = activeCategory();

    if (category) {
        activate(category, kecamatanId);
    } else {
        focusKecamatan(kecamatanId);
    }
}

// Mereset seluruh pilihan dashboard: hapus layer, uncheck semua radio/checkbox, hapus highlight
function clearDashboardSelection() {
    selectedKecamatanId = null;
    clearMapLayer();
    document.querySelectorAll('input[name="kecamatan"]').forEach((radio) => {
        radio.checked = false;
    });
    document.querySelectorAll("[data-kecamatan-id]").forEach((button) => {
        button.classList.remove("bg-cyan-400/30", "text-white");
    });
    document.querySelectorAll("[data-layer-toggle]").forEach((checkbox) => {
        checkbox.checked = false;
    });
}

// Membuat icon marker (divIcon) berwarna sesuai kategori (ormas/partai/agama/konflik)
function markerIcon(category) {
    return L.divIcon({
        className: "custom-neon-marker",
        html: `<span style="--marker-color: ${colors[category]}"></span>`,
        iconSize: [24, 50],
        iconAnchor: [12, 50],
    });
}

let openSpiderfy = null;

// Membuat key unik string dari koordinat lat/long (dibulatkan 4 desimal) untuk pengelompokan
function coordKey(lat, long) {
    return `${Number(lat).toFixed(4)}_${Number(long).toFixed(4)}`;
}

// Mengelompokkan item-item berdasarkan koordinat yang sama (untuk deteksi lokasi yang bertumpuk)
function groupByCoordinate(items) {
    const groups = new Map();

    items.forEach((item) => {
        const key = coordKey(item.lat, item.long);

        if (!groups.has(key)) {
            groups.set(key, []);
        }

        groups.get(key).push(item);
    });

    return Array.from(groups.values());
}

// Membuat icon marker cluster berbentuk lingkaran berisi angka jumlah item yang bertumpuk
function clusterIcon(category, count) {
    const color = colors[category] ?? "#38bdf8";

    return L.divIcon({
        className: "custom-neon-cluster",
        html: `
            <div style="
                width:30px;height:30px;border-radius:50%;
                background:${color};
                border:2px solid rgba(255,255,255,0.85);
                box-shadow:0 0 10px ${color}aa, 0 0 2px rgba(0,0,0,0.6);
                display:flex;align-items:center;justify-content:center;
                color:#0b1220;font-weight:700;font-size:12px;
                font-family:'DM Sans',sans-serif;
            ">${count}</div>
        `,
        iconSize: [30, 30],
        iconAnchor: [15, 15],
    });
}

// Menghitung posisi-posisi melingkar (spiderfy) di sekitar titik pusat untuk memisahkan marker yang bertumpuk
function spiderfyPositions(center, count) {
    const centerPoint = map.latLngToLayerPoint(center);
    const radiusPx = 42 + count * 3;
    const positions = [];

    for (let i = 0; i < count; i++) {
        const angle = (2 * Math.PI * i) / count - Math.PI / 2;
        const point = L.point(
            centerPoint.x + radiusPx * Math.cos(angle),
            centerPoint.y + radiusPx * Math.sin(angle),
        );

        positions.push(map.layerPointToLatLng(point));
    }

    return positions;
}

// Menambahkan grup marker ke peta: marker tunggal langsung ditampilkan, marker bertumpuk jadi cluster yang bisa di-klik untuk "spiderfy" (menyebar)
function addSpiderfiableGroup(layerGroup, category, items, buildMarker) {
    if (items.length === 1) {
        buildMarker(items[0]).addTo(layerGroup);
        return;
    }

    const center = L.latLng(items[0].lat, items[0].long);
    const legLayer = L.layerGroup();
    let spiderMarkers = [];
    let spiderfied = false;

    const cluster = L.marker(center, {
        icon: clusterIcon(category, items.length),
        zIndexOffset: 1000,
    });

    // Menutup tampilan spiderfy: menghapus marker & garis penghubung yang sudah disebar
    function closeSpider() {
        if (!spiderfied) return;

        legLayer.clearLayers();
        layerGroup.removeLayer(legLayer);
        spiderMarkers.forEach((marker) => layerGroup.removeLayer(marker));
        spiderMarkers = [];
        cluster.setOpacity(1);
        spiderfied = false;

        if (openSpiderfy === closeSpider) {
            openSpiderfy = null;
        }
    }

    // Membuka tampilan spiderfy: menyebar marker yang bertumpuk ke posisi melingkar beserta garis penghubung
    function openSpider() {
        if (openSpiderfy && openSpiderfy !== closeSpider) {
            openSpiderfy();
        }

        const positions = spiderfyPositions(center, items.length);

        items.forEach((item, index) => {
            const marker = buildMarker(item);
            marker.setLatLng(positions[index]);
            marker.addTo(layerGroup);
            spiderMarkers.push(marker);

            L.polyline([center, positions[index]], {
                color: "rgba(148,163,184,0.9)",
                weight: 1.5,
                dashArray: "4,4",
                interactive: false,
            }).addTo(legLayer);
        });

        legLayer.addTo(layerGroup);
        cluster.setOpacity(0.35);
        spiderfied = true;
        openSpiderfy = closeSpider;
    }

    cluster.on("click", (event) => {
        L.DomEvent.stopPropagation(event);
        spiderfied ? closeSpider() : openSpider();
    });

    cluster.addTo(layerGroup);
}

// Menutup spiderfy yang sedang terbuka saat peta mulai di-zoom, digeser, atau diklik
map.on("zoomstart movestart click", () => {
    if (openSpiderfy) {
        openSpiderfy();
    }
});

// Merender marker ormas/partai ke peta (semua kecamatan atau satu kecamatan tertentu) lengkap dengan popup detail
function renderOrganization(category, kecamatanId = null) {
    const data = kecamatanId
        ? dashboardData.filter((item) => item.id === kecamatanId)
        : dashboardData;

    data.forEach((kecamatan) => {
        const groups = groupByCoordinate(kecamatan[category]);

        groups.forEach((group) => {
            addSpiderfiableGroup(activeLayer, category, group, (item) =>
                L.marker([item.lat, item.long], {
                    icon: markerIcon(category),
                }).bindPopup(
                    `
                        <div class="min-w-[190px] text-slate-900">
                            <div class="text-sm">Desa: <b>${escapeHtml(item.nama_desa)}</b></div>
                            <strong class="text-base">${escapeHtml(item.nama)}</strong>
                            <div class="mt-2 space-y-1 text-sm">
                                <div>${category === "ormas" ? "Anggota" : "Kader"}: <b>${formatNumber(category === "ormas" ? item.jumlah_anggota : item.jumlah_kader)}</b></div>
                                <div>Ketua: ${escapeHtml(item.ketua)}</div>
                                <div>Sekretaris: ${escapeHtml(item.sekretaris)}</div>
                                <div>Bendahara: ${escapeHtml(item.bendahara)}</div>
                                <div>Alamat: ${escapeHtml(item.alamat)}</div>
                            </div>
                        </div>
                    `,
                    { className: "dashboard-dark-popup" },
                ),
            );
        });
    });
}

// Merender marker sebaran agama per kecamatan (satu marker per kecamatan) dengan popup rincian persentase agama
function renderReligion(selectedKecamatanId = null) {
    const data = selectedKecamatanId
        ? dashboardData.filter((item) => item.id === selectedKecamatanId)
        : dashboardData;

    data.forEach((kecamatan) => {
        const agamaRows = Object.entries(kecamatan.agama)
            .map(([agama, jumlah]) => {
                const percentage = kecamatan.total_penduduk
                    ? (jumlah / kecamatan.total_penduduk) * 100
                    : 0;

                return `<div>${escapeHtml(agama.replaceAll("_", " "))}: <b>${formatNumber(jumlah)}</b> (${formatPercentage(percentage)})</div>`;
            })
            .join("");

        L.marker([kecamatan.lat, kecamatan.long], { icon: markerIcon("agama") })
            .bindPopup(
                `
                <div class="min-w-[210px] text-slate-900">
                    <strong class="text-base">${escapeHtml(kecamatan.nama)}</strong>
                    <div class="mt-2 border-b border-slate-200 pb-2 text-sm">Penduduk 2025: <b>${formatNumber(kecamatan.total_penduduk)}</b></div>
                    <div class="mt-2 space-y-1 text-sm">${agamaRows || "Belum ada data agama."}</div>
                </div>
            `,
                { className: "dashboard-dark-popup" },
            )
            .addTo(activeLayer);
    });
}

// Merender marker titik konflik ke peta (semua kecamatan atau satu kecamatan tertentu) lengkap dengan popup status
function renderKonflik(kecamatanId = null) {
    const data = kecamatanId
        ? dashboardData.filter((item) => item.id === kecamatanId)
        : dashboardData;

    data.forEach((kecamatan) => {
        const groups = groupByCoordinate(kecamatan.konflik);

        groups.forEach((group) => {
            addSpiderfiableGroup(activeLayer, "konflik", group, (item) =>
                L.marker([item.lat, item.long], {
                    icon: markerIcon("konflik"),
                }).bindPopup(
                    `
                        <div class="min-w-[200px] text-slate-900">
                            <div class="text-sm">Desa: <b>${escapeHtml(item.nama_desa)}</b></div>
                            <div class="text-sm">Kecamatan: <b>${escapeHtml(kecamatan.nama)}</b></div>
                            <strong class="text-base">${escapeHtml(item.judul_konflik)}</strong>
                            <div class="mt-2 text-sm">Tanggal: ${escapeHtml(item.tanggal_konflik)}</div>
                            <div class="mt-2">${statusBadge(item.status)}</div>
                        </div>
                    `,
                    { className: "dashboard-dark-popup" },
                ),
            );
        });
    });
}

// Merender daftar (list) seluruh data konflik dari semua kecamatan ke panel [data-list="konflik"]
function renderKonflikList() {
    const container = document.querySelector('[data-list="konflik"]');

    if (!container) {
        return;
    }

    const allKonflik = dashboardData.flatMap((kecamatan) =>
        kecamatan.konflik.map((item) => ({
            ...item,
            nama_kecamatan: kecamatan.nama,
        })),
    );

    if (!allKonflik.length) {
        container.innerHTML =
            '<div class="dashboard-empty-state px-4 py-3 text-xs text-slate-500">Belum ada data konflik.</div>';
        return;
    }

    const rows = allKonflik
        .map(
            (item, index) => `
            <div class="flex items-start justify-between gap-2 border-b border-white/5 px-4 py-2 last:border-0">
                <div class="flex items-start gap-2">
                    <span class="shrink-0 text-xs font-semibold text-emerald-300">${index + 1}.</span>
                    <span class="text-xs text-slate-200">${escapeHtml(item.judul_konflik)} - ${escapeHtml(item.nama_desa)} [${escapeHtml(item.nama_kecamatan)}]</span>
                </div>
                <div class="shrink-0">${statusBadge(item.status)}</div>
            </div>
        `,
        )
        .join("");

    container.innerHTML = `<div class="divide-y divide-white/5">${rows}</div>`;
}

// Fungsi utama switch layer: membersihkan peta lalu menampilkan data sesuai kategori terpilih (konflik/agama/ormas/partai)
function activate(category, kecamatanId = null) {
    clearMapLayer();

    if (kecamatanId) {
        focusKecamatan(kecamatanId);
    }

    if (category === "konflik") {
        applyKonflikBoundaryColors();
        renderKonflik(kecamatanId);
        return;
    }

    if (category === "agama") {
        renderReligion(kecamatanId);
        return;
    }

    renderOrganization(category, kecamatanId);
}

// Mendaftarkan seluruh event listener untuk kontrol UI (toggle layer, toggle boundary, pilih kecamatan, tombol clear)
function bindControls() {
    document.querySelectorAll("[data-boundary-toggle]").forEach((checkbox) => {
        checkbox.addEventListener("change", (event) => {
            toggleBoundaryLayer(
                event.target.dataset.boundaryToggle,
                event.target.checked,
            );
        });
    });

    document
        .querySelector("[data-clear-map]")
        ?.addEventListener("change", (event) => {
            if (event.target.checked) {
                clearDashboardSelection();
                event.target.checked = false;
            }
        });

    document.querySelectorAll("[data-layer-toggle]").forEach((checkbox) => {
        checkbox.addEventListener("change", (event) => {
            const category = event.target.dataset.layerToggle;

            document
                .querySelectorAll("[data-layer-toggle]")
                .forEach((other) => {
                    if (other !== event.target) {
                        other.checked = false;
                    }
                });

            if (event.target.checked) {
                activate(category, selectedKecamatanId);
            } else {
                clearMapLayer();
            }
        });
    });

    document.querySelectorAll("[data-kecamatan-id]").forEach((button) => {
        button.addEventListener("click", () => {
            selectKecamatan(button.dataset.kecamatanId);
        });
    });
}

// Entry point: mengambil data dashboard dari server lalu menginisialisasi seluruh tampilan (chart, peta, kontrol)
fetch("/dashboard/data")
    .then((response) => {
        if (!response.ok) {
            throw new Error("Data dashboard tidak dapat dimuat.");
        }

        return response.json();
    })
    .then((data) => {
        dashboardData = data;
        kecamatanById = new Map(data.map((item) => [item.id, item]));
        renderHeaderTotals();
        renderDesaBoundaries();
        renderKecamatanBoundaries();
        document
            .querySelectorAll("[data-boundary-toggle]")
            .forEach((checkbox) => {
                toggleBoundaryLayer(
                    checkbox.dataset.boundaryToggle,
                    checkbox.checked,
                );
            });
        renderAllCharts();
        renderKonflikList();
        bindControls();
    })
    .catch((error) => {
        console.error(error);
    });
