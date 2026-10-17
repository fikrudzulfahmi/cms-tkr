<script setup>
/**
 * Landing page Jurusan TKR — tema "Blueprint Drive" (spesifikasi desain).
 * Struktur: hero (nav + teks + simulasi scan EFI) → mitra → kompetensi →
 * alur → fasilitas → karier → CTA → footer.
 */
import { Head, usePage } from '@inertiajs/vue3'
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import EfiScan from '@/Components/Landing/EfiScan.vue'
import { CONFIG } from './content.js'
import '../../../css/tkr.css'

const props = defineProps({
  sliders: { type: Array, default: () => [] },
  competencies: { type: Array, default: () => [] },
  programs: { type: Array, default: () => [] },
  facilities: { type: Array, default: () => [] },
  achievements: { type: Array, default: () => [] },
  posts: { type: Array, default: () => [] },
  categories: { type: Array, default: () => [] },
  testimonials: { type: Array, default: () => [] },
  teachers: { type: Array, default: () => [] },
  partners: { type: Array, default: () => [] },
  gallery: { type: Array, default: () => [] },
  stats: { type: Object, default: () => ({}) },
})

const page = usePage()
const s = computed(() => page.props.settings || {})
const school = computed(() => s.value.school_name || CONFIG.namaSmk)

const title = computed(() => `Jurusan Teknik Kendaraan Ringan — ${school.value}`)
const metaDesc = computed(() => `Jurusan Teknik Kendaraan Ringan ${school.value}: belajar engine, EFI & diagnosis OBD, chassis, pemindah tenaga, kelistrikan, hingga kendaraan listrik. SPMB ${CONFIG.tahunAjaran} dibuka.`)

const tahun = CONFIG.tahunAjaran
const tanggalTutup = CONFIG.tanggalTutup

/* ---------- navigasi ---------- */
const navLinks = [
  { label: 'Kompetensi', href: '#kompetensi' },
  { label: 'Alur Belajar', href: '#alur' },
  { label: 'Fasilitas', href: '#fasilitas' },
  { label: 'Prospek Karier', href: '#karier' },
]
const menuOpen = ref(false)
const scrolled = ref(false)
const closeMenu = () => { menuOpen.value = false }
const onScroll = () => { scrolled.value = window.scrollY > 8 }

onMounted(() => {
  onScroll()
  window.addEventListener('scroll', onScroll, { passive: true })
})
onBeforeUnmount(() => window.removeEventListener('scroll', onScroll))

/* ---------- ikon inline (spesifikasi §4.5) ---------- */
const ICONS = {
  engine: '<path d="M4 10h3l2-3h6l2 3h3v8H4z"/><path d="M8 7V5h8v2M2 12v4M22 12v4"/>',
  diagnosis: '<rect x="3" y="5" width="18" height="12" rx="2"/><path d="M7 21h10M12 17v4M7 13l3-3 2 2 4-4"/>',
  chassis: '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/><path d="M12 3v6M4.5 16.5l5-3M19.5 16.5l-5-3"/>',
  drivetrain: '<circle cx="6" cy="6" r="2"/><circle cx="12" cy="6" r="2"/><circle cx="18" cy="6" r="2"/><circle cx="6" cy="18" r="2"/><circle cx="12" cy="18" r="2"/><path d="M6 8v8M12 8v8M18 8v4H6"/>',
  electrical: '<path d="M12 2v20M4.9 7l14.2 10M4.9 17L19.1 7"/><path d="M9 4l3 2 3-2M9 20l3-2 3 2"/>',
  ev: '<rect x="3" y="7" width="16" height="10" rx="2"/><path d="M21 10v4"/><path d="M11 9l-2 3h4l-2 3"/>',
  // alias ikon lama dari admin
  'wrench-screwdriver': '<path d="M4 10h3l2-3h6l2 3h3v8H4z"/><path d="M8 7V5h8v2M2 12v4M22 12v4"/>',
  'cpu-chip': '<rect x="3" y="5" width="18" height="12" rx="2"/><path d="M7 21h10M12 17v4M7 13l3-3 2 2 4-4"/>',
  bolt: '<path d="M12 2v20M4.9 7l14.2 10M4.9 17L19.1 7"/><path d="M9 4l3 2 3-2M9 20l3-2 3 2"/>',
  'shield-check': '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/><path d="M12 3v6M4.5 16.5l5-3M19.5 16.5l-5-3"/>',
}

/* ---------- kompetensi ---------- */
const KOMP_FALLBACK = [
  { title: 'Engine Bensin & Diesel', short_description: 'Tune up, overhaul mesin, sistem pelumasan, pendinginan, dan bahan bakar.', icon: 'engine' },
  { title: 'EFI & Diagnosis OBD', short_description: 'Membaca data sensor dan kode kerusakan ECU menggunakan scan tool OBD-II.', icon: 'diagnosis' },
  { title: 'Chassis: Rem, Suspensi & Kemudi', short_description: 'Sistem rem termasuk ABS, suspensi, power steering, serta spooring dan balancing.', icon: 'chassis' },
  { title: 'Pemindah Tenaga', short_description: 'Kopling, transmisi manual, transmisi otomatis dan CVT, poros propeler, serta diferensial.', icon: 'drivetrain' },
  { title: 'Kelistrikan Bodi & AC', short_description: 'Sistem starter, pengisian, penerangan, kelistrikan bodi, dan AC mobil.', icon: 'electrical' },
  { title: 'Kendaraan Listrik & Hybrid', short_description: 'Dasar baterai tegangan tinggi, motor listrik, dan prosedur keselamatan kerja EV.', icon: 'ev' },
]
const kompList = computed(() => {
  const src = props.competencies?.length ? props.competencies : KOMP_FALLBACK
  return src.slice(0, 6).map((c, i) => ({
    title: c.title,
    desc: c.short_description || c.description || '',
    icon: c.icon || KOMP_FALLBACK[i % 6].icon,
  }))
})
const kompNum = (i) => String(i + 1).padStart(2, '0')

/* ---------- fasilitas ---------- */
const FAS_FALLBACK = [
  { name: 'Bengkel Praktik & Car Lift', description: '[Jumlah] stall dan [jumlah] unit car lift.', image: null },
  { name: 'Scan Tool & Engine Analyzer', description: 'Alat diagnosis untuk sistem EFI dan OBD-II.', image: null },
  { name: 'Spooring & Balancing', description: 'Peralatan penyetelan roda dan kaki-kaki.', image: null },
  { name: 'Trainer Kelistrikan & EV', description: 'Media praktik kelistrikan bodi dan kendaraan listrik.', image: null },
]
const fasList = computed(() => {
  const src = props.facilities?.length ? props.facilities : FAS_FALLBACK
  return src.slice(0, 4).map((f) => ({
    name: f.name,
    description: f.description || '',
    image: f.image || null,
  }))
})
const img = (p) => (p ? (/^https?:\/\//.test(p) ? p : `/storage/${p}`) : null)

/* ---------- mitra ---------- */
const mitraList = computed(() => {
  if (props.partners?.length) {
    return props.partners.slice(0, 5).map((p) => ({ name: p.name, logo: p.logo }))
  }
  return CONFIG.mitra.map((n) => ({ name: n, logo: null }))
})

/* ---------- statistik ---------- */
const partnerCount = computed(() => props.partners.filter((p) => p.name && p.name !== '[LOGO MITRA]').length)
const statCards = computed(() => [
  { value: s.value.stat_students || CONFIG.stats.siswa, label: 'Siswa aktif' },
  { value: partnerCount.value > 0 ? `${partnerCount.value}+` : CONFIG.stats.mitra, label: 'Mitra dealer & industri' },
  { value: s.value.stat_absorption || CONFIG.stats.serapan, label: 'Lulusan terserap' },
])

/* ---------- alur belajar ---------- */
const alurList = [
  { roman: 'X', title: 'Dasar Otomotif', desc: 'K3 bengkel, alat ukur presisi, gambar teknik, dan prinsip kerja kendaraan.', white: false },
  { roman: 'XI', title: 'Praktik Sistem', desc: 'Engine, chassis, pemindah tenaga, dan kelistrikan pada unit mobil di bengkel sekolah.', white: false },
  { roman: 'XII', title: 'PKL & Sertifikasi', desc: 'Praktik kerja lapangan di dealer atau bengkel mitra, lalu uji kompetensi keahlian.', white: true },
]

/* ---------- karier ---------- */
const karierList = [
  { title: 'Mekanik Dealer Resmi', desc: 'Teknisi servis di bengkel resmi berbagai merek mobil.' },
  { title: 'Service Advisor', desc: 'Penghubung antara pelanggan dan tim mekanik.' },
  { title: 'Wirausaha Otomotif', desc: 'Bengkel umum, spesialis AC, atau usaha suku cadang.' },
  { title: 'Kuliah Lanjut', desc: 'D3/D4/S1 Teknik Otomotif atau Teknik Mesin.' },
]

/* ---------- form CTA ---------- */
const wa = ref('')
const error = ref('')
const success = ref('')
const sending = ref(false)

async function submit() {
  error.value = ''
  success.value = ''
  const digits = wa.value.replace(/[\s-]/g, '')
  if (!/^(\+62|62|0)8[0-9]{7,12}$/.test(digits)) {
    error.value = 'Nomor WhatsApp belum valid. Contoh: 081234567890.'
    return
  }

  const admin = String(s.value.whatsapp || CONFIG.nomorAdmin || '').replace(/\D/g, '').replace(/^0/, '62')

  // Simpan sebagai pesan masuk agar admin melihatnya di panel (fitur yang sama dengan CMS).
  sending.value = true
  try {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
    await fetch('/kontak', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrf,
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: JSON.stringify({
        name: 'Calon Siswa TKR',
        phone: digits,
        subject: 'Pendaftaran TKR (landing)',
        message: `Calon siswa ingin mendaftar Jurusan TKR. Nomor WhatsApp: ${digits}`,
      }),
    })
  } catch (e) {
    // Penyimpanan pesan bersifat tambahan; alur WhatsApp tetap bisa dilanjutkan.
  } finally {
    sending.value = false
  }

  if (admin) {
    const text = encodeURIComponent(`Halo, saya ingin mendaftar Jurusan Teknik Kendaraan Ringan. Nomor WhatsApp saya: ${digits}`)
    window.open(`https://wa.me/${admin}?text=${text}`, '_blank', 'noopener')
    success.value = 'Membuka WhatsApp admin SPMB…'
  } else {
    success.value = 'Nomor WhatsApp kamu sudah kami terima. Tim SPMB akan menghubungi.'
  }
  wa.value = ''
}
</script>

<template>
  <div class="tkr">
    <Head>
      <title>{{ title }}</title>
      <meta name="description" :content="metaDesc" />
      <meta property="og:title" :content="title" />
      <meta property="og:description" :content="metaDesc" />
    </Head>

    <header class="hero grid-ice">
      <nav class="tkr-nav" :class="{ scrolled }" aria-label="Navigasi utama">
        <div class="container nav-inner">
          <a class="brand" href="#top" aria-label="Beranda TKR">
            <span class="brand-mark">
              <svg width="28" height="28" viewBox="0 0 32 32" fill="none" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M4 19l3-7a3 3 0 0 1 2.8-2h12.4a3 3 0 0 1 2.8 2l3 7v5H4z" />
                <path d="M4 19h24" />
                <circle cx="10" cy="24" r="2.5" fill="#7FD0FF" stroke="none" />
                <circle cx="22" cy="24" r="2.5" fill="#7FD0FF" stroke="none" />
              </svg>
            </span>
            <span class="brand-text">
              <strong>TKR</strong>
              <span>{{ school }}</span>
            </span>
          </a>

          <div class="nav-links">
            <a v-for="l in navLinks" :key="l.href" :href="l.href">{{ l.label }}</a>
          </div>

          <div class="nav-actions">
            <a class="btn btn-secondary" href="#kontak">Kontak</a>
            <a class="btn btn-primary" href="#daftar">Daftar Sekarang</a>
            <button type="button" class="nav-burger" :aria-expanded="menuOpen" aria-label="Buka menu" @click="menuOpen = !menuOpen">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                <path v-if="!menuOpen" d="M4 7h16M4 12h16M4 17h16" />
                <path v-else d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <div v-if="menuOpen" class="mobile-menu">
          <div class="container">
            <a v-for="l in navLinks" :key="l.href" :href="l.href" @click="closeMenu">{{ l.label }}</a>
            <div class="nav-actions">
              <a class="btn btn-secondary" href="#kontak" @click="closeMenu">Kontak</a>
              <a class="btn btn-primary" href="#daftar" @click="closeMenu">Daftar Sekarang</a>
            </div>
          </div>
        </div>
      </nav>

      <div id="top" class="container hero-content">
        <div class="hero-text">
          <span class="hero-badge">SPMB {{ tahun }} · KUOTA TERBATAS</span>
          <h1>Teknik Kendaraan Ringan<span class="dot">.</span></h1>
          <p class="hero-sub">Kuasai teknologi mobil masa kini, dari mesin hingga kendaraan listrik.</p>
          <p class="hero-lead">Belajar diagnosis, perawatan, dan perbaikan mobil di bengkel praktik berstandar industri, bersama guru produktif dan mitra dealer resmi.</p>

          <div class="hero-actions">
            <a class="btn btn-primary btn-hero" href="#daftar">
              Daftar Sekarang
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M5 12h14M13 6l6 6-6 6" />
              </svg>
            </a>
            <a class="btn btn-secondary btn-hero" href="#kompetensi">Jelajahi Kompetensi</a>
          </div>

          <div class="stats">
            <div v-for="st in statCards" :key="st.label" class="stat">
              <b>{{ st.value }}</b>
              <span>{{ st.label }}</span>
            </div>
          </div>
        </div>

        <div class="hero-sim">
          <EfiScan />
        </div>
      </div>
    </header>

    <main>
      <!-- Mitra -->
      <section class="mitra" aria-label="Mitra dealer dan industri">
        <div class="container mitra-inner">
          <span class="mitra-label">MITRA DEALER &amp; INDUSTRI</span>
          <div class="mitra-row">
            <div v-for="(m, i) in mitraList" :key="i" class="mitra-logo">
              <img v-if="m.logo" :src="img(m.logo)" :alt="m.name" loading="lazy" />
              <span v-else>{{ m.name }}</span>
            </div>
          </div>
        </div>
      </section>

      <!-- Kompetensi -->
      <section id="kompetensi" class="section">
        <div class="container">
          <div class="section-head">
            <div>
              <span class="mono-label">[01] KOMPETENSI KEAHLIAN</span>
              <h2>Enam bidang yang kamu kuasai di TKR.</h2>
            </div>
            <p class="aside">Kurikulum disusun bersama dunia industri, dengan porsi praktik langsung pada unit mobil nyata.</p>
          </div>

          <div class="komp-grid">
            <article v-for="(k, i) in kompList" :key="k.title" class="komp-card"
              :class="{ 'is-blue': i === 1, 'is-sky': i === 5 }">
              <div class="komp-top">
                <span class="komp-icon">
                  <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" v-html="ICONS[k.icon] || ICONS.engine" />
                </span>
                <span class="komp-num">{{ kompNum(i) }}<template v-if="i === 5"> · BARU</template></span>
              </div>
              <h3>{{ k.title }}</h3>
              <p>{{ k.desc }}</p>
            </article>
          </div>
        </div>
      </section>

      <!-- Alur belajar -->
      <section id="alur" class="section" style="padding-top: 0">
        <div class="container">
          <div class="panel grid-blue">
            <span class="mono-label on-blue">[02] ALUR BELAJAR</span>
            <h2>Tiga tahun, satu jalur menuju bengkel profesional.</h2>

            <div class="alur-grid">
              <article v-for="a in alurList" :key="a.roman" class="alur-card" :class="{ 'is-white': a.white }">
                <div class="alur-top">
                  <span class="alur-num">{{ a.roman }}</span>
                  <span class="alur-rule" aria-hidden="true"></span>
                </div>
                <h3>{{ a.title }}</h3>
                <p>{{ a.desc }}</p>
              </article>
            </div>
          </div>
        </div>
      </section>

      <!-- Fasilitas -->
      <section id="fasilitas" class="section">
        <div class="container">
          <div class="section-head">
            <div>
              <span class="mono-label">[03] FASILITAS</span>
              <h2>Bengkel yang terasa seperti dealer resmi.</h2>
            </div>
          </div>

          <div class="fas-grid">
            <article v-for="(f, i) in fasList" :key="f.name" class="fas-card"
              :class="{ 'is-blue': i === 3, 'has-img': !!f.image }">
              <img v-if="f.image" :src="img(f.image)" :alt="f.name" loading="lazy" />
              <span class="fas-caption">[FOTO]</span>
              <h3>{{ f.name }}</h3>
              <p>{{ f.description }}</p>
            </article>
          </div>
        </div>
      </section>

      <!-- Karier -->
      <section id="karier" class="karier grid-ice">
        <div class="container karier-layout">
          <div class="karier-copy">
            <span class="mono-label">[04] PROSPEK KARIER</span>
            <h2>Setelah lulus, jalanmu terbuka lebar.</h2>
            <p>Industri otomotif terus butuh teknisi yang paham sistem konvensional maupun teknologi baru.</p>
          </div>
          <div class="karier-grid">
            <article v-for="k in karierList" :key="k.title" class="karier-card">
              <h3>{{ k.title }}</h3>
              <p>{{ k.desc }}</p>
            </article>
          </div>
        </div>
      </section>

      <!-- CTA -->
      <section id="daftar" class="cta-wrap" style="padding-top: 104px">
        <div class="container">
          <div class="panel grid-blue cta-panel">
            <div class="cta-copy">
              <h2>Nyalakan mesinmu. <span>Daftar TKR.</span></h2>
              <p>Isi nomor WhatsApp, tim SPMB akan menghubungimu. Pendaftaran ditutup {{ tanggalTutup }}.</p>
            </div>

            <form class="cta-form" novalidate @submit.prevent="submit">
              <label class="sr-only" for="tkr-wa">Nomor WhatsApp</label>
              <input id="tkr-wa" v-model="wa" class="cta-input" :class="{ invalid: !!error }"
                type="tel" inputmode="tel" autocomplete="tel" placeholder="08xx-xxxx-xxxx" />
              <button type="submit" class="btn btn-white cta-submit" :disabled="sending">
                {{ sending ? 'Mengirim…' : 'Daftar Sekarang' }}
              </button>
              <p v-if="error" class="cta-error" role="alert">{{ error }}</p>
              <p v-if="success" class="cta-ok" role="status">{{ success }}</p>
            </form>
          </div>
        </div>
      </section>
    </main>

    <footer id="kontak" class="footer">
      <div class="container">
        <div class="footer-grid">
          <div class="footer-brand">
            <strong>Teknik Kendaraan Ringan</strong>
            <span>{{ school }}</span>
            <span>{{ s.address || CONFIG.alamat }}</span>
          </div>

          <div class="footer-cols">
            <div class="footer-col">
              <h4>JURUSAN</h4>
              <ul>
                <li><a href="#kompetensi">Kompetensi</a></li>
                <li><a href="#fasilitas">Fasilitas</a></li>
                <li><a href="#karier">Prospek Karier</a></li>
              </ul>
            </div>
            <div class="footer-col">
              <h4>KONTAK</h4>
              <ul>
                <li>
                  <a v-if="s.phone" :href="`tel:${s.phone}`">{{ s.phone }}</a>
                  <span v-else>{{ CONFIG.telepon }}</span>
                </li>
                <li>
                  <a v-if="s.email" :href="`mailto:${s.email}`">{{ s.email }}</a>
                  <span v-else>{{ CONFIG.email }}</span>
                </li>
                <li>
                  <a v-if="s.instagram_url" :href="s.instagram_url" target="_blank" rel="noopener">Instagram</a>
                  <span v-else>{{ CONFIG.instagram }}</span>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div class="footer-bottom">© 2026 Jurusan Teknik Kendaraan Ringan — {{ school }}</div>
      </div>
    </footer>
  </div>
</template>
