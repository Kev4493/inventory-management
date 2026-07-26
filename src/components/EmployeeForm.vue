<template>
  <form
    @submit.prevent="handleSubmit"
    class="addEmployeeForm"
  >
    <h2>{{ $t('employeeForm.headline.personalInfo') }}</h2>

    <!-- ===== Vorname: ===== -->
    <div>
      <label for="firstName">{{ $t('employeeForm.label.firstName') }}:</label>
      <InputText
        id="firstName"
        type="text"
        v-model="newEmployee.firstName"
        name="firstName"
        required
      />
    </div>

    <!-- ===== Nachname: ===== -->
    <div>
      <label for="lastName">{{ $t('employeeForm.label.lastName') }}:</label>
      <InputText
        id="lastName"
        type="text"
        v-model="newEmployee.lastName"
        name="lastName"
        required
      />
    </div>

    <!-- ===== Straße + Hausnummer: ===== -->
    <div>
      <label for="street">{{ $t('employeeForm.label.street') }}:</label>
      <InputText
        id="street"
        type="text"
        v-model="newEmployee.street"
        name="street"
        required
      />
    </div>

    <!-- ===== Postleitzahl: ===== -->
    <div>
      <label for="zip">{{ $t('employeeForm.label.zip') }}:</label>
      <InputText
        v-model="newEmployee.zipCode"
        type="text"
        id="zip"
        name="zip"
        required
      />
    </div>

    <!-- ===== Stadt: ===== -->
    <div>
      <label for="city">{{ $t('employeeForm.label.city') }}:</label>
      <InputText
        v-model="newEmployee.city"
        type="text"
        id="city"
        name="city"
        required
      />
    </div>

    <h2>{{ $t('employeeForm.headline.jobInfo') }}</h2>

    <!-- ===== Beschäftigungsart: ===== -->
    <div class="flex flex-col">
      <label for="employmentType">{{ $t('employeeForm.label.employmentType') }}:</label>

      <Select
        id="employmentType"
        name="employmentType"
        v-model="newEmployee.typeOfEmployment"
        :options="employmentTypes"
        optionLabel="name"
        optionValue="code"
        :placeholder="$t('employeeForm.placeholder.chooseEmploymentType')"
        required
      />
    </div>

    <!-- ===== Abteilung: ===== -->
    <div class="flex flex-col">
      <label for="department">{{ $t('employeeForm.label.department') }}:</label>
      <Select
        id="department"
        name="department"
        v-model="newEmployee.department"
        :options="departments"
        optionLabel="name"
        optionValue="code"
        :placeholder="$t('employeeForm.placeholder.chooseDepartment')"
        required
      />
    </div>

    <!-- ===== Email: ===== -->
    <div>
      <label for="email">{{ $t('employeeForm.label.email') }}:</label>
      <InputText
        v-model="newEmployee.emailAddress"
        type="text"
        id="email"
        name="email"
        required
      />
    </div>

    <!-- ===== Eintrittsdatum: ===== -->
    <div>
      <label for="entryDate">{{ $t('employeeForm.label.entryDate') }}:</label>

      <div class="flex-auto">
        <label
          for="icondisplay"
          class="font-bold block mb-2"
        >
        </label>
        <DatePicker
          v-model="newEmployee.dateOfEntry"
          showIcon
          fluid
          iconDisplay="input"
          inputId="icondisplay"
        />
      </div>
    </div>

    <!-- ===== Austrittsdatum: ===== -->
    <div>
      <label for="exitDate">{{ $t('employeeForm.label.exitDate') }}:</label>
      <DatePicker
        v-model="newEmployee.dateOfLeaving"
        showIcon
        fluid
        iconDisplay="input"
        inputId="exitDate"
      />
    </div>

    <!-- ===== Anmerkungen: ===== -->
    <div>
      <label for="notes">{{ $t('employeeForm.label.notes') }}:</label>
      <Textarea
        id="notes"
        name="notes"
        v-model="newEmployee.notes"
        rows="4"
        autoResize
      />
    </div>

    <button
      type="submit"
      :disabled="isSaving"
      aria-busy="true"
    >
      {{
        employee
          ? $t('employeeForm.button.saveEmployee')
          : $t('employeeForm.button.addEmployee')
      }}
    </button>
  </form>
</template>

<script setup lang="ts">
import type { Employee } from '@/types/employee.ts';
import { reactive, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { addEmployee, updateEmployee } from '@/stores/employeeStore.ts';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import DatePicker from 'primevue/datepicker';
import Textarea from 'primevue/textarea';

const { t } = useI18n();

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

watch(
  () => props.employee,
  (employee) => {
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
  isSaving.value = true;

  try {
    const payload = { ...newEmployee };
    if (props.employee) {
      await updateEmployee(props.employee.id, payload);
    } else {
      await addEmployee(payload);
    }

    Object.assign(newEmployee, emptyEmployee());
    emit('saved');
  } catch (e: unknown) {
    console.error(e);
    alert(e instanceof Error ? e.message : 'Fehler beim Speichern');
  } finally {
    isSaving.value = false;
  }
}
</script>

<style scoped lang="scss">
.addEmployeeForm {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  width: 100%;
  padding: 2rem;

  label {
    font-weight: bold;
  }

  input,
  textarea,
  select {
    padding: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 4px;
    width: 100%;
    box-sizing: border-box;
  }

  button {
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
}
</style>
