<template>
  <div class="card flex justify-center">
    <Dialog
      v-model:visible="isOpen"
      modal
      :header="employee ? 'Mitarbeiter bearbeiten' : 'Neuer Mitarbeiter'"
      :style="{ width: '50rem' }"
    >
      <EmployeeForm
        :employee="employee"
        @saved="handleSaved"
      />
    </Dialog>
  </div>
</template>

<script setup lang="ts">
import Dialog from 'primevue/dialog';
import EmployeeForm from '@/components/EmployeeForm.vue';
import type { Employee } from '@/types/employee.ts';

defineProps<{
  employee?: Employee | null;
}>();

// Verbindet isOpen mit v-model:isOpen aus der Parent-Komponente.
// Dadurch hat isOpen ein 'true' oder 'false' und der Dialog wird angezeigt oder nicht.
const isOpen = defineModel<boolean>('isOpen');

const emit = defineEmits<{
  saved: [];
}>();

function handleSaved() {
  isOpen.value = false;
  emit('saved');
}
</script>
