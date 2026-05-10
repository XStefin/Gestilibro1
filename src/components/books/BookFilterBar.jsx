/**
 * BookFilterBar – barra de filtro por disponibilidad para BooksList.
 */
export default function BookFilterBar({ value, onChange }) {
  return (
    <div className="books-filter-box">
      <select
        name="disponibilidad"
        className="books-filter-select"
        value={value}
        onChange={(e) => onChange(e.target.value)}
      >
        <option value="">-- Filtrar por disponibilidad --</option>
        <option value="disponible">Disponible</option>
        <option value="no_disponible">No disponible</option>
      </select>
      <button type="button" className="btn-filter">
        Filtrar
      </button>
    </div>
  );
}
