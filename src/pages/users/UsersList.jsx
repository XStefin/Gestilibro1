import { useState } from "react";
import { Link } from "react-router-dom";
import {
  FaPenToSquare,
  FaTrash,
  FaUserPlus,
  FaUsers,
} from "react-icons/fa6";
import Sidebar from "../../components/layout/Sidebar";
import "./UsersList.css";

export default function UsersList() {
  const [users, setUsers] = useState([
    {
      id_usuario: 1,
      nombre: "Diana",
      apellido: "Sterpin",
      correo: "diana@example.com",
      username: "dsterpin",
      rol: "Administrador",
      pin: "1234",
      active: true,
    },
    {
      id_usuario: 2,
      nombre: "Carlos",
      apellido: "Ramírez",
      correo: "carlos@example.com",
      username: "cramirez",
      rol: "Bibliotecario",
      pin: "5678",
      active: true,
    },
    {
      id_usuario: 3,
      nombre: "Laura",
      apellido: "Gómez",
      correo: "laura@example.com",
      username: "lgomez",
      rol: "Usuario",
      pin: "9876",
      active: false,
    },
  ]);

  const handleDelete = (id) => {
    const confirmDelete = window.confirm(
      "¿Seguro que deseas eliminar este usuario?"
    );

    if (!confirmDelete) return;

    setUsers((prev) => prev.filter((user) => user.id_usuario !== id));
  };

  return (
    <div className="users-layout">
      <Sidebar />

      <main className="users-content">
        <div className="users-header">
          <h4 className="users-title">
            <FaUsers />
            <span>Gestión de Usuarios</span>
          </h4>

          <Link to="/users/create" className="btn-new-user">
            <FaUserPlus />
            <span>Nuevo Usuario</span>
          </Link>
        </div>

        <div className="users-table-card">
          <div className="users-table-wrapper">
            <table className="users-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Apellido</th>
                  <th>Correo</th>
                  <th>Username</th>
                  <th>Rol</th>
                  <th>PIN</th>
                  <th>Activo</th>
                  <th className="text-center">Acciones</th>
                </tr>
              </thead>

              <tbody>
                {users.length > 0 ? (
                  users.map((u) => (
                    <tr key={u.id_usuario}>
                      <td>{u.id_usuario}</td>
                      <td>{u.nombre}</td>
                      <td>{u.apellido}</td>
                      <td>{u.correo}</td>
                      <td>{u.username}</td>
                      <td>{u.rol}</td>
                      <td>{u.pin}</td>
                      <td>{u.active ? "Sí" : "No"}</td>
                      <td className="text-center">
                        <div className="action-buttons">
                          <Link
                            to={`/users/edit/${u.id_usuario}`}
                            className="btn-action btn-edit"
                            title="Editar"
                          >
                            <FaPenToSquare />
                          </Link>

                          <button
                            type="button"
                            className="btn-action btn-delete"
                            title="Eliminar"
                            onClick={() => handleDelete(u.id_usuario)}
                          >
                            <FaTrash />
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))
                ) : (
                  <tr>
                    <td colSpan="9" className="empty-row">
                      No hay usuarios registrados.
                    </td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  );
}