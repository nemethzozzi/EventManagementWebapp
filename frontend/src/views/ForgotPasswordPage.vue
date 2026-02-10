<template>
  <div class="reset-page">
    <Card class="reset-card">
      <template #title>
        <div class="title-row">
          <i class="pi pi-envelope" />
          <span>{{ $t('forgot_password_page.title') }}</span>
        </div>
      </template>

      <template #content>
        <form class="form mt-4" @submit.prevent="handleForgotPassword">
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
              <label for="email">{{ $t('forgot_password_page.email') }}</label>
            </FloatLabel>

            <small v-if="submitted && !isEmailValid" class="p-error">
              {{ $t('validation.invalid_email') ?? 'Adj meg egy érvényes email címet.' }}
            </small>
          </div>

          <!-- Submit -->
          <Button
            type="submit"
            class="w-full"
            :label="$t('forgot_password_page.send_email')"
            :loading="loading"
            :disabled="loading"
          />

          <div class="mt-3 text-center">
            <router-link :to="ROUTES.LOGIN">
              {{ $t('forgot_password_page.login') }}
            </router-link>
          </div>
        </form>
      </template>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import Card from 'primevue/card'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import FloatLabel from 'primevue/floatlabel'
import apiClient from '../api/axios'
import { ROUTES } from '../routes/routes'
import { notify } from '../lib/notifyBus'
import { isValidEmail } from '../utils/emailValidation.ts'

/**
 * Email
 */
const email = ref('')
/**
 * Betöltés
 */
const loading = ref(false)
/**
 * Submitelve lett-e a form
 */
const submitted = ref(false)

/**
 * Valid-e az email
 */
const isEmailValid = computed(() => isValidEmail(email.value))

const handleForgotPassword = async () => {
  submitted.value = true
  if (!isEmailValid.value) return

  loading.value = true
  try {
    const response = await apiClient.post('/password/forgot', { email: email.value })
    notify.success(response.data.message_key)
    email.value = ''
    submitted.value = false
  } catch (error: any) {
    console.error(error)
    notify.error(error.response.data.error_key)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* ugyanaz, mint a reset password oldalon */
.reset-page {
  min-height: calc(100vh - 40px);
  display: grid;
  place-items: center;
  padding: 20px;
}

.reset-card {
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

/* Input szélesség biztosra */
:deep(.p-inputtext) {
  width: 100%;
}
</style>
