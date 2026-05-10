/**
 * CheckboxField – checkbox con label.
 */
export default function CheckboxField({
  label,
  id,
  name,
  checked,
  onChange,
  disabled = false,
}) {
  const fieldId = id || name;

  return (
    <div className="form-check">
      <input
        type="checkbox"
        id={fieldId}
        name={name}
        className="form-check-input"
        checked={checked}
        onChange={onChange}
        disabled={disabled}
      />
      {label && (
        <label htmlFor={fieldId} className="form-check-label">
          {label}
        </label>
      )}
    </div>
  );
}
