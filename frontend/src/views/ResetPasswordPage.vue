<template>
  <div class="reset-page">
    <Card class="reset-card">
      <template #title>
        <div class="title-row">
          <i class="pi pi-lock" />
          <span>{{ $t('reset_password_page.title') }}</span>
        </div>
      </template>
      <template #content>
        <form class="form mt-4" @submit.prevent="handleResetPassword">
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
              <label for="email">{{ $t('reset_password_page.email') }}</label>
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
                :invalid="submitted"
                autocomplete="new-password"
              />
              <label for="password">{{ $t('reset_password_page.password') }}</label>
            </FloatLabel>
          </div>

          <!-- Password confirmation -->
          <div class="field">
            <FloatLabel>
              <Password
                inputId="password_confirmation"
                v-model="passwordConfirmation"
                class="w-full"
                toggleMask
                :feedback="false"
                :invalid="submitted && !isPasswordConfirmationValid"
                autocomplete="new-password"
              />
              <label for="password_confirmation">
                {{ $t('reset_password_page.password_confirmation') }}
              </label>
            </FloatLabel>
          </div>

          <!-- Submit -->
          <Button
            type="submit"
            class="w-full"
            :label="$t('reset_password_page.change_password')"
            :loading="loading"
            :disabled="loading"
          />
        </form>
      </template>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import Card from 'primevue/card'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import FloatLabel from 'primevue/floatlabel'
import apiClient from '../api/axios'
import { ROUTES } from '../routes/routes'
import { notify } from '../lib/notifyBus'
import { isValidEmail } from '../utils/emailValidation.ts'

const route = useRoute()
const router = useRouter()

/**
 * Token kiolvasása az url-ből
 */
const token = ref(route.params.token as string)

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
 * Form submitelve lett-e
 */
const submitted = ref(false)

/**
 * Email validáció
 */
const isEmailValid = computed(() => isValidEmail(email.value))

/**
 * Ugyanez-e a jelszó confirm
 */
const isPasswordConfirmationValid = computed(
  () => passwordConfirmation.value === password.value && passwordConfirmation.value.length > 0,
)

/**
 * Valid-e a form
 */
const isFormValid = computed(() => isEmailValid.value && isPasswordConfirmationValid.value)

const handleResetPassword = async () => {
  submitted.value = true
  if (!isFormValid.value) return

  loading.value = true

  try {
    const response = await apiClient.post('/password/reset', {
      token: token.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    })

    notify.success(response.data.message_key)
    setTimeout(() => router.push(ROUTES.LOGIN), 1200)
  } catch (error: any) {
    console.error(error)
    notify.error(error.response.data.error_key)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
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

.subtitle {
  opacity: 0.75;
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

/* A PrimeVue Password belső wrapperét is húzzuk full szélességre */
:deep(.p-password),
:deep(.p-password-input),
:deep(.p-inputtext) {
  width: 100%;
}

/* A szem ikon (toggleMask) legyen a mező jobb oldalán belül */
:deep(.p-password) {
  position: relative;
}

/* Néhány theme-nél kell, hogy a belső input kapjon helyet az ikon miatt */
:deep(.p-password-input) {
  padding-right: 2.5rem;
}
</style>
