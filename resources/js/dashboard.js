import { BarController, BarElement, CategoryScale, Chart, LinearScale, Tooltip } from 'chart.js';

Chart.register(BarController, BarElement, CategoryScale, LinearScale, Tooltip);

const BRAND = '#059670';
const BRAND_HOVER = '#04785c';
const INK_MUTED = '#64748b';
const GRID = '#eef2f6';

const pesos = new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 });

/** 1250000000 => "$1.250 M" para ejes y etiquetas cortas. */
function millones(valor) {
    return `$${new Intl.NumberFormat('es-CO', { maximumFractionDigits: 1 }).format(valor / 1_000_000)} M`;
}

Chart.defaults.font.family = '"Plus Jakarta Sans", ui-sans-serif, system-ui, sans-serif';
Chart.defaults.color = INK_MUTED;

const tooltip = {
    backgroundColor: '#0b1220',
    padding: 12,
    cornerRadius: 10,
    titleFont: { weight: '600' },
    displayColors: false,
};

const datos = document.getElementById('dashboard-data');

if (datos) {
    const { ventasPorMes, precioM2 } = JSON.parse(datos.textContent);

    new Chart(document.getElementById('grafica-ventas'), {
        type: 'bar',
        data: {
            labels: ventasPorMes.map((m) => m.mes),
            datasets: [{
                data: ventasPorMes.map((m) => m.total),
                backgroundColor: BRAND,
                hoverBackgroundColor: BRAND_HOVER,
                borderRadius: { topLeft: 4, topRight: 4 },
                borderSkipped: 'bottom',
                maxBarThickness: 36,
            }],
        },
        options: {
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    ...tooltip,
                    callbacks: {
                        label: (ctx) => pesos.format(ctx.parsed.y),
                        afterLabel: (ctx) => {
                            const n = ventasPorMes[ctx.dataIndex].operaciones;
                            return `${n} ${n === 1 ? 'operación' : 'operaciones'}`;
                        },
                    },
                },
            },
            scales: {
                x: { grid: { display: false }, border: { display: false }, ticks: { maxRotation: 0, autoSkipPadding: 12 } },
                y: {
                    beginAtZero: true,
                    border: { display: false },
                    grid: { color: GRID },
                    ticks: { callback: (v) => millones(v), maxTicksLimit: 5 },
                },
            },
        },
    });

    const ciudades = Object.keys(precioM2);

    new Chart(document.getElementById('grafica-m2'), {
        type: 'bar',
        data: {
            labels: ciudades,
            datasets: [{
                data: ciudades.map((c) => precioM2[c]),
                backgroundColor: BRAND,
                hoverBackgroundColor: BRAND_HOVER,
                borderRadius: { topRight: 4, bottomRight: 4 },
                borderSkipped: 'left',
                maxBarThickness: 22,
            }],
        },
        options: {
            indexAxis: 'y',
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { ...tooltip, callbacks: { label: (ctx) => `${pesos.format(ctx.parsed.x)} por m²` } },
            },
            scales: {
                x: {
                    beginAtZero: true,
                    border: { display: false },
                    grid: { color: GRID },
                    ticks: { callback: (v) => millones(v), maxTicksLimit: 4 },
                },
                y: { grid: { display: false }, border: { display: false }, ticks: { color: '#334155', font: { weight: '600' } } },
            },
        },
    });
}
