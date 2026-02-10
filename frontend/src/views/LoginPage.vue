<template>
  <div class="auth-page">
    <Card class="auth-card">
      <template #title>
        <div class="title-row">
          <span>{{ $t('login_page.title') }}</span>
        </div>
      </template>

      <template #content>
        <form class="form mt-4" @submit.prevent="handleLogin">
          <!-- Email -->
          <div class="field">
            <FloatLabel>
              <InputText
                id="email"
                v-model.trim="email"
                class="w-full"
                :invalid="submitted && !isEmailValid"
                autocomplete="email"
              />
              <label for="email">{{ $t('login_page.email') }}</label>
            </FloatLabel>

            <small v-if="submitted && !isEmailValid" class="p-error">
              {{ $t('validation.invalid_email') ?? 'Adj meg egy érvényes email címet.' }}
            </small>
          </div>

          <!-- Password -->
          <div class="field">
            <FloatLabel>
              <Password
                inputId="password"
                v-model="password"
                class="w-full"
                toggleMask
                :feedback="false"
                :invalid="submitted && !password"
                autocomplete="current-password"
              />
              <label for="password">{{ $t('login_page.password') }}</label>
            </FloatLabel>

            <small v-if="submitted && !password" class="p-error">
              {{ $t('validation.required') }}
            </small>
          </div>

          <!-- Submit -->
          <Button
            type="submit"
            class="w-full"
            :label="$t('login_page.login')"
            icon="pi pi-check"
            :loading="loading"
            :disabled="loading"
          />
        </form>

        <div class="links mt-3">
          <router-link :to="ROUTES.FORGOT_PASSWORD">
            {{ $t('login_page.forgot_password') }}
          </router-link>
        </div>

        <Message class="mt-4" severity="info" :closable="false">
          <div>
            <strong>{{ $t('login_page.demo_accounts') }}:</strong><br />
            Admin: admin@ucc.local / password123<br />
            Agent: agent@ucc.local / password123<br />
            User: user@ucc.local / password123
          </div>
        </Message>
      </template>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'

import Card from 'primevue/card'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import FloatLabel from 'primevue/floatlabel'
import Message from 'primevue/message'

import { useAuth } from '../composables/useAuth'
import { ROUTES } from '../routes/routes'
import { notify } from '../lib/notifyBus'
import { isValidEmail } from '../utils/emailValidation.ts'

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
 * Form submitelve lett-e
 */
const submitted = ref(false)

/**
 * Valid-e az email
 */
const isEmailValid = computed(() => isValidEmail(email.value))

/**
 * Valid-e a form
 */
const isFormValid = computed(() => isEmailValid.value && password.value.length > 0)

/**
 * Bejelentkezés
 */
const handleLogin = async () => {
  submitted.value = true
  if (!isFormValid.value) return

  loading.value = true
  try {
    await login(email.value, password.value)
    await router.push(ROUTES.EVENTS)
  } catch (error: any) {
    notify.error(error.response.data.error_key)
    console.error(error)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.auth-page {
  min-height: calc(100vh - 40px);
  display: grid;
  place-items: center;
  padding: 20px;
}

.auth-card {
  width: 100%;
  max-width: 520px;
  border-radius: 18px;
}

.title-row {
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 800;
}

.form {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-top: 10px;
}

.w-full {
  width: 100%;
}

.links {
  text-align: center;
}

:deep(.p-password),
:deep(.p-password-input),
:deep(.p-inputtext) {
  width: 100%;
}

/* szem ikon (toggleMask) belül maradjon */
:deep(.p-password) {
  position: relative;
}
:deep(.p-password-input) {
  padding-right: 2.5rem;
}
</style>
