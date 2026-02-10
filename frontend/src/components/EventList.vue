<template>
  <div class="card">
    <div class="card-header">
      <h4>{{ $t('events_list.my_events') }}</h4>
    </div>

    <div class="card-body">
      <div v-if="loading" class="d-flex justify-content-center align-items-center w-100">
        <ProgressSpinner
            style="width: 50px;
          height: 50px"
            strokeWidth="8"
            fill="transparent"
            animationDuration=".5s"
        />
      </div>
      <div v-else-if="events.length === 0" class="alert alert-info">
        {{ $t('events_list.no_events') }}
      </div>

      <div v-else v-for="event in events" :key="event.id" class="card mb-3">
        <div class="card-body">
          <h5 class="card-title">{{ event.title }}</h5>
          <p class="card-text">
            <strong>{{ $t('events_list.time') }}:</strong> {{ formatDate(event.occurs_at) }}
          </p>
          
          <div v-if="editingId === event.id">
            <textarea
              v-model="editDescription"
              class="form-control mb-2"
              rows="3"
            ></textarea>
            <button @click="saveEdit(event.id)" class="btn btn-success btn-sm me-2">
              {{ $t('events_list.button.save') }}
            </button>
            <button @click="cancelEdit" class="btn btn-secondary btn-sm">
              {{ $t('events_list.button.cancel') }}
            </button>
          </div>

          <div v-else>
            <p class="card-text">
              <strong>{{ $t('events_list.description') }}:</strong> {{ event.description || $t('events_list.no_description') }}
            </p>
            <button @click="startEdit(event)" class="btn btn-warning btn-sm me-2">
              {{ $t('events_list.button.edit_description') }}
            </button>
            <button @click="deleteEvent(event.id)" class="btn btn-danger btn-sm">
              {{ $t('events_list.button.delete') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {type PropType, ref} from 'vue'
import apiClient from '../api/axios'
import type { Event } from '../types/Event.ts'
import {notify} from "../lib/notifyBus.ts";
import {t} from "../lib/i18n.ts";
import { confirm } from '../lib/confirmBus'
import ProgressSpinner from "primevue/progressspinner";

defineProps({
  events: {
    type: Array as PropType<Event[]>,
    required: true,
  },
  loading: {
    type: Boolean,
    default: false,
  }
})


const editingId = ref<number | null>(null)
const editDescription = ref('')

/**
 * Dátum formázása
 *
 * @param dateString
 */
const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleString('hu-HU')
}

/**
 * Módosítás kezdése
 *
 * @param event
 */
const startEdit = (event: Event) => {
  editingId.value = event.id
  editDescription.value = event.description || ''
}

/**
 * Módosítás megszakítása
 */
const cancelEdit = () => {
  editingId.value = null
  editDescription.value = ''
}

/**
 * Módosítás mentése
 *
 * @param eventId
 */
const saveEdit = async (eventId: number) => {
  try {
    const response = await apiClient.put(`/events/${eventId}`, {
      description: editDescription.value,
    })
    
    cancelEdit()
    notify.success(response.data.message_key)
  } catch (error: any) {
    console.error(error)
    notify.error(error.response.data.error_key)
  }
}

/**
 * Esemény törlése
 *
 * @param eventId
 */
const deleteEvent = async (eventId: number) => {
  const ok = await confirm.open({
    title: t('events_list.popup.title'),
    message: t('events_list.popup.message'),
    severity: 'danger',
    confirmLabel: t('events_list.popup.delete'),
    cancelLabel: t('events_list.popup.cancel'),
  })

  if (!ok) return

  try {
    const response = await apiClient.delete(`/events/${eventId}`)
    notify.success(response.data.message_key)
  } catch (error: any) {
    console.error(error)
    notify.error(error.response.data.error_key)
  }
}
</script>