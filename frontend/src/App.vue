<template>
  <ToastHost />
  <AppNavBar v-if="showNavbar" />
  <ConfirmPopup />
  <ChatWidget v-if="showChatWidget" />
  <router-view />
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { useAuth } from './composables/useAuth'
import ToastHost from './components/ToastHost.vue'
import { useRoute } from 'vue-router'
import AppNavBar from './components/AppNavBar.vue'
import ConfirmPopup from './components/ConfirmPopup.vue'
import { ROUTES } from './routes/routes.ts'
import { ROLE } from './types/Role.ts'
import ChatWidget from './components/ChatWidget.vue'

/**
 * Felhasználó betöltése
 */
const { loadUser } = useAuth()

const route = useRoute()
const { isAuthenticated, userRole } = useAuth()

/**
 * Navbar mutatása
 */
const showNavbar = computed(() => !route.meta.guest)

/**
 * Chat widget mutatása
 */
const showChatWidget = computed(() => {
  const isOnEvents = route.path === ROUTES.EVENTS
  const isUser = userRole.value === ROLE.USER
  return isAuthenticated.value && isUser && isOnEvents
})

onMounted(() => {
  loadUser()
})
</script>
