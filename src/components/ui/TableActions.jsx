import { Link } from "react-router-dom";
import { FaPenToSquare, FaTrash } from "react-icons/fa6";

/**
 * TableActions – par de botones editar / eliminar para una fila de tabla.
 * Props:
 *   editTo      : ruta del Link editar (string)
 *   onDelete    : () => void
 *   showDelete  : boolean (default true)
 */
export default function TableActions({
  editTo,
  onDelete,
  showDelete = true,
}) {
  return (
    <div className="action-buttons">
      {editTo && (
        <Link to={editTo} className="btn-action btn-edit" title="Editar">
          <FaPenToSquare />
        </Link>
      )}

      {showDelete && onDelete && (
        <button
          type="button"
          className="btn-action btn-delete"
          title="Eliminar"
          onClick={onDelete}
        >
          <FaTrash />
        </button>
      )}
    </div>
  );
}
