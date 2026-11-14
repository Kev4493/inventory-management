<template>
  <form
    class="flex flex-col gap-6"
    novalidate
    @submit.prevent="handleSubmit"
  >
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
      <!-- ===== Inventar-Nr. ===== -->
      <div class="flex flex-col gap-2">
        <label
          for="inventoryNumber"
          class="font-medium"
        >
          {{ $t('itemForm.label.inventoryNumber') }} <span class="text-red-500">*</span>
        </label>
        <InputText
          id="inventoryNumber"
          v-model="newItem.inventoryNumber"
          :invalid="showError('inventoryNumber')"
          fluid
        />
        <Message
          v-if="showError('inventoryNumber')"
          severity="error"
          size="small"
          variant="simple"
        >
          {{ $t('form.validation.required') }}
        </Message>
      </div>

      <!-- ===== Produktname ===== -->
      <div class="flex flex-col gap-2">
        <label
          for="itemName"
          class="font-medium"
        >
          {{ $t('itemForm.label.productName') }} <span class="text-red-500">*</span>
        </label>
        <InputText
          id="itemName"
          v-model="newItem.name"
          :invalid="showError('name')"
          fluid
        />
        <Message
          v-if="showError('name')"
          severity="error"
          size="small"
          variant="simple"
        >
          {{ $t('form.validation.required') }}
        </Message>
      </div>

      <!-- ===== Kategorie ===== -->
      <div class="flex flex-col gap-2">
        <label
          for="category"
          class="font-medium"
        >
          {{ $t('itemForm.label.category') }} <span class="text-red-500">*</span>
        </label>
        <Select
          v-model="newItem.category"
          inputId="category"
          :options="categories"
          optionLabel="name"
          optionValue="name"
          :placeholder="$t('itemForm.placeholder.chooseCategory')"
          :invalid="showError('category')"
          fluid
        />
        <Message
          v-if="showError('category')"
          severity="error"
          size="small"
          variant="simple"
        >
          {{ $t('form.validation.required') }}
        </Message>
      </div>

      <!-- ===== Standort ===== -->
      <div class="flex flex-col gap-2">
        <label
          for="location"
          class="font-medium"
        >
          {{ $t('itemForm.label.location') }} <span class="text-red-500">*</span>
        </label>
        <Select
          v-model="newItem.location"
          inputId="location"
          :options="locations"
          optionLabel="name"
          optionValue="name"
          :placeholder="$t('itemForm.placeholder.location')"
          :invalid="showError('location')"
          fluid
        />
        <Message
          v-if="showError('location')"
          severity="error"
          size="small"
          variant="simple"
        >
          {{ $t('form.validation.required') }}
        </Message>
      </div>

      <!-- ===== Zugewiesen an ===== -->
      <div class="flex flex-col gap-2">
        <label
          for="person"
          class="font-medium"
        >
          {{ $t('itemForm.label.assigned') }}
        </label>
        <Select
          v-model="newItem.personId"
          inputId="person"
          :options="employeeOptions"
          optionLabel="label"
          optionValue="value"
          :loading="employeesLoading"
          :disabled="employeesLoading"
          :placeholder="$t('itemForm.placeholder.assigned')"
          showClear
          filter
          fluid
        />
      </div>

      <!-- ===== Anschaffungsjahr ===== -->
      <div class="flex flex-col gap-2">
        <label
          for="purchaseDate"
          class="font-medium"
        >
          {{ $t('itemForm.label.purchaseDate') }} <span class="text-red-500">*</span>
        </label>
        <InputNumber
          v-model="newItem.purchaseDate"
          inputId="purchaseDate"
          :min="2000"
          :max="currentYear"
          :useGrouping="false"
          :invalid="showError('purchaseDate')"
          fluid
        />
        <Message
          v-if="showError('purchaseDate')"
          severity="error"
          size="small"
          variant="simple"
        >
          {{ $t('form.validation.required') }}
        </Message>
      </div>

      <!-- ===== Produktbild ===== -->
      <div class="flex flex-col gap-2 sm:col-span-2">
        <span class="font-medium">{{ $t('itemForm.label.image') }}</span>
        <div
          v-if="props.item?.imageUrl && !selectedImage && !removeExistingImage"
          class="flex items-start gap-2"
        >
          <Image
            :src="props.item.imageUrl"
            :alt="$t('itemForm.image.existingAlt')"
            width="160"
            preview
          />
          <Button
            type="button"
            icon="pi pi-trash"
            severity="danger"
            text
            rounded
            :aria-label="$t('itemForm.image.removeExisting')"
            @click="removeStoredImage"
          />
        </div>
        <FileUpload
          ref="fileUpload"
          mode="advanced"
          name="image"
          :chooseLabel="$t('itemForm.image.choose')"
          accept="image/jpeg,image/png,image/webp"
          :multiple="false"
          :fileLimit="1"
          :maxFileSize="5242880"
          :invalidFileTypeMessage="$t('itemForm.image.invalidFileType', ['{0}'])"
          :invalidFileSizeMessage="$t('itemForm.image.invalidFileSize', ['{0}'])"
          :customUpload="true"
          :showUploadButton="false"
          :showCancelButton="false"
          :previewWidth="160"
          :pt="{
            pcFileBadge: { root: { style: 'display: none' } },
            pcFileRemoveButton: { root: { 'aria-label': $t('itemForm.image.discardSelected') } },
          }"
          @select="onImageSelect"
          @remove="onImageRemove"
        >
          <template #empty>{{ $t('itemForm.image.empty') }}</template>
        </FileUpload>
        <Message
          severity="secondary"
          size="small"
          variant="simple"
        >
          {{ $t('itemForm.image.hint') }}
        </Message>
      </div>

      <!-- ===== Anmerkungen ===== -->
      <div class="flex flex-col gap-2 sm:col-span-2">
        <label
          for="notes"
          class="font-medium"
        >
          {{ $t('itemForm.label.notes') }}
        </label>
        <Textarea
          id="notes"
          v-model="newItem.notes"
          rows="4"
          autoResize
          fluid
        />
      </div>
    </div>

    <div class="flex items-center justify-between gap-4">
      <Message
        severity="secondary"
        size="small"
        variant="simple"
      >
        {{ $t('form.requiredHint') }}
      </Message>
      <Button
        type="submit"
        icon="pi pi-check"
        :label="item ? $t('itemForm.button.saveItem') : $t('itemForm.button.addItem')"
        :loading="isSaving"
      />
    </div>
  </form>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { useToast } from 'primevue/usetoast';
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
import Message from 'primevue/message';
import type { FileUploadSelectEvent } from 'primevue/fileupload';

const props = defineProps<{
  item?: Item | null;
}>();

const emit = defineEmits<{
  saved: [];
}>();

const { t } = useI18n();
const toast = useToast();
const currentYear = new Date().getFullYear();

const emptyItem = (): Omit<Item, 'id'> => ({
  inventoryNumber: '',
  name: '',
  category: '',
  location: '',
  personId: null,
  purchaseDate: currentYear,
  notes: null,
  imageUrl: null,
});

// Reaktives Objekt für ein neu hinzuzufügendes Item (wird direkt durch V-model befüllt)
const newItem = reactive<Omit<Item, 'id'>>(emptyItem());
const selectedImage = ref<File | null>(null);
const removeExistingImage = ref(false);
const fileUpload = ref<{ clear: () => void } | null>(null);

// Fehler werden erst nach dem ersten Absenden angezeigt.
const submitted = ref(false);

const errors = computed(() => ({
  inventoryNumber: !newItem.inventoryNumber.trim(),
  name: !newItem.name.trim(),
  category: !newItem.category,
  location: !newItem.location,
  purchaseDate: !newItem.purchaseDate,
}));

const isValid = computed(() => !Object.values(errors.value).some(Boolean));

function showError(field: keyof typeof errors.value): boolean {
  return submitted.value && errors.value[field];
}

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

function resetForm(item?: Item | null) {
  submitted.value = false;
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
}

watch(() => props.item, resetForm, { immediate: true });

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

onMounted(async () => {
  try {
    await ensureEmployeesLoaded();
  } catch {
    // Der Store hält den Fehlerzustand; das Formular bleibt weiterhin bedienbar.
  }
});

async function handleSubmit() {
  if (isSaving.value) return;
  submitted.value = true;
  if (!isValid.value) return;

  isSaving.value = true;

  try {
    // imageUrl kommt nur vom Server und wird nicht mitgeschickt.
    const payload = {
      inventoryNumber: newItem.inventoryNumber,
      name: newItem.name,
      category: newItem.category,
      location: newItem.location,
      personId: newItem.personId,
      purchaseDate: newItem.purchaseDate,
      notes: newItem.notes,
    };
    if (props.item) {
      await updateItem(props.item.id, payload, selectedImage.value, removeExistingImage.value);
    } else {
      await addItem(payload, selectedImage.value);
    }

    resetForm();
    emit('saved');
  } catch (e: unknown) {
    console.error(e);
    toast.add({
      severity: 'error',
      summary: t('form.toast.errorSummary'),
      detail: e instanceof Error ? e.message : t('form.toast.errorDetail'),
      life: 5000,
    });
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
