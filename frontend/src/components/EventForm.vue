<template>
  <div class="form-page">
    <Card class="form-card">
      <template #title>
        <div class="title-row">
          <i class="pi pi-calendar-plus" />
          <span>{{ $t('events_form.new_event') }}</span>
        </div>
      </template>

      <template #content>
        <form class="form mt-3" @submit.prevent="createEvent">
          <!-- Title -->
          <div class="field">
            <FloatLabel>
              <InputText
                id="title"
                v-model.trim="form.title"
                class="w-full"
                :invalid="submitted && !form.title"
                autocomplete="off"
              />
              <label for="title">{{ $t('events_form.title') }} *</label>
            </FloatLabel>

            <small v-if="submitted && !form.title" class="p-error">
              {{ $t('validation.required') }}
            </small>
          </div>

          <!-- DateTime (PrimeVue DatePicker) -->
          <div class="field">
            <FloatLabel>
              <DatePicker
                inputId="occurs_at"
                v-model="occursAt"
                class="w-full"
                :invalid="submitted && !occursAt"
                showIcon
                iconDisplay="input"
                showTime
                hourFormat="24"
                :manualInput="false"
              />
              <label for="occurs_at">{{ $t('events_form.time') }} *</label>
            </FloatLabel>

            <small v-if="submitted && !occursAt" class="p-error">
              {{ $t('validation.required') }}
            </small>
          </div>

          <!-- Description -->
          <div class="field">
            <FloatLabel>
              <Textarea
                id="description"
                v-model.trim="form.description"
                class="w-full"
                autoResize
                rows="3"
              />
              <label for="description">{{ $t('events_form.description') }}</label>
            </FloatLabel>
          </div>

          <!-- Submit -->
          <Button
            type="submit"
            class="w-full"
            :label="$t('events_form.create_event')"
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
import apiClient from '../api/axios'
import { ROUTES } from '../routes/routes'
import { notify } from '../lib/notifyBus'

import Card from 'primevue/card'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import FloatLabel from 'primevue/floatlabel'
import Textarea from 'primevue/textarea'
import DatePicker from 'primevue/datepicker'

const emit = defineEmits<{
  (e: 'event-created'): void
}>()

const form = ref({
  title: '',
  description: '',
})

/**
 * DatePicker Date objektummal dolgozik
 */
const occursAt = ref<Date | null>(null)

/**
 * Betöltés
 */
const loading = ref(false)

/**
 * Submitelve lett-e a form
 */
const submitted = ref(false)

/**
 * Valid-e a form
 */
const isFormValid = computed(() => {
  return form.value.title.trim().length > 0 && !!occursAt.value
})

/**
 * Form reset
 */
const resetForm = () => {
  form.value = { title: '', description: '' }
  occursAt.value = null
  submitted.value = false
}

/**
 * Esemény létrehozása
 */
const createEvent = async () => {
  submitted.value = true
  if (!isFormValid.value) return

  loading.value = true
  try {
    const payload = {
      title: form.value.title,
      occurs_at: occursAt.value!.toISOString(),
      description: form.value.description?.trim() ? form.value.description.trim() : null,
    }

    const response = await apiClient.post(ROUTES.EVENTS, payload)

    notify.success(response.data.message_key)
    emit('event-created')
    resetForm()
  } catch (error: any) {
    console.error(error)
    notify.error(error.response.data.error_key)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.form-page {
  width: 100%;
}

.form-card {
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

:deep(.p-inputtext),
:deep(.p-textarea),
:deep(.p-datepicker),
:deep(.p-datepicker-input) {
  width: 100%;
}
</style>
