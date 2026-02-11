<template>
  <div class="mb-3 d-flex" :class="rowClass">
    <div
      class="d-inline-block p-3 rounded bubble-shell"
      :class="bubbleClass"
      style="max-width: 70%"
    >
      <div class="fw-bold small">{{ senderName }}</div>
      <div>{{ message.content }}</div>
      <div class="bubble-time small mt-1">{{ formatTime(message.created_at) }}</div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { PropType } from 'vue'
import type { Message } from '../types/Message'
import { ROLE, type RoleType } from '../types/Role'
import { SENDER } from '../types/Sender'
import { t } from '../lib/i18n'

const props = defineProps({
  message: {
    type: Object as PropType<Message>,
    required: true,
  },
  currentRole: {
    type: String as PropType<RoleType>,
    required: true,
  },
  userName: {
    type: String,
    required: false,
    default: '',
  },
})

/**
 * Az én üzenetem
 */
const isMine = computed(() => {
  if (props.currentRole === ROLE.HELPDESK_AGENT) {
    return props.message.sender_type === SENDER.AGENT
  }
  return props.message.sender_type === SENDER.USER
})

/**
 * Küldő neve
 */
const senderName = computed(() => {
  // Saját üzenet mindig: You
  if (isMine.value) return t('chat.you')

  // Bot
  if (props.message.sender_type === SENDER.BOT) return t('chat.bot')

  // Agent
  if (props.message.sender_type === SENDER.AGENT) {
    return props.message.sender.name
  }

  // ide akkor jutunk, ha agent nézetben a user írt
  return props.userName
})

/**
 * Oszlop class
 */
const rowClass = computed(() => (isMine.value ? 'justify-content-end' : 'justify-content-start'))

/**
 * Buborék osztályok
 */
const bubbleClass = computed(() => {
  // saját üzenet más színnel
  if (isMine.value) return 'bubble bubble--mine'
  return 'bubble bubble--other'
})

/**
 * Dátum formázása
 *
 * @param dateString
 */
const formatTime = (dateString: string) => {
  return new Date(dateString).toLocaleTimeString('hu-HU', {
    hour: '2-digit',
    minute: '2-digit',
  })
}
</script>

<style scoped>
.bubble-shell {
  border-radius: 14px;
}

.bubble {
  background: var(--p-surface-0);
  color: var(--p-text-color);
  border: 1px solid var(--p-surface-200);
}

.bubble--mine {
  background: var(--p-primary-color);
  color: var(--p-primary-contrast-color);
  border-color: color-mix(in srgb, var(--p-primary-color), #000 15%);
}

.bubble--other {
  background: var(--p-surface-100);
  color: var(--p-text-color);
  border-color: var(--p-surface-200);
}

:deep(.bubble-time) {
  color: var(--p-text-muted-color);
}

html.dark .bubble {
  background: var(--p-surface-800);
  color: var(--p-text-color);
  border-color: var(--p-surface-700);
}

html.dark .bubble--mine {
  background: var(--p-primary-color);
  color: var(--p-text-color);
  border-color: color-mix(in srgb, var(--p-primary-color), #000 15%);
}
</style>
