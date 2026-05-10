import { useMemo, useState } from "react";
import { Link } from "react-router-dom";
import { FaBook, FaBookMedical } from "react-icons/fa6";
import PageLayout from "../../components/layout/PageLayout";
import PageHeader from "../../components/ui/PageHeader";
import AlertMessage from "../../components/ui/AlertMessage";
import TableStatusRow from "../../components/ui/TableStatusRow";
import BookFilterBar from "../../components/books/BookFilterBar";
import BookTableRow from "../../components/books/BookTableRow";
import { useApiList } from "../../hooks/useApiList";
import { useAuthUser } from "../../hooks/useAuthUser";
import { apiRequest } from "../../services/api";
import "./BooksList.css";

export default function BooksList() {
  const { canManage } = useAuthUser();
  const { data: books, loading, error: fetchError, reload } = useApiList("/libros");
  const [filter, setFilter] = useState("");
  const [actionMsg, setActionMsg] = useState({ type: "", text: "" });

  const filteredBooks = useMemo(() => {
    if (!filter) return books;
    return books.filter((b) => b.disponibilidad === filter);
  }, [books, filter]);

  const handleDelete = async (id) => {
    if (!window.confirm("¿Seguro que deseas eliminar este libro?")) return;
    try {
      await apiRequest(`/libros/${id}`, { method: "DELETE" });
      reload();
      setActionMsg({ type: "success", text: "Libro eliminado correctamente." });
    } catch (err) {
      setActionMsg({ type: "danger", text: err.message || "No fue posible eliminar el libro." });
    }
  };

  const colSpan = canManage ? 9 : 8;

  return (
    <PageLayout>
      <AlertMessage type={actionMsg.type} message={actionMsg.text} onClose={() => setActionMsg({ type: "", text: "" })} />

      <PageHeader
        icon={<FaBook />}
        title="Gestión de Libros"
        action={
          canManage && (
            <Link to="/books/create" className="btn-new-book">
              <FaBookMedical />
              <span>Nuevo Libro</span>
            </Link>
          )
        }
      />

      <BookFilterBar value={filter} onChange={setFilter} />

      <div className="books-table-wrapper">
        <table className="books-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Título</th>
              <th>Autor</th>
              <th>Editorial</th>
              <th>Año</th>
              <th>ID Categoría</th>
              <th>Cantidad</th>
              <th>Disponibilidad</th>
              {canManage && <th>Acciones</th>}
            </tr>
          </thead>
          <tbody>
            {loading ? (
              <TableStatusRow colSpan={colSpan} message="Cargando libros..." />
            ) : fetchError ? (
              <TableStatusRow colSpan={colSpan} message={fetchError} />
            ) : filteredBooks.length > 0 ? (
              filteredBooks.map((book) => (
                <BookTableRow
                  key={book.id_libro}
                  book={book}
                  canManage={canManage}
                  onDelete={handleDelete}
                />
              ))
            ) : (
              <TableStatusRow colSpan={colSpan} message="No hay libros registrados" />
            )}
          </tbody>
        </table>
      </div>
    </PageLayout>
  );
}
