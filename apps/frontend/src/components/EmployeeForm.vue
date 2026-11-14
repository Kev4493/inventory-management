<template>
  <form
    class="flex flex-col gap-6"
    novalidate
    @submit.prevent="handleSubmit"
  >
    <!-- ===== Persönliche Informationen ===== -->
    <section class="flex flex-col gap-4">
      <h3 class="mb-0 text-lg font-semibold">{{ $t('employeeForm.headline.personalInfo') }}</h3>

      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <!-- ===== Foto ===== -->
        <div class="flex flex-col gap-2 sm:col-span-2">
          <span class="font-medium">{{ $t('employeeForm.label.photo') }}</span>
          <div
            v-if="props.employee?.photoUrl && !selectedPhoto && !removeExistingPhoto"
            class="flex items-start gap-2"
          >
            <Image
              :src="props.employee.photoUrl"
              :alt="$t('employeeForm.photo.existingAlt')"
              width="160"
              preview
            />
            <Button
              type="button"
              icon="pi pi-trash"
              severity="danger"
              text
              rounded
              :aria-label="$t('employeeForm.photo.removeExisting')"
              @click="removeStoredPhoto"
            />
          </div>
          <FileUpload
            ref="fileUpload"
            mode="advanced"
            name="photo"
            :chooseLabel="$t('employeeForm.photo.choose')"
            accept="image/jpeg,image/png,image/webp"
            :multiple="false"
            :maxFileSize="5242880"
            :invalidFileTypeMessage="$t('employeeForm.photo.invalidFileType', ['{0}'])"
            :invalidFileSizeMessage="$t('employeeForm.photo.invalidFileSize', ['{0}'])"
            :customUpload="true"
            :showUploadButton="false"
            :showCancelButton="false"
            :previewWidth="160"
            :pt="{
              pcFileBadge: { root: { style: 'display: none' } },
              pcFileRemoveButton: { root: { 'aria-label': $t('employeeForm.photo.discardSelected') } },
            }"
            @select="onPhotoSelect"
            @remove="onPhotoRemove"
          >
            <template #empty>{{ $t('employeeForm.photo.empty') }}</template>
          </FileUpload>
          <Message
            severity="secondary"
            size="small"
            variant="simple"
          >
            {{ $t('employeeForm.photo.hint') }}
          </Message>

          <!-- Bildausschnitt: Vorschau im selben Format wie im Drawer -->
          <div
            v-if="previewUrl"
            class="flex flex-col gap-2"
          >
            <label
              for="photoPosition"
              class="font-medium"
            >
              {{ $t('employeeForm.photo.position') }}
            </label>
            <div class="w-full max-w-md aspect-photo overflow-hidden rounded-xl">
              <Image
                :src="previewUrl"
                :alt="$t('employeeForm.photo.previewAlt')"
                class="block w-full h-full"
                imageClass="w-full h-full object-cover"
                :imageStyle="{ objectPosition: `50% ${newEmployee.photoPositionY}%` }"
              />
            </div>
            <div class="flex w-full max-w-md items-center gap-4">
              <span class="text-sm">{{ $t('employeeForm.photo.positionTop') }}</span>
              <Slider
                id="photoPosition"
                v-model="newEmployee.photoPositionY"
                :min="0"
                :max="100"
                :aria-label="$t('employeeForm.photo.position')"
                class="flex-1"
              />
              <span class="text-sm">{{ $t('employeeForm.photo.positionBottom') }}</span>
            </div>
          </div>
        </div>

        <!-- ===== Vorname ===== -->
        <div class="flex flex-col gap-2">
          <label
            for="firstName"
            class="font-medium"
          >
            {{ $t('employeeForm.label.firstName') }} <span class="text-red-500">*</span>
          </label>
          <InputText
            id="firstName"
            v-model="newEmployee.firstName"
            :invalid="showError('firstName')"
            fluid
          />
          <Message
            v-if="showError('firstName')"
            severity="error"
            size="small"
            variant="simple"
          >
            {{ $t('form.validation.required') }}
          </Message>
        </div>

        <!-- ===== Nachname ===== -->
        <div class="flex flex-col gap-2">
          <label
            for="lastName"
            class="font-medium"
          >
            {{ $t('employeeForm.label.lastName') }} <span class="text-red-500">*</span>
          </label>
          <InputText
            id="lastName"
            v-model="newEmployee.lastName"
            :invalid="showError('lastName')"
            fluid
          />
          <Message
            v-if="showError('lastName')"
            severity="error"
            size="small"
            variant="simple"
          >
            {{ $t('form.validation.required') }}
          </Message>
        </div>

        <!-- ===== Straße + Hausnummer ===== -->
        <div class="flex flex-col gap-2 sm:col-span-2">
          <label
            for="street"
            class="font-medium"
          >
            {{ $t('employeeForm.label.street') }} <span class="text-red-500">*</span>
          </label>
          <InputText
            id="street"
            v-model="newEmployee.street"
            :invalid="showError('street')"
            fluid
          />
          <Message
            v-if="showError('street')"
            severity="error"
            size="small"
            variant="simple"
          >
            {{ $t('form.validation.required') }}
          </Message>
        </div>

        <!-- ===== Postleitzahl ===== -->
        <div class="flex flex-col gap-2">
          <label
            for="zip"
            class="font-medium"
          >
            {{ $t('employeeForm.label.zip') }} <span class="text-red-500">*</span>
          </label>
          <InputText
            id="zip"
            v-model="newEmployee.zipCode"
            inputmode="numeric"
            :invalid="showError('zipCode')"
            fluid
          />
          <Message
            v-if="showError('zipCode')"
            severity="error"
            size="small"
            variant="simple"
          >
            {{ $t('form.validation.required') }}
          </Message>
        </div>

        <!-- ===== Stadt ===== -->
        <div class="flex flex-col gap-2">
          <label
            for="city"
            class="font-medium"
          >
            {{ $t('employeeForm.label.city') }} <span class="text-red-500">*</span>
          </label>
          <InputText
            id="city"
            v-model="newEmployee.city"
            :invalid="showError('city')"
            fluid
          />
          <Message
            v-if="showError('city')"
            severity="error"
            size="small"
            variant="simple"
          >
            {{ $t('form.validation.required') }}
          </Message>
        </div>
      </div>
    </section>

    <Divider class="my-0" />

    <!-- ===== Beschäftigungsinformationen ===== -->
    <section class="flex flex-col gap-4">
      <h3 class="mb-0 text-lg font-semibold">{{ $t('employeeForm.headline.jobInfo') }}</h3>

      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <!-- ===== Beschäftigungsart ===== -->
        <div class="flex flex-col gap-2">
          <label
            for="employmentType"
            class="font-medium"
          >
            {{ $t('employeeForm.label.employmentType') }} <span class="text-red-500">*</span>
          </label>
          <Select
            v-model="newEmployee.typeOfEmployment"
            inputId="employmentType"
            :options="employmentTypes"
            optionLabel="name"
            optionValue="code"
            :placeholder="$t('employeeForm.placeholder.chooseEmploymentType')"
            :invalid="showError('typeOfEmployment')"
            fluid
          />
          <Message
            v-if="showError('typeOfEmployment')"
            severity="error"
            size="small"
            variant="simple"
          >
            {{ $t('form.validation.required') }}
          </Message>
        </div>

        <!-- ===== Abteilung ===== -->
        <div class="flex flex-col gap-2">
          <label
            for="department"
            class="font-medium"
          >
            {{ $t('employeeForm.label.department') }} <span class="text-red-500">*</span>
          </label>
          <Select
            v-model="newEmployee.department"
            inputId="department"
            :options="departments"
            optionLabel="name"
            optionValue="code"
            :placeholder="$t('employeeForm.placeholder.chooseDepartment')"
            :invalid="showError('department')"
            fluid
          />
          <Message
            v-if="showError('department')"
            severity="error"
            size="small"
            variant="simple"
          >
            {{ $t('form.validation.required') }}
          </Message>
        </div>

        <!-- ===== E-Mail ===== -->
        <div class="flex flex-col gap-2 sm:col-span-2">
          <label
            for="email"
            class="font-medium"
          >
            {{ $t('employeeForm.label.email') }} <span class="text-red-500">*</span>
          </label>
          <InputText
            id="email"
            v-model="newEmployee.emailAddress"
            type="email"
            autocomplete="email"
            :invalid="showError('emailAddress')"
            fluid
          />
          <Message
            v-if="showError('emailAddress')"
            severity="error"
            size="small"
            variant="simple"
          >
            {{ newEmployee.emailAddress.trim() ? $t('form.validation.email') : $t('form.validation.required') }}
          </Message>
        </div>

        <!-- ===== Eintrittsdatum ===== -->
        <div class="flex flex-col gap-2">
          <label
            for="entryDate"
            class="font-medium"
          >
            {{ $t('employeeForm.label.entryDate') }} <span class="text-red-500">*</span>
          </label>
          <DatePicker
            v-model="newEmployee.dateOfEntry"
            inputId="entryDate"
            dateFormat="dd.mm.yy"
            showIcon
            iconDisplay="input"
            :invalid="showError('dateOfEntry')"
            fluid
          />
          <Message
            v-if="showError('dateOfEntry')"
            severity="error"
            size="small"
            variant="simple"
          >
            {{ $t('form.validation.required') }}
          </Message>
        </div>

        <!-- ===== Austrittsdatum ===== -->
        <div class="flex flex-col gap-2">
          <label
            for="exitDate"
            class="font-medium"
          >
            {{ $t('employeeForm.label.exitDate') }}
          </label>
          <DatePicker
            v-model="newEmployee.dateOfLeaving"
            inputId="exitDate"
            dateFormat="dd.mm.yy"
            :minDate="newEmployee.dateOfEntry ?? undefined"
            showIcon
            showButtonBar
            iconDisplay="input"
            fluid
          />
        </div>

        <!-- ===== Anmerkungen ===== -->
        <div class="flex flex-col gap-2 sm:col-span-2">
          <label
            for="notes"
            class="font-medium"
          >
            {{ $t('employeeForm.label.notes') }}
          </label>
          <Textarea
            id="notes"
            v-model="newEmployee.notes"
            rows="4"
            autoResize
            fluid
          />
        </div>
      </div>
    </section>

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
        :label="
          employee
            ? $t('employeeForm.button.saveEmployee')
            : $t('employeeForm.button.addEmployee')
        "
        :loading="isSaving"
      />
    </div>
  </form>
</template>

<script setup lang="ts">
import type { Employee, EmployeeInput } from '@/types/employee.ts';
import { computed, reactive, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { useToast } from 'primevue/usetoast';
import {
  addEmployee,
  deleteEmployeePhoto,
  updateEmployee,
  uploadEmployeePhoto,
} from '@/stores/employeeStore.ts';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import DatePicker from 'primevue/datepicker';
import Textarea from 'primevue/textarea';
import Button from 'primevue/button';
import Message from 'primevue/message';
import Divider from 'primevue/divider';
import FileUpload from 'primevue/fileupload';
import Image from 'primevue/image';
import Slider from 'primevue/slider';
import type { FileUploadSelectEvent } from 'primevue/fileupload';

const { t } = useI18n();
const toast = useToast();

const props = defineProps<{
  employee?: Employee | null;
}>();

const emit = defineEmits<{
  saved: [];
}>();

const employmentTypes = ref([
  { name: t('employeeForm.employmentType.fullTime'), code: 'fullTime' },
  { name: t('employeeForm.employmentType.partTime'), code: 'partTime' },
  { name: t('employeeForm.employmentType.workingStudent'), code: 'workingStudent' },
  { name: t('employeeForm.employmentType.intern'), code: 'intern' },
  { name: t('employeeForm.employmentType.trainee'), code: 'trainee' },
  { name: t('employeeForm.employmentType.freelancer'), code: 'freelancer' },
]);

const departments = ref([
  { name: t('employeeForm.department.development'), code: 'development' },
  { name: t('employeeForm.department.marketing'), code: 'marketing' },
  { name: t('employeeForm.department.sales'), code: 'sales' },
  { name: t('employeeForm.department.design'), code: 'design' },
  { name: t('employeeForm.department.management'), code: 'management' },
  { name: t('employeeForm.department.humanResources'), code: 'humanResources' },
]);

// Standard-Bildausschnitt: Gesichter liegen meist im oberen Drittel.
const DEFAULT_PHOTO_POSITION_Y = 25;

const emptyEmployee = (): EmployeeInput => ({
  firstName: '',
  lastName: '',
  street: '',
  zipCode: '',
  city: '',
  typeOfEmployment: '' as '' | 'fullTime' | 'partTime' | 'workingStudent' | 'intern' | 'trainee' | 'freelancer',
  emailAddress: '',
  department: '',
  dateOfEntry: new Date(),
  dateOfLeaving: null,
  notes: null,
  photoPositionY: DEFAULT_PHOTO_POSITION_Y,
});

// Objekt für ein neu hinzuzufügenden Mitarbeiter
const newEmployee = reactive<EmployeeInput>(emptyEmployee());
const selectedPhoto = ref<File | null>(null);
const removeExistingPhoto = ref(false);
const fileUpload = ref<{ clear: () => void; remove: (index: number) => void } | null>(null);

// Fehler werden erst nach dem ersten Absenden angezeigt.
const submitted = ref(false);

const EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

const errors = computed(() => ({
  firstName: !newEmployee.firstName.trim(),
  lastName: !newEmployee.lastName.trim(),
  street: !newEmployee.street.trim(),
  zipCode: !newEmployee.zipCode.trim(),
  city: !newEmployee.city.trim(),
  typeOfEmployment: !newEmployee.typeOfEmployment,
  department: !newEmployee.department,
  emailAddress: !EMAIL_PATTERN.test(newEmployee.emailAddress.trim()),
  dateOfEntry: !newEmployee.dateOfEntry,
}));

const isValid = computed(() => !Object.values(errors.value).some(Boolean));

function showError(field: keyof typeof errors.value): boolean {
  return submitted.value && errors.value[field];
}

// FileUpload hängt neue Dateien an (z. B. per Drag & Drop), daher gilt immer die zuletzt
// gewählte Datei. Ältere Einträge werden aus der Liste entfernt, damit nur ein Foto übrig bleibt.
function onPhotoSelect(event: FileUploadSelectEvent) {
  const files = event.files as File[];
  const file = files[files.length - 1];
  // Ungültige Dateien landen nicht in files, dann bleibt die bisherige Auswahl bestehen.
  if (!file || file === selectedPhoto.value) return;

  clearSelectedPhoto();
  selectedPhoto.value = file;
  removeExistingPhoto.value = false;
  // Ein neues Foto braucht einen neuen Ausschnitt, der alte Wert passt meist nicht.
  newEmployee.photoPositionY = DEFAULT_PHOTO_POSITION_Y;

  for (let i = files.length - 1; i > 0; i--) {
    fileUpload.value?.remove(0);
  }
}

// Bild für die Ausschnitt-Vorschau: neu gewähltes Foto, sonst das gespeicherte, sofern es nicht entfernt wird.
const previewUrl = computed<string | null>(() => {
  const objectUrl = (selectedPhoto.value as File & { objectURL?: string } | null)?.objectURL;
  if (objectUrl) return objectUrl;
  if (props.employee?.photoUrl && !removeExistingPhoto.value) return props.employee.photoUrl;
  return null;
});

function onPhotoRemove(event: { file: File }) {
  if (event.file === selectedPhoto.value) {
    clearSelectedPhoto();
    // Zurück zum gespeicherten Foto, also auch zu dessen gespeichertem Ausschnitt.
    newEmployee.photoPositionY = props.employee?.photoPositionY ?? DEFAULT_PHOTO_POSITION_Y;
  }
}

function removeStoredPhoto() {
  removeExistingPhoto.value = true;
}

function clearSelectedPhoto() {
  const objectUrl = (selectedPhoto.value as File & { objectURL?: string } | null)?.objectURL;
  if (objectUrl) URL.revokeObjectURL(objectUrl);
  selectedPhoto.value = null;
}

// Setzt Formularfelder und Fotoauswahl zurück, mit den Daten des Mitarbeiters oder leer.
function resetForm(employee?: Employee | null) {
  submitted.value = false;
  clearSelectedPhoto();
  removeExistingPhoto.value = false;
  fileUpload.value?.clear();
  if (employee) {
    Object.assign(newEmployee, {
      firstName: employee.firstName,
      lastName: employee.lastName,
      street: employee.street,
      zipCode: employee.zipCode,
      city: employee.city,
      typeOfEmployment: employee.typeOfEmployment,
      emailAddress: employee.emailAddress,
      department: employee.department,
      dateOfEntry: new Date(employee.dateOfEntry),
      dateOfLeaving: employee.dateOfLeaving ? new Date(employee.dateOfLeaving) : null,
      notes: employee.notes,
      photoPositionY: employee.photoPositionY,
    });
  } else {
    Object.assign(newEmployee, emptyEmployee());
  }
}

watch(() => props.employee, resetForm, { immediate: true });

const isSaving = ref(false);

async function handleSubmit() {
  if (isSaving.value) return;
  submitted.value = true;
  if (!isValid.value) return;

  isSaving.value = true;

  try {
    const payload = { ...newEmployee };
    if (props.employee) {
      await updateEmployee(props.employee.id, payload);
      if (selectedPhoto.value) {
        await uploadEmployeePhoto(props.employee.id, selectedPhoto.value);
      } else if (removeExistingPhoto.value) {
        await deleteEmployeePhoto(props.employee.id);
      }
    } else {
      const created = await addEmployee(payload);
      if (selectedPhoto.value) {
        // Der Mitarbeiter existiert bereits. Ein Fehler beim Foto darf nicht dazu führen,
        // dass der Dialog offen bleibt und erneutes Absenden ihn doppelt anlegt.
        try {
          await uploadEmployeePhoto(created.id, selectedPhoto.value);
        } catch (e: unknown) {
          console.error(e);
          toast.add({
            severity: 'warn',
            summary: t('employeeForm.photo.uploadFailedSummary'),
            detail: t('employeeForm.photo.uploadFailedDetail', [
              e instanceof Error ? e.message : t('form.toast.errorDetail'),
            ]),
            life: 8000,
          });
        }
      }
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
</script>
