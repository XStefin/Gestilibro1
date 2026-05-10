/**
 * TableStatusRow – fila de tabla para loading, error o lista vacía.
 * Props:
 *   colSpan  : número de columnas
 *   message  : texto a mostrar
 */
export default function TableStatusRow({ colSpan, message }) {
  return (
    <tr>
      <td colSpan={colSpan} className="empty-row">
        {message}
      </td>
    </tr>
  );
}
