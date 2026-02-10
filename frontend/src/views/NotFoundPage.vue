<template>
  <div class="nf-shell">
    <div class="nf-card">
      <div class="nf-code">404</div>
      <h1 class="nf-title">{{ $t('not_found.title') }}</h1>
      <p class="nf-text">{{ $t('not_found.text') }}</p>

      <div class="nf-actions">
        <Button
            icon="pi pi-home"
            :label="$t('not_found.go_home')"
            @click="goHome"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'
import Button from 'primevue/button'
import { ROUTES } from '../routes/routes'

const router = useRouter()
const goHome = () => router.push(ROUTES.EVENTS)
</script>

<style scoped>
.nf-shell {
  min-height: calc(100vh - 64px);
  display: grid;
  place-items: center;
  padding: 24px;
}

.nf-card {
  width: min(540px, 100%);
  border-radius: 18px;
  padding: 26px;
  background: #fff;
  box-shadow: 0 18px 50px rgba(0, 0, 0, 0.12);
  text-align: center;
}

.nf-code {
  font-size: 64px;
  font-weight: 900;
  letter-spacing: 2px;
  opacity: 0.15;
}

.nf-title {
  margin: 6px 0 8px;
  font-size: 24px;
  font-weight: 800;
}

.nf-text {
  margin: 0 0 18px;
  opacity: 0.75;
}

.nf-actions {
  display: flex;
  justify-content: center;
}
</style>
