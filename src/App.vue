<template>
  <div class="app-layout">
    <Sidebar></Sidebar>
    <Toast position="top-right" />
    <main class="content">
      <RouterView />
    </main>
  </div>
</template>

<script setup lang="ts">
import { RouterView } from 'vue-router'
import Toast from 'primevue/toast'
import Sidebar from '@/components/Sidebar.vue'
</script>

<style scoped lang="scss">
.app-layout {
  display: flex;
}
.content {
  margin-left: calc(200px);
  padding: 4rem;
  width: 100%;
}
</style>
