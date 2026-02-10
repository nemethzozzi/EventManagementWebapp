<template>
  <Card class="card-shell">
    <template #title>
      <div class="title-row mb-4">
        <i class="pi pi-user-plus" />
        <div class="title-text">
          <div class="title">{{ $t('admin_user_create_from.new_user') }}</div>
        </div>
      </div>
    </template>

    <template #content>
      <form class="form" @submit.prevent="onSubmit">
        <div class="grid row">
          <div class="field col-12 md:col-4">
            <FloatLabel>
              <InputText
                id="name"
                v-model.trim="form.name"
                class="w-full"
                :invalid="submitted && !form.name.trim()"
                autocomplete="name"
              />
              <label for="name">{{ $t('admin_user_create_from.name') }}</label>
            </FloatLabel>
            <small v-if="submitted && !form.name.trim()" class="p-error">
              {{ $t('validation.required') }}
            </small>
          </div>

          <div class="field col-12 md:col-8">
            <FloatLabel>
              <InputText
                id="email"
                v-model.trim="form.email"
                class="w-full"
                :invalid="submitted && !isEmailValid"
                autocomplete="email"
              />
              <label for="email">{{ $t('admin_user_create_from.email') }}</label>
            </FloatLabel>
            <small v-if="submitted && !isEmailValid" class="p-error">
              {{ $t('validation.invalid_email') }}
            </small>
          </div>
        </div>
        <div class="grid row">
          <div class="field col-12 md:col-5">
            <FloatLabel>
              <Password
                inputId="password"
                v-model="form.password"
                class="w-full"
                toggleMask
                :feedback="false"
                :invalid="submitted && form.password.length < 8"
                autocomplete="new-password"
              />
              <label for="password">{{ $t('admin_user_create_from.password') }}</label>
            </FloatLabel>
            <small v-if="submitted && form.password.length < 8" class="p-error">
              {{ $t('validation.min_characters') }}
            </small>
          </div>

          <div class="field col-12 md:col-4">
            <FloatLabel>
              <Select
                v-model="form.role"
                :options="roleOptions"
                optionLabel="label"
                optionValue="value"
                class="w-full"
                :invalid="submitted && !form.role"
              />
              <label>{{ $t('admin_user_create_from.role') }}</label>
            </FloatLabel>
          </div>

          <div class="field col-12 md:col-3">
            <Button
              type="submit"
              class="w-full create-btn"
              :label="$t('admin_user_create_from.create')"
              :loading="creating"
              :disabled="creating"
            />
          </div>
        </div>
      </form>
    </template>
  </Card>
</template>

<script setup lang="ts">
import { computed, reactive, ref } from 'vue'
import Card from 'primevue/card'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import FloatLabel from 'primevue/floatlabel'
import Select from 'primevue/select'
import { ROLE, type RoleType } from '../types/Role.ts'
import { isValidEmail } from '../utils/emailValidation.ts'
import { useRoleOptions } from '../constants/roleOptions.ts'

type CreateUserPayload = {
  name: string
  email: string
  password: string
  role: RoleType
}

defineProps({
  creating: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits<{
  (e: 'submit', payload: CreateUserPayload): void
}>()

/**
 * Szerepkör opciók
 */
const { roleOptions } = useRoleOptions()

/**
 * Submitelve lett-e
 */
const submitted = ref(false)

/**
 * Form
 */
const form = reactive<CreateUserPayload>({
  name: '',
  email: '',
  password: '',
  role: ROLE.USER,
})

/**
 * Valid-e az email
 */
const isEmailValid = computed(() => isValidEmail(form.email))

/**
 * Valid-e a form
 */
const isFormValid = computed(() => {
  return (
    form.name.trim().length > 0 && isEmailValid.value && form.password.length >= 8 && !!form.role
  )
})

/**
 * Form reset
 */
const reset = () => {
  form.name = ''
  form.email = ''
  form.password = ''
  form.role = ROLE.USER
  submitted.value = false
}

/**
 * Felhasználó létrehozásának submitálása
 */
const onSubmit = () => {
  submitted.value = true
  if (!isFormValid.value) return
  emit('submit', { ...form })
  // a parent majd resetel, ha siker volt – de itt is resetelheted lokálisan:
  reset()
}
</script>

<style scoped>
.card-shell {
  border-radius: 18px;
}

.title-row {
  display: flex;
  align-items: center;
  gap: 12px;
}

.title-text {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.title {
  font-weight: 800;
  line-height: 1.1;
}

.form {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.row {
  row-gap: 14px; /* grid sorok közti spacing */
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

.create-btn {
  height: 44px; /* vizuálisan egyvonalba a mezőkkel */
}

/* PrimeVue belső elemek full width */
:deep(.p-password),
:deep(.p-password-input),
:deep(.p-inputtext),
:deep(.p-select) {
  width: 100%;
}
</style>
