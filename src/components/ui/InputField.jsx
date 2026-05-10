/**
 * InputField – campo de texto genérico con label.
 * Props: label, id, name, type, value, onChange, required,
 *        disabled, placeholder, autoComplete, min, max, step, maxLength
 */
export default function InputField({
  label,
  id,
  name,
  type = "text",
  value,
  onChange,
  required = false,
  disabled = false,
  placeholder = "",
  autoComplete,
  min,
  max,
  step,
  maxLength,
}) {
  const fieldId = id || name;

  return (
    <div className="form-group">
      {label && (
        <label htmlFor={fieldId} className="form-label">
          {label}
        </label>
      )}
      <input
        id={fieldId}
        type={type}
        name={name}
        className="form-control"
        value={value}
        onChange={onChange}
        required={required}
        disabled={disabled}
        placeholder={placeholder}
        autoComplete={autoComplete}
        min={min}
        max={max}
        step={step}
        maxLength={maxLength}
      />
    </div>
  );
}
