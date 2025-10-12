const de = {
  // ===== Navigation =====
  sidebar: {
    home: 'Home',
    inventory: 'Inventar',
    addItem: 'Neues Item',
    addEmployee: 'Neuer Mitarbeiter',
  },

  // ===== Views ======
  inventory: {
    title: 'Inventarliste',
    noItems: 'Keine Items im Inventar.',
  },

  addItem: {
    title: 'Neues Item hinzufügen',
    subtitle: 'Füge ein neues Produkt zu deinem Inventar hinzu.',
  },

  addEmployee: {
    title: 'Neuen Mitarbeiter hinzufügen',
    subtitle: 'Füge einen neuen Mitarbeiter zu deinem Team hinzu.',
  },

  // ===== Forms =====
  itemForm: {
    label: {
      productName: 'Produktname',
      category: 'Kategorie',
      location: 'Standort',
      assigned: 'Zugewiesen an',
      purchaseDate: 'Anschaffungsjahr',
      notes: 'Anmerkungen',
    },
    placeholder: {
      chooseCategory: 'Kategorie auswählen',
      location: 'Standort auswählen',
      assigned: 'Person auswählen',
    },
  },

  employeeForm: {
    headline: {
      personalInfo: 'Persönliche Informationen',
      jobInfo: 'Beschäftigungsinformationen',
    },
    label: {
      firstName: 'Vorname',
      lastName: 'Nachname',
      street: 'Straße + Hausnummer',
      zip: 'Postleitzahl',
      city: 'Stadt',
      employmentType: 'Beschäftigungsart',
      department: 'Abteilung',
      email: 'E-Mail',
      entryDate: 'Eintrittsdatum',
      exitDate: 'Austrittsdatum',
      notes: 'Anmerkungen',
    },
    placeholder: {
      chooseEmploymentType: 'Beschäftigungsart auswählen',
      chooseDepartment: 'Abteilung auswählen',
    },
    employmentType: {
      fullTime: 'Vollzeit',
      partTime: 'Teilzeit',
      workingStudent: 'Werkstudent',
      intern: 'Praktikant',
      trainee: 'Trainee',
      freelancer: 'Freelancer',
    },
    department: {
      development: 'Entwicklung',
      marketing: 'Marketing',
      sales: 'Vertrieb',
      design: 'Design',
      management: 'Geschäftsführung',
      humanResources: 'Personalabteilung',
    },
    button: {
      addEmployee: 'Mitarbeiter hinzufügen',
    }
  },
}

export default de
