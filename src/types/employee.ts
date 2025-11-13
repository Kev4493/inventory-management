// Definition eines Objekttyps namens "Employee"
export type Employee = {
  id: number
  firstName: string
  lastName: string
  street: string
  zipCode: string
  city: string
  typeOfEmployment: '' | 'fullTime' | 'partTime' | 'workingStudent' | 'intern' | 'trainee' | 'freelancer'
  department: '' | 'development' | 'marketing' | 'sales' | 'design' | 'management' | 'humanResources'
  emailAddress: string
  dateOfEntry: string
  dateOfLeaving: string | null
  notes: string | null;
};
