import TableActions from "../ui/TableActions";
import AvailabilityBadge from "./AvailabilityBadge";

/**
 * BookTableRow – fila individual de la tabla de libros.
 */
export default function BookTableRow({ book, canManage, onDelete }) {
  return (
    <tr>
      <td>{book.id_libro}</td>
      <td>{book.titulo}</td>
      <td>{book.autor}</td>
      <td>{book.editorial}</td>
      <td>{book.anio}</td>
      <td>{book.id_categoria}</td>
      <td>{book.cantidad}</td>
      <td>
        <AvailabilityBadge disponibilidad={book.disponibilidad} />
      </td>
      {canManage && (
        <td>
          <TableActions
            editTo={`/books/edit/${book.id_libro}`}
            onDelete={() => onDelete(book.id_libro)}
          />
        </td>
      )}
    </tr>
  );
}
