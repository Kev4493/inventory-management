<template>
  <form
    @submit.prevent="handleSubmit"
    class="addItemForm"
  >
    <!-- ===== Inventar-Nr.: ===== -->
    <div>
      <label for="inventoryNumber">{{ $t('itemForm.label.inventoryNumber') }}:</label>
      <InputText
        id="inventoryNumber"
        type="text"
        v-model="newItem.inventoryNumber"
        name="inventoryNumber"
        required
      />
    </div>

    <!-- ===== Produktname: ===== -->
    <div>
      <label for="itemName">{{ $t('itemForm.label.productName') }}:</label>
      <InputText
        id="itemName"
        name="itemName"
        type="text"
        v-model="newItem.name"
        required
      />
    </div>

    <div class="imageField">
      <label>Produktbild:</label>
      <div
        v-if="props.item?.imageUrl && !selectedImage && !removeExistingImage"
        class="flex items-start gap-2"
      >
        <Image :src="props.item.imageUrl" alt="Vorhandenes Produktbild" width="160" preview />
        <Button
          type="button"
          icon="pi pi-trash"
          severity="danger"
          text
          rounded
          aria-label="Vorhandenes Produktbild entfernen"
          @click="removeStoredImage"
        />
      </div>
      <FileUpload
        ref="fileUpload"
        mode="advanced"
        name="image"
        chooseLabel="Bild auswählen"
        accept="image/jpeg,image/png,image/webp"
        :multiple="false"
        :fileLimit="1"
        :maxFileSize="5242880"
        invalidFileTypeMessage="{0}: Bitte JPEG, PNG oder WebP auswählen."
        invalidFileSizeMessage="{0}: Die Datei darf höchstens 5 MB groß sein."
        :customUpload="true"
        :showUploadButton="false"
        :showCancelButton="false"
        :previewWidth="160"
        :pt="{
          pcFileBadge: { root: { style: 'display: none' } },
          pcFileRemoveButton: { root: { 'aria-label': 'Ausgewähltes Bild verwerfen' } },
        }"
        @select="onImageSelect"
        @remove="onImageRemove"
      >
        <template #empty>Bild hierher ziehen oder oben auswählen.</template>
      </FileUpload>
      <small>JPEG, PNG oder WebP, maximal 5 MB.</small>
    </div>

    <!-- ===== Kategorie: ===== -->
    <div class="flex flex-col">
      <label for="category">{{ $t('itemForm.label.category') }}:</label>
      <Select
        id="category"
        name="category"
        v-model="newItem.category"
        :options="categories"
        optionLabel="name"
        optionValue="name"
        :placeholder="$t('itemForm.placeholder.chooseCategory')"
      />
    </div>

    <!-- ===== Standort: ===== -->
    <div class="flex flex-col">
      <label for="location">{{ $t('itemForm.label.location') }}:</label>
      <Select
        id="location"
        name="location"
        v-model="newItem.location"
        :options="locations"
        optionLabel="name"
        optionValue="name"
        :placeholder="$t('itemForm.placeholder.location')"
      />
    </div>

    <!-- ===== Zugewiesen an: ===== -->
    <div class="flex flex-col">
      <label for="person">{{ $t('itemForm.label.assigned') }}:</label>
      <Select
        id="person"
        name="person"
        v-model="newItem.personId"
        :options="employeeOptions"
        optionLabel="label"
        optionValue="value"
        :disabled="loading"
        :placeholder="$t('itemForm.label.assigned')"
      />
    </div>

    <!-- ===== Anschaffungsjahr: ===== -->
    <div class="flex flex-col">
      <label for="purchaseDate">{{ $t('itemForm.label.purchaseDate') }}:</label>
      <InputNumber
        id="purchaseDate"
        name="purchaseDate"
        v-model="newItem.purchaseDate"
        :min="2000"
        :max="new Date().getFullYear()"
        :useGrouping="false"
        required
      />
    </div>

    <div>
      <label for="notes">{{ $t('itemForm.label.notes') }}:</label>
      <Textarea
        id="notes"
        name="notes"
        v-model="newItem.notes"
        rows="4"
        autoResize
      />
    </div>

    <button
      type="submit"
      :disabled="isSaving"
      aria-busy="true"
    >
      {{ item ? $t('itemForm.button.saveItem') : $t('itemForm.button.addItem') }}
    </button>
  </form>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { addItem, updateItem } from '@/stores/inventoryStore.ts';
import type { Item } from '@/types/item.ts';
import {
  allEmployees,
  employeesLoading,
  ensureEmployeesLoaded,
} from '@/stores/employeeStore.ts';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import InputNumber from 'primevue/inputnumber';
import Textarea from 'primevue/textarea';
import FileUpload from 'primevue/fileupload';
import Image from 'primevue/image';
import Button from 'primevue/button';
import type { FileUploadSelectEvent } from 'primevue/fileupload';

const props = defineProps<{
  item?: Item | null;
}>();

const emit = defineEmits<{
  saved: [];
}>();

const emptyItem = (): Omit<Item, 'id'> => ({
  inventoryNumber: '',
  name: '',
  category: '',
  location: '',
  personId: null,
  purchaseDate: new Date().getFullYear(),
  notes: null,
  imageUrl: null,
});

// Reaktives Objekt für ein neu hinzuzufügendes Item (wird direkt durch V-model befüllt)
const newItem = reactive<Omit<Item, 'id'>>(emptyItem());
const selectedImage = ref<File | null>(null);
const removeExistingImage = ref(false);
const fileUpload = ref<{ clear: () => void } | null>(null);

function onImageSelect(event: FileUploadSelectEvent) {
  const file = (event.files as File[])[0];
  if (!file) return;

  clearSelectedImage();
  selectedImage.value = file;
  removeExistingImage.value = false;
}

function onImageRemove(event: { file: File }) {
  if (event.file === selectedImage.value) {
    clearSelectedImage();
  }
}

function removeStoredImage() {
  removeExistingImage.value = true;
}

function clearSelectedImage() {
  const objectUrl = (selectedImage.value as File & { objectURL?: string } | null)?.objectURL;
  if (objectUrl) URL.revokeObjectURL(objectUrl);
  selectedImage.value = null;
}

watch(
  () => props.item,
  (item) => {
    clearSelectedImage();
    removeExistingImage.value = false;
    fileUpload.value?.clear();
    if (item) {
      Object.assign(newItem, {
        inventoryNumber: item.inventoryNumber,
        name: item.name,
        category: item.category,
        location: item.location,
        personId: item.personId,
        purchaseDate: item.purchaseDate,
        notes: item.notes,
        imageUrl: item.imageUrl,
      });
    } else {
      Object.assign(newItem, emptyItem());
    }
  },
  { immediate: true },
);

const categories = ref([
  { name: 'Laptop', code: 'laptop' },
  { name: 'Bildschirm', code: 'monitor' },
  { name: 'Tastatur', code: 'keyboard' },
  { name: 'Maus', code: 'mouse' },
  { name: 'Docking station', code: 'docking_station' },
  { name: 'Headset', code: 'headset' },
  { name: 'Fernseher', code: 'tv' },
  { name: 'Kamera', code: 'camera' },
  { name: 'Laptop Ständer', code: 'laptop_stand' },
  { name: 'Beamer', code: 'projector' },
]);

const locations = ref([
  { name: 'Besprechungsraum', code: 'besprechungsraum' },
  { name: 'Eingangsbereich', code: 'eingangsbereich' },
  { name: 'Küche', code: 'küche' },
  { name: 'Online Marketing', code: 'om' },
  { name: 'PM-Büro', code: 'pm' },
  { name: 'Konfetti', code: 'konfetti' },
  { name: 'Messe', code: 'messe' },
  { name: 'GF-Büro', code: 'gf' },
  { name: 'Kleines Büro', code: 'kleines_buero' },
  { name: 'Home-Office', code: 'home_office' },
]);

const isSaving = ref(false);
const loading = employeesLoading;

onMounted(async () => {
  try {
    await ensureEmployeesLoaded();
  } catch {
    // Der Store hält den Fehlerzustand; das Formular bleibt weiterhin bedienbar.
  }
});

async function handleSubmit() {
  if (isSaving.value) return;
  isSaving.value = true;

  try {
    const { imageUrl: _imageUrl, ...payload } = newItem;
    if (props.item) {
      await updateItem(props.item.id, payload, selectedImage.value, removeExistingImage.value);
    } else {
      await addItem(payload, selectedImage.value);
    }

    Object.assign(newItem, emptyItem());
    clearSelectedImage();
    removeExistingImage.value = false;
    fileUpload.value?.clear();
    emit('saved');
  } catch (e: unknown) {
    console.error(e);
    alert(e instanceof Error ? e.message : 'Fehler beim Speichern');
  } finally {
    isSaving.value = false;
  }
}

const employeeOptions = computed(() =>
  allEmployees.value.map((employee) => ({
    label: `${employee.firstName} ${employee.lastName}`,
    value: employee.id,
  })),
);
</script>

<style scoped lang="scss">
.addItemForm {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  width: 100%;
  padding: 2rem;

  input,
  textarea,
  select {
    padding: 0.5rem;
    border-radius: 4px;
    width: 100%;
    box-sizing: border-box;
  }

  > button[type='submit'] {
    padding: 0.5rem;
    background-color: $brandYellowBG;
    color: $mainBlackText;
    border: none;
    margin-left: auto;
    margin-right: auto;
    margin-top: 2rem;
    cursor: pointer;
    width: 200px;

    &:hover {
      background-color: #218838;
    }
  }

  > button[type='submit'][disabled] {
    opacity: 0.6;
    cursor: not-allowed;
  }
}

.imageField {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
</style>
