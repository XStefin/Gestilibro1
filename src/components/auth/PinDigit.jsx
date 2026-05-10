/**
 * PinDigit – input de un solo dígito del PIN.
 * Parte atómica de PinInputGroup.
 */
export default function PinDigit({ inputRef, digit, index, onChange, onKeyDown }) {
  return (
    <input
      ref={inputRef}
      className={`pin-input ${digit ? "pin-input--filled" : ""}`}
      type="text"
      inputMode="numeric"
      maxLength={1}
      value={digit}
      onChange={(e) => onChange(index, e.target.value)}
      onKeyDown={(e) => onKeyDown(index, e)}
    />
  );
}
