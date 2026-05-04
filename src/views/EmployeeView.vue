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
        <tr
          v-for="employee in allEmployees"
          :key="employee.id"
          @click="openEmployeeDetails(employee)"
        >
          <td>{{ employee.firstName }}</td>
          <td>{{ employee.lastName }}</td>
          <td>{{ $t(`employeeForm.department.${employee.department}`) }}</td>
          <td>{{ $t(`employeeForm.employmentType.${employee.typeOfEmployment}`) }}</td>
        </tr>
      </tbody>
    </table>

    <p v-else>Keine Mitarbeiter gefunden</p>
  </div>

  <EmployeeDetailDrawer
    v-if="selectedEmployee"
    :employee="selectedEmployee"
    :open="isDrawerOpen"
    @close="isDrawerOpen = false"
  />
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { allEmployees, loadAllEmployees } from '@/stores/employeeStore.ts'
import EmployeeDetailDrawer from '@/components/EmployeeDetailDrawer.vue'
import type { Employee } from '@/types/employee.ts'

const loading = ref(false)
const error = ref<string | null>(null)

const selectedEmployee = ref<Employee | null>(null)
const isDrawerOpen = ref(false)

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

function openEmployeeDetails(employee: Employee) {
  selectedEmployee.value = employee
  isDrawerOpen.value = true
}
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
  transition: background-color 0.2s ease;
}

tbody {
  tr {
    cursor: pointer;
  }

  tr:hover {
    background-color: #d7eef7;
  }
}

tr:nth-child(even) {
  background-color: #fafafa;
}
</style>
