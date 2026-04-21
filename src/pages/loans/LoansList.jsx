import { useState } from "react";
import { Link } from "react-router-dom";
import {
  FaHandshake,
  FaPenToSquare,
  FaTrash,
  FaUsers,
} from "react-icons/fa6";
import Sidebar from "../../components/layout/Sidebar";
import "./LoansList.css";

export default function LoansList() {
  const authUser = {
    rol: "Administrador",
  };

  const rol = authUser.rol.toLowerCase();
  const puedeGestionar = ["administrador", "bibliotecario"].includes(rol);

  const [loans, setLoans] = useState([
    {
      id_prestamo: 1,
      nombre_usuario: "Diana",
      apellido: "Sterpin",
      titulo_libro: "Clean Code",
      fecha_prestamo: "2026-04-20",
      fecha_devolucion: "2026-04-28",
      estado: "prestado",
    },
    {
      id_prestamo: 2,
      nombre_usuario: "Carlos",
      apellido: "Ramírez",
      titulo_libro: "Cien años de soledad",
      fecha_prestamo: "2026-04-10",
      fecha_devolucion: "2026-04-17",
      estado: "devuelto",
    },
    {
      id_prestamo: 3,
      nombre_usuario: "Laura",
      apellido: "Gómez",
      titulo_libro: "Introducción a la Historia",
      fecha_prestamo: "2026-04-01",
      fecha_devolucion: "",
      estado: "atrasado",
    },
  ]);

  const handleDelete = (id) => {
    const confirmDelete = window.confirm(
      "¿Seguro que deseas eliminar este préstamo?"
    );

    if (!confirmDelete) return;

    setLoans((prev) => prev.filter((loan) => loan.id_prestamo !== id));
  };

  return (
    <div className="loans-layout">
      <Sidebar />

      <main className="loans-content">
        <div className="loans-header">
          <h4 className="loans-title">
            <FaUsers />
            <span>Gestión de Préstamos</span>
          </h4>

          <Link to="/loans/create" className="btn-new-loan">
            <FaHandshake />
            <span>Nuevo Préstamo</span>
          </Link>
        </div>

        <div className="loans-table-wrapper">
          <table className="loans-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Libro</th>
                <th>Fecha Préstamo</th>
                <th>Fecha Devolución</th>
                <th>Estado</th>
                {puedeGestionar && <th>Acciones</th>}
              </tr>
            </thead>

            <tbody>
              {loans.length > 0 ? (
                loans.map((loan) => (
                  <tr key={loan.id_prestamo}>
                    <td>{loan.id_prestamo}</td>
                    <td>{`${loan.nombre_usuario || ""} ${loan.apellido || ""}`}</td>
                    <td>{loan.titulo_libro}</td>
                    <td>{loan.fecha_prestamo}</td>
                    <td>{loan.fecha_devolucion || "-"}</td>
                    <td>
                      {loan.estado === "prestado" ? (
                        <span className="badge badge-warning">Prestado</span>
                      ) : loan.estado === "devuelto" ? (
                        <span className="badge badge-success">Devuelto</span>
                      ) : (
                        <span className="badge badge-danger">Atrasado</span>
                      )}
                    </td>

                    {puedeGestionar && (
                      <td>
                        <div className="action-buttons">
                          <Link
                            to={`/loans/edit/${loan.id_prestamo}`}
                            className="btn-action btn-edit"
                            title="Editar"
                          >
                            <FaPenToSquare />
                          </Link>

                          <button
                            type="button"
                            className="btn-action btn-delete"
                            title="Eliminar"
                            onClick={() => handleDelete(loan.id_prestamo)}
                          >
                            <FaTrash />
                          </button>
                        </div>
                      </td>
                    )}
                  </tr>
                ))
              ) : (
                <tr>
                  <td
                    colSpan={puedeGestionar ? 7 : 6}
                    className="empty-row"
                  >
                    No hay préstamos registrados
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </main>
    </div>
  );
}