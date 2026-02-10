<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h3>{{ $t('forgot_password_page.title') }}</h3>
          </div>
          <div class="card-body">
            <form @submit.prevent="handleForgotPassword">
              <div class="mb-3">
                <label for="email" class="form-label">{{ $t('forgot_password_page.email') }}</label>
                <input
                  type="email"
                  class="form-control"
                  id="email"
                  v-model="email"
                  required
                />
              </div>
              <button type="submit" class="btn btn-primary w-100" :disabled="loading">
                {{ loading ? $t('forgot_password_page.send_email') : $t('forgot_password_page.send_email') }}
              </button>
            </form>

            <div class="mt-3 text-center">
              <router-link :to=ROUTES.LOGIN>{{ $t('forgot_password_page.login') }}</router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import apiClient from '../api/axios'
import {ROUTES} from "../routes/routes.ts";
import {notify} from "../lib/notifyBus.ts";

/**
 * Email
 */
const email = ref('')
const loading = ref(false)

/**
 * Elfelejtett jelszó email küldése
 */
const handleForgotPassword = async () => {
  loading.value = true

  try {
    const response = await apiClient.post('/password/forgot', { email: email.value })
    if (response.data?.message_key) {
      notify.success(response.data.message_key)
    }
    email.value = ''
  } catch (error: any) {
    console.error(error)
    notify.error(error.response?.data?.error_key)
  } finally {
    loading.value = false
  }
}
</script>