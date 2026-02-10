<template>
  <div class="mb-3" :class="alignmentClass">
    <div class="d-inline-block p-3 rounded" :class="bubbleClass" style="max-width: 70%;">
      <div class="fw-bold small">{{ senderName }}</div>
      <div>{{ message.content }}</div>
      <div class="text-muted small mt-1">{{ formatTime(message.created_at) }}</div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type {Message} from "../types/Message.ts";
import { t } from '../lib/i18n'
import {SENDER} from "../types/Sender.ts";

const props = defineProps<{
  message: Message
}>()

const senderName = computed(() => {
  if (props.message.sender_type === SENDER.BOT) return t('chat.bot')
  if (props.message.sender_type === SENDER.AGENT) return props.message.sender?.name || t('chat.agent')
  return t('chat.you')
})

const alignmentClass = computed(() => {
  return props.message.sender_type === SENDER.USER ? 'text-end' : 'text-start'
})

const bubbleClass = computed(() => {
  if (props.message.sender_type === SENDER.USER) return 'bg-primary text-white'
  if (props.message.sender_type === SENDER.BOT) return 'bg-light'
  return 'bg-success text-white'
})

const formatTime = (dateString: string) => {
  return new Date(dateString).toLocaleTimeString('hu-HU', {
    hour: '2-digit',
    minute: '2-digit',
  })
}
</script>