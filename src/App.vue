<template>
  <div class="app-layout">
    <Sidebar></Sidebar>
    <main class="content">
      <RouterView />
    </main>
  </div>
</template>

<script setup lang="ts">
import { RouterView } from 'vue-router'
import Sidebar from '@/components/Sidebar.vue'
</script>

<style scoped lang="scss">
.app-layout {
  display: flex;
}
.content {
  margin-left: calc(200px);
  padding: 0 2rem;
  width: 100%;
}
</style>
