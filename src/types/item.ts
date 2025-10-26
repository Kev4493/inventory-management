// Definition eines Objekttyps namens "Item"
export type Item = {
  id: number;
  name: string;
  category: string;
  location: string;
  person: string | null;
  purchaseDate: number;
  notes: string | null;
};
