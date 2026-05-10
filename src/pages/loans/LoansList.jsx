import { useState } from "react";
import { Link } from "react-router-dom";
import { FaHandshake, FaUsers } from "react-icons/fa6";
import PageLayout from "../../components/layout/PageLayout";
import PageHeader from "../../components/ui/PageHeader";
import AlertMessage from "../../components/ui/AlertMessage";
import TableStatusRow from "../../components/ui/TableStatusRow";
import LoanTableRow from "../../components/loans/LoanTableRow";
import { useApiList } from "../../hooks/useApiList";
import { useAuthUser } from "../../hooks/useAuthUser";
import { apiRequest } from "../../services/api";
import "./LoansList.css";

export default function LoansList() {
  const { canManage } = useAuthUser();
  const { data: loans, loading, error: fetchError, reload } = useApiList("/prestamos");
  const [actionMsg, setActionMsg] = useState({ type: "", text: "" });

  const handleDelete = async (id) => {
    if (!window.confirm("¿Seguro que deseas eliminar este préstamo?")) return;
    try {
      await apiRequest(`/prestamos/${id}`, { method: "DELETE" });
      reload();
    } catch (err) {
      setActionMsg({ type: "danger", text: err.message || "No fue posible eliminar el préstamo" });
    }
  };

  const colSpan = canManage ? 7 : 6;

  return (
    <PageLayout>
      <AlertMessage type={actionMsg.type} message={actionMsg.text} onClose={() => setActionMsg({ type: "", text: "" })} />

      <PageHeader
        icon={<FaUsers />}
        title="Gestión de Préstamos"
        action={
          canManage && (
            <Link to="/loans/create" className="btn-new-loan">
              <FaHandshake />
              <span>Nuevo Préstamo</span>
            </Link>
          )
        }
      />

      <div className="loans-table-wrapper">
        <table className="loans-table">
          <thead>
            <tr>
              <th>ID</th><th>Usuario</th><th>Libro</th>
              <th>Fecha Préstamo</th><th>Fecha Devolución</th><th>Estado</th>
              {canManage && <th>Acciones</th>}
            </tr>
          </thead>
          <tbody>
            {loading ? (
              <TableStatusRow colSpan={colSpan} message="Cargando préstamos..." />
            ) : fetchError ? (
              <TableStatusRow colSpan={colSpan} message={fetchError} />
            ) : loans.length > 0 ? (
              loans.map((loan) => (
                <LoanTableRow
                  key={loan.id_prestamo}
                  loan={loan}
                  canManage={canManage}
                  onDelete={handleDelete}
                />
              ))
            ) : (
              <TableStatusRow colSpan={colSpan} message="No hay préstamos registrados" />
            )}
          </tbody>
        </table>
      </div>
    </PageLayout>
  );
}
