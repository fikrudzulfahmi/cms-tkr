<script setup>
// Navbar publik tunggal: dipakai landing (/) dan AppLayout (/berita, artikel)
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'

const page = usePage()
const s = computed(() => page.props.settings || {})
const open = ref(false)
const scrolled = ref(false)
// Tombol masuk admin sengaja tidak ditampilkan; admin akses manual lewat /admin.
const path = computed(() => (page.url || '/').split('#')[0].split('?')[0])
const logo = computed(() => {
  const p = s.value.site_logo
  return p ? (/^https?:\/\//.test(p) ? p : `/storage/${p}`) : null
})

const links = [
  { label: 'Beranda', href: '/' },
  { label: 'Profil', href: '/profil', also: ['/guru'] },
  { label: 'Kompetensi', href: '/kompetensi' },
  { label: 'Program', href: '/program' },
  { label: 'Sarana', href: '/sarana' },
  { label: 'Prestasi', href: '/prestasi' },
  { label: 'Berita', href: '/berita' },
  { label: 'Alumni', href: '/alumni' },
  { label: 'Kontak', href: '/#kontak' },
]
// Anchor yang punya section di landing (untuk penanda menu aktif saat menggulir).
const sectionIds = links.filter((l) => l.href.startsWith('/#')).map((l) => l.href.slice(2))
const activeHash = ref('')

function updateActiveHash() {
  if (path.value !== '/') { activeHash.value = ''; return }
  const offset = 130
  let current = ''
  for (const id of sectionIds) {
    const el = document.getElementById(id)
    if (el && el.getBoundingClientRect().top - offset <= 0) current = '#' + id
  }
  // Di dasar halaman, section terakhir yang ada selalu dianggap aktif.
  if (window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 4) {
    for (let i = sectionIds.length - 1; i >= 0; i--) {
      if (document.getElementById(sectionIds[i])) { current = '#' + sectionIds[i]; break }
    }
  }
  activeHash.value = current
}

const isActive = (href) => {
  if (href.startsWith('/#')) return path.value === '/' && activeHash.value === href.slice(1)
  if (href === '/') return path.value === '/' && !activeHash.value
  const l = links.find((x) => x.href === href)
  return [href, ...(l?.also || [])].some((h) => path.value === h || path.value.startsWith(h + '/'))
}

// Di landing, anchor /#x cukup scroll native; dari halaman lain pakai Link (tanpa reload).
const navTag = (href) => (href.startsWith('/#') && path.value === '/' ? 'a' : Link)

function onNavClick(href) {
  open.value = false
  if (href.startsWith('/#')) activeHash.value = href.slice(1)
}

let ticking = false
function onScroll() {
  scrolled.value = window.scrollY > 16
  if (ticking) return
  ticking = true
  requestAnimationFrame(() => { updateActiveHash(); ticking = false })
}

onMounted(() => {
  updateActiveHash()
  scrolled.value = window.scrollY > 16
  window.addEventListener('scroll', onScroll, { passive: true })
  window.addEventListener('resize', onScroll, { passive: true })
})
onBeforeUnmount(() => {
  window.removeEventListener('scroll', onScroll)
  window.removeEventListener('resize', onScroll)
})
// Pindah halaman lewat Inertia (/ -> /berita) harus memindahkan penanda aktif.
watch(path, () => { open.value = false; updateActiveHash() })
</script>

<template>
  <nav aria-label="Navigasi utama" class="site-nav fixed inset-x-0 top-0 z-50 backdrop-blur-md border-b transition-colors duration-150"
    :class="scrolled || open ? 'bg-ice/95 border-line' : 'bg-ice/85 border-transparent'">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4 min-h-[76px]">
      <Link href="/" class="flex items-center gap-3 min-w-0" aria-label="Beranda TKR">
        <img v-if="logo" :src="logo" alt="" class="w-11 h-11 object-contain flex-shrink-0" />
        <span v-else class="w-11 h-11 rounded-xl bg-blue-500 flex items-center justify-center flex-shrink-0" aria-hidden="true">
          <svg width="28" height="28" viewBox="0 0 32 32" fill="none" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 19l3-7a3 3 0 0 1 2.8-2h12.4a3 3 0 0 1 2.8 2l3 7v5H4z" />
            <path d="M4 19h24" />
            <circle cx="10" cy="24" r="2.5" fill="#7FD0FF" stroke="none" />
            <circle cx="22" cy="24" r="2.5" fill="#7FD0FF" stroke="none" />
          </svg>
        </span>
        <span class="flex flex-col leading-tight min-w-0">
          <strong class="font-display font-bold text-navy text-[20px] tracking-[1.5px] truncate">{{ s.site_name || s.department_name || 'TKR' }}</strong>
          <span class="text-xs text-muted truncate">{{ s.school_name || '[NAMA SMK]' }}</span>
        </span>
      </Link>

      <div class="hidden xl:flex items-center gap-6">
        <component :is="navTag(l.href)" v-for="l in links" :key="l.href" :href="l.href" @click="onNavClick(l.href)"
          class="py-1.5 text-[15px] font-medium border-b-2 transition-colors"
          :class="isActive(l.href) ? 'text-blue-500 border-accent' : 'text-navy border-transparent hover:text-blue-500 hover:border-accent'"
          :aria-current="isActive(l.href) ? 'page' : undefined">{{ l.label }}</component>
      </div>

      <div class="flex items-center gap-2.5 flex-shrink-0">
        <component :is="navTag('/#spmb')" href="/#spmb" class="btn-primary hidden md:inline-flex">Daftar Sekarang</component>
        <button type="button" @click="open = !open" :aria-expanded="open" :aria-label="open ? 'Tutup menu' : 'Buka menu'"
          class="xl:hidden w-11 h-11 rounded-xl bg-white border border-blue-200 text-navy flex items-center justify-center">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path v-if="!open" stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16"/>
            <path v-else stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
    </div>

    <Transition enter-active-class="transition duration-200" enter-from-class="opacity-0 -translate-y-1" leave-active-class="transition duration-150" leave-to-class="opacity-0 -translate-y-1">
      <div v-if="open" class="xl:hidden bg-white border-t border-line">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 pt-2 pb-4">
          <component :is="navTag(l.href)" v-for="l in links" :key="l.href" :href="l.href" @click="onNavClick(l.href)"
            class="block px-1 py-3 text-base font-medium border-b border-line-soft"
            :class="isActive(l.href) ? 'text-blue-500' : 'text-navy'"
            :aria-current="isActive(l.href) ? 'page' : undefined">{{ l.label }}</component>
          <component :is="navTag('/#spmb')" href="/#spmb" @click="open = false" class="btn-primary w-full mt-4">Daftar Sekarang</component>
        </div>
      </div>
    </Transition>
  </nav>
</template>
