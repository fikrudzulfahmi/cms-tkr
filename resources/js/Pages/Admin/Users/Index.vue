<script setup>
import { ref, computed, watch } from 'vue'
import { router, Link, useForm, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps(['users', 'filters', 'roles', 'currentUserId'])

const page = usePage()
const q = ref(props.filters?.q || '')
const role = ref(props.filters?.role || '')

watch(() => props.filters, (f) => {
  q.value = f?.q || ''
  role.value = f?.role || ''
}, { deep: true })

const applyFilter = () => {
  router.get('/admin/pengguna', {
    q: q.value || undefined,
    role: role.value || undefined,
  }, { preserveState: true, replace: true })
}

/* ---------- Modal tambah / edit ---------- */
const modalOpen = ref(false)
const editing = ref(null)          // null = tambah baru
const showPassword = ref(false)

const form = useForm({
  name: '',
  email: '',
  role: 'author',
  position: '',
  bio: '',
  password: '',
  is_active: true,
})

const title = computed(() => editing.value ? 'Edit Akun' : 'Tambah Akun Baru')

const openCreate = () => {
  editing.value = null
  form.reset()
  form.clearErrors()
  form.role = 'author'
  form.is_active = true
  form.password = ''
  showPassword.value = false
  modalOpen.value = true
}

const openEdit = (u) => {
  editing.value = u
  form.clearErrors()
  form.name = u.name || ''
  form.email = u.email || ''
  form.role = u.role || 'author'
  form.position = u.position || ''
  form.bio = u.bio || ''
  form.password = ''
  form.is_active = !!u.is_active
  showPassword.value = false
  modalOpen.value = true
}

const closeModal = () => { modalOpen.value = false }

const submit = () => {
  if (editing.value) {
    form.put(`/admin/pengguna/${editing.value.id}`, {
      preserveScroll: true,
      onSuccess: () => closeModal(),
    })
  } else {
    form.post('/admin/pengguna', {
      preserveScroll: true,
      onSuccess: () => closeModal(),
    })
  }
}

/* ---------- Password generator ---------- */
const generatePassword = () => {
  const chars = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789'
  const rnd = new Uint32Array(12)
  crypto.getRandomValues(rnd)
  form.password = Array.from(rnd, n => chars[n % chars.length]).join('')
  showPassword.value = true
  form.is_active = true
}

const copyPassword = async () => {
  if (!form.password) return
  try { await navigator.clipboard.writeText(form.password) } catch (e) {}
}

/* ---------- Aksi lain ---------- */
const toggleUser = (u) => {
  const verb = u.is_active ? 'menonaktifkan' : 'mengaktifkan'
  if (confirm(`Yakin ${verb} akun ${u.name}?`)) {
    router.patch(`/admin/pengguna/${u.id}/aktif`, {}, { preserveScroll: true })
  }
}

const removeUser = (u) => {
  if (confirm(`Hapus akun ${u.name} (${u.email})? Berita miliknya akan dialihkan ke akun Anda.`)) {
    router.delete(`/admin/pengguna/${u.id}`, { preserveScroll: true })
  }
}

const resetTarget = ref(null)
const resetForm = useForm({ password: '' })

const openReset = (u) => {
  resetTarget.value = u
  resetForm.reset()
  resetForm.clearErrors()
}

const submitReset = () => {
  resetForm.put(`/admin/pengguna/${resetTarget.value.id}/password`, {
    preserveScroll: true,
    onSuccess: () => { resetTarget.value = null },
  })
}

const roleBadge = (r) => ({
  admin: 'bg-navy text-accent',
  editor: 'bg-blue-100 text-blue-800 border border-blue-200',
  author: 'bg-amber-100 text-amber-800 border border-amber-200',
}[r] || 'bg-ice text-label border border-line')

const formatDate = (d) => d
  ? new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
  : '—'
</script>

<template>
  <AdminLayout>
    <title>Akun Pengguna - Admin TKR</title>
    <div class="space-y-6">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
          <h1 class="font-display text-[28px] font-bold text-navy tracking-[-0.5px]">
            <span class="block font-mono text-[11px] font-normal tracking-[2px] uppercase text-label mb-1">Panel Kendali · TKR</span>
            Akun Pengguna
          </h1>
          <p class="text-slate-500 text-sm">Kelola akun administrator, editor, dan penulis berita</p>
        </div>
        <button @click="openCreate" class="btn-primary text-sm flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Tambah Akun
        </button>
      </div>

      <div class="card p-4">
        <form @submit.prevent="applyFilter" class="grid sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
          <div class="sm:col-span-2 lg:col-span-7">
            <input v-model="q" type="search" placeholder="Cari nama atau email..." class="form-input text-sm" />
          </div>
          <div class="lg:col-span-3">
            <select v-model="role" @change="applyFilter" class="form-input text-sm">
              <option value="">Semua Role</option>
              <option v-for="r in roles" :key="r.value" :value="r.value">{{ r.label }}</option>
            </select>
          </div>
          <div class="lg:col-span-2 flex items-center gap-2">
            <button type="submit" class="btn-primary text-xs px-4 py-2.5 min-h-0 w-full justify-center">Cari</button>
            <button v-if="q || role" type="button" @click="q=''; role=''; applyFilter()"
              class="text-xs text-slate-500 hover:text-red-600 px-2 py-2">Reset</button>
          </div>
        </form>
      </div>

      <div class="card overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
              <tr>
                <th class="p-4">Nama</th>
                <th class="p-4">Role</th>
                <th class="p-4">Status</th>
                <th class="p-4">Berita</th>
                <th class="p-4">Login Terakhir</th>
                <th class="p-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="u in users.data" :key="u.id" class="hover:bg-slate-50/50">
                <td class="p-4">
                  <div class="flex items-center gap-3">
                    <img v-if="u.avatar" :src="`/storage/${u.avatar}`" alt="" class="w-9 h-9 rounded-full object-cover" />
                    <div v-else class="w-9 h-9 rounded-full bg-ice text-navy flex items-center justify-center text-sm font-bold">{{ u.name?.[0] }}</div>
                    <div class="min-w-0">
                      <div class="font-semibold text-slate-800 truncate">
                        {{ u.name }}
                        <span v-if="u.id === currentUserId" class="text-[10px] font-mono uppercase text-label">(Anda)</span>
                      </div>
                      <div class="text-xs text-slate-500 truncate">{{ u.email }}</div>
                      <div v-if="u.position" class="text-[11px] text-slate-400">{{ u.position }}</div>
                    </div>
                  </div>
                </td>
                <td class="p-4"><span class="badge text-xs" :class="roleBadge(u.role)">{{ u.role_label }}</span></td>
                <td class="p-4">
                  <span class="badge text-xs" :class="u.is_active ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-slate-100 text-slate-500 border border-slate-200'">
                    {{ u.is_active ? 'Aktif' : 'Nonaktif' }}
                  </span>
                </td>
                <td class="p-4 text-slate-600">{{ u.posts_count }}</td>
                <td class="p-4 text-xs text-slate-500">{{ formatDate(u.last_login_at) }}</td>
                <td class="p-4 text-right whitespace-nowrap space-x-2">
                  <button @click="openEdit(u)" class="text-blue-600 hover:underline font-medium">Edit</button>
                  <button @click="openReset(u)" class="text-slate-600 hover:underline font-medium">Password</button>
                  <button v-if="u.id !== currentUserId" @click="toggleUser(u)" class="text-amber-700 hover:underline font-medium">
                    {{ u.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                  </button>
                  <button v-if="u.id !== currentUserId" @click="removeUser(u)" class="text-red-600 hover:underline font-medium">Hapus</button>
                </td>
              </tr>
              <tr v-if="!users.data?.length">
                <td colspan="6" class="p-8 text-center text-slate-400">Belum ada akun</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
          <p class="text-xs text-slate-500">
            Menampilkan <span class="font-semibold text-slate-700">{{ users.data?.length || 0 }}</span> dari
            <span class="font-semibold text-slate-700">{{ users.total || 0 }}</span> akun
          </p>
          <div v-if="users.links && users.links.length > 1" class="flex flex-wrap items-center gap-1">
            <a v-for="(link, idx) in users.links" :key="idx" :href="link.url || undefined"
              @click="!link.url && $event.preventDefault()" v-html="link.label"
              :class="[
                'px-3.5 py-1.5 rounded-full text-xs font-semibold border font-mono transition inline-flex items-center justify-center',
                link.active ? 'bg-navy border-navy text-white'
                  : link.url ? 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50'
                  : 'bg-slate-100 border-slate-200 text-slate-400 cursor-not-allowed pointer-events-none'
              ]" />
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Tambah / Edit -->
    <div v-if="modalOpen" class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-navy/40 backdrop-blur-sm p-4 py-10">
      <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl">
        <div class="p-6 border-b border-slate-100 flex items-start justify-between gap-4">
          <div>
            <h3 class="font-display text-xl font-bold text-navy">{{ title }}</h3>
            <p class="text-xs text-slate-500 mt-1">Akun panel CMS — bukan akun pengunjung website.</p>
          </div>
          <button @click="closeModal" class="text-slate-400 hover:text-slate-700 text-xl leading-none">×</button>
        </div>

        <form @submit.prevent="submit" class="p-6 space-y-4">
          <div>
            <label class="form-label">Nama Lengkap *</label>
            <input v-model="form.name" type="text" class="form-input" placeholder="Mis. Ahmad Fauzi" required />
            <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
          </div>

          <div>
            <label class="form-label">Email *</label>
            <input v-model="form.email" type="email" class="form-input" placeholder="nama@tkr.sch.id" required />
            <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
          </div>

          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="form-label">Role *</label>
              <select v-model="form.role" class="form-input">
                <option v-for="r in roles" :key="r.value" :value="r.value">{{ r.label }}</option>
              </select>
              <p v-if="form.errors.role" class="text-red-500 text-xs mt-1">{{ form.errors.role }}</p>
            </div>
            <div>
              <label class="form-label">Status</label>
              <select v-model="form.is_active" class="form-input">
                <option :value="true">Aktif</option>
                <option :value="false">Nonaktif</option>
              </select>
            </div>
          </div>

          <div>
            <label class="form-label">Jabatan <span class="text-slate-400 font-normal">(tampil di berita)</span></label>
            <input v-model="form.position" type="text" class="form-input" placeholder="Mis. Guru Produktif TKR" />
            <p v-if="form.errors.position" class="text-red-500 text-xs mt-1">{{ form.errors.position }}</p>
          </div>

          <div>
            <label class="form-label">Bio Singkat <span class="text-slate-400 font-normal">(opsional)</span></label>
            <textarea v-model="form.bio" rows="3" class="form-input text-sm" placeholder="Satu-dua kalimat tentang penulis."></textarea>
            <p v-if="form.errors.bio" class="text-red-500 text-xs mt-1">{{ form.errors.bio }}</p>
          </div>

          <div>
            <label class="form-label">
              Password {{ editing ? '(kosongkan bila tidak diubah)' : '*' }}
            </label>
            <div class="flex gap-2">
              <input v-model="form.password" :type="showPassword ? 'text' : 'password'" class="form-input font-mono text-sm"
                :placeholder="editing ? '••••••••' : 'Minimal 8 karakter'" :required="!editing" autocomplete="new-password" />
              <button type="button" @click="generatePassword"
                class="text-xs font-semibold px-3 py-2 rounded-xl border border-line text-slate-600 hover:border-navy hover:text-navy whitespace-nowrap">
                Generate
              </button>
              <button v-if="form.password" type="button" @click="copyPassword"
                class="text-xs font-semibold px-3 py-2 rounded-xl border border-line text-slate-600 hover:border-navy hover:text-navy">
                Salin
              </button>
            </div>
            <p v-if="form.password" class="text-[11px] text-amber-700 mt-1">
              Catat / salin password ini sekarang — tidak ditampilkan lagi setelah disimpan.
            </p>
            <p v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</p>
          </div>

          <div class="flex justify-end gap-2 pt-2">
            <button type="button" @click="closeModal"
              class="text-sm font-semibold px-4 py-2.5 rounded-full border border-line text-slate-600 hover:bg-slate-50">Batal</button>
            <button type="submit" :disabled="form.processing" class="btn-primary text-sm disabled:opacity-60">
              {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Reset Password -->
    <div v-if="resetTarget" class="fixed inset-0 z-50 flex items-center justify-center bg-navy/40 backdrop-blur-sm p-4">
      <div class="bg-white rounded-3xl w-full max-w-sm shadow-2xl p-6">
        <h3 class="font-display text-lg font-bold text-navy">Reset Password</h3>
        <p class="text-xs text-slate-500 mt-1 mb-4">{{ resetTarget.name }} · {{ resetTarget.email }}</p>
        <form @submit.prevent="submitReset" class="space-y-4">
          <div>
            <label class="form-label">Password Baru *</label>
            <input v-model="resetForm.password" type="text" class="form-input font-mono text-sm" placeholder="Minimal 8 karakter" required />
            <p v-if="resetForm.errors.password" class="text-red-500 text-xs mt-1">{{ resetForm.errors.password }}</p>
          </div>
          <div class="flex justify-end gap-2">
            <button type="button" @click="resetTarget = null"
              class="text-sm font-semibold px-4 py-2.5 rounded-full border border-line text-slate-600">Batal</button>
            <button type="submit" :disabled="resetForm.processing" class="btn-primary text-sm disabled:opacity-60">
              {{ resetForm.processing ? 'Menyimpan...' : 'Reset' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
