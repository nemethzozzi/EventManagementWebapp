<template>
  <div class="card">
    <div class="card-header">
      <h5>{{ $t('agent_dashboard.conversations.title') }}</h5>
    </div>

    <div v-if="loading" class="d-flex justify-content-center align-items-center w-100">
      <ProgressSpinner
        style="width: 50px; height: 50px"
        strokeWidth="8"
        fill="transparent"
        animationDuration=".5s"
      />
    </div>
    <div v-else class="card-body" :style="{ maxHeight, overflowY: 'auto' }">
      <div
        v-for="conv in conversations"
        :key="conv.id"
        class="card mb-2 cursor-pointer"
        :class="{ 'border-primary': selectedId === conv.id }"
        @click="$emit('select', conv.id)"
        style="cursor: pointer"
      >
        <div class="card-body p-2">
          <div class="fw-bold">{{ conv.user?.name ?? '-' }}</div>

          <div class="small text-muted d-flex align-items-center gap-2">
            <span>{{ $t('agent_dashboard.conversations.status_label') }}</span>

            <span class="badge" :class="statusBadgeClass(conv.status)">
              {{ statusText(conv.status) }}
            </span>
          </div>

          <div v-if="conv.messages?.length" class="small text-truncate">
            {{ conv.messages[0].content }}
          </div>
        </div>
      </div>

      <div v-if="conversations.length === 0" class="text-muted text-center">
        {{ $t('agent_dashboard.conversations.empty') }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { PropType } from 'vue'
import { STATUS, type StatusType } from '../types/Status.ts'
import { t } from '../lib/i18n.ts'
import type { Conversation } from '../types/Conversation.ts'
import ProgressSpinner from 'primevue/progressspinner'

defineProps({
  conversations: {
    type: Array as PropType<Conversation[]>,
    required: true,
  },
  selectedId: {
    type: Number as PropType<number | null>,
    default: null,
  },
  maxHeight: {
    type: String,
    default: '600px',
  },
  loading: {
    type: Boolean,
    default: false,
  },
})

defineEmits<{
  (e: 'select', conversationId: number): void
}>()

/**
 * Backend státusz -> badge szín (Bootstrap)
 */
const statusBadgeClass = (status: StatusType) => {
  switch (status) {
    case STATUS.OPEN:
      return 'bg-success'
    case STATUS.ASSIGNED:
      return 'bg-primary'
    case STATUS.PENDING:
      return 'bg-warning text-dark'
    case STATUS.CLOSED:
      return 'bg-danger'
    default:
      return 'bg-dark'
  }
}

/**
 * Backend státusz -> i18n kulcs
 */
const statusText = (status: string) => {
  switch (status) {
    case STATUS.OPEN:
      return t('chat.status.open')
    case STATUS.PENDING:
      return t('chat.status.pending')
    case STATUS.ASSIGNED:
      return t('chat.status.assigned')
    case STATUS.CLOSED:
      return t('chat.status.closed')
    default:
      return status
  }
}
</script>

<style scoped>
.card {
  background: var(--p-surface-0);
  color: var(--p-text-color);
  border-color: var(--p-surface-200);
}

.card-header {
  background: color-mix(in srgb, var(--p-surface-0), var(--p-surface-100) 35%);
  border-bottom-color: var(--p-surface-200);
}

.card-body {
  background: transparent;
}

.card-body .card {
  background: var(--p-surface-0);
  border-color: var(--p-surface-200);
  transition:
    background 120ms ease,
    border-color 120ms ease,
    transform 120ms ease;
}

.card-body .card:hover {
  background: color-mix(in srgb, var(--p-surface-0), var(--p-surface-100) 45%);
  transform: translateY(-1px);
}

.card-body .card.border-primary {
  border-color: var(--p-primary-color) !important;
  box-shadow: 0 0 0 2px color-mix(in srgb, var(--p-primary-color), transparent 70%);
}

:deep(.text-muted) {
  color: var(--p-text-muted-color) !important;
}

.badge {
  border: 1px solid color-mix(in srgb, currentColor, transparent 70%);
}

html.dark .card {
  background: var(--p-surface-900);
  border-color: var(--p-surface-700);
}

html.dark .card-header {
  background: color-mix(in srgb, var(--p-surface-900), var(--p-surface-800) 35%);
  border-bottom-color: var(--p-surface-700);
}

html.dark .card-body .card {
  background: var(--p-surface-900);
  border-color: var(--p-surface-700);
}

html.dark .card-body .card:hover {
  background: color-mix(in srgb, var(--p-surface-900), var(--p-surface-800) 45%);
}

.small {
  color: var(--p-text-color);
}
</style>
