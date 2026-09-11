<template>
  <div class="employeeView">
    <h1>{{ $t('employee.title') }}</h1>

    <p v-if="loading">{{ $t('employee.loading') }}</p>
    <p v-else-if="error">{{ error }}</p>

    <Toolbar class="mb-6">
      <template #start>
        <Button
          label="Hinzufügen"
          icon="pi pi-plus"
          class="mr-2"
          @click="openCreateModal = true"
        />
        <Button
          label="Bearbeiten"
          icon="pi pi-pencil"
          class="mr-2"
          severity="secondary"
          variant="outlined"
          :disabled="selectedEmployees.length !== 1"
          @click="openEditModal = true"
        />
        <Button
          label="Löschen"
          icon="pi pi-trash"
          severity="danger"
          variant="outlined"
          :disabled="!selectedEmployees.length"
          @click="confirmDelete"
        />
      </template>

      <template #end> </template>
    </Toolbar>

    <DataTable
      v-model:filters="filters"
      v-model:selection="selectedEmployees"
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
            <InputText
              v-model="filters.global.value"
              :placeholder="$t('table.globalFilter')"
            />
          </IconField>
        </div>
      </template>

      <template #empty>{{ $t('employee.noEmployee') }}</template>
      <Column
        selectionMode="multiple"
        headerStyle="width: 3rem"
      ></Column>
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

    <!-- ===== Create Modal: =====-->
    <EmployeeDialog
      v-model:isOpen="openCreateModal"
      @saved="handleEmployeeCreated"
    />

    <!-- ===== Edit Modal: =====-->
    <EmployeeDialog
      v-model:isOpen="openEditModal"
      :employee="selectedEmployees[0] ?? null"
      @saved="handleEmployeeUpdated"
    />
    <ConfirmDialog></ConfirmDialog>
  </div>

  <EmployeeDetailDrawer
    v-if="selectedEmployee"
    :employee="selectedEmployee"
    :open="isDrawerOpen"
    @close="isDrawerOpen = false"
  />
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { FilterMatchMode } from '@primevue/core/api';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputIcon from 'primevue/inputicon';
import IconField from 'primevue/iconfield';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import {
  allEmployees,
  deleteEmployees,
  employeesError,
  employeesLoading,
  ensureEmployeesLoaded,
} from '@/stores/employeeStore.ts';
import EmployeeDetailDrawer from '@/components/EmployeeDetailDrawer.vue';
import type { Employee } from '@/types/employee.ts';
import Toolbar from 'primevue/toolbar';
import Button from 'primevue/button';
import EmployeeDialog from '@/components/EmployeeDialog.vue';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';

const { t } = useI18n();
const selectedEmployee = ref<Employee | null>(null);
const isDrawerOpen = ref(false);
const selectedEmployees = ref<Employee[]>([]);
const openCreateModal = ref(false);
const openEditModal = ref(false);
const confirm = useConfirm();
const toast = useToast();

const loading = employeesLoading;
const error = employeesError;

const filters = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
  firstName: { value: null, matchMode: FilterMatchMode.CONTAINS },
  lastName: { value: null, matchMode: FilterMatchMode.CONTAINS },
  typeOfEmployment: { value: null, matchMode: FilterMatchMode.EQUALS },
  department: { value: null, matchMode: FilterMatchMode.EQUALS },
});

const employmentTypeOptions = computed(() => [
  { label: t('employeeForm.employmentType.fullTime'), value: 'fullTime' },
  { label: t('employeeForm.employmentType.partTime'), value: 'partTime' },
  { label: t('employeeForm.employmentType.workingStudent'), value: 'workingStudent' },
  { label: t('employeeForm.employmentType.intern'), value: 'intern' },
  { label: t('employeeForm.employmentType.trainee'), value: 'trainee' },
  { label: t('employeeForm.employmentType.freelancer'), value: 'freelancer' },
]);

const departmentOptions = computed(() => [
  { label: t('employeeForm.department.development'), value: 'development' },
  { label: t('employeeForm.department.marketing'), value: 'marketing' },
  { label: t('employeeForm.department.sales'), value: 'sales' },
  { label: t('employeeForm.department.design'), value: 'design' },
  { label: t('employeeForm.department.management'), value: 'management' },
  { label: t('employeeForm.department.humanResources'), value: 'humanResources' },
]);

function handleEmployeeCreated() {
  toast.add({
    severity: 'success',
    summary: 'Hinzugefügt',
    detail: 'Der Mitarbeiter wurde erfolgreich hinzugefügt.',
    life: 3000,
  });
}

function handleEmployeeUpdated() {
  selectedEmployees.value = [];
  toast.add({
    severity: 'success',
    summary: 'Bearbeitet',
    detail: 'Der Mitarbeiter wurde erfolgreich bearbeitet.',
    life: 3000,
  });
}

onMounted(async () => {
  try {
    await ensureEmployeesLoaded();
  } catch {
    // Der Store stellt die Fehlermeldung für die View bereit.
  }
});

function openEmployeeDetails(event: { data: Employee }) {
  selectedEmployee.value = event.data;
  isDrawerOpen.value = true;
}

async function handleDelete() {
  const ids = selectedEmployees.value.map((employee) => employee.id);

  if (ids.length === 0) return;

  try {
    await deleteEmployees(ids);
    selectedEmployees.value = [];
    toast.add({
      severity: 'success',
      summary: 'Gelöscht',
      detail: `${ids.length} Mitarbeiter wurden gelöscht.`,
      life: 3000,
    });
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Löschen fehlgeschlagen',
      detail: error instanceof Error ? error.message : 'Mitarbeiter konnten nicht gelöscht werden.',
      life: 4000,
    });
  }
}

const confirmDelete = () => {
  confirm.require({
    message: 'Möchtest du die ausgewählten Mitarbeiter löschen?',
    header: 'Mitarbeiter löschen',
    icon: 'pi pi-info-circle',
    rejectLabel: 'Abbrechen',
    rejectProps: {
      label: 'Abbrechen',
      severity: 'secondary',
      outlined: true,
    },
    acceptProps: {
      label: 'Löschen',
      severity: 'danger',
    },
    accept: () => {
      void handleDelete();
    },
  });
};
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
