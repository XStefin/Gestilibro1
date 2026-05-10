/**
 * TextAreaField – textarea genérica con label.
 */
export default function TextAreaField({
  label,
  id,
  name,
  value,
  onChange,
  rows = 4,
  required = false,
  disabled = false,
  placeholder = "",
}) {
  const fieldId = id || name;

  return (
    <div className="form-group">
      {label && (
        <label htmlFor={fieldId} className="form-label">
          {label}
        </label>
      )}
      <textarea
        id={fieldId}
        name={name}
        className="form-control"
        value={value}
        onChange={onChange}
        rows={rows}
        required={required}
        disabled={disabled}
        placeholder={placeholder}
      />
    </div>
  );
}
