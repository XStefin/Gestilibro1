import TableActions from "../ui/TableActions";

/**
 * UserTableRow – fila individual de la tabla de usuarios.
 */
export default function UserTableRow({ user, onDelete }) {
  const isActive =
    user.active === true || user.active === 1 || user.active === "1";

  return (
    <tr>
      <td>{user.id_usuario}</td>
      <td>{user.nombre}</td>
      <td>{user.apellido}</td>
      <td>{user.correo}</td>
      <td>{user.username}</td>
      <td>{user.rol ?? ""}</td>
      <td>{user.pin ?? ""}</td>
      <td>{isActive ? "Sí" : "No"}</td>
      <td className="text-center">
        <TableActions
          editTo={`/users/edit/${user.id_usuario}`}
          onDelete={() => onDelete(user.id_usuario)}
        />
      </td>
    </tr>
  );
}
