  import { Link } from "react-router-dom";
  import { FaUserPlus, FaUsers } from "react-icons/fa6";
  import PageLayout from "../../components/layout/PageLayout";
  import PageHeader from "../../components/ui/PageHeader";
  import AlertMessage from "../../components/ui/AlertMessage";
  import TableStatusRow from "../../components/ui/TableStatusRow";
  import UserTableRow from "../../components/users/UserTableRow";
  import { useApiList } from "../../hooks/useApiList";
  import { apiRequest } from "../../services/api";
  import { useState } from "react";
  import "./UsersList.css";

  export default function UsersList() {
    const { data: users, loading, error: fetchError, reload } = useApiList("/usuarios");
    const [actionMsg, setActionMsg] = useState({ type: "", text: "" });

    const handleDelete = async (id) => {
      if (!window.confirm("¿Seguro que deseas eliminar este usuario?")) return;
      try {
        await apiRequest(`/usuarios/${id}`, { method: "DELETE" });
        reload();
      } catch (err) {
        setActionMsg({ type: "danger", text: err.message || "No fue posible eliminar el usuario" });
      }
    };

    return (
      <PageLayout>
        <AlertMessage type={actionMsg.type} message={actionMsg.text} onClose={() => setActionMsg({ type: "", text: "" })} />

        <PageHeader
          icon={<FaUsers />}
          title="Gestión de Usuarios"
          action={
            <Link to="/users/create" className="btn-new-user">
              <FaUserPlus />
              <span>Nuevo Usuario</span>
            </Link>
          }
        />

        <div className="users-table-card">
          <div className="users-table-wrapper">
            <table className="users-table">
              <thead>
                <tr>
                  <th>ID</th><th>Nombre</th><th>Apellido</th><th>Correo</th>
                  <th>Username</th><th>Rol</th><th>PIN</th><th>Activo</th>
                  <th className="text-center">Acciones</th>
                </tr>
              </thead>
              <tbody>
                {loading ? (
                  <TableStatusRow colSpan={9} message="Cargando usuarios..." />
                ) : fetchError ? (
                  <TableStatusRow colSpan={9} message={fetchError} />
                ) : users.length > 0 ? (
                  users.map((u) => (
                    <UserTableRow key={u.id_usuario} user={u} onDelete={handleDelete} />
                  ))
                ) : (
                  <TableStatusRow colSpan={9} message="No hay usuarios registrados." />
                )}
              </tbody>
            </table>
          </div>
        </div>
      </PageLayout>
    );
  }
