<template>
  <div class="inventory">
    <h1>Mitarbeiterliste</h1>

    <p v-if="loading">Lade Items…</p>
    <p v-else-if="error">{{ error }}</p>

    <table v-if="allEmployees.length > 0">
      <thead>
        <tr>
          <th>{{ $t('employeeForm.label.firstName') }}</th>
          <th>{{ $t('employeeForm.label.lastName') }}</th>
          <th>{{ $t('employeeForm.label.department') }}</th>
          <th>{{ $t('employeeForm.label.employmentType') }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="employee in allEmployees" :key="employee.id">
          <td>{{ employee.firstName }}</td>
          <td>{{ employee.lastName }}</td>
          <td>{{ employee.department }}</td>
          <td>{{ employee.typeOfEmployment }}</td>
        </tr>
      </tbody>
    </table>

    <p v-else>Keine Mitarbeiter gefunden</p>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { allEmployees, loadAllEmployees } from '@/stores/employeeStore.ts'

const loading = ref(false)
const error = ref<string | null>(null)

onMounted(async () => {
  loading.value = true
  error.value = null

  try {
    await loadAllEmployees()
  } catch (e: any) {
    error.value = e?.message || 'Konnte Items nicht laden'
  } finally {
    loading.value = false
  }
})
</script>

<style scoped lang="scss">
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
