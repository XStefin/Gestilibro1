import { useState } from "react";
import { Link } from "react-router-dom";
import { FaTag, FaTags } from "react-icons/fa6";
import PageLayout from "../../components/layout/PageLayout";
import PageHeader from "../../components/ui/PageHeader";
import AlertMessage from "../../components/ui/AlertMessage";
import TableStatusRow from "../../components/ui/TableStatusRow";
import CategoryTableRow from "../../components/categories/CategoryTableRow";
import { useApiList } from "../../hooks/useApiList";
import { apiRequest } from "../../services/api";
import "./CategoriesList.css";

export default function CategoriesList() {
  const { data: categories, loading, error: fetchError, reload } = useApiList("/categorias");
  const [actionMsg, setActionMsg] = useState({ type: "", text: "" });

  const handleDelete = async (id) => {
    if (!window.confirm("¿Eliminar categoría?")) return;
    try {
      await apiRequest(`/categorias/${id}`, { method: "DELETE" });
      reload();
    } catch (err) {
      setActionMsg({ type: "danger", text: err.message || "No se pudo eliminar la categoría" });
    }
  };

  return (
    <PageLayout>
      <AlertMessage
        type={actionMsg.type}
        message={actionMsg.text}
        onClose={() => setActionMsg({ type: "", text: "" })}
      />

      <PageHeader
        icon={<FaTags />}
        title="Gestión de Categorías"
        action={
          <Link to="/categories/create" className="btn-new-category">
            <FaTag />
            <span>Nueva Categoría</span>
          </Link>
        }
      />

      <div className="categories-table-wrapper">
        <table className="categories-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Descripción</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            {loading ? (
              <TableStatusRow colSpan={4} message="Cargando categorías..." />
            ) : fetchError ? (
              <TableStatusRow colSpan={4} message={fetchError} />
            ) : categories.length > 0 ? (
              categories.map((cat) => (
                <CategoryTableRow
                  key={cat.id_categoria}
                  category={cat}
                  onDelete={handleDelete}
                />
              ))
            ) : (
              <TableStatusRow colSpan={4} message="No hay categorías registradas." />
            )}
          </tbody>
        </table>
      </div>
    </PageLayout>
  );
}
