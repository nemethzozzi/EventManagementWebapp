<template>
  <div class="list-page">
    <Card class="list-card">
      <template #title>
        <div class="title-row">
          <i class="pi pi-calendar" />
          <span>{{ $t('events_list.my_events') }}</span>
        </div>
      </template>

      <template #content>
        <!-- Loading -->
        <div v-if="loading" class="center">
          <ProgressSpinner
            style="width: 50px; height: 50px"
            strokeWidth="8"
            fill="transparent"
            animationDuration=".5s"
          />
        </div>

        <!-- Empty -->
        <Message v-else-if="events.length === 0" severity="info" :closable="false">
          {{ $t('events_list.no_events') }}
        </Message>

        <!-- List -->
        <div v-else class="events">
          <Card v-for="event in events" :key="event.id" class="event-card">
            <template #content>
              <div class="event-head">
                <div class="event-title">
                  <div class="title">{{ event.title }}</div>
                  <div class="meta">
                    <i class="pi pi-clock" />
                    <span>
                      <strong>{{ $t('events_list.time') }}:</strong>
                      {{ formatDate(event.occurs_at) }}
                    </span>
                  </div>
                </div>

                <Tag
                  :value="
                    event.description
                      ? $t('events_list.description')
                      : $t('events_list.no_description')
                  "
                  :severity="event.description ? 'success' : 'warning'"
                />
              </div>

              <Divider />

              <!-- Editing -->
              <div v-if="editingId === event.id" class="edit-area">
                <FloatLabel>
                  <Textarea
                    id="desc"
                    v-model.trim="editDescription"
                    class="w-full"
                    autoResize
                    rows="3"
                  />
                  <label for="desc">{{ $t('events_list.description') }}</label>
                </FloatLabel>

                <div class="actions">
                  <Button
                    size="small"
                    icon="pi pi-check"
                    severity="success"
                    :label="$t('events_list.button.save')"
                    :loading="saving"
                    :disabled="saving"
                    @click="saveEdit(event.id)"
                  />
                  <Button
                    size="small"
                    icon="pi pi-times"
                    severity="secondary"
                    :label="$t('events_list.button.cancel')"
                    :disabled="saving"
                    @click="cancelEdit"
                  />
                </div>
              </div>

              <!-- Normal -->
              <div v-else class="view-area">
                <div class="desc">
                  <strong>{{ $t('events_list.description') }}:</strong>
                  <span class="desc-text">
                    {{ event.description || $t('events_list.no_description') }}
                  </span>
                </div>

                <div class="actions">
                  <Button
                    size="small"
                    icon="pi pi-pencil"
                    severity="warning"
                    :label="$t('events_list.button.edit_description')"
                    @click="startEdit(event)"
                  />
                  <Button
                    size="small"
                    icon="pi pi-trash"
                    severity="danger"
                    :label="$t('events_list.button.delete')"
                    @click="deleteEvent(event.id)"
                  />
                </div>
              </div>
            </template>
          </Card>
        </div>
      </template>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { type PropType, ref } from 'vue'
import apiClient from '../api/axios'
import type { Event } from '../types/Event'
import { notify } from '../lib/notifyBus'
import { t } from '../lib/i18n'
import { confirm } from '../lib/confirmBus'

import Card from 'primevue/card'
import ProgressSpinner from 'primevue/progressspinner'
import Message from 'primevue/message'
import Button from 'primevue/button'
import Textarea from 'primevue/textarea'
import FloatLabel from 'primevue/floatlabel'
import Divider from 'primevue/divider'
import Tag from 'primevue/tag'

const props = defineProps({
  events: {
    type: Array as PropType<Event[]>,
    required: true,
  },
  loading: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits<{
  (e: 'changed'): void
}>()

/**
 * Szerkesztendő event azonosítója
 */
const editingId = ref<number | null>(null)

/**
 * Leírás szerkesztése
 */
const editDescription = ref('')

/**
 * Mentés
 */
const saving = ref(false)

/**
 * Dátum formázása
 *
 * @param dateString
 */
const formatDate = (dateString: string) => new Date(dateString).toLocaleString('hu-HU')

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
  saving.value = true
  try {
    const response = await apiClient.put(`/events/${eventId}`, {
      description: editDescription.value,
    })

    cancelEdit()
    notify.success(response.data.message_key)
    emit('changed') // parent frissíthet listát
  } catch (error: any) {
    console.error(error)
    notify.error(error.response.data.error_key)
  } finally {
    saving.value = false
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
    emit('changed') // parent frissíthet listát
  } catch (error: any) {
    console.error(error)
    notify.error(error.response.data.error_key)
  }
}
</script>

<style scoped>
.list-page {
  width: 100%;
}

.list-card {
  border-radius: 18px;
}

.title-row {
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 800;
}

.center {
  display: grid;
  place-items: center;
  padding: 28px 0;
}

.events {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.event-card {
  border-radius: 16px;
}

.event-head {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
}

.event-title .title {
  font-weight: 800;
  font-size: 1.05rem;
}

.meta {
  display: flex;
  align-items: center;
  gap: 8px;
  opacity: 0.8;
  margin-top: 6px;
  font-size: 0.95rem;
}

.view-area,
.edit-area {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.desc {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.desc-text {
  opacity: 0.9;
}

.actions {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.w-full {
  width: 100%;
}

:deep(.p-textarea),
:deep(.p-inputtext) {
  width: 100%;
}
</style>
