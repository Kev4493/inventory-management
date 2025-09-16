<script setup lang="ts">
import { allItems } from '@/stores/inventoryStore.ts'
</script>

<template>
  <div class="inventory">
    <h1>Inventarliste</h1>

    <table v-if="allItems.length > 0">
      <thead>
        <tr>
          <th>Produktname</th>
          <th>Kategorie</th>
          <th>Standort</th>
          <th>Person</th>
          <th>Anschaffungsjahr</th>
          <th>Anmerkung</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in allItems" :key="item.id">
          <td>{{ item.name }}</td>
          <td>{{ item.category }}</td>
          <td>{{ item.location }}</td>
          <td>{{ item.person }}</td>
          <td>{{ item.purchaseDate }}</td>
          <td>{{ item.notes }}</td>
        </tr>
      </tbody>
    </table>

    <p v-else>Keine Items vorhanden.</p>
  </div>
</template>

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
