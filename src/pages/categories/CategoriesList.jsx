import { useState } from "react";
import { Link } from "react-router-dom";
import { FaPenToSquare, FaTag, FaTags, FaTrash } from "react-icons/fa6";
import Sidebar from "../../components/layout/Sidebar";
import "./CategoriesList.css";

export default function CategoriesList() {
  const [categories, setCategories] = useState([
    {
      id_categoria: 1,
      nombre: "Novela",
      descripcion: "Libros de narrativa y ficción",
    },
    {
      id_categoria: 2,
      nombre: "Tecnología",
      descripcion: "Libros de programación, software y sistemas",
    },
    {
      id_categoria: 3,
      nombre: "Historia",
      descripcion: "Textos históricos y documentales",
    },
  ]);

  const handleDelete = (id) => {
    const confirmDelete = window.confirm("¿Eliminar categoría?");
    if (!confirmDelete) return;

    setCategories((prev) =>
      prev.filter((category) => category.id_categoria !== id)
    );
  };

  return (
    <div className="categories-layout">
      <Sidebar />

      <main className="categories-content">
        <h4 className="categories-title">
          <FaTags className="title-icon" />
          Gestión de Categorías
        </h4>

        <div className="categories-actions">
          <Link to="/categories/create" className="btn-new-category">
            <FaTag />
            <span>Nueva Categoría</span>
          </Link>
        </div>

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
              {categories.length > 0 ? (
                categories.map((category) => (
                  <tr key={category.id_categoria}>
                    <td>{category.id_categoria}</td>
                    <td>{category.nombre}</td>
                    <td>{category.descripcion}</td>
                    <td>
                      <div className="action-buttons">
                        <Link
                          to={`/categories/edit/${category.id_categoria}`}
                          className="btn-action btn-edit"
                          title="Editar"
                        >
                          <FaPenToSquare />
                        </Link>

                        <button
                          type="button"
                          className="btn-action btn-delete"
                          title="Eliminar"
                          onClick={() => handleDelete(category.id_categoria)}
                        >
                          <FaTrash />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan="4" className="empty-row">
                    No hay categorías registradas.
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