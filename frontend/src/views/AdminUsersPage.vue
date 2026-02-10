<template>
  <div class="container mt-4">
    <div class="d-flex justify-content-between mb-3">
      <h2>{{ $t('admin_users_page.users_management') }}</h2>
      <router-link :to=ROUTES.EVENTS class="btn btn-secondary">{{ $t('admin_users_page.button.back') }}</router-link>
    </div>

    <!-- Új felhasználó létrehozása -->
    <div class="card mb-4">
      <div class="card-header">
        <h5>{{ $t('admin_users_page.new_user') }}</h5>
      </div>
      <div class="card-body">
        <form @submit.prevent="createUser">
          <div class="row">
            <div class="col-md-3">
              <input
                type="text"
                class="form-control"
                :placeholder="$t('admin_users_page.name')"
                v-model="newUser.name"
                required
              />
            </div>
            <div class="col-md-3">
              <input
                type="email"
                class="form-control"
                :placeholder="$t('admin_users_page.email')"
                v-model="newUser.email"
                required
              />
            </div>
            <div class="col-md-2">
              <input
                type="password"
                class="form-control"
                :placeholder="$t('admin_users_page.password')"
                v-model="newUser.password"
                required
                minlength="8"
              />
            </div>
            <div class="col-md-2">
              <select class="form-select" v-model="newUser.role" required>
                <option :value=ROLE.USER>User</option>
                <option :value=ROLE.HELPDESK_AGENT>Helpdesk Agent</option>
                <option :value=ROLE.ADMIN>Admin</option>
              </select>
            </div>
            <div class="col-md-2">
              <button type="submit" class="btn btn-primary w-100" :disabled="creating">
                {{ creating ? $t('admin_users_page.creating') : $t('admin_users_page.create') }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Felhasználók listája -->
    <div class="card">
      <div class="card-header">
        <h5>{{ $t('admin_users_page.table.title') }}</h5>
      </div>
      <div class="card-body">
        <!-- TODO table komponenst létrehozni -->
        <table class="table table-striped">
          <thead>
            <tr>
              <th>{{ $t('admin_users_page.table.name') }}</th>
              <th>{{ $t('admin_users_page.table.email') }}</th>
              <th>{{ $t('admin_users_page.table.role') }}</th>
              <th>{{ $t('admin_users_page.table.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.id">
              <td>{{ user.name }}</td>
              <td>{{ user.email }}</td>
              <td>
                <select
                  class="form-select form-select-sm"
                  :value="user.role"
                  @change="updateUserRole(user.id, ($event.target as HTMLSelectElement).value)"
                >
                  <!-- TODO nyelvi kulcs -->
                  <option :value=ROLE.USER>User</option>
                  <option :value=ROLE.HELPDESK_AGENT>Helpdesk Agent</option>
                  <option :value=ROLE.ADMIN>Admin</option>
                </select>
              </td>
              <td>
                <button
                  @click="deleteUser(user.id)"
                  class="btn btn-danger btn-sm"
                  :disabled="user.id === currentUserId"
                >
                  {{ $t('admin_users_page.button.delete') }}
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import apiClient from '../api/axios'
import { useAuth } from '../composables/useAuth'
import type {User} from "../types/User.ts";
import { t } from '../lib/i18n'
import { notify } from '../lib/notifyBus'
import {ROUTES} from "../routes/routes.ts";
import {ROLE} from "../types/Role.ts";

/**
 * Jelenlegi felhasználó
 */
const { user: currentUser } = useAuth()

/**
 * Felhasználók
 */
const users = ref<User[]>([])
const creating = ref(false)

/**
 * Új felhasználó
 */
const newUser = ref({
  name: '',
  email: '',
  password: '',
  role: 'user' as 'user' | 'helpdesk_agent' | 'admin',
})

/**
 * Jelenlegi felhasználó azonosítója
 */
const currentUserId = computed(() => currentUser.value?.id)

/**
 * Felhasználók betöltése
 */
const loadUsers = async () => {
  try {
    const response = await apiClient.get('/admin/users')
    users.value = response.data
  } catch (error) {
    console.error(error)
  }
}

/**
 * Felhasználó létrehozása
 */
const createUser = async () => {
  creating.value = true
  try {
    await apiClient.post('/admin/users', newUser.value)
    
    // Reset form
    newUser.value = {
      name: '',
      email: '',
      password: '',
      role: 'user',
    }
    
    await loadUsers()
    notify.success(response.data.message_key)
    notify.success('admin.user_created') // TODO backendről jöjjön
  } catch (error: any) {
    console.error(error)
    const errKey = error.response?.data?.error_key || error.response?.data?.error || 'errors.generic'
    notify.error(errKey)
  } finally {
    creating.value = false
  }
}

/**
 * Felhasználó role-jának frissítése
 *
 * @param userId
 * @param newRole
 */
const updateUserRole = async (userId: number, newRole: string) => {
  try {
    await apiClient.put(`/admin/users/${userId}/role`, { role: newRole })
    await loadUsers()
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
  // TODO ide is popup
  try {
    await apiClient.delete(`/admin/users/${userId}`)
    await loadUsers()
  } catch (error: any) {
    console.error(error)
    notify.error(error.response.data.error_key)
  }
}

onMounted(() => {
  loadUsers()
})
</script>