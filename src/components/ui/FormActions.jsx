import { Link } from "react-router-dom";

/**
 * FormActions – barra inferior de un formulario.
 * Props:
 *   cancelTo    : ruta a la que vuelve el Link "Cancelar"
 *   submitLabel : texto del botón submit (default "Guardar")
 *   loading     : boolean – deshabilita y cambia texto
 *   loadingLabel: texto mientras loading (default "Guardando...")
 *   disabled    : boolean extra (p.ej. cuando el recurso no se encontró)
 */
export default function FormActions({
  cancelTo,
  submitLabel = "Guardar",
  loading = false,
  loadingLabel = "Guardando...",
  disabled = false,
}) {
  return (
    <div className="form-actions">
      <button
        type="submit"
        className="btn-save"
        disabled={loading || disabled}
      >
        {loading ? loadingLabel : submitLabel}
      </button>

      {cancelTo && (
        <Link to={cancelTo} className="btn-cancel">
          Cancelar
        </Link>
      )}
    </div>
  );
}
