import "leaflet/dist/leaflet.css";
import L from "leaflet";

const BULELENG_CENTER = [-8.1509, 115.0489];
const DEFAULT_ZOOM = 10;
let dashboardData = [];
let kecamatanById = new Map();

const map = L.map("map", { zoomControl: true }).setView(
    BULELENG_CENTER,
    DEFAULT_ZOOM,
);

L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution:
        '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    maxZoom: 19,
}).addTo(map);

const activeLayer = L.layerGroup().addTo(map);
const colors = {
    ormas: "#22d3ee",
    partai: "#a3e635",
    agama: "#fbbf24",
};

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

function clearMapLayer() {
    activeLayer.clearLayers();
}

function markerIcon(category) {
    return L.divIcon({
        className: "custom-neon-marker",
        html: `<span style="--marker-color: ${colors[category]}"></span>`,
        iconSize: [18, 18],
        iconAnchor: [9, 9],
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
                    <strong class="text-base">${escapeHtml(item.nama)}</strong>
                    <div class="mt-2 space-y-1 text-sm">
                        <div>${category === "ormas" ? "Anggota" : "Kader"}: <b>${formatNumber(category === "ormas" ? item.jumlah_anggota : item.jumlah_kader)}</b></div>
                        <div>Ketua: ${escapeHtml(item.ketua)}</div>
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
            .map(
                ([agama, jumlah]) =>
                    `<div>${escapeHtml(agama.replaceAll("_", " "))}: <b>${formatNumber(jumlah)}</b></div>`,
            )
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

    if (category === "agama") {
        renderReligion(kecamatanId);
        return;
    }

    if (kecamatanId) {
        renderOrganization(category, kecamatanId);
    }
}

function bindControls() {
    document.querySelectorAll("[data-layer-toggle]").forEach((checkbox) => {
        checkbox.addEventListener("change", (event) => {
            const category = event.target.dataset.layerToggle;
            const selected = document.querySelector(
                `input[name="${category}_kecamatan"]:checked`,
            );

            document
                .querySelectorAll("[data-layer-toggle]")
                .forEach((other) => {
                    if (other !== event.target) {
                        other.checked = false;
                    }
                });

            if (event.target.checked) {
                activate(category, selected?.value ?? null);
            } else {
                clearMapLayer();
            }
        });
    });

    document.querySelectorAll('input[type="radio"]').forEach((radio) => {
        radio.addEventListener("change", (event) => {
            const category = event.target.name.replace("_kecamatan", "");
            const checkbox = document.querySelector(
                `[data-layer-toggle="${category}"]`,
            );

            if (!checkbox.checked) {
                checkbox.checked = true;
                document
                    .querySelectorAll("[data-layer-toggle]")
                    .forEach((other) => {
                        if (other !== checkbox) {
                            other.checked = false;
                        }
                    });
            }

            activate(category, event.target.value);
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
        bindControls();
    })
    .catch((error) => {
        console.error(error);
    });
