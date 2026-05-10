import TableActions from "../ui/TableActions";
import LoanStatusBadge from "./LoanStatusBadge";

/**
 * LoanTableRow – fila individual de la tabla de préstamos.
 */
export default function LoanTableRow({ loan, canManage, onDelete }) {
  const nombreUsuario = `${loan.nombre_usuario || ""} ${loan.apellido || ""}`.trim();
  const isPrestado = String(loan.estado || "").toLowerCase() === "prestado";

  return (
    <tr>
      <td>{loan.id_prestamo}</td>
      <td>{nombreUsuario}</td>
      <td>{loan.titulo_libro || "-"}</td>
      <td>{loan.fecha_prestamo || "-"}</td>
      <td>{loan.fecha_devolucion || "-"}</td>
      <td>
        <LoanStatusBadge estado={loan.estado} />
      </td>
      {canManage && (
        <td>
          <TableActions
            editTo={`/loans/edit/${loan.id_prestamo}`}
            onDelete={() => onDelete(loan.id_prestamo)}
            showDelete={!isPrestado}
          />
        </td>
      )}
    </tr>
  );
}
