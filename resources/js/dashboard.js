import "leaflet/dist/leaflet.css";
import L from "leaflet";

const BULELENG_CENTER = [-8.1509, 115.0489];
const DEFAULT_ZOOM = 9.5;
let dashboardData = [];
let kecamatanById = new Map();
let selectedKecamatanId = null;

const map = L.map("map", { zoomControl: true }).setView(
    BULELENG_CENTER,
    DEFAULT_ZOOM,
);

L.tileLayer("https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png", {
    attribution:
        '&copy; <a href="https://carto.com/attributions">CARTO</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    maxZoom: 19,
}).addTo(map);

const activeLayer = L.layerGroup().addTo(map);
const colors = {
    ormas: "#2563eb",
    partai: "#dc2626",
    agama: "#facc15",
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

function formatNumber(value) {
    return new Intl.NumberFormat("id-ID").format(value ?? 0);
}

function formatPercentage(value) {
    return `${value.toLocaleString("id-ID", { maximumFractionDigits: 1 })}%`;
}

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
            '<p class="text-xs text-slate-500">Belum ada data diagram.</p>';
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

function renderAllCharts() {
    ["ormas", "partai", "agama"].forEach(renderPieChart);
}

function clearMapLayer() {
    activeLayer.clearLayers();
}

function focusKecamatan(kecamatanId) {
    const kecamatan = kecamatanById.get(kecamatanId);

    if (!kecamatan) {
        return;
    }

    map.flyTo([kecamatan.lat, kecamatan.long], Math.max(map.getZoom(), 11), {
        duration: 0.7,
    });
}

function activeCategory() {
    return document.querySelector("[data-layer-toggle]:checked")?.dataset
        .layerToggle;
}

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

function clearDashboardSelection() {
    selectedKecamatanId = null;
    activeLayer.clearLayers();
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

function markerIcon(category) {
    return L.divIcon({
        className: "custom-neon-marker",
        html: `<span style="--marker-color: ${colors[category]}"></span>`,
        iconSize: [28, 38],
        iconAnchor: [14, 38],
    });
}

function renderOrganization(category, kecamatanId) {
    const kecamatan = kecamatanById.get(kecamatanId);

    if (!kecamatan) {
        return;
    }

    kecamatan[category].forEach((item) => {
        L.marker([item.lat, item.long], { icon: markerIcon(category) })
            .bindPopup(
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
            )
            .addTo(activeLayer);
    });
}

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
            )
            .addTo(activeLayer);
    });
}

function activate(category, kecamatanId = null) {
    clearMapLayer();

    if (kecamatanId) {
        focusKecamatan(kecamatanId);
    }

    if (category === "agama") {
        renderReligion(kecamatanId);
        return;
    }

    if (kecamatanId) {
        renderOrganization(category, kecamatanId);
    }
}

function bindControls() {
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
        renderAllCharts();
        bindControls();
    })
    .catch((error) => {
        console.error(error);
    });
