<template>
  <Drawer
    :visible="open"
    position="right"
    style="width: min(480px, 100vw)"
    @update:visible="handleVisibleChange"
  >
    <div v-if="employee">
      <h1>{{ employee.firstName }} {{ employee.lastName }}</h1>

      <div class="drawer__profileSection">
        <div class="drawer__profile">
          <img
            class="drawer__profileImg"
            src="../assets/Images/examples/premium_photo-1682096259050-361e2989706d.avif"
            :alt="`Profilbild von ${employee.firstName} ${employee.lastName}`"
          />
        </div>

        <div class="drawer__meta">
          <span class="drawer__metaTag">{{
            $t(`employeeForm.department.${employee.department}`)
          }}</span>
          <span class="drawer__metaTag">{{
            $t(`employeeForm.employmentType.${employee.typeOfEmployment}`)
          }}</span>
        </div>
      </div>

      <!-- ===== Assigned items ===== -->
      <Card class="employeeCard">
        <template #title>Zugewiesene Items</template>
        <template #content>
          <div
            v-if="assignedItems.length > 0"
            class="drawer__infoList"
          >
            <div
              v-for="item in assignedItems"
              :key="item.id"
              class="drawer__infoRow"
            >
              <span class="drawer__infoLabel">{{ item.category }}</span>
              <span class="drawer__infoValue">{{ item.name }}</span>
            </div>
          </div>

          <p
            v-else
            class="drawer__note"
          >
            Keine Items zugewiesen.
          </p>
        </template>
      </Card>

      <!-- ===== Contact Informations ===== -->
      <div class="drawer__content">
        <Card class="employeeCard">
          <template #title>Kontakt</template>
          <template #content>
            <div class="drawer__infoList">
              <div class="drawer__infoRow">
                <span class="drawer__infoLabel">E-Mail</span>
                <span class="drawer__infoValue">{{ employee.emailAddress }}</span>
              </div>
            </div>
          </template>
        </Card>

        <!-- ===== Address Informations ===== -->
        <Card class="employeeCard">
          <template #title>Adresse</template>
          <template #content>
            <div class="drawer__infoList">
              <div class="drawer__infoRow">
                <span class="drawer__infoLabel">Straße</span>
                <span class="drawer__infoValue">{{ employee.street }}</span>
              </div>
              <div class="drawer__infoRow">
                <span class="drawer__infoLabel">PLZ</span>
                <span class="drawer__infoValue">{{ employee.zipCode }}</span>
              </div>
              <div class="drawer__infoRow">
                <span class="drawer__infoLabel">Ort</span>
                <span class="drawer__infoValue">{{ employee.city }}</span>
              </div>
            </div>
          </template>
        </Card>

        <!-- ===== Job Informations ===== -->
        <Card class="employeeCard">
          <template #title>Beschäftigung</template>
          <template #content>
            <div class="drawer__infoList">
              <div class="drawer__infoRow">
                <span class="drawer__infoLabel">Eintritt</span>
                <span class="drawer__infoValue">{{ employee.dateOfEntry }}</span>
              </div>

              <div class="drawer__infoRow">
                <span class="drawer__infoLabel">Austritt</span>
                <span class="drawer__infoValue">{{ employee.dateOfLeaving ?? '-' }}</span>
              </div>
            </div>
          </template>
        </Card>

        <!-- ===== Notes ===== -->
        <Card class="employeeCard">
          <template #title>Notizen</template>
          <template #content>
            <p class="drawer__note">{{ employee.notes ?? 'Keine Notizen hinterlegt.' }}</p>
          </template>
        </Card>
      </div>
    </div>
  </Drawer>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue';
import Drawer from 'primevue/drawer';
import type { Employee } from '@/types/employee.ts';
import { allItems, ensureItemsLoaded } from '@/stores/inventoryStore.ts';
import Card from 'primevue/card';

const props = defineProps<{
  employee: Employee | null;
  open: boolean;
}>();

const emit = defineEmits<{
  close: [];
}>();

function handleVisibleChange(visible: boolean) {
  if (!visible) {
    emit('close');
  }
}

// Alle Items filtern, die diesem Mitarbeiter zugewiesen sind
// computed reagiert automatisch wenn sich allItems oder employee ändert
const assignedItems = computed(() =>
  allItems.value.filter((item) => item.personId === props.employee?.id),
);

onMounted(async () => {
  try {
    await ensureItemsLoaded();
  } catch {
    // Der Inventory-Store hält den Fehlerzustand.
  }
});
</script>

<style scoped lang="scss">
.drawer__profileSection {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.drawer__profile {
  width: 100%;
  height: 300px;
  border-radius: 12px;
  overflow: hidden;
}

.drawer__profileImg {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.drawer__meta {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
}

.drawer__metaTag {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0.45rem 0.85rem;
  border-radius: 999px;
  background: $lightGrayBG;
  color: #3b3125;
  font-size: 0.85rem;
  font-weight: 600;
}

.drawer__content {
  display: grid;
  gap: 1rem;
  padding-top: 1.5rem;
}

.drawer__infoList {
  display: grid;
  gap: 0.85rem;
}

.drawer__infoRow {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
}

.drawer__infoLabel {
  font-size: 0.92rem;
}

.drawer__infoValue {
  font-size: 0.95rem;
  text-align: right;
}

.drawer__note {
  margin: 0;
  line-height: 1.6;
}

.employeeCard {
  border: 1px solid var(--p-content-border-color);
}
</style>
