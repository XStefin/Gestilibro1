import { useEffect, useState } from "react";
import PageLayout from "../components/layout/PageLayout";
import AlertMessage from "../components/ui/AlertMessage";
import { apiRequest } from "../services/api";
import "./Dashboard.css";

export default function Dashboard() {
  const [data, setData] = useState({ libros: 0, prestamos: 0, usuarios: 0 });
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    (async () => {
      try {
        setLoading(true);
        const res = await apiRequest("/dashboard");
        setData({
          libros: res?.data?.libros ?? 0,
          prestamos: res?.data?.prestamos ?? 0,
          usuarios: res?.data?.usuarios ?? 0,
        });
      } catch (err) {
        setError(err.message || "No fue posible cargar el dashboard");
      } finally {
        setLoading(false);
      }
    })();
  }, []);

  return (
    <PageLayout>
      <h5 className="dashboard-heading">Dashboard</h5>
      <AlertMessage type="danger" message={error} onClose={() => setError("")} />

      <div className="dashboard-cards">
        <div className="dashboard-card bg-light-blue">
          <h6>Libros Totales</h6>
          <h3>{loading ? "..." : data.libros}</h3>
        </div>
        <div className="dashboard-card bg-light-pink">
          <h6>Préstamos activos</h6>
          <h3>{loading ? "..." : data.prestamos}</h3>
        </div>
        <div className="dashboard-card bg-light-purple">
          <h6>Usuarios</h6>
          <h3>{loading ? "..." : data.usuarios}</h3>
        </div>
      </div>
    </PageLayout>
  );
}
