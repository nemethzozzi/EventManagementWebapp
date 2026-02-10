<template>
  <div class="container mt-4">
    <h2>{{ $t('events_page.title') }}</h2>
    <EventForm @event-created="loadEvents" />
    <EventList :events="events" :loading="loading" @changed="loadEvents" />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import apiClient from '../api/axios'
import EventForm from '../components/EventForm.vue'
import EventList from '../components/EventList.vue'
import { ROUTES } from '../routes/routes.ts'
import type { Event } from '../types/Event.ts'
import { notify } from '../lib/notifyBus.ts'

/**
 * Eventek
 */
const events = ref<Event[]>([])

/**
 * Betöltés
 */
const loading = ref(false)

/**
 * Eventek betöltése
 */
const loadEvents = async () => {
  loading.value = true
  try {
    const response = await apiClient.get(ROUTES.EVENTS)
    events.value = response.data
  } catch (error: any) {
    notify.error(error.response.data.error_key)
    console.error(error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadEvents()
})
</script>
