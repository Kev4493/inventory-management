<template>
  <div class="employeeView">
    <h1>Mitarbeiterliste</h1>

    <p v-if="loading">Lade Mitarbeiter...</p>
    <p v-else-if="error">{{ error }}</p>

    <DataTable
      v-else-if="allEmployees.length > 0"
      v-model:filters="filters"
      :value="allEmployees"
      @row-click="openEmployeeDetails"
      paginator
      :rows="10"
      dataKey="id"
      filterDisplay="row"
      :globalFilterFields="['firstName', 'lastName', 'department', 'typeOfEmployment']"
      tableStyle="min-width: 50rem"
      rowHover
    >
      <template #header>
        <div class="tableHeader">
          <IconField>
            <InputIcon class="pi pi-search" />
            <InputText v-model="filters.global.value" placeholder="Suche in allen Spalten" />
          </IconField>
        </div>
      </template>

      <template #empty>Keine Mitarbeiter gefunden</template>

      <Column
        field="firstName"
        :header="$t('employeeForm.label.firstName')"
        style="min-width: 12rem"
        :showFilterMenu="false"
      >
        <template #filter="{ filterModel, filterCallback }">
          <InputText
            v-model="filterModel.value"
            class="filterField"
            type="text"
            :placeholder="$t('employeeForm.label.firstName')"
            @input="filterCallback()"
          />
        </template>
      </Column>

      <Column
        field="lastName"
        :header="$t('employeeForm.label.lastName')"
        style="min-width: 12rem"
        :showFilterMenu="false"
      >
        <template #filter="{ filterModel, filterCallback }">
          <InputText
            v-model="filterModel.value"
            class="filterField"
            type="text"
            :placeholder="$t('employeeForm.label.lastName')"
            @input="filterCallback()"
          />
        </template>
      </Column>

      <Column
        field="typeOfEmployment"
        :header="$t('employeeForm.label.employmentType')"
        style="min-width: 14rem"
        :showFilterMenu="false"
      >
        <template #body="{ data }">
          {{ $t(`employeeForm.employmentType.${data.typeOfEmployment}`) }}
        </template>
        <template #filter="{ filterModel, filterCallback }">
          <Select
            v-model="filterModel.value"
            class="filterField"
            :options="employmentTypeOptions"
            optionLabel="label"
            optionValue="value"
            :placeholder="$t('employeeForm.label.employmentType')"
            :showClear="true"
            fluid
            @change="filterCallback()"
          />
        </template>
      </Column>

      <Column
        field="department"
        :header="$t('employeeForm.label.department')"
        style="min-width: 14rem"
        :showFilterMenu="false"
      >
        <template #body="{ data }">
          {{ $t(`employeeForm.department.${data.department}`) }}
        </template>
        <template #filter="{ filterModel, filterCallback }">
          <Select
            v-model="filterModel.value"
            class="filterField"
            :options="departmentOptions"
            optionLabel="label"
            optionValue="value"
            :placeholder="$t('employeeForm.label.department')"
            :showClear="true"
            fluid
            @change="filterCallback()"
          />
        </template>
      </Column>
    </DataTable>

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
import { computed, onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { FilterMatchMode } from '@primevue/core/api'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import InputIcon from 'primevue/inputicon'
import IconField from 'primevue/iconfield'
import InputText from 'primevue/inputtext'
import Select from 'primevue/select'
import { allEmployees, loadAllEmployees } from '@/stores/employeeStore.ts'
import EmployeeDetailDrawer from '@/components/EmployeeDetailDrawer.vue'
import type { Employee } from '@/types/employee.ts'

const { t } = useI18n()
const loading = ref(false)
const error = ref<string | null>(null)
const selectedEmployee = ref<Employee | null>(null)
const isDrawerOpen = ref(false)

const filters = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
  firstName: { value: null, matchMode: FilterMatchMode.CONTAINS },
  lastName: { value: null, matchMode: FilterMatchMode.CONTAINS },
  typeOfEmployment: { value: null, matchMode: FilterMatchMode.EQUALS },
  department: { value: null, matchMode: FilterMatchMode.EQUALS },
})

const employmentTypeOptions = computed(() => [
  { label: t('employeeForm.employmentType.fullTime'), value: 'fullTime' },
  { label: t('employeeForm.employmentType.partTime'), value: 'partTime' },
  { label: t('employeeForm.employmentType.workingStudent'), value: 'workingStudent' },
  { label: t('employeeForm.employmentType.intern'), value: 'intern' },
  { label: t('employeeForm.employmentType.trainee'), value: 'trainee' },
  { label: t('employeeForm.employmentType.freelancer'), value: 'freelancer' },
])

const departmentOptions = computed(() => [
  { label: t('employeeForm.department.development'), value: 'development' },
  { label: t('employeeForm.department.marketing'), value: 'marketing' },
  { label: t('employeeForm.department.sales'), value: 'sales' },
  { label: t('employeeForm.department.design'), value: 'design' },
  { label: t('employeeForm.department.management'), value: 'management' },
  { label: t('employeeForm.department.humanResources'), value: 'humanResources' },
])

onMounted(async () => {
  loading.value = true
  error.value = null

  try {
    await loadAllEmployees()
  } catch (e: any) {
    error.value = e?.message || 'Konnte Mitarbeiter nicht laden'
  } finally {
    loading.value = false
  }
})

function openEmployeeDetails(event: { data: Employee }) {
  selectedEmployee.value = event.data
  isDrawerOpen.value = true
}
</script>

<style scoped lang="scss">
.tableHeader {
  display: flex;
  justify-content: flex-end;
}

:deep(.filterField) {
  width: 100%;
}

:deep(.p-datatable-tbody > tr) {
  cursor: pointer;
}
</style>
