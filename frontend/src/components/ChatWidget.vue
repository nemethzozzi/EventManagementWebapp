<template>
  <!-- Floating bubble -->
  <Button
    class="chat-fab"
    rounded
    aria-label="Chat"
    icon="pi pi-comments"
    severity="info"
    @click="toggle"
  />

  <!-- Popup box -->
  <Transition name="chat-pop">
    <div v-if="open" class="chat-box" role="dialog" aria-label="Chat">
      <div class="chat-box-header">
        <div class="left">
          <i class="pi pi-comments" />
          <span class="title">{{ $t('chat.title') }}</span>
        </div>

        <Button
          class="close-btn"
          icon="pi pi-times"
          severity="secondary"
          text
          rounded
          aria-label="Close"
          @click="open = false"
        />
      </div>

      <div class="chat-box-body">
        <ChatPanel :mode="ROLE.USER" />
      </div>
    </div>
  </Transition>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import Button from 'primevue/button'
import ChatPanel from './ChatPanel.vue'
import { ROLE } from '../types/Role.ts'

const open = ref(false)

const toggle = () => {
  open.value = !open.value
}
</script>

<style scoped>
/* Floating bubble */
.chat-fab {
  position: fixed;
  right: 18px;
  bottom: 18px;
  width: 56px;
  height: 56px;
  z-index: 9999;
  box-shadow: 0 10px 26px rgba(0, 0, 0, 0.18);
}

/* Popup box */
.chat-box {
  position: fixed;
  right: 18px;
  bottom: 86px; /* a gomb fölé */
  width: 380px;
  max-width: calc(100vw - 36px);
  height: 520px;
  max-height: calc(100vh - 120px);
  z-index: 9998;

  /* LIGHT */
  background: color-mix(in srgb, var(--p-surface-0), transparent 8%);
  border: 1px solid var(--p-surface-200);
  color: var(--p-text-color);

  /* glassy */
  backdrop-filter: blur(10px);
  border-radius: 16px;
  overflow: hidden;

  box-shadow:
    0 20px 60px rgba(0, 0, 0, 0.2),
    0 6px 18px rgba(0, 0, 0, 0.1);

  display: flex;
  flex-direction: column;
}

/* DARK */
html.dark .chat-box {
  background: color-mix(in srgb, var(--p-surface-900), transparent 10%);
  border-color: var(--p-surface-700);
  box-shadow:
    0 24px 70px rgba(0, 0, 0, 0.55),
    0 10px 26px rgba(0, 0, 0, 0.35);
}

.chat-box-header {
  padding: 12px 12px 12px 14px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;

  border-bottom: 1px solid var(--p-surface-200);
}

html.dark .chat-box-header {
  border-bottom-color: var(--p-surface-700);
}

.chat-box-header .left {
  display: flex;
  align-items: center;
  gap: 10px;
  color: var(--p-text-color);
}

.chat-box-header .title {
  font-weight: 800;
}

.chat-box-body {
  flex: 1;
  min-height: 0;
  padding: 12px;
  display: flex;
  flex-direction: column;
}

/* Animation (bottom -> up + fade) */
.chat-pop-enter-active,
.chat-pop-leave-active {
  transition:
    transform 180ms ease,
    opacity 180ms ease;
}
.chat-pop-enter-from,
.chat-pop-leave-to {
  transform: translateY(14px) scale(0.98);
  opacity: 0;
}
.chat-pop-enter-to,
.chat-pop-leave-from {
  transform: translateY(0) scale(1);
  opacity: 1;
}

/* Mobilon legyen majdnem full width */
@media (max-width: 480px) {
  .chat-box {
    width: calc(100vw - 36px);
    height: 70vh;
  }
}
</style>
