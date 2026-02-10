<template>
  <header class="nav-shell">
    <div class="nav-inner">
      <RouterLink :to="ROUTES.EVENTS" class="brand">
        <i class="pi pi-calendar"></i>
        <span>UCC</span>
      </RouterLink>

      <div class="nav-actions">
        <Select
            v-model="selectedLocale"
            :options="localeOptions"
            optionLabel="label"
            optionValue="value"
            class="lang-select"
        />
        <Button
            v-if="isAgentOrAdmin"
            :label="$t('nav.agent_dashboard')"
            icon="pi pi-headphones"
            severity="secondary"
            :outlined="true"
            @click="go(ROUTES.AGENT_DASHBOARD)"
        />

        <Button
            v-if="isAdmin"
            :label="$t('nav.admin')"
            icon="pi pi-cog"
            severity="warning"
            :outlined="true"
            @click="go(ROUTES.ADMIN)"
        />

        <Button
            :label="$t('nav.logout')"
            icon="pi pi-sign-out"
            severity="danger"
            @click="handleLogout"
        />
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import {computed, ref, watch} from 'vue'
import { useRouter } from 'vue-router'
import { ROUTES } from '../routes/routes'
import { useAuth } from '../composables/useAuth'

import Button from 'primevue/button'
import Select from 'primevue/select'
import {ROLE} from "../types/Role.ts";
import {LOCAL, type LocalType} from "../types/Local.ts";
import {getLocale, setLocale, t} from "../lib/i18n.ts";

const router = useRouter()
const { userRole, logout } = useAuth()

/**
 * Admin-e
 */
const isAdmin = computed(() => userRole.value === ROLE.ADMIN)

/**
 * Admin vagy agent-e
 */
const isAgentOrAdmin = computed(
    () => userRole.value === ROLE.HELPDESK_AGENT || userRole.value === ROLE.ADMIN
)

const go = (path: string) => router.push(path)

/**
 * Nyelvi opciók
 */
const localeOptions = computed(() => [
  { label: t('nav.lang.hu'), value: LOCAL.HU },
  { label: t('nav.lang.en'), value: LOCAL.ENG },
])

/**
 * Nyelv kiválasztása
 */
const selectedLocale = ref<LocalType>(getLocale())

/**
 * Kijelentkezés
 */
const handleLogout = async () => {
  await logout()
}

watch(selectedLocale, (l) => setLocale(l), { immediate: true })

</script>

<style scoped>
.nav-shell {
  position: sticky;
  top: 0;
  z-index: 50;
  backdrop-filter: blur(10px);
  background: rgba(255, 255, 255, 0.75);
  border-bottom: 1px solid rgba(0, 0, 0, 0.06);
}

.nav-inner {
  max-width: 1100px;
  margin: 0 auto;
  padding: 12px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}

.brand {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  font-weight: 800;
  text-decoration: none;
  color: inherit;
}

.brand i {
  font-size: 1.15rem;
}

.nav-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}
</style>
