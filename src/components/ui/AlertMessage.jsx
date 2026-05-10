/**
 * AlertMessage – componente atómico de alerta.
 * Props:
 *   type    : "success" | "danger" | "warning" | "info"
 *   message : string
 *   onClose : () => void  (si no se pasa, no aparece el botón ×)
 */
export default function AlertMessage({ type = "info", message, onClose }) {
  if (!message) return null;

  return (
    <div className={`alert-box alert-${type}`}>
      <span>{message}</span>
      {onClose && (
        <button type="button" onClick={onClose}>
          ×
        </button>
      )}
    </div>
  );
}
