<template>
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="m-0">{{ $t('admin_users_page.users_management') }}</h2>

      <Button
        severity="secondary"
        icon="pi pi-arrow-left"
        :label="$t('admin_users_page.button.back')"
        @click="goBack"
        :outlined="true"
      />
    </div>

    <AdminUserCreateForm :creating="creating" @submit="createUser" class="mb-4" />

    <AdminUsersTable
      :users="users"
      :currentUserId="currentUserId"
      :loading="loading"
      @role-change="({ userId, role }) => updateUserRole(userId, role)"
      @delete="deleteUser"
    />
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import apiClient from '../api/axios'
import { useAuth } from '../composables/useAuth'
import type { User } from '../types/User'
import { notify } from '../lib/notifyBus'
import { ROUTES } from '../routes/routes'

import Button from 'primevue/button'
import AdminUserCreateForm from '../components/AdminUserCreateForm.vue'
import AdminUsersTable from '../components/AdminUsersTable.vue'
import { confirm } from '../lib/confirmBus'
import { t } from '../lib/i18n'

const router = useRouter()
const { user: currentUser } = useAuth()

/**
 * Felhasználók
 */
const users = ref<User[]>([])

/**
 * Létrehozás alatt van-e
 */
const creating = ref(false)

/**
 * Betöltés
 */
const loading = ref(false)

/**
 * Jelenlegi felhasználó azonosító
 */
const currentUserId = computed(() => currentUser.value?.id ?? null)

/**
 * Visszanavigálás az események oldalra
 */
const goBack = () => router.push(ROUTES.EVENTS)

/**
 * Felhasználók betöltése
 */
const loadUsers = async () => {
  loading.value = true
  try {
    const response = await apiClient.get('/admin/users')
    users.value = response.data
  } catch (error: any) {
    console.error(error)
    notify.error(error.response.data.error_key)
  } finally {
    loading.value = false
  }
}

/**
 * Felhasználó létrehozása
 *
 * @param payload
 */
const createUser = async (payload: {
  name: string
  email: string
  password: string
  role: string
}) => {
  creating.value = true
  try {
    const response = await apiClient.post('/admin/users', payload)
    await loadUsers()
    notify.success(response.data.message_key)
  } catch (error: any) {
    console.error(error)
    notify.error(error.response.data.error_key)
  } finally {
    creating.value = false
  }
}

/**
 * Szerepkör frissítése
 *
 * @param userId
 * @param role
 */
const updateUserRole = async (userId: number, role: string) => {
  try {
    const response = await apiClient.put(`/admin/users/${userId}/role`, { role })
    await loadUsers()
    notify.success(response.data.message_key)
  } catch (error: any) {
    console.error(error)
    notify.error(error.response.data.error_key)
  }
}

/**
 * Felhasználó törlése
 *
 * @param userId
 */
const deleteUser = async (userId: number) => {
  const ok = await confirm.open({
    title: t('admin_users_page.popup.title'),
    message: t('admin_users_page.popup.message'),
    severity: 'danger',
    confirmLabel: t('admin_users_page.popup.delete'),
    cancelLabel: t('admin_users_page.popup.cancel'),
  })

  if (!ok) return

  try {
    const response = await apiClient.delete(`/admin/users/${userId}`)
    await loadUsers()
    notify.success(response.data.message_key)
  } catch (error: any) {
    console.error(error)
    notify.error(error.response.data.error_key)
  }
}

onMounted(loadUsers)
</script>
