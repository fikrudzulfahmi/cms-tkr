<script setup>
import { ref, computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps(['account'])
const page = usePage()

const avatarPreview = ref(props.account?.avatar ? `/storage/${props.account.avatar}` : null)

const profile = useForm({
  name: props.account?.name || '',
  email: props.account?.email || '',
  position: props.account?.position || '',
  bio: props.account?.bio || '',
  avatar: null,
})

const onAvatar = (ev) => {
  const file = ev.target.files?.[0]
  if (!file) return
  profile.avatar = file
  avatarPreview.value = URL.createObjectURL(file)
}

const saveProfile = () => {
  profile.post('/admin/akun', { forceFormData: true, preserveScroll: true })
}

const password = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

const savePassword = () => {
  password.put('/admin/akun/password', {
    preserveScroll: true,
    onSuccess: () => password.reset(),
  })
}

const roleLabel = computed(() => props.account?.role_label || page.props.auth?.user?.roleLabel || '')
</script>

<template>
  <AdminLayout>
    <title>Akun Saya - Admin TKR</title>
    <div class="space-y-6 max-w-3xl">
      <div>
        <h1 class="font-display text-[28px] font-bold text-navy tracking-[-0.5px]">
          <span class="block font-mono text-[11px] font-normal tracking-[2px] uppercase text-label mb-1">Panel Kendali · TKR</span>
          Akun Saya
        </h1>
        <p class="text-slate-500 text-sm">
          Profil ini tampil di halaman berita sebagai penulis.
          <span v-if="roleLabel" class="badge text-[10px] ml-1 bg-ice text-navy border border-line">{{ roleLabel }}</span>
        </p>
      </div>

      <!-- Profil -->
      <div class="card p-6">
        <h2 class="font-display text-lg font-bold text-navy mb-4">Profil</h2>
        <form @submit.prevent="saveProfile" class="space-y-4">
          <div class="flex items-center gap-4">
            <img v-if="avatarPreview" :src="avatarPreview" alt="Avatar" class="w-20 h-20 rounded-full object-cover border border-line" />
            <div v-else class="w-20 h-20 rounded-full bg-ice text-navy flex items-center justify-center text-2xl font-bold">
              {{ profile.name?.[0] || '?' }}
            </div>
            <div>
              <label class="text-sm font-semibold text-navy cursor-pointer">
                Ganti foto
                <input type="file" accept="image/png,image/jpeg,image/webp" class="hidden" @change="onAvatar" />
              </label>
              <p class="text-xs text-slate-500 mt-1">JPG/PNG/WebP, maks. 2 MB. Tampil sebagai avatar penulis.</p>
              <p v-if="profile.errors.avatar" class="text-red-500 text-xs mt-1">{{ profile.errors.avatar }}</p>
            </div>
          </div>

          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="form-label">Nama Lengkap *</label>
              <input v-model="profile.name" type="text" class="form-input" required />
              <p v-if="profile.errors.name" class="text-red-500 text-xs mt-1">{{ profile.errors.name }}</p>
            </div>
            <div>
              <label class="form-label">Email *</label>
              <input v-model="profile.email" type="email" class="form-input" required />
              <p v-if="profile.errors.email" class="text-red-500 text-xs mt-1">{{ profile.errors.email }}</p>
            </div>
          </div>

          <div>
            <label class="form-label">Jabatan</label>
            <input v-model="profile.position" type="text" class="form-input" placeholder="Mis. Guru Produktif TKR" />
            <p v-if="profile.errors.position" class="text-red-500 text-xs mt-1">{{ profile.errors.position }}</p>
          </div>

          <div>
            <label class="form-label">Bio Singkat</label>
            <textarea v-model="profile.bio" rows="3" class="form-input text-sm" placeholder="Tampil singkat di bawah artikel." />
            <p v-if="profile.errors.bio" class="text-red-500 text-xs mt-1">{{ profile.errors.bio }}</p>
          </div>

          <div class="flex justify-end">
            <button type="submit" :disabled="profile.processing" class="btn-primary text-sm disabled:opacity-60">
              {{ profile.processing ? 'Menyimpan...' : 'Simpan Profil' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Password -->
      <div class="card p-6">
        <h2 class="font-display text-lg font-bold text-navy mb-1">Ubah Password</h2>
        <p class="text-xs text-slate-500 mb-4">Masukkan password lama untuk konfirmasi.</p>
        <form @submit.prevent="savePassword" class="space-y-4">
          <div>
            <label class="form-label">Password Saat Ini *</label>
            <input v-model="password.current_password" type="password" class="form-input" autocomplete="current-password" required />
            <p v-if="password.errors.current_password" class="text-red-500 text-xs mt-1">{{ password.errors.current_password }}</p>
          </div>
          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="form-label">Password Baru *</label>
              <input v-model="password.password" type="password" class="form-input" autocomplete="new-password" required />
              <p v-if="password.errors.password" class="text-red-500 text-xs mt-1">{{ password.errors.password }}</p>
            </div>
            <div>
              <label class="form-label">Ulangi Password Baru *</label>
              <input v-model="password.password_confirmation" type="password" class="form-input" autocomplete="new-password" required />
            </div>
          </div>
          <div class="flex justify-end">
            <button type="submit" :disabled="password.processing" class="btn-primary text-sm disabled:opacity-60">
              {{ password.processing ? 'Menyimpan...' : 'Ubah Password' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
