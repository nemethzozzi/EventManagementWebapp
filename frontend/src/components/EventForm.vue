<template>
  <div class="card mb-4">
    <div class="card-header">
      <h4>{{ $t('events_form.new_event') }}</h4>
    </div>
    <div class="card-body">
      <form @submit.prevent="createEvent">
        <div class="mb-3">
          <label for="title" class="form-label">{{ $t('events_form.title') }} *</label>
          <input
            type="text"
            class="form-control"
            id="title"
            v-model="form.title"
            required
          />
        </div>

        <div class="mb-3">
          <label for="occurs_at" class="form-label">{{ $t('events_form.time') }} *</label>
          <input
            type="datetime-local"
            class="form-control"
            id="occurs_at"
            v-model="form.occurs_at"
            required
          />
        </div>

        <div class="mb-3">
          <label for="description" class="form-label">{{ $t('events_form.description') }}</label>
          <textarea
            class="form-control"
            id="description"
            rows="3"
            v-model="form.description"
          ></textarea>
        </div>

        <button type="submit" class="btn btn-primary" :disabled="loading">
          {{ loading ? $t('events_form.creating') : $t('events_form.create_event') }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import apiClient from '../api/axios'
import {ROUTES} from "../routes/routes.ts";
import {notify} from "../lib/notifyBus.ts";

const emit = defineEmits(['event-created'])

/**
 * Form - TODO az összes helyen lehetne így, ahol form van
 */
const form = ref({
  title: '',
  occurs_at: '',
  description: '',
})

/**
 * Betöltés
 */
const loading = ref(false)

/**
 * Event létrehozása
 */
const createEvent = async () => {
  loading.value = true

  try {
    const response = await apiClient.post(ROUTES.EVENTS, form.value)

    // Form reset
    form.value = {
      title: '',
      occurs_at: '',
      description: '',
    }
    notify.success(response.data?.message_key)
  } catch (error: any) {
    console.error(error)
    notify.error(error.response?.data?.error_key)
  } finally {
    loading.value = false
  }
}
</script>