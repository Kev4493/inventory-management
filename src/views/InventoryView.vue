<template>
  <div class="inventory">
    <h1>{{ $t('inventory.title') }}</h1>

    <p v-if="loading">{{ $t('inventory.loading') }}</p>
    <p v-else-if="error">{{ error }}</p>

    <Toolbar class="mb-6">
      <template #start>
        <Button label="New" icon="pi pi-plus" class="mr-2" @click="openCreateModal = true" />
        <Button
          label="Delete"
          icon="pi pi-trash"
          severity="danger"
          variant="outlined"
          :disabled="!selectedProducts || !selectedProducts.length"
        />
      </template>

      <template #end> </template>
    </Toolbar>

    <DataTable
      v-model:filters="filters"
      v-model:selection="selectedProducts"
      :value="tableItems"
      paginator
      :rows="10"
      dataKey="id"
      filterDisplay="row"
      :globalFilterFields="[
        'name',
        'category',
        'location',
        'assignedEmployeeName',
        'purchaseDate',
        'notes',
      ]"
      tableStyle="min-width: 50rem"
      rowHover
    >
      <template #header>
        <div class="tableHeader">
          <IconField>
            <InputIcon class="pi pi-search" />
            <InputText v-model="filters.global.value" :placeholder="$t('table.globalFilter')" />
          </IconField>
        </div>
      </template>

      <template #empty>{{ $t('inventory.noItems') }}</template>

      <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>

      <Column
        field="name"
        :header="$t('itemForm.label.productName')"
        style="min-width: 12rem"
        :showFilterMenu="false"
      >
        <template #filter="{ filterModel, filterCallback }">
          <InputText
            v-model="filterModel.value"
            class="filterField"
            type="text"
            :placeholder="$t('itemForm.label.productName')"
            @input="filterCallback()"
          />
        </template>
      </Column>

      <Column
        field="category"
        :header="$t('itemForm.label.category')"
        style="min-width: 12rem"
        :showFilterMenu="false"
      >
        <template #filter="{ filterModel, filterCallback }">
          <Select
            v-model="filterModel.value"
            class="filterField"
            :options="categoryOptions"
            :placeholder="$t('itemForm.label.category')"
            :showClear="true"
            fluid
            @change="filterCallback()"
          />
        </template>
      </Column>

      <Column
        field="location"
        :header="$t('itemForm.label.location')"
        style="min-width: 12rem"
        :showFilterMenu="false"
      >
        <template #filter="{ filterModel, filterCallback }">
          <Select
            v-model="filterModel.value"
            class="filterField"
            :options="locationOptions"
            :placeholder="$t('itemForm.label.location')"
            :showClear="true"
            fluid
            @change="filterCallback()"
          />
        </template>
      </Column>

      <Column
        field="assignedEmployeeName"
        :header="$t('itemForm.label.assigned')"
        style="min-width: 14rem"
        :showFilterMenu="false"
      >
        <template #filter="{ filterModel, filterCallback }">
          <Select
            v-model="filterModel.value"
            class="filterField"
            :options="employeeOptions"
            :placeholder="$t('itemForm.label.assigned')"
            :showClear="true"
            fluid
            @change="filterCallback()"
          />
        </template>
      </Column>

      <Column
        field="purchaseDate"
        :header="$t('itemForm.label.purchaseDate')"
        style="min-width: 10rem"
        :showFilterMenu="false"
      >
        <template #filter="{ filterModel, filterCallback }">
          <Select
            v-model="filterModel.value"
            class="filterField"
            :options="purchaseYearOptions"
            :placeholder="$t('itemForm.label.purchaseDate')"
            :showClear="true"
            fluid
            @change="filterCallback()"
          />
        </template>
      </Column>

      <Column
        field="notes"
        :header="$t('itemForm.label.notes')"
        style="min-width: 14rem"
        :showFilterMenu="false"
      >
        <template #body="{ data }">
          {{ data.notes }}
        </template>
        <template #filter="{ filterModel, filterCallback }">
          <InputText
            v-model="filterModel.value"
            class="filterField"
            type="text"
            :placeholder="$t('itemForm.label.notes')"
            @input="filterCallback()"
          />
        </template>
      </Column>
    </DataTable>

    <!--
      v-model:isOpen verbindet openCreateModal aus InventoryView.vue
      mit defineModel('isOpen') in CreateItemDialog.vue.
    -->
    <CreateItemDialog v-model:isOpen="openCreateModal" />
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { FilterMatchMode } from '@primevue/core/api'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import InputText from 'primevue/inputtext'
import IconField from 'primevue/iconfield'
import InputIcon from 'primevue/inputicon'
import Select from 'primevue/select'
import { allItems, loadAllItems } from '@/stores/inventoryStore.ts'
import { getEmployeeNameById, loadAllEmployees } from '@/stores/employeeStore.ts'
import Button from 'primevue/button'
import Toolbar from 'primevue/toolbar'
import CreateItemDialog from '@/components/CreateItemDialog.vue'

const loading = ref(false)
const error = ref<string | null>(null)
const selectedProducts = ref()
const openCreateModal = ref(false)

const filters = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
  name: { value: null, matchMode: FilterMatchMode.CONTAINS },
  category: { value: null, matchMode: FilterMatchMode.EQUALS },
  location: { value: null, matchMode: FilterMatchMode.EQUALS },
  assignedEmployeeName: { value: null, matchMode: FilterMatchMode.EQUALS },
  purchaseDate: { value: null, matchMode: FilterMatchMode.CONTAINS },
  notes: { value: null, matchMode: FilterMatchMode.CONTAINS },
})

const tableItems = computed(() =>
  allItems.value.map((item) => ({
    ...item,
    assignedEmployeeName: getEmployeeNameById(item.personId),
    notes: item.notes ?? '-',
  })),
)

const categoryOptions = computed(() =>
  [...new Set(tableItems.value.map((item) => item.category))].sort((a, b) => a.localeCompare(b)),
)

const locationOptions = computed(() =>
  [...new Set(tableItems.value.map((item) => item.location))].sort((a, b) => a.localeCompare(b)),
)

const employeeOptions = computed(() =>
  [...new Set(tableItems.value.map((item) => item.assignedEmployeeName))]
    .filter((name) => name !== '-')
    .sort((a, b) => a.localeCompare(b)),
)

const purchaseYearOptions = computed(() =>
  [...new Set(tableItems.value.map((item) => item.purchaseDate))].sort((a, b) => a - b),
)

onMounted(async () => {
  loading.value = true
  error.value = null

  try {
    // Beide API-Requests gleichzeitig starten (parallel, nicht nacheinander)
    // Promise.all wartet bis BEIDE fertig sind, bevor es weitergeht
    await Promise.all([loadAllItems(), loadAllEmployees()])
  } catch (e: any) {
    // Falls einer der Requests fehlschlägt, Fehlermeldung speichern
    // e?.message nimmt die Fehlermeldung des Servers, falls vorhanden
    error.value = e?.message || 'Konnte Daten nicht laden'
    // finally läuft IMMER – egal ob Erfolg oder Fehler
    // Ladezustand deaktivieren, damit die Tabelle oder der Fehler angezeigt wird
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.tableHeader {
  display: flex;
  justify-content: flex-end;
}

:deep(.filterField) {
  width: 100%;
}
</style>
