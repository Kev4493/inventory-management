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
import type { Employee } from '@/types/employee.ts';
import { computed, reactive, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { useToast } from 'primevue/usetoast';
import { addEmployee, updateEmployee } from '@/stores/employeeStore.ts';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import DatePicker from 'primevue/datepicker';
import Textarea from 'primevue/textarea';
import Button from 'primevue/button';
import Message from 'primevue/message';
import Divider from 'primevue/divider';

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

const emptyEmployee = (): Omit<Employee, 'id'> => ({
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
});

// Objekt für ein neu hinzuzufügenden Mitarbeiter
const newEmployee = reactive<Omit<Employee, 'id'>>(emptyEmployee());

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

watch(
  () => props.employee,
  (employee) => {
    submitted.value = false;
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
      });
    } else {
      Object.assign(newEmployee, emptyEmployee());
    }
  },
  { immediate: true },
);

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
    } else {
      await addEmployee(payload);
    }

    Object.assign(newEmployee, emptyEmployee());
    submitted.value = false;
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
