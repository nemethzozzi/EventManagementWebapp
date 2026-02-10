<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h3>{{ $t('reset_password_page.title') }}</h3>
          </div>
          <div class="card-body">
            <form @submit.prevent="handleResetPassword">
              <div class="mb-3">
                <label for="email" class="form-label">{{ $t('reset_password_page.email') }}</label>
                <input
                  type="email"
                  class="form-control"
                  id="email"
                  v-model="email"
                  required
                />
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">{{ $t('reset_password_page.password') }}</label>
                <input
                  type="password"
                  class="form-control"
                  id="password"
                  v-model="password"
                  required
                  minlength="8"
                />
              </div>

              <div class="mb-3">
                <label for="password_confirmation" class="form-label">{{ $t('reset_password_page.password_confirmation') }}</label>
                <input
                  type="password"
                  class="form-control"
                  id="password_confirmation"
                  v-model="passwordConfirmation"
                  required
                />
              </div>
              <!-- TODO erre egy komponenst létrehozni loading prop-al --->
              <button type="submit" class="btn btn-primary w-100" :disabled="loading">
                {{ loading ? $t('reset_password_page.change_password') : $t('reset_password_page.change_password') }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import apiClient from '../api/axios'
import {ROUTES} from "../routes/routes.ts";
import {notify} from "../lib/notifyBus.ts";

const route = useRoute()
const router = useRouter()

/**
 * Token kiolvasása az url-ből
 */
const token = ref(route.params.token as string)
/**
 * Email
 */
const email = ref('')
/**
 * Új jelszó
 */
const password = ref('')

/**
 * Új jelszó megerősítése
 */
const passwordConfirmation = ref('')

/**
 * Betöltés
 */
const loading = ref(false)

/**
 * Új jelszó beállítása
 */
const handleResetPassword = async () => {
  loading.value = true

  try {
    const response = await apiClient.post('/password/reset', {
      token: token.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    })
    notify.success(response.data?.message_key)
    setTimeout(() => {
      router.push(ROUTES.LOGIN)
    }, 2000)
  } catch (error: any) {
    console.error(error)
    notify.error(error.response?.data?.error_key)
  } finally {
    loading.value = false
  }
}
</script>