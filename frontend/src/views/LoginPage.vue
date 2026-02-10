<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h3>{{ $t('login_page.title') }}</h3>
          </div>
          <div class="card-body">
            <form @submit.prevent="handleLogin">
              <div class="mb-3">
                <label for="email" class="form-label">{{ $t('login_page.email') }}</label>
                <input
                  type="email"
                  class="form-control"
                  id="email"
                  v-model="email"
                  required
                />
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">{{ $t('login_page.password') }}</label>
                <input
                  type="password"
                  class="form-control"
                  id="password"
                  v-model="password"
                  required
                />
              </div>
              <button type="submit" class="btn btn-primary w-100" :disabled="loading">
                {{ loading ? $t('login_page.login') + '...' : $t('login_page.login') }}
              </button>
            </form>

            <div class="mt-3 text-center">
              <router-link :to=ROUTES.FORGOT_PASSWORD>{{ $t('login_page.forgot_password') }}</router-link>
            </div>
          </div>
        </div>

        <div class="mt-3 alert alert-info">
          <strong>{{ $t('login_page.demo_accounts') }}:</strong><br>
          Admin: admin@ucc.local / password123<br>
          Agent: agent@ucc.local / password123<br>
          User: user@ucc.local / password123
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../composables/useAuth'
import {ROUTES} from "../routes/routes.ts";
import {notify} from "../lib/notifyBus.ts";

const router = useRouter()
const { login } = useAuth()

/**
 * Email
 */
const email = ref('')

/**
 * Jelszó
 */
const password = ref('')

/**
 * Betöltés
 */
const loading = ref(false)

/**
 * Bejelentkezés
 */
const handleLogin = async () => {
  loading.value = true

  try {
    await login(email.value, password.value)
    await router.push(ROUTES.EVENTS)
  } catch (error: any) {
    notify.error(error.response?.data?.error_key)
  } finally {
    loading.value = false
  }
}
</script>