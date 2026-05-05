<template>
  <div v-if="open" class="drawerOverlay" @click="$emit('close')">
    <aside class="drawer" @click.stop>
      <button class="closeButton" type="button" @click="$emit('close')">X</button>

      <div v-if="employee">
        <h2>{{ employee.firstName }} {{ employee.lastName }}</h2>

        <div class="drawer__profileSection">
          <div class="drawer__profile">
            <img
              class="drawer__profileImg"
              src="../assets/Images/examples/premium_photo-1682096259050-361e2989706d.avif"
              alt=""
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
        <section class="drawer__section">
          <p class="drawer__sectionTitle">Zugewiesene Items</p>

          <div v-if="assignedItems.length > 0" class="drawer__infoList">
            <div v-for="item in assignedItems" :key="item.id" class="drawer__infoRow">
              <span class="drawer__infoLabel">{{ item.category }}</span>
              <span class="drawer__infoValue">{{ item.name }}</span>
            </div>
          </div>

          <p v-else class="drawer__note">Keine Items zugewiesen.</p>
        </section>

        <!-- ===== Contact Informations ===== -->
        <div class="drawer__content">
          <section class="drawer__section">
            <p class="drawer__sectionTitle">Kontakt</p>

            <div class="drawer__infoList">
              <div class="drawer__infoRow">
                <span class="drawer__infoLabel">E-Mail</span>
                <span class="drawer__infoValue">{{ employee.emailAddress }}</span>
              </div>
            </div>
          </section>

          <section class="drawer__section">
            <p class="drawer__sectionTitle">Adresse</p>
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
          </section>

          <!-- ===== Job Informations ===== -->
          <section class="drawer__section">
            <p class="drawer__sectionTitle">Beschäftigung</p>

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
          </section>

          <section class="drawer__section">
            <p class="drawer__sectionTitle">Notizen</p>
            <p class="drawer__note">{{ employee.notes ?? 'Keine Notizen hinterlegt.' }}</p>
          </section>
        </div>
      </div>
    </aside>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue'
import type { Employee } from '@/types/employee.ts'
import { allItems, loadAllItems } from '@/stores/inventoryStore.ts'

const props = defineProps<{
  employee: Employee | null
  open: boolean
}>()

defineEmits<{
  close: []
}>()

// Alle Items filtern, die diesem Mitarbeiter zugewiesen sind
// computed reagiert automatisch wenn sich allItems oder employee ändert
const assignedItems = computed(() =>
  allItems.value.filter((item) => item.personId === props.employee?.id),
)

onMounted(async () => {
  // Items nur laden falls noch nicht vorhanden
  if (allItems.value.length === 0) {
    await loadAllItems()
  }
})
</script>

<style scoped lang="scss">
.drawerOverlay {
  position: fixed;
  inset: 0;
  z-index: 1000;
  display: flex;
  justify-content: flex-end;
  background: rgba(0, 0, 0, 0.35);
}

.drawer {
  width: min(480px, 100%);
  height: 100vh;
  overflow-y: auto;
  background: $drawerOffwhiteBG;
  box-shadow: -18px 0 44px rgba(15, 23, 42, 0.16);
  border-radius: 24px 0 0 24px;
  padding: 2.5rem 2.25rem;
  animation: slideIn 0.25s ease-out;
}

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

.closeButton {
  display: block;
  margin-left: auto;
  margin-bottom: 1rem;
  border: none;
  background: transparent;
  font-size: 1.25rem;
  cursor: pointer;
}

.drawer__content {
  display: grid;
  gap: 1rem;
  padding-top: 1.5rem;
}

.drawer__section {
  padding: 1rem 1.1rem;
  border: 1px solid $lightGrayBG;
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.55);
}

.drawer__sectionTitle {
  margin: 0 0 1rem;
  color: #6b7280;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
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
  color: #6b7280;
  font-size: 0.92rem;
}

.drawer__infoValue {
  color: #111827;
  font-size: 0.95rem;
  font-weight: 600;
  text-align: right;
}

.drawer__note {
  margin: 0;
  color: #1f2937;
  line-height: 1.6;
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
  }

  to {
    transform: translateX(0);
  }
}
</style>
