<template>
  <div class="inventory">
    <h1>{{ $t('inventory.title') }}</h1>

    <p v-if="loading">Lade Items…</p>
    <p v-else-if="error">{{ error }}</p>

    <table v-if="allItems.length > 0">
      <thead>
      <tr>
        <th>{{ $t('itemForm.label.productName') }}</th>
        <th>{{ $t('itemForm.label.category') }}</th>
        <th>{{ $t('itemForm.label.location') }}:</th>
        <th>{{ $t('itemForm.label.assigned') }}:</th>
        <th>{{ $t('itemForm.label.purchaseDate') }}:</th>
        <th>{{ $t('itemForm.label.notes') }}:</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="item in allItems" :key="item.id">
        <td>{{ item.name }}</td>
        <td>{{ item.category }}</td>
        <td>{{ item.location }}</td>
        <td>{{ item.person ?? '—' }}</td>
        <td>{{ item.purchaseDate }}</td>
        <td>{{ item.notes ?? '—' }}</td>
      </tr>
      </tbody>
    </table>

    <p v-else>{{ $t('inventory.noItems') }}</p>
  </div>
</template>

<script setup lang="ts">
import { allItems, loadAllItems } from '@/stores/inventoryStore.ts'
import { onMounted, ref } from 'vue'

const loading = ref(false)
const error = ref<string | null>(null)

onMounted(async () => {
  loading.value = true
  error.value = null

  try {
    await loadAllItems()
  } catch (e: any) {
    error.value = e?.message || 'Konnte Items nicht laden'
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 1rem;
}

th,
td {
  padding: 0.75rem;
  text-align: left;
}

th {
  background-color: #f0f0f0;
}

tr {
  margin: 2rem;
  background-color: lightblue;
  border-radius: 12px;
}

tr:nth-child(even) {
  background-color: #fafafa;
}
</style>
