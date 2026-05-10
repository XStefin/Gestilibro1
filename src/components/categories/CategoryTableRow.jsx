import TableActions from "../ui/TableActions";

/**
 * CategoryTableRow – fila individual de la tabla de categorías.
 */
export default function CategoryTableRow({ category, onDelete }) {
  return (
    <tr>
      <td>{category.id_categoria}</td>
      <td>{category.nombre}</td>
      <td>{category.descripcion}</td>
      <td>
        <TableActions
          editTo={`/categories/edit/${category.id_categoria}`}
          onDelete={() => onDelete(category.id_categoria)}
        />
      </td>
    </tr>
  );
}
