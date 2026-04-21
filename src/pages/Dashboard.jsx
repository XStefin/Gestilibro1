import Sidebar from "../components/layout/Sidebar";
import "./Dashboard.css";

export default function Dashboard() {
  const dashboardData = {
    libros: 120,
    prestamos: 32,
    usuarios: 18,
  };

  return (
    <div className="dashboard-layout">
      <Sidebar />

      <main className="dashboard-content">
        <h5 className="dashboard-heading">Dashboard</h5>

        <div className="dashboard-cards">
          <div className="dashboard-card bg-light-blue">
            <h6>Libros Totales</h6>
            <h3>{dashboardData.libros}</h3>
          </div>

          <div className="dashboard-card bg-light-pink">
            <h6>Préstamos activos</h6>
            <h3>{dashboardData.prestamos}</h3>
          </div>

          <div className="dashboard-card bg-light-purple">
            <h6>Usuarios</h6>
            <h3>{dashboardData.usuarios}</h3>
          </div>
        </div>
      </main>
    </div>
  );
}