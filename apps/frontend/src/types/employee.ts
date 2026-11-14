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
  dateOfEntry: Date
  dateOfLeaving: Date | null
  notes: string | null;
  // Kommt nur vom Server, null ohne Foto.
  photoUrl: string | null;
  // Vertikaler Bildausschnitt in Prozent (0 = oben, 100 = unten).
  photoPositionY: number;
};

// Felder, die das Formular an die API schickt (ohne die vom Server vergebenen Werte).
export type EmployeeInput = Omit<Employee, 'id' | 'photoUrl'>;
