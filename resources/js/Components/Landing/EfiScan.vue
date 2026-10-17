<script setup>
/**
 * Simulasi Scan EFI — mengikuti spesifikasi §6.
 * Semua angka adalah DATA SIMULASI (mesin idle), bukan pembacaan nyata.
 */
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'

const SCAN_MS = 6500

const SENSORS = [
  { label: 'Putaran mesin', unit: 'rpm', base: 750, amp: 18, f: 0.0021, dec: 0, short: 'RPM' },
  { label: 'Suhu pendingin (ECT)', unit: '°C', base: 87, amp: 0.6, f: 0.0004, dec: 0 },
  { label: 'Bukaan throttle (TPS)', unit: '%', base: 0.8, amp: 0.15, f: 0.0013, dec: 1 },
  { label: 'Tekanan intake (MAP)', unit: 'kPa', base: 33, amp: 1.2, f: 0.0017, dec: 0 },
  { label: 'Sensor oksigen (O2)', unit: 'V', base: 0.48, amp: 0.38, f: 0.0045, dec: 2 },
  { label: 'Durasi injeksi', unit: 'ms', base: 2.7, amp: 0.12, f: 0.0019, dec: 2 },
]

function sensorValue(s, i, t) {
  const v = s.base
    + s.amp * Math.sin(t * s.f + i * 1.3)
    + s.amp * 0.35 * Math.sin(t * s.f * 2.7 + i)
  return v.toFixed(s.dec)
}

const root = ref(null)
const reduce = ref(false)
const start = ref(Number.POSITIVE_INFINITY)
const now = ref(0)

let raf = 0
let lastPaint = 0
let running = false

/* ---------- state turunan ---------- */
const idle = computed(() => now.value < start.value)
const pRaw = computed(() => (idle.value ? 0 : Math.max(0, (now.value - start.value) / SCAN_MS)))
const p = computed(() => Math.min(pRaw.value, 1))
const linked = computed(() => pRaw.value >= 0.12)
const scanning = computed(() => pRaw.value >= 0.12 && pRaw.value < 1)
const done = computed(() => pRaw.value >= 1)

const chipText = computed(() => {
  if (idle.value) return 'STATUS: SIAGA'
  if (!linked.value) return 'STATUS: MENGHUBUNGKAN'
  if (!done.value) return 'STATUS: SCANNING'
  return 'STATUS: SELESAI'
})

const screenText = computed(() => {
  if (idle.value) return 'Siap terhubung ke ECU'
  if (!linked.value) return 'Menghubungkan ke ECU…'
  if (!done.value) return 'Membaca data sensor…'
  return 'Scan selesai · 1 DTC ditemukan'
})

const btnLabel = computed(() => {
  if (idle.value) return 'Mulai Scan'
  if (done.value) return 'Scan Ulang'
  return 'Sedang Scan…'
})

const pctText = computed(() => Math.round(p.value * 100) + '%')

/* ---------- animasi ---------- */
const barWidth = computed(() => 192 * p.value)
const cableOffset = computed(() => -((now.value * 0.05) % 15))
const cableOpacity = computed(() => (idle.value ? (reduce.value ? 0.5 : 0) : 1))
const cableDash = computed(() => (reduce.value ? 'none' : '3 12'))
const beamX = computed(() => 10 + 342 * ((now.value / 1400) % 1))
const beamOpacity = computed(() => (scanning.value && linked.value ? 0.85 : 0))

const rpmText = computed(() => (linked.value ? sensorValue(SENSORS[0], 0, now.value) + ' rpm' : '— rpm'))

const wavePoints = computed(() => {
  const pts = []
  for (let j = 0; j < 32; j++) {
    const x = 404 + j * 6.19
    const y = linked.value
      ? 168 + 14 * Math.sin(j * 0.6 + now.value * 0.006) + 5 * Math.sin(j * 1.7 + now.value * 0.011)
      : 168
    pts.push(x.toFixed(1) + ',' + y.toFixed(1))
  }
  return pts.join(' ')
})

const tiles = computed(() => SENSORS.map((s, i) => {
  const shown = pRaw.value >= 0.15 + i * 0.13
  return {
    ...s,
    shown,
    value: shown ? sensorValue(s, i, now.value) : '—',
    status: shown ? '● OK' : (idle.value ? 'Menunggu' : 'Membaca…'),
  }
}))

const dtcTitle = computed(() => {
  if (idle.value) return 'Belum ada data. Tekan Mulai Scan.'
  if (done.value) return 'P0171 · Campuran bahan bakar terlalu kurus (Bank 1)'
  return 'Memeriksa kode kerusakan…'
})
const dtcNote = computed(() => (done.value
  ? 'Contoh hasil scan untuk simulasi. Di TKR kamu belajar menelusuri penyebabnya, misalnya kebocoran vakum atau tekanan bahan bakar rendah.'
  : 'Scan tool membaca ECU melalui port OBD-II.'))

/* ---------- loop ---------- */
function frame(t) {
  if (!running) return
  if (!lastPaint || t - lastPaint >= (reduce.value ? 1000 : 50)) {
    lastPaint = t
    now.value = t
  }
  raf = requestAnimationFrame(frame)
}

function restart() {
  const t = performance.now()
  start.value = t
  now.value = t
  lastPaint = 0
}

let observer = null
function onVisibility() {
  if (document.hidden) stop()
  else begin()
}

function stop() {
  running = false
  cancelAnimationFrame(raf)
}
function begin() {
  if (running || !root.value) return
  const rect = root.value.getBoundingClientRect()
  if (rect.bottom < 0 || rect.top > window.innerHeight) return
  running = true
  lastPaint = 0
  raf = requestAnimationFrame(frame)
}

onMounted(() => {
  reduce.value = window.matchMedia('(prefers-reduced-motion: reduce)').matches
  const t = performance.now()
  now.value = t
  // Auto-start setelah jeda 900ms, kecuali pengguna meminta gerak dikurangi.
  start.value = reduce.value ? Number.POSITIVE_INFINITY : t + 900

  observer = new IntersectionObserver(([entry]) => {
    if (entry.isIntersecting) begin()
    else stop()
  }, { threshold: 0 })
  if (root.value) observer.observe(root.value)

  document.addEventListener('visibilitychange', onVisibility)
  begin()
})

onBeforeUnmount(() => {
  stop()
  observer?.disconnect()
  document.removeEventListener('visibilitychange', onVisibility)
})
</script>

<template>
  <div ref="root" class="efi-wrap">
    <!-- A. Panel scan -->
    <div class="efi-panel grid-blue">
      <div class="efi-head">
        <div class="efi-title">
          <span class="efi-kicker">SIMULASI SCAN EFI · OBD-II</span>
          <span class="efi-chip" :class="{ idle }" aria-live="off">{{ chipText }}</span>
        </div>
        <button type="button" class="efi-btn" @click="restart">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M21 12a9 9 0 1 1-3-6.7" />
            <path d="M21 3v6h-6" />
          </svg>
          <span>{{ btnLabel }}</span>
        </button>
      </div>

      <figure class="efi-stage">
        <svg class="efi-svg" viewBox="0 0 640 300" width="100%" role="img"
          aria-label="Ilustrasi mobil yang tersambung kabel OBD ke laptop scan tool untuk membaca sistem EFI">
          <!-- garis lantai -->
          <line x1="0" y1="270" x2="640" y2="270" stroke="#FFFFFF" stroke-opacity=".3" stroke-dasharray="6 6" />

          <!-- mobil -->
          <g transform="translate(-14 85) scale(0.6)">
            <path d="M40 250 L40 215 Q42 195 70 190 L170 178 L240 120 Q255 108 280 106 L410 106 Q435 107 450 120 L520 178 L585 188 Q605 192 608 212 L610 250 Q610 262 598 262 L546 262 A46 46 0 0 0 454 262 L196 262 A46 46 0 0 0 104 262 L52 262 Q40 262 40 250 Z"
              fill="#FFFFFF" fill-opacity=".1" stroke="#FFFFFF" stroke-width="4" stroke-linejoin="round" />
            <path d="M188 174 L248 124 Q258 116 272 116 L340 116 L340 174 Z" fill="#7FD0FF" fill-opacity=".28" stroke="#FFFFFF" stroke-opacity=".8" stroke-width="2.5" />
            <path d="M352 116 L408 116 Q428 116 440 128 L500 174 L352 174 Z" fill="#7FD0FF" fill-opacity=".28" stroke="#FFFFFF" stroke-opacity=".8" stroke-width="2.5" />
            <path d="M346 112 L346 256 M200 182 L200 250" stroke="#FFFFFF" stroke-opacity=".45" stroke-width="2.5" />
            <rect x="586" y="198" width="20" height="12" rx="3" fill="#7FD0FF" />
            <g>
              <circle cx="150" cy="262" r="40" fill="#0F3FB8" stroke="#FFFFFF" stroke-width="3.5" />
              <circle cx="150" cy="262" r="20" fill="none" stroke="#7FD0FF" stroke-width="3" />
              <circle cx="150" cy="262" r="7" fill="#FFFFFF" />
            </g>
            <g>
              <circle cx="500" cy="262" r="40" fill="#0F3FB8" stroke="#FFFFFF" stroke-width="3.5" />
              <circle cx="500" cy="262" r="20" fill="none" stroke="#7FD0FF" stroke-width="3" />
              <circle cx="500" cy="262" r="7" fill="#FFFFFF" />
            </g>
          </g>

          <!-- port OBD -->
          <rect x="179" y="217" width="14" height="10" rx="2" fill="#7FD0FF" stroke="#FFFFFF" stroke-width="1.5" />
          <text x="186" y="208" text-anchor="middle" font-size="10" fill="#DCEEFF">OBD</text>

          <!-- sinar scan -->
          <line :x1="beamX" :x2="beamX" y1="140" y2="266" stroke="#7FD0FF" stroke-width="2" :opacity="beamOpacity" />

          <!-- kabel -->
          <path d="M186 226 C 198 296, 330 300, 382 224" fill="none" stroke="#FFFFFF" stroke-opacity=".35" stroke-width="3" stroke-linecap="round" />
          <path d="M186 226 C 198 296, 330 300, 382 224" fill="none" stroke="#7FD0FF" stroke-width="3"
            stroke-linecap="round" :stroke-dasharray="cableDash" :stroke-dashoffset="cableOffset" :opacity="cableOpacity" />
          <rect x="374" y="220" width="10" height="8" rx="2" fill="#7FD0FF" />

          <!-- laptop -->
          <rect x="386" y="62" width="228" height="152" rx="10" fill="#0F3FB8" stroke="#FFFFFF" stroke-width="2" />
          <rect x="396" y="72" width="208" height="132" rx="4" fill="#081843" />
          <path d="M370 214 L630 214 L618 230 L382 230 Z" fill="#FFFFFF" fill-opacity=".92" />
          <rect x="478" y="216" width="44" height="4" rx="2" fill="#B9D3F5" />

          <!-- isi layar scan tool -->
          <rect x="396" y="72" width="208" height="18" fill="#1D5BF0" />
          <text x="403" y="85" font-size="9" fill="#FFFFFF" letter-spacing=".8">TKR SCAN · ECU ENGINE</text>
          <circle cx="594" cy="81" r="4" :fill="idle ? '#8AA2C8' : '#7FD0FF'" />

          <text x="404" y="106" class="t-body-text" font-size="10.5" fill="#DCEEFF">{{ screenText }}</text>
          <text x="596" y="106" text-anchor="end" font-size="10" fill="#7FD0FF">{{ pctText }}</text>

          <rect x="404" y="113" width="192" height="6" rx="3" fill="#FFFFFF" fill-opacity=".15" />
          <rect x="404" y="113" :width="barWidth" height="6" rx="3" fill="#7FD0FF" />

          <line x1="404" y1="148" x2="596" y2="148" stroke="#FFFFFF" stroke-opacity=".06" />
          <line x1="404" y1="168" x2="596" y2="168" stroke="#FFFFFF" stroke-opacity=".1" />
          <line x1="404" y1="188" x2="596" y2="188" stroke="#FFFFFF" stroke-opacity=".06" />

          <text x="404" y="136" font-size="8.5" fill="#7FD0FF">RPM · LIVE</text>
          <text x="596" y="136" text-anchor="end" font-size="10" fill="#FFFFFF">{{ rpmText }}</text>

          <polyline :points="wavePoints" fill="none" stroke="#7FD0FF" stroke-width="1.6"
            stroke-linejoin="round" :opacity="linked ? 1 : 0.3" />
        </svg>
      </figure>
    </div>

    <!-- B. Panel live data -->
    <div class="live-card">
      <div class="live-head">
        <strong>LIVE DATA SENSOR EFI</strong>
        <span>Mesin idle · data simulasi</span>
      </div>

      <div class="live-grid" aria-live="off">
        <div v-for="(t, i) in tiles" :key="i" class="tile" :class="{ read: t.shown }">
          <span class="tile-label">{{ t.label }}</span>
          <span class="tile-value">{{ t.value }}<small>{{ t.unit }}</small></span>
          <span class="tile-status">{{ t.status }}</span>
        </div>
      </div>

      <div class="dtc" :class="{ done }" aria-live="polite">
        <span class="dtc-label">KODE KERUSAKAN (DTC)</span>
        <span class="dtc-title">{{ dtcTitle }}</span>
        <span class="dtc-note">{{ dtcNote }}</span>
      </div>
    </div>
  </div>
</template>
