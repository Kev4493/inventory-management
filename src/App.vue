<script setup lang="ts">
import { ref } from 'vue'
import { RouterView } from 'vue-router'
import Sidebar from '@/components/Sidebar.vue'

const itemsMsg = ref('items noch nix')

// Testfunktion zum Laden der Items von /api/items
async function loadItems() {
  try {
    const res = await fetch('/api/items')
    const json = await res.json()
    console.log('Items:', json) // hilft beim Debuggen

    if (Array.isArray(json) && json.length > 0) {
      itemsMsg.value = json.map((i: any) => i.name).join(', ')
    } else {
      itemsMsg.value = '(keine Items gefunden)'
    }
  } catch (e) {
    itemsMsg.value = 'Fehler bei /api/items'
  }
}
</script>

<template>
  <div class="app-layout">
    <Sidebar></Sidebar>
    <main class="content">
      <!--Test -->
      <div class="dev-test">
        <button @click="loadItems">/api/items laden</button>
        <span class="msg">{{ itemsMsg }}</span>
      </div>
      <RouterView />
    </main>
  </div>
</template>

<style scoped lang="scss">
// Test styling für die /api/items Ladefunktion
.dev-test {
  display: flex;
  gap: 0.75rem;
  align-items: center;
  margin: 1rem 0;
}

.app-layout {
  display: flex;
}

.content {
  margin-left: calc(200px);
  padding: 0 2rem;
  width: 100%;
}
</style>
