<template>
  <div class="mb-3 d-flex" :class="rowClass">
    <div class="d-inline-block p-3 rounded" :class="bubbleClass" style="max-width: 70%;">
      <div class="fw-bold small">{{ senderName }}</div>
      <div>{{ message.content }}</div>
      <div class="text-muted small mt-1">{{ formatTime(message.created_at) }}</div>
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
  // saját üzenet legyen "primary", a másik oldali meg külön szín
  if (isMine.value) return 'bg-primary text-white'

  if (props.message.sender_type === SENDER.BOT) return 'bg-light'
  return 'bg-success text-white' // itt lehet pl bg-white border is, ha akarod
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
