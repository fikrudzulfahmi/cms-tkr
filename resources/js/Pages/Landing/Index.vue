<script setup>
import SiteNav from '@/Components/Shared/SiteNav.vue'
/**
 * Landing page Jurusan TKR — tema "Blueprint Drive".
 * Struktur: hero TKR (nav + headline + simulasi scan EFI) → ticker → mitra →
 * seluruh section data-driven dari HomeController (tentang, kompetensi, alur,
 * program, sarana, prestasi, berita, galeri, guru, karier, alumni, FAQ, SPMB,
 * kontak) → footer.
 */
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import { computed, onMounted, onBeforeUnmount, ref, nextTick, watch } from 'vue'
import EfiScan from '@/Components/Landing/EfiScan.vue'
import { CONFIG } from './content.js'
import '../../../css/tkr.css'
import '../../../css/landing-sections.css'

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
const flash = computed(() => page.props.flash || {})
const img = (p) => (p ? (/^https?:\/\//.test(p) ? p : `/storage/${p}`) : null)
const schoolName = computed(() => s.value.school_name || CONFIG.namaSmk)
const address = computed(() => s.value.address || CONFIG.alamat)
const tahun = computed(() => s.value.spmb_year || CONFIG.tahunAjaran)

/* ---------- HEADLINE (hero berganti dari slider) ---------- */
const slideIdx = ref(0)
const headlines = computed(() => {
  if (props.sliders.length) return props.sliders
  return [{ title: s.value.hero_title || 'Teknik Kendaraan Ringan', subtitle: s.value.hero_subtitle || '', image: s.value.hero_bg }]
})
const current = computed(() => headlines.value[slideIdx.value % headlines.value.length])
// Jangan pernah merender javascript:/data: dari isian admin sebagai href.
const safeHref = (url) => (/^(https?:\/\/|\/|#)/i.test(String(url || '')) ? url : '#kompetensi')
let slideTimer = null
function goSlide(i) { slideIdx.value = (i + headlines.value.length) % headlines.value.length }

/* ---------- Ticker pengumuman ---------- */
const ticker = computed(() => {
  const ann = props.posts.filter(p => p.category?.slug === 'pengumuman')
  return (ann.length ? ann : props.posts).slice(0, 5)
})

/* ---------- Statistik hero ---------- */
const heroStats = computed(() => [
  { label: 'Siswa aktif', value: props.stats?.students ?? '—' },
  { label: 'Mitra industri', value: props.stats?.partners ?? '—' },
  { label: 'Lulusan terserap', value: props.stats?.absorption ?? '—' },
])

/* ---------- Ikon inline (Blueprint Drive) ---------- */
const ICONS = {
  engine: '<path d="M4 10h3l2-3h6l2 3h3v8H4z"/><path d="M8 7V5h8v2M2 12v4M22 12v4"/>',
  diagnosis: '<rect x="3" y="5" width="18" height="12" rx="2"/><path d="M7 21h10M12 17v4M7 13l3-3 2 2 4-4"/>',
  chassis: '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/><path d="M12 3v6M4.5 16.5l5-3M19.5 16.5l-5-3"/>',
  drivetrain: '<circle cx="6" cy="6" r="2"/><circle cx="12" cy="6" r="2"/><circle cx="18" cy="6" r="2"/><circle cx="6" cy="18" r="2"/><circle cx="12" cy="18" r="2"/><path d="M6 8v8M12 8v8M18 8v4H6"/>',
  electrical: '<path d="M12 2v20M4.9 7l14.2 10M4.9 17L19.1 7"/><path d="M9 4l3 2 3-2M9 20l3-2 3 2"/>',
  ev: '<rect x="3" y="7" width="16" height="10" rx="2"/><path d="M21 10v4"/><path d="M11 9l-2 3h4l-2 3"/>',
  // alias nama ikon dari panel admin
  'wrench-screwdriver': '<path d="M4 10h3l2-3h6l2 3h3v8H4z"/><path d="M8 7V5h8v2M2 12v4M22 12v4"/>',
  'cpu-chip': '<rect x="3" y="5" width="18" height="12" rx="2"/><path d="M7 21h10M12 17v4M7 13l3-3 2 2 4-4"/>',
  bolt: '<path d="M12 2v20M4.9 7l14.2 10M4.9 17L19.1 7"/><path d="M9 4l3 2 3-2M9 20l3-2 3 2"/>',
  'shield-check': '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/><path d="M12 3v6M4.5 16.5l5-3M19.5 16.5l-5-3"/>',
  'building-storefront': '<path d="M3 9l1.5-5h15L21 9M3 9v11h18V9M3 9h18M9 20v-6h6v6"/>',
  briefcase: '<path d="M3 7h18v13H3zM8 7V4h8v3M3 13h18"/>',
  'document-check': '<path d="M6 2h9l5 5v15H6zM14 2v6h6M9 15l2 2 4-4"/>',
  users: '<path d="M16 20v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 10a4 4 0 1 0 0-8 4 4 0 0 0 0 8M22 20v-2a4 4 0 0 0-3-3.9M16 2.1a4 4 0 0 1 0 7.8"/>',
}
const icon = (name) => ICONS[name] || ICONS.engine
const kompNum = (i) => String(i + 1).padStart(2, '0')


/* ---------- Alur belajar ---------- */
const alur = [
  { label: 'KELAS X', title: 'Dasar Otomotif', desc: 'Keselamatan kerja, penggunaan alat ukur dan perkakas, serta prinsip kerja kendaraan ringan.', dark: false },
  { label: 'KELAS XI', title: 'Praktik Bengkel', desc: 'Servis mesin, kelistrikan, sasis, dan sistem injeksi pada unit kendaraan nyata di bengkel sekolah.', dark: false },
  { label: 'KELAS XII', title: 'PKL & Uji Kompetensi', desc: 'Praktik kerja lapangan di mitra industri, lalu uji kompetensi untuk sertifikat keahlian.', dark: true },
]

/* ---------- Sarana ---------- */
const facCat = ref('Semua')
const facCats = computed(() => ['Semua', ...new Set(props.facilities.map(f => f.category).filter(Boolean))])
const facList = computed(() => facCat.value === 'Semua' ? props.facilities : props.facilities.filter(f => f.category === facCat.value))

/* ---------- Berita ---------- */
const postCat = ref('semua')
const postList = computed(() => {
  const list = postCat.value === 'semua' ? props.posts : props.posts.filter(p => p.category?.slug === postCat.value)
  return list.slice(0, 6)
})
const featuredPost = computed(() => postList.value[0])
const otherPosts = computed(() => postList.value.slice(1))
const fmtDate = (d) => d ? new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : ''

/* ---------- Sambutan kepala jurusan ---------- */
const kajur = computed(() => {
  const t = props.teachers.find(t => /kepala/i.test(t.position || '')) || props.teachers[0]
  if (!s.value.profile_head_name) return t
  return { name: s.value.profile_head_name, position: s.value.profile_head_position, photo: s.value.profile_head_photo || null }
})
// Kutipan sambutan: paragraf terakhir yang bukan salam pembuka, dari Pengaturan → Profil
const sambutan = computed(() => {
  const ps = String(s.value.profile_speech || '').split(/\n\s*\n/).map(p => p.trim()).filter(p => p && !/^assalamu/i.test(p))
  const t = ps[0] || 'Kami berkomitmen membekali setiap siswa dengan keterampilan nyata, karakter Islami, dan kesiapan kerja agar mampu bersaing di dunia industri otomotif.'
  return t.length > 260 ? t.slice(0, 257).replace(/\s+\S*$/, '') + '…' : t
})

const karier = [
  { no: '01', title: 'Mekanik Dealer Resmi', desc: 'Di dealer dan bengkel resmi berbagai merek kendaraan.' },
  { no: '02', title: 'Wirausaha Bengkel', desc: 'Membuka bengkel umum, spesialis AC, atau usaha suku cadang.' },
  { no: '03', title: 'Teknisi Industri', desc: 'Quality control dan perakitan di pabrik otomotif.' },
  { no: '04', title: 'Kuliah Lanjut', desc: 'Ke D3/D4/S1 Teknik Mesin atau Otomotif.' },
]

const faqs = [
  { q: 'Apa syarat mendaftar SPMB di jurusan TKR?', a: 'Lulusan SMP/MTs sederajat, mendaftar melalui aplikasi SPMB sekolah, serta melampirkan fotokopi ijazah/SKL, KK, dan akta kelahiran.' },
  { q: 'Apakah siswa perempuan boleh masuk TKR?', a: 'Tentu boleh. Materi dan fasilitas praktik terbuka untuk semua siswa, dan banyak alumni perempuan berkarier di dunia otomotif.' },
  { q: 'Apakah ada sertifikat keahlian?', a: 'Ya. Di kelas XII siswa mengikuti Uji Kompetensi Keahlian (UKK) dan sertifikasi bersama mitra industri.' },
  { q: 'Di mana siswa melaksanakan PKL?', a: 'Di dealer, bengkel resmi, dan industri otomotif yang menjadi mitra jurusan, dengan pendampingan guru pembimbing.' },
  { q: 'Apakah tersedia beasiswa?', a: 'Tersedia beasiswa prestasi dan bantuan bagi siswa kurang mampu. Hubungi panitia SPMB untuk informasi lengkap.' },
]
const openFaq = ref(0)

/* ---------- Galeri lightbox ---------- */
const lightbox = ref(null)

/* ---------- Form ---------- */
const testiForm = useForm({ name: '', graduation_year: '', occupation: '', workplace: '', message: '', rating: 5, website: '' })
const showTesti = ref(false)
function submitTesti() {
  testiForm.post('/testimoni', { preserveScroll: true, onSuccess: () => { testiForm.reset(); showTesti.value = false } })
}
const contactForm = useForm({ name: '', email: '', phone: '', subject: '', message: '', website: '' })
function submitContact() {
  contactForm.post('/kontak', { preserveScroll: true, onSuccess: () => contactForm.reset() })
}

const waNumber = computed(() => {
  const raw = (s.value.whatsapp || s.value.phone || CONFIG.nomorAdmin || '').replace(/\D/g, '')
  if (!raw || raw.length < 9) return ''
  return raw.startsWith('0') ? '62' + raw.slice(1) : raw
})
const waLink = computed(() => waNumber.value ? `https://wa.me/${waNumber.value}?text=${encodeURIComponent('Halo, saya ingin bertanya tentang pendaftaran Jurusan TKR.')}` : '#kontak')

/* ---------- Form cepat SPMB: simpan pesan + lanjut ke WhatsApp ---------- */
const waInput = ref('')
const waErr = ref('')
const waOk = ref('')
const waSending = ref(false)
async function submitWa() {
  waErr.value = ''
  waOk.value = ''
  const digits = String(waInput.value || '').replace(/[\s.-]/g, '')
  if (!/^(?:\+62|62|0)8[0-9]{7,12}$/.test(digits)) {
    waErr.value = 'Nomor WhatsApp belum valid. Contoh: 081234567890.'
    return
  }
  // Buka WhatsApp lebih dulu, masih di dalam gesture klik, agar tidak diblokir popup blocker.
  if (waNumber.value) {
    const text = encodeURIComponent(`Halo, saya ingin mendaftar Jurusan Teknik Kendaraan Ringan. Nomor WhatsApp saya: ${digits}`)
    window.open(`https://wa.me/${waNumber.value}?text=${text}`, '_blank', 'noopener')
  }
  waSending.value = true
  try {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
    const res = await fetch('/kontak', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
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
    if (!res.ok) throw new Error('gagal')
    waOk.value = waNumber.value
      ? 'Membuka WhatsApp admin SPMB. Nomor kamu juga sudah tersimpan.'
      : 'Nomor WhatsApp kamu sudah kami terima. Tim SPMB akan menghubungi.'
    waInput.value = ''
  } catch (e) {
    waOk.value = waNumber.value
      ? 'Membuka WhatsApp admin SPMB…'
      : 'Nomor belum terkirim. Coba lagi atau pakai formulir di bawah.'
  } finally {
    waSending.value = false
  }
}

const logoPath = computed(() => s.value.site_logo || null)
const logoUrl = computed(() => logoPath.value ? img(logoPath.value) : null)
const spmbUrl = computed(() => {
  const u = (s.value.spmb_url || '').trim()
  return /^https?:\/\//i.test(u) ? u : null
})
// Sembunyikan gambar yang gagal dimuat (file terhapus/rusak) agar tidak tampil ikon rusak.
const hideImg = (e) => { e.target.style.visibility = 'hidden' }

const siteUrl = computed(() => (typeof window !== 'undefined' ? window.location.origin : ''))
const pageTitle = computed(() => `${s.value.department_name || 'Teknik Kendaraan Ringan'} — ${schoolName.value}`)
const pageDesc = computed(() => `Jurusan ${s.value.department_name || 'Teknik Kendaraan Ringan'} ${schoolName.value}: kompetensi keahlian, program unggulan, sarana praktik, prestasi, berita, dan testimoni alumni. ${address.value}.`)
const ogImage = computed(() => {
  const p = s.value.hero_bg || s.value.site_logo
  if (!p) return null
  return /^https?:/i.test(p) ? p : siteUrl.value + '/storage/' + p
})

const socials = computed(() => [
  { key: 'instagram_url', label: 'Instagram' },
  { key: 'facebook_url', label: 'Facebook' },
  { key: 'youtube_url', label: 'YouTube' },
  { key: 'tiktok_url', label: 'TikTok' },
].filter(x => s.value[x.key]).map(x => ({ ...x, url: s.value[x.key] })))

/* ---------- Scroll / reveal ---------- */
let io = null
function observeReveal() {
  const reduce = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches
  const els = document.querySelectorAll('.tkr .reveal:not(.is-in)')
  if (reduce || !('IntersectionObserver' in window)) { els.forEach(el => el.classList.add('is-in')); return }
  if (!io) {
    io = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (!entry.isIntersecting) return
        const siblings = [...entry.target.parentElement.children].filter(c => c.classList.contains('reveal'))
        entry.target.style.transitionDelay = `${Math.min(6, Math.max(0, siblings.indexOf(entry.target))) * 60}ms`
        entry.target.classList.add('is-in')
        io.unobserve(entry.target)
      })
    }, { threshold: 0.1 })
  }
  els.forEach(el => io.observe(el))
}
// Item baru setelah ganti tab langsung tampil
watch([postCat, facCat], () => nextTick(() => document.querySelectorAll('.tkr .reveal:not(.is-in)').forEach(el => el.classList.add('is-in'))))
watch(lightbox, (v) => { if (typeof document !== 'undefined') document.body.style.overflow = v ? 'hidden' : '' })
const onKey = (e) => { if (e.key === 'Escape' && lightbox.value) lightbox.value = null }

// Data terstruktur untuk mesin pencari (Google) — disuntik saat komponen terpasang.
function injectJsonLd() {
  if (typeof document === 'undefined') return
  document.getElementById('ld-school')?.remove()
  const el = document.createElement('script')
  el.type = 'application/ld+json'
  el.id = 'ld-school'
  el.textContent = JSON.stringify({
    '@context': 'https://schema.org',
    '@type': 'School',
    name: schoolName.value,
    alternateName: s.value.department_name || undefined,
    url: siteUrl.value + '/',
    logo: logoPath.value ? siteUrl.value + img(logoPath.value) : undefined,
    image: ogImage.value || undefined,
    telephone: s.value.phone || undefined,
    email: s.value.email || undefined,
    address: {
      '@type': 'PostalAddress',
      streetAddress: s.value.address || '',
      addressLocality: s.value.city || undefined,
      addressRegion: s.value.province || undefined,
      addressCountry: 'ID',
    },
    sameAs: socials.value.map(x => x.url),
  })
  document.head.appendChild(el)
}

const showTop = ref(false)
const onScroll = () => {
  showTop.value = window.scrollY > 480
}
function toTop() {
  const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches
  window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' })
}

onMounted(() => {
  onScroll()
  window.addEventListener('scroll', onScroll, { passive: true })
  observeReveal()
  injectJsonLd()
  document.addEventListener('keydown', onKey)
  if (headlines.value.length > 1) slideTimer = setInterval(() => goSlide(slideIdx.value + 1), 6000)
})
onBeforeUnmount(() => {
  window.removeEventListener('scroll', onScroll)
  io?.disconnect()
  clearInterval(slideTimer)
  document.removeEventListener('keydown', onKey)
  document.body.style.overflow = ''
})
</script>

<template>
  <div class="tkr">
    <Head>
      <title>{{ pageTitle }}</title>
      <!-- Meta deskripsi, canonical, Open Graph, dan Twitter Card dirender server-side
           di resources/views/app.blade.php agar tetap terbaca crawler tanpa JavaScript. -->
    </Head>

    <!-- ================= HERO ================= -->
    <SiteNav />
    <header class="hero grid-ice">

      <div id="top" class="container hero-content">
        <div class="hero-text">
          <span class="hero-badge">SPMB {{ tahun }} · KUOTA TERBATAS</span>
          <transition name="fade" mode="out-in">
            <h1 :key="slideIdx">{{ current.title }}<span class="dot">.</span></h1>
          </transition>
          <p class="hero-sub">{{ current.subtitle || 'Kuasai teknologi kendaraan ringan masa kini, dari mesin hingga kendaraan listrik.' }}</p>
          <p class="hero-lead">{{ s.site_tagline || 'Belajar diagnosis, perawatan, dan perbaikan kendaraan di bengkel praktik berstandar industri, bersama guru produktif dan mitra dealer resmi.' }}</p>

          <div class="hero-actions">
            <a class="btn btn-primary btn-hero" href="#spmb">
              Daftar Sekarang
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M5 12h14M13 6l6 6-6 6" />
              </svg>
            </a>
            <a :href="safeHref(current.button_url)" class="btn btn-secondary btn-hero">{{ current.button_text || 'Jelajahi Kompetensi' }}</a>
          </div>

          <div v-if="headlines.length > 1" class="hero-dots" aria-label="Pilih headline">
            <button v-for="(h, i) in headlines" :key="i" type="button" :class="{ on: i === slideIdx }"
              :aria-label="`Headline ${i + 1}`" @click="goSlide(i)"></button>
          </div>

          <div class="stats">
            <div v-for="st in heroStats" :key="st.label" class="stat">
              <b>{{ st.value }}</b>
              <span>{{ st.label }}</span>
            </div>
          </div>
        </div>

        <div class="hero-sim">
          <EfiScan />
        </div>
      </div>

      <!-- Ticker info terkini -->
      <div v-if="ticker.length" class="ticker">
        <div class="container ticker-inner">
          <span class="ticker-label">INFO TERKINI</span>
          <div class="ticker-track">
            <div class="ticker-move">
              <Link v-for="(p, i) in [...ticker, ...ticker]" :key="i" :href="`/berita/${p.slug}`"
                :aria-hidden="i >= ticker.length ? 'true' : null" :tabindex="i >= ticker.length ? -1 : null">
                <b>{{ p.category?.name }}</b>{{ p.title }}
              </Link>
            </div>
          </div>
        </div>
      </div>
    </header>

    <main>
      <!-- ================= MITRA ================= -->
      <section v-if="partners.length" class="mitra" aria-label="Mitra industri">
        <div class="container mitra-inner">
          <span class="mitra-label">MITRA INDUSTRI &amp; DUDI</span>
          <div class="marquee">
            <ul class="marquee-move">
              <li v-for="(m, i) in [...partners, ...partners]" :key="i" class="mitra-logo" :title="m.name"
                :aria-hidden="i >= partners.length ? 'true' : null">
                <img v-if="m.logo" :src="img(m.logo)" :alt="m.name" loading="lazy" @error="hideImg" />
                <span v-else>{{ m.name }}</span>
              </li>
            </ul>
          </div>
        </div>
      </section>

      <!-- ================= TENTANG + SAMBUTAN ================= -->
      <section id="tentang" class="section">
        <div class="container about-wrap">
          <div v-if="s.about_image" class="about-media reveal">
            <img :src="img(s.about_image)" alt="Tentang jurusan TKR" loading="lazy" @error="hideImg" />
          </div>
          <div class="about-text reveal">
            <span class="mono-label">TENTANG JURUSAN</span>
            <h2>{{ s.department_name || 'Teknik Kendaraan Ringan' }}</h2>
            <div class="about-body" v-html="s.about_text"></div>
            <blockquote v-if="kajur" class="sambutan">
              <p>“{{ sambutan }}”</p>
              <footer>
                <img v-if="kajur.photo" :src="img(kajur.photo)" :alt="kajur.name" loading="lazy" @error="hideImg" />
                <span><b>{{ kajur.name }}</b><small>{{ kajur.position }}</small></span>
              </footer>
            </blockquote>
            <Link href="/profil" class="btn btn-outline btn-sm">Profil lengkap jurusan →</Link>
          </div>
        </div>
      </section>

      <!-- ================= KOMPETENSI ================= -->
      <section id="kompetensi" class="section section-tint">
        <div class="container">
          <div class="section-head reveal">
            <div>
              <span class="mono-label">KOMPETENSI KEAHLIAN</span>
              <h2>Apa saja yang kamu pelajari di TKR?</h2>
            </div>
            <Link href="/kompetensi" class="btn btn-outline btn-sm">Semua kompetensi →</Link>
          </div>
          <div class="komp-grid">
            <Link v-for="(k, i) in competencies" :key="k.id" :href="`/kompetensi/${k.slug}`"
              class="komp-card reveal" :class="{ 'is-blue': i === 1 }">
              <div class="komp-top">
                <span class="komp-icon">
                  <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" v-html="icon(k.icon)" />
                </span>
                <span class="komp-num">{{ kompNum(i) }}</span>
              </div>
              <h3>{{ k.title }}</h3>
              <p>{{ k.short_description }}</p>
            </Link>
          </div>
        </div>
      </section>

      <!-- ================= ALUR ================= -->
      <section id="alur" class="section grid-ice">
        <div class="container">
          <div class="panel grid-blue">
            <span class="mono-label on-blue">ALUR BELAJAR</span>
            <h2>Tiga tahun, dari dasar hingga siap terjun ke bengkel.</h2>
            <div class="alur-grid">
              <article v-for="st in alur" :key="st.label" class="alur-card reveal" :class="{ 'is-white': st.dark }">
                <div class="alur-top">
                  <span class="alur-step">{{ st.label }}</span>
                  <span class="alur-rule" aria-hidden="true"></span>
                </div>
                <h3>{{ st.title }}</h3>
                <p>{{ st.desc }}</p>
              </article>
            </div>
          </div>
        </div>
      </section>

      <!-- ================= PROGRAM UNGGULAN ================= -->
      <section id="program" class="section">
        <div class="container">
          <div class="section-head reveal">
            <div>
              <span class="mono-label">PROGRAM UNGGULAN</span>
              <h2>Lebih dari sekadar belajar di kelas.</h2>
            </div>
            <Link href="/program" class="btn btn-outline btn-sm">Semua program →</Link>
          </div>
          <div class="prog-grid">
            <article v-for="p in programs" :key="p.id" class="prog-card reveal">
              <div class="prog-img">
                <img v-if="p.image" :src="img(p.image)" :alt="p.title" loading="lazy" @error="hideImg" />
                <span class="prog-icon">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" v-html="icon(p.icon)" />
                </span>
              </div>
              <div class="prog-body">
                <span v-if="p.subtitle" class="mono-label">{{ p.subtitle }}</span>
                <h3>{{ p.title }}</h3>
                <p>{{ p.description }}</p>
              </div>
            </article>
          </div>
        </div>
      </section>

      <!-- ================= SARANA ================= -->
      <section id="sarana" class="section section-tint">
        <div class="container">
          <div class="section-head reveal">
            <div>
              <span class="mono-label">SARANA &amp; PRASARANA</span>
              <h2>Bengkel praktik berstandar industri.</h2>
            </div>
            <div class="chips">
              <Link href="/sarana" class="chip">Lihat halaman sarana →</Link>
              <button v-for="c in facCats" :key="c" type="button" class="chip" :class="{ on: facCat === c }" @click="facCat = c">{{ c }}</button>
            </div>
          </div>
          <div class="fac-grid">
            <article v-for="(f, i) in facList" :key="f.id" class="fac-card reveal" :class="{ big: i === 0 }">
              <img v-if="f.image" :src="img(f.image)" :alt="f.name" loading="lazy" @error="hideImg" />
              <div class="fac-info">
                <span class="mono-label accent">{{ f.category }}</span>
                <h3>{{ f.name }}</h3>
                <p v-if="f.quantity" class="fac-meta">{{ f.quantity }} unit<span v-if="f.condition"> · {{ f.condition }}</span></p>
              </div>
            </article>
          </div>
        </div>
      </section>

      <!-- ================= PRESTASI ================= -->
      <section id="prestasi" class="section grid-blue section-dark">
        <div class="container">
          <div class="section-head reveal">
            <div>
              <span class="mono-label accent">PRESTASI</span>
              <h2>Bukti kompetensi di ajang lomba.</h2>
            </div>
            <Link href="/prestasi" class="btn btn-ghost btn-sm">Semua {{ stats.achievements }} prestasi →</Link>
          </div>
          <div class="ach-grid">
            <article v-for="a in achievements" :key="a.id" class="ach-card reveal">
              <div class="ach-img">
                <img v-if="a.image" :src="img(a.image)" :alt="a.title" loading="lazy" @error="hideImg" />
                <span v-if="a.rank" class="ach-rank">{{ a.rank }}</span>
              </div>
              <div class="ach-body">
                <span class="ach-level">{{ a.level || 'Umum' }}<template v-if="a.year"> · {{ a.year }}</template></span>
                <h3>{{ a.title }}</h3>
                <p>{{ a.student_name }}<span v-if="a.organizer"> — {{ a.organizer }}</span></p>
              </div>
            </article>
          </div>
        </div>
      </section>

      <!-- ================= BERITA TERKINI ================= -->
      <section id="berita" class="section">
        <div class="container">
          <div class="section-head reveal">
            <div>
              <span class="mono-label">BERITA TERKINI</span>
              <h2>Kabar dari jurusan.</h2>
            </div>
            <Link href="/berita" class="btn btn-outline btn-sm">Semua berita →</Link>
          </div>
          <div class="chips chips-row reveal" aria-label="Kategori berita">
            <button type="button" class="chip" :class="{ on: postCat === 'semua' }" @click="postCat = 'semua'">Semua</button>
            <button v-for="c in categories" :key="c.id" type="button" class="chip" :class="{ on: postCat === c.slug }" @click="postCat = c.slug">
              <i :style="{ background: c.color }"></i>{{ c.name }} <small>{{ c.posts_count }}</small>
            </button>
          </div>

          <div v-if="featuredPost" class="news-wrap">
            <Link :href="`/berita/${featuredPost.slug}`" class="news-feature reveal">
              <img v-if="featuredPost.cover_image" :src="img(featuredPost.cover_image)" :alt="featuredPost.cover_alt || featuredPost.title" loading="lazy" @error="hideImg" />
              <div class="news-feature-body">
                <span class="cat-tag" :style="{ background: featuredPost.category?.color }">{{ featuredPost.category?.name }}</span>
                <h3>{{ featuredPost.title }}</h3>
                <p>{{ featuredPost.excerpt }}</p>
                <small>{{ fmtDate(featuredPost.published_at) }}<span v-if="featuredPost.reading_time"> · {{ featuredPost.reading_time }} menit baca</span></small>
              </div>
            </Link>
            <div class="news-list">
              <Link v-for="p in otherPosts" :key="p.id" :href="`/berita/${p.slug}`" class="news-item reveal">
                <img v-if="p.cover_image" :src="img(p.cover_image)" :alt="p.cover_alt || p.title" loading="lazy" @error="hideImg" />
                <div>
                  <span class="cat-text" :style="{ color: p.category?.color }">{{ p.category?.name }}</span>
                  <h4>{{ p.title }}</h4>
                  <small>{{ fmtDate(p.published_at) }}</small>
                </div>
              </Link>
            </div>
          </div>
          <p v-else class="empty">Belum ada tulisan di kategori ini.</p>
        </div>
      </section>

      <!-- ================= GALERI ================= -->
      <section v-if="gallery.length" id="galeri" class="section section-tint">
        <div class="container">
          <div class="section-head reveal">
            <div>
              <span class="mono-label">GALERI</span>
              <h2>Suasana belajar di TKR.</h2>
            </div>
          </div>
          <div class="gal-grid">
            <button v-for="g in gallery" :key="g.id" type="button" class="gal-item reveal" @click="lightbox = g">
              <img :src="img(g.file)" :alt="g.title || g.caption || 'Galeri'" loading="lazy" @error="hideImg" />
              <span>{{ g.album || g.title }}</span>
            </button>
          </div>
        </div>
      </section>

      <!-- ================= GURU ================= -->
      <section v-if="teachers.length" id="guru" class="section">
        <div class="container">
          <div class="section-head reveal">
            <div>
              <span class="mono-label">GURU &amp; INSTRUKTUR</span>
              <h2>Dibimbing praktisi berpengalaman.</h2>
            </div>
            <Link href="/guru" class="btn btn-outline btn-sm">Semua guru →</Link>
          </div>
          <div class="teach-grid">
            <article v-for="t in teachers" :key="t.id" class="teach-card reveal">
              <img v-if="t.photo" :src="img(t.photo)" :alt="t.name" loading="lazy" @error="hideImg" />
              <h3>{{ t.name }}</h3>
              <span class="mono-label">{{ t.position }}</span>
              <p>{{ t.subject }}</p>
            </article>
          </div>
        </div>
      </section>

      <!-- ================= KARIER ================= -->
      <section id="karier" class="section section-tint">
        <div class="container karier-layout">
          <div class="karier-copy reveal">
            <span class="mono-label">PROSPEK KARIER</span>
            <h2>Lulus, lalu ke mana?</h2>
            <p>Keahlian otomotif kendaraan ringan dibutuhkan di banyak jalur, baik bekerja, membuka usaha, maupun melanjutkan studi.</p>
          </div>
          <div class="karier-grid">
            <article v-for="c in karier" :key="c.no" class="karier-card reveal">
              <span class="karier-no">{{ c.no }}</span>
              <div><h3>{{ c.title }}</h3><p>{{ c.desc }}</p></div>
            </article>
          </div>
        </div>
      </section>

      <!-- ================= UMPAN BALIK LULUSAN ================= -->
      <section id="alumni" class="section">
        <div class="container">
          <div class="section-head reveal">
            <div>
              <span class="mono-label">UMPAN BALIK LULUSAN</span>
              <h2>Kata alumni tentang TKR.</h2>
            </div>
            <div class="head-actions">
              <button type="button" class="btn btn-outline btn-sm" @click="showTesti = !showTesti">{{ showTesti ? 'Tutup formulir' : 'Kirim testimoni' }}</button>
              <Link href="/alumni" class="btn btn-primary btn-sm">Semua testimoni →</Link>
            </div>
          </div>

          <p v-if="flash.success && !showTesti" class="form-ok" role="status">{{ flash.success }}</p>
          <form v-if="showTesti" class="lp-form testi-form" @submit.prevent="submitTesti">
            <input v-model="testiForm.website" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true" class="hidden" />
            <div class="form-grid">
              <label>Nama lengkap<input v-model="testiForm.name" required maxlength="150" /></label>
              <label>Tahun lulus<input v-model="testiForm.graduation_year" type="number" min="2000" :max="new Date().getFullYear()" required /></label>
              <label>Pekerjaan<input v-model="testiForm.occupation" maxlength="150" /></label>
              <label>Tempat kerja / kuliah<input v-model="testiForm.workplace" maxlength="150" /></label>
              <label class="full">Pesan &amp; kesan<textarea v-model="testiForm.message" rows="3" required maxlength="1000"></textarea></label>
              <div class="full rating-pick">
                <span>Penilaian:</span>
                <button v-for="n in 5" :key="n" type="button" :class="{ on: n <= testiForm.rating }" :aria-label="`${n} bintang`" @click="testiForm.rating = n">★</button>
              </div>
            </div>
            <p v-for="(e, k) in testiForm.errors" :key="k" class="form-err">{{ e }}</p>
            <button type="submit" class="btn btn-primary btn-sm" :disabled="testiForm.processing">Kirim testimoni</button>
          </form>

          <div class="testi-grid">
            <figure v-for="t in testimonials" :key="t.id" class="testi-card reveal">
              <div class="stars" :aria-label="`${t.rating} dari 5 bintang`">
                <span v-for="n in 5" :key="n" :class="{ on: n <= t.rating }">★</span>
              </div>
              <blockquote>“{{ t.message }}”</blockquote>
              <figcaption>
                <img v-if="t.photo" :src="img(t.photo)" :alt="t.name" loading="lazy" @error="hideImg" />
                <span v-else class="avatar">{{ t.name?.[0] }}</span>
                <span><b>{{ t.name }}</b><small>Lulusan {{ t.graduation_year }}<template v-if="t.occupation"> · {{ t.occupation }}</template><template v-if="t.workplace">, {{ t.workplace }}</template></small></span>
              </figcaption>
            </figure>
          </div>
        </div>
      </section>

      <!-- ================= FAQ ================= -->
      <section id="faq" class="section section-tint">
        <div class="container faq-wrap">
          <div class="faq-intro reveal">
            <span class="mono-label">FAQ</span>
            <h2>Pertanyaan yang sering diajukan.</h2>
          </div>
          <div class="faq-list reveal">
            <div v-for="(f, i) in faqs" :key="i" class="faq-item" :class="{ open: openFaq === i }">
              <button type="button" :aria-expanded="openFaq === i" @click="openFaq = openFaq === i ? -1 : i">
                {{ f.q }}<span aria-hidden="true">{{ openFaq === i ? '−' : '+' }}</span>
              </button>
              <p v-show="openFaq === i">{{ f.a }}</p>
            </div>
          </div>
        </div>
      </section>

      <!-- ================= CTA / SPMB ================= -->
      <section id="spmb" class="section section-cta">
        <div class="container">
          <div class="panel grid-blue cta-panel reveal">
            <div class="cta-copy">
              <span class="mono-label on-blue">SPMB — SISTEM PENERIMAAN MURID BARU</span>
              <h2>Siap jadi <span>teknisi kendaraan ringan?</span></h2>
              <p>{{ s.spmb_open === '1' ? (s.spmb_info || 'Pendaftaran SPMB sudah dibuka.') : 'Pendaftaran SPMB belum dibuka. Pantau terus pengumuman terbaru dari kami.' }} Daftar online sekarang atau tanya panitia lewat WhatsApp.</p>
              <div class="cta-actions">
                <a v-if="spmbUrl" :href="spmbUrl" target="_blank" rel="noopener" class="btn btn-white">Daftar SPMB</a>
                <a v-else href="#kontak" class="btn btn-white">Tanya panitia SPMB</a>
                <a v-if="waNumber" :href="waLink" class="btn btn-ghost" target="_blank" rel="noopener">Tanya via WhatsApp</a>
                <a v-else href="#kontak" class="btn btn-ghost">Kirim pesan</a>
              </div>
            </div>

            <form class="cta-form" novalidate @submit.prevent="submitWa">
              <span class="cta-form-label">Daftar cepat lewat WhatsApp</span>
              <label class="sr-only" for="cta-wa">Nomor WhatsApp</label>
              <input id="cta-wa" v-model="waInput" class="cta-input" :class="{ invalid: !!waErr }"
                type="tel" inputmode="tel" autocomplete="tel" placeholder="08xx-xxxx-xxxx" />
              <button type="submit" class="btn btn-white cta-submit" :disabled="waSending">
                {{ waSending ? 'Mengirim…' : 'Daftar Sekarang' }}
              </button>
              <p v-if="waErr" class="cta-error" role="alert">{{ waErr }}</p>
              <p v-if="waOk" class="cta-ok" role="status">{{ waOk }}</p>
            </form>
          </div>
        </div>
      </section>

      <!-- ================= KONTAK ================= -->
      <section id="kontak" class="section">
        <div class="container contact-wrap">
          <div class="contact-info reveal">
            <span class="mono-label">HUBUNGI KAMI</span>
            <h2>Kunjungi atau hubungi kami.</h2>
            <ul>
              <li><b>Alamat</b>{{ address }}</li>
              <li v-if="s.phone"><b>Telepon</b>{{ s.phone }}</li>
              <li v-if="s.email"><b>Email</b><a :href="`mailto:${s.email}`">{{ s.email }}</a></li>
            </ul>
            <div v-if="s.maps_embed" class="map" v-html="s.maps_embed"></div>
          </div>
          <form class="lp-form contact-form reveal" @submit.prevent="submitContact">
            <input v-model="contactForm.website" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true" class="hidden" />
            <div class="form-grid">
              <label>Nama<input v-model="contactForm.name" required maxlength="150" /></label>
              <label>No. WhatsApp<input v-model="contactForm.phone" type="tel" maxlength="20" /></label>
              <label class="full">Email<input v-model="contactForm.email" type="email" maxlength="150" /></label>
              <label class="full">Subjek<input v-model="contactForm.subject" maxlength="200" /></label>
              <label class="full">Pesan<textarea v-model="contactForm.message" rows="5" required maxlength="2000"></textarea></label>
            </div>
            <p v-for="(e, k) in contactForm.errors" :key="k" class="form-err">{{ e }}</p>
            <p v-if="contactForm.wasSuccessful" class="form-ok" role="status">Pesan terkirim. Terima kasih!</p>
            <button type="submit" class="btn btn-primary btn-sm" :disabled="contactForm.processing">Kirim pesan</button>
          </form>
        </div>
      </section>
    </main>

    <!-- ================= FOOTER ================= -->
    <footer class="footer">
      <div class="container">
        <div class="footer-grid">
          <div class="footer-brand">
            <div class="footer-logo">
              <img v-if="logoUrl" :src="logoUrl" alt="" width="30" height="30" @error="hideImg" />
              <svg v-else width="30" height="30" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M4 19l3-7a3 3 0 0 1 2.8-2h12.4a3 3 0 0 1 2.8 2l3 7v5H4z" />
                <path d="M4 19h24" />
                <circle cx="10" cy="24" r="2.5" fill="currentColor" stroke="none" />
                <circle cx="22" cy="24" r="2.5" fill="currentColor" stroke="none" />
              </svg>
              <span>{{ s.site_name || s.department_name || 'Teknik Kendaraan Ringan' }}</span>
            </div>
            <p>{{ schoolName }}<br />{{ address }}</p>
            <div v-if="socials.length" class="socials">
              <a v-for="so in socials" :key="so.key" :href="so.url" target="_blank" rel="noopener">{{ so.label }}</a>
            </div>
          </div>
          <div class="footer-cols">
            <div class="footer-col">
              <h4>JURUSAN</h4>
              <ul>
                <li><a href="#kompetensi">Kompetensi</a></li>
                <li><a href="#program">Program Unggulan</a></li>
                <li><a href="#sarana">Sarana</a></li>
                <li><a href="#prestasi">Prestasi</a></li>
              </ul>
            </div>
            <div class="footer-col">
              <h4>INFORMASI</h4>
              <ul>
                <li><Link href="/berita">Semua Berita</Link></li>
                <li v-for="c in categories.slice(0, 4)" :key="c.id"><Link :href="`/berita?kategori=${c.slug}`">{{ c.name }}</Link></li>
                <li v-if="spmbUrl"><a :href="spmbUrl" target="_blank" rel="noopener">SPMB</a></li>
              </ul>
            </div>
            <div class="footer-col">
              <h4>KONTAK</h4>
              <ul>
                <li v-if="s.phone"><a :href="`tel:${s.phone}`">{{ s.phone }}</a></li>
                <li v-if="s.email"><a :href="`mailto:${s.email}`">{{ s.email }}</a></li>
                <li v-if="!s.phone && !s.email"><a href="#kontak">Formulir kontak</a></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="footer-bottom">{{ s.footer_text || `© ${new Date().getFullYear()} Jurusan Teknik Kendaraan Ringan — ${schoolName}` }}</div>
      </div>
    </footer>

    <!-- Lightbox galeri -->
    <div v-if="lightbox" class="lightbox" role="dialog" aria-modal="true" aria-label="Pratinjau galeri" @click.self="lightbox = null">
      <button type="button" class="lb-close" aria-label="Tutup" @click="lightbox = null">×</button>
      <img :src="img(lightbox.file)" :alt="lightbox.title" />
      <p>{{ lightbox.caption || lightbox.title }}</p>
    </div>

    <!-- Kembali ke atas -->
    <button v-if="showTop" type="button" class="to-top" @click="toTop" aria-label="Kembali ke atas" title="Kembali ke atas">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 19V5" /><path d="m5 12 7-7 7 7" /></svg>
    </button>

    <!-- Tombol WA melayang -->
    <a v-if="waNumber" :href="waLink" class="wa-float" target="_blank" rel="noopener" aria-label="Chat WhatsApp">
      <svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm5.3 14.1c-.2.6-1.3 1.2-1.8 1.2-.5.1-1 .2-3.3-.7-2.8-1.1-4.5-4-4.7-4.2-.1-.2-1.1-1.5-1.1-2.9s.7-2.1 1-2.4c.3-.3.6-.3.8-.3h.6c.2 0 .4 0 .6.5l.9 2.1c.1.2.1.4 0 .5l-.4.6-.4.4c-.1.1-.3.3-.1.6.2.3.8 1.3 1.7 2.1 1.2 1 2.1 1.3 2.4 1.5.3.1.5.1.6-.1l.9-1c.2-.3.4-.2.6-.1l2 .9c.3.1.5.2.5.3.1.2.1.7-.2 1.4z" /></svg>
    </a>
  </div>
</template>
