/**
 * SelectField – select genérico con label.
 * Props:
 *   label, id, name, value, onChange, required, disabled
 *   options: [{ value, label }]
 *   placeholder: texto de la opción vacía inicial
 */
export default function SelectField({
  label,
  id,
  name,
  value,
  onChange,
  required = false,
  disabled = false,
  placeholder = "Seleccione...",
  options = [],
}) {
  const fieldId = id || name;

  return (
    <div className="form-group">
      {label && (
        <label htmlFor={fieldId} className="form-label">
          {label}
        </label>
      )}
      <select
        id={fieldId}
        name={name}
        className="form-control"
        value={value}
        onChange={onChange}
        required={required}
        disabled={disabled}
      >
        <option value="">{placeholder}</option>
        {options.map((opt) => (
          <option key={opt.value} value={opt.value}>
            {opt.label}
          </option>
        ))}
      </select>
    </div>
  );
}
