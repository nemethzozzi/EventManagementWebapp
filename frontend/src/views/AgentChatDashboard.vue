<template>
  <div class="container-fluid mt-4">
    <div class="d-flex justify-content-between mb-3">
      <h2>{{ $t('agent_dashboard.title') }}</h2>
    </div>

    <div class="row">
      <!-- Beszélgetések listája -->
      <div class="col-md-4">
        <ConversationList
          :conversations="conversations"
          :selected-id="selectedConversationId"
          :loading="loading"
          @select="selectConversation"
        />
      </div>

      <!-- Beszélgetés ablak -->
      <div class="col-md-8 d-flex mb-4">
        <div class="chat-shell flex-grow-1 d-flex flex-column">
          <ChatPanel
            v-if="selectedConversationId"
            :conversationId="selectedConversationId"
            :mode="ROLE.HELPDESK_AGENT"
            @closed="onClosed"
          />

          <div v-else class="alert alert-info m-0">
            {{ $t('agent_dashboard.choose_conversation') }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import apiClient from '../api/axios'
import ChatPanel from '../components/ChatPanel.vue'
import { notify } from '../lib/notifyBus.ts'
import { ROLE } from '../types/Role.ts'
import ConversationList from '../components/ConversationList.vue'
import type { Conversation } from '../types/Conversation.ts'

/**
 * Beszélgetések
 */
const conversations = ref<Conversation[]>([])

/**
 * Kiválasztott beszélgetés
 */
const selectedConversationId = ref<number | null>(null)

/**
 * Betöltés
 */
const loading = ref(false)

/**
 * Beszélgetések betöltése
 */
const loadConversations = async () => {
  loading.value = true
  try {
    const response = await apiClient.get('/chat/conversations')
    conversations.value = response.data
  } catch (error: any) {
    notify.error(error.response.data.error_key)
    console.error(error)
  } finally {
    loading.value = false
  }
}

/**
 * Kiválasztott beszélgetés
 *
 * @param conversationId
 */
const selectConversation = async (conversationId: number) => {
  selectedConversationId.value = conversationId
}

/**
 * Beszélgetés lezárása
 */
const onClosed = async () => {
  await loadConversations()
  selectedConversationId.value = null
}

onMounted(() => {
  loadConversations()
})
</script>

<style>
.chat-shell {
  height: calc(100vh - 140px);
  min-height: 0;
}
</style>
