<template>
  <div class="chat-panel">
    <div class="chat-top" v-if="props.mode === ROLE.HELPDESK_AGENT && !loading">
      <div class="title">
        <i class="pi pi-user"></i>
        <span>{{ conversation?.user?.name ?? '—' }}</span>
      </div>

      <Button
        v-if="!isClosed"
        size="small"
        severity="danger"
        icon="pi pi-lock"
        :label="$t('chat.close')"
        @click="closeConversation"
      />
    </div>

    <div ref="chatContainer" class="chat-body">
      <div v-if="loading" class="d-flex justify-content-center align-items-center w-100 h-100">
        <ProgressSpinner
          style="width: 50px; height: 50px"
          strokeWidth="8"
          fill="transparent"
          animationDuration=".5s"
        />
      </div>
      <template v-else>
        <MessageBubble
          v-for="m in conversation?.messages ?? []"
          :key="m.id"
          :message="m"
          :currentRole="props.mode"
          :userName="conversation?.user?.name ?? ''"
        />
      </template>
    </div>

    <div class="chat-footer" v-if="!isClosed">
      <form class="chat-form" @submit.prevent="sendMessage">
        <InputText
          v-model="message"
          class="w-100"
          :placeholder="$t('chat.placeholder')"
          :disabled="sending"
        />
        <Button
          type="submit"
          icon="pi pi-send"
          :label="$t('chat.send')"
          :disabled="sending || !message.trim()"
        />
      </form>
    </div>

    <div class="chat-closed" v-else>
      <i class="pi pi-lock"></i>
      <span>{{ $t('chat.closed') }}</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, nextTick, computed, watch, type PropType, onUnmounted } from 'vue'
import apiClient from '../api/axios'
import MessageBubble from '../components/MessageBubble.vue'
import { ROLE, type RoleType } from '../types/Role.ts'
import ProgressSpinner from 'primevue/progressspinner'
import type { Conversation } from '../types/Conversation.ts'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import { STATUS } from '../types/Status.ts'
import { SENDER } from '../types/Sender.ts'

const props = defineProps({
  conversationId: {
    type: Number,
    required: false, // user módban nincs
    default: null,
  },
  mode: {
    type: String as PropType<RoleType>,
    required: true,
  },
})

const emit = defineEmits<{
  (e: 'closed'): void
}>()

/**
 * Beszélgetés
 */
const conversation = ref<Conversation | null>(null)

/**
 * Üzenet
 */
const message = ref('')

/**
 * Betöltés
 */
const loading = ref(false)

/**
 * Küldés
 */
const sending = ref(false)

/**
 * Chat container
 */
const chatContainer = ref<HTMLElement | null>(null)

/**
 * Le van-e zárva
 */
const isClosed = computed(() => conversation.value?.status === STATUS.CLOSED)

/**
 * Scrollozás az aljára
 */
const scrollToBottom = () => {
  if (chatContainer.value) {
    chatContainer.value.scrollTop = chatContainer.value.scrollHeight
  }
}

const channelName = computed(() => {
  const id = conversation.value?.id
  return id ? `conversation.${id}` : null
})

/**
 * Mód alapján megkülönböztetjük az endpointokat
 */
const endpoints = computed(() => {
  return props.mode === ROLE.HELPDESK_AGENT
    ? {
        get: (id: number) => `/chat/conversations/${id}`,
        send: (id: number) => `/chat/conversations/${id}/message`,
        close: (id: number) => `/chat/conversations/${id}/close`,
      }
    : {
        get: () => `/chat/conversation`,
        send: () => `/chat/message`,
        close: null,
      }
})

/**
 * Beszélgetés betöltése
 */
const loadConversation = async () => {
  loading.value = true
  try {
    const res =
      props.mode === ROLE.HELPDESK_AGENT
        ? await apiClient.get(endpoints.value.get(props.conversationId as number))
        : await apiClient.get(endpoints.value.get())

    conversation.value = res.data

    // ha mégis null lenne
    if (!conversation.value) {
      conversation.value = { id: -1, status: STATUS.OPEN, messages: [] } as any
    }

    await nextTick()
    scrollToBottom()
    setupEcho()
  } finally {
    loading.value = false
  }
}

/**
 * Üzenet elküldése
 */
const sendMessage = async () => {
  if (!message.value.trim()) return
  sending.value = true
  try {
    const url =
      props.mode === ROLE.HELPDESK_AGENT
        ? endpoints.value.send(props.conversationId as number)
        : endpoints.value.send()

    await apiClient.post(url, { content: message.value })
    message.value = ''
  } finally {
    sending.value = false
  }
}

/**
 * Beszélgetés lezárása
 */
const closeConversation = async () => {
  if (!endpoints.value.close) return
  await apiClient.post(endpoints.value.close(props.conversationId))
  emit('closed')
  await loadConversation()
}

let currentEchoChannel: any = null
let currentChannelName: string | null = null

const setupEcho = () => {
  const ch = channelName.value
  if (!ch) return

  if (currentEchoChannel && currentChannelName) {
    currentEchoChannel.stopListening('.message.sent')
    window.Echo.leaveChannel(currentChannelName) // régi csatorna
  }

  currentChannelName = ch

  currentEchoChannel = window.Echo.channel(ch).listen('.message.sent', async (e: any) => {
    if (!conversation.value) return
    conversation.value.messages.push(e.message)
    await nextTick()
    scrollToBottom()
  })
}

watch(
  () => [props.mode, props.conversationId] as const,
  async ([mode, id]) => {
    if (mode === ROLE.HELPDESK_AGENT && !id) return
    await loadConversation()
  },
  { immediate: true },
)

watch(
  () => conversation.value?.status,
  async (s) => {
    if (props.mode === ROLE.USER && s === STATUS.CLOSED) {
      await loadConversation()
    }
  },
)

onUnmounted(() => {
  if (currentEchoChannel) {
    currentEchoChannel.stopListening('.message.sent')
  }
  if (currentChannelName) {
    window.Echo.leaveChannel(currentChannelName)
  }
})
</script>

<style scoped>
.chat-panel {
  height: 100%;
  min-height: 0;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

/* Top bar */
.chat-top {
  flex: 0 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
}

.chat-top .title {
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 700;
  color: var(--p-text-color);
}

/* Body */
.chat-body {
  flex: 1 1 auto;
  min-height: 0;
  overflow-y: auto;

  padding: 10px;
  border-radius: 14px;

  /* LIGHT: szép, világos “panel” */
  background: var(--p-surface-50);
  border: 1px solid var(--p-surface-200);
}

/* Dark mód: legyen sötétebb panel + border */
html.dark .chat-body {
  background: var(--p-surface-900);
  border-color: var(--p-surface-700);
}

/* Footer */
.chat-footer {
  flex: 0 0 auto;
}

.chat-form {
  display: flex;
  gap: 10px;
  align-items: center;
}

/* Closed state */
.chat-closed {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;

  padding: 10px;
  border-radius: 12px;

  background: var(--p-surface-50);
  border: 1px solid var(--p-surface-200);
  color: var(--p-text-muted-color);
}

html.dark .chat-closed {
  background: var(--p-surface-900);
  border-color: var(--p-surface-700);
  color: var(--p-text-muted-color);
}
</style>
