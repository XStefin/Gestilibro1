import { useMemo, useState } from "react";
import { Link } from "react-router-dom";
import {
  FaBook,
  FaBookMedical,
  FaPenToSquare,
  FaTrash,
} from "react-icons/fa6";
import Sidebar from "../../components/layout/Sidebar";
import "./BooksList.css";

export default function BooksList() {
  // Usuario simulado
  const authUser = {
    rol: "Administrador",
  };

  const rol = authUser.rol.toLowerCase();

  const [success, setSuccess] = useState("");
  const [error, setError] = useState("");
  const [filter, setFilter] = useState("");

  const [books, setBooks] = useState([
    {
      id_libro: 1,
      titulo: "Clean Code",
      autor: "Robert C. Martin",
      editorial: "Prentice Hall",
      anio: 2008,
      categoria: "Tecnología",
      cantidad: 8,
      copias_disponibles: 5,
      disponibilidad: "disponible",
    },
    {
      id_libro: 2,
      titulo: "Cien años de soledad",
      autor: "Gabriel García Márquez",
      editorial: "Sudamericana",
      anio: 1967,
      categoria: "Novela",
      cantidad: 4,
      copias_disponibles: 0,
      disponibilidad: "no_disponible",
    },
    {
      id_libro: 3,
      titulo: "Introducción a la Historia",
      autor: "Marc Bloch",
      editorial: "FCE",
      anio: 1999,
      categoria: "Historia",
      cantidad: 6,
      copias_disponibles: 2,
      disponibilidad: "disponible",
    },
  ]);

  const filteredBooks = useMemo(() => {
    if (!filter) return books;
    return books.filter((book) => book.disponibilidad === filter);
  }, [books, filter]);

  const handleDelete = (id) => {
    const confirmDelete = window.confirm(
      "¿Seguro que deseas eliminar este libro?"
    );

    if (!confirmDelete) return;

    setBooks((prev) => prev.filter((book) => book.id_libro !== id));
    setSuccess("Libro eliminado correctamente.");
    setError("");
  };

  return (
    <div className="books-layout">
      <Sidebar />

      <main className="books-content">
        {success && (
          <div className="alert-box alert-success">
            <span>{success}</span>
            <button type="button" onClick={() => setSuccess("")}>
              ×
            </button>
          </div>
        )}

        {error && (
          <div className="alert-box alert-danger">
            <span>{error}</span>
            <button type="button" onClick={() => setError("")}>
              ×
            </button>
          </div>
        )}

        <div className="books-header">
          <h4 className="books-title">
            <FaBook />
            <span>Gestión de Libros</span>
          </h4>

          {(rol === "administrador" || rol === "bibliotecario") && (
            <Link to="/books/create" className="btn-new-book">
              <FaBookMedical />
              <span>Nuevo Libro</span>
            </Link>
          )}
        </div>

        <div className="books-filter-box">
          <select
            name="disponibilidad"
            className="books-filter-select"
            value={filter}
            onChange={(e) => setFilter(e.target.value)}
          >
            <option value="">-- Filtrar por disponibilidad --</option>
            <option value="disponible">Disponible</option>
            <option value="no_disponible">No disponible</option>
          </select>

          <button type="button" className="btn-filter">
            Filtrar
          </button>
        </div>

        <div className="books-table-wrapper">
          <table className="books-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Autor</th>
                <th>Editorial</th>
                <th>Año</th>
                <th>Categoría</th>
                <th>Cantidad Total</th>
                <th>Copias Disponibles</th>
                <th>Disponibilidad</th>
                {(rol === "administrador" || rol === "bibliotecario") && (
                  <th>Acciones</th>
                )}
              </tr>
            </thead>

            <tbody>
              {filteredBooks.length > 0 ? (
                filteredBooks.map((book) => (
                  <tr key={book.id_libro}>
                    <td>{book.id_libro}</td>
                    <td>{book.titulo}</td>
                    <td>{book.autor}</td>
                    <td>{book.editorial}</td>
                    <td>{book.anio}</td>
                    <td>{book.categoria}</td>
                    <td>{book.cantidad}</td>
                    <td>{book.copias_disponibles}</td>
                    <td>
                      {book.disponibilidad === "disponible" ? (
                        <span className="badge badge-success">Disponible</span>
                      ) : (
                        <span className="badge badge-secondary">
                          No disponible
                        </span>
                      )}
                    </td>

                    {(rol === "administrador" || rol === "bibliotecario") && (
                      <td>
                        <div className="action-buttons">
                          <Link
                            to={`/books/edit/${book.id_libro}`}
                            className="btn-action btn-edit"
                            title="Editar"
                          >
                            <FaPenToSquare />
                          </Link>

                          <button
                            type="button"
                            className="btn-action btn-delete"
                            title="Eliminar"
                            onClick={() => handleDelete(book.id_libro)}
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
                    colSpan={
                      rol === "administrador" || rol === "bibliotecario"
                        ? 10
                        : 9
                    }
                    className="empty-row"
                  >
                    No hay libros registrados
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