import { useMemo, useState } from "react";
import { Link, useParams } from "react-router-dom";
import { FaPenToSquare } from "react-icons/fa6";
import Sidebar from "../../components/layout/Sidebar";
import "./BookEdit.css";

export default function BookEdit() {
  const { id } = useParams();

  const categorias = [
    { id_categoria: 1, nombre: "Tecnología" },
    { id_categoria: 2, nombre: "Novela" },
    { id_categoria: 3, nombre: "Historia" },
    { id_categoria: 4, nombre: "Educación" },
  ];

  const librosMock = [
    {
      id_libro: 1,
      titulo: "Clean Code",
      autor: "Robert C. Martin",
      editorial: "Prentice Hall",
      anio: "2008",
      id_categoria: 1,
      cantidad: 8,
      disponibilidad: "disponible",
    },
    {
      id_libro: 2,
      titulo: "Cien años de soledad",
      autor: "Gabriel García Márquez",
      editorial: "Sudamericana",
      anio: "1967",
      id_categoria: 2,
      cantidad: 4,
      disponibilidad: "no_disponible",
    },
    {
      id_libro: 3,
      titulo: "Introducción a la Historia",
      autor: "Marc Bloch",
      editorial: "FCE",
      anio: "1999",
      id_categoria: 3,
      cantidad: 6,
      disponibilidad: "disponible",
    },
  ];

  const libroInicial = useMemo(() => {
    return (
      librosMock.find((libro) => String(libro.id_libro) === String(id)) || null
    );
  }, [id]);

  const [form, setForm] = useState(
    libroInicial || {
      titulo: "",
      autor: "",
      editorial: "",
      anio: "",
      id_categoria: "",
      cantidad: 1,
      disponibilidad: "disponible",
    }
  );

  const [error, setError] = useState(
    libroInicial ? "" : "No se encontró el libro a editar."
  );
  const [success, setSuccess] = useState("");

  const handleChange = (e) => {
    const { name, value } = e.target;

    if (name === "anio") {
      const onlyNumbers = value.replace(/\D/g, "").slice(0, 4);
      setForm((prev) => ({ ...prev, [name]: onlyNumbers }));
    } else {
      setForm((prev) => ({ ...prev, [name]: value }));
    }

    if (error === "No se encontró el libro a editar.") return;

    if (error) setError("");
    if (success) setSuccess("");
  };

  const handleSubmit = (e) => {
    e.preventDefault();

    if (!form.titulo.trim()) {
      setError("El título es obligatorio.");
      return;
    }

    if (!form.autor.trim()) {
      setError("El autor es obligatorio.");
      return;
    }

    if (!/^\d{4}$/.test(form.anio)) {
      setError("El año debe tener 4 dígitos.");
      return;
    }

    if (!form.id_categoria) {
      setError("Debe seleccionar una categoría.");
      return;
    }

    if (Number(form.cantidad) < 1) {
      setError("La cantidad debe ser mayor o igual a 1.");
      return;
    }

    setError("");
    setSuccess("Libro actualizado correctamente.");
  };

  return (
    <div className="book-edit-layout">
      <Sidebar />

      <main className="book-edit-content">
        <div className="book-edit-card">
          <h2 className="book-edit-title">
            <FaPenToSquare />
            <span>Editar Libro</span>
          </h2>

          {error && (
            <div className="alert-box alert-danger">
              <span>{error}</span>
              <button type="button" onClick={() => setError("")}>
                ×
              </button>
            </div>
          )}

          {success && (
            <div className="alert-box alert-success">
              <span>{success}</span>
              <button type="button" onClick={() => setSuccess("")}>
                ×
              </button>
            </div>
          )}

          <form onSubmit={handleSubmit}>
            <div className="form-group">
              <label className="form-label">Título</label>
              <input
                type="text"
                name="titulo"
                className="form-control"
                value={form.titulo}
                onChange={handleChange}
                required
                disabled={!libroInicial}
              />
            </div>

            <div className="form-group">
              <label className="form-label">Autor</label>
              <input
                type="text"
                name="autor"
                className="form-control"
                value={form.autor}
                onChange={handleChange}
                required
                disabled={!libroInicial}
              />
            </div>

            <div className="form-group">
              <label className="form-label">Editorial</label>
              <input
                type="text"
                name="editorial"
                className="form-control"
                value={form.editorial}
                onChange={handleChange}
                disabled={!libroInicial}
              />
            </div>

            <div className="form-grid">
              <div className="form-group">
                <label className="form-label">Año</label>
                <input
                  type="text"
                  name="anio"
                  className="form-control"
                  maxLength="4"
                  value={form.anio}
                  onChange={handleChange}
                  required
                  disabled={!libroInicial}
                />
              </div>

              <div className="form-group">
                <label className="form-label">Categoría</label>
                <select
                  name="id_categoria"
                  className="form-control"
                  value={form.id_categoria}
                  onChange={handleChange}
                  required
                  disabled={!libroInicial}
                >
                  {categorias.map((c) => (
                    <option key={c.id_categoria} value={c.id_categoria}>
                      {c.nombre}
                    </option>
                  ))}
                </select>
              </div>

              <div className="form-group">
                <label className="form-label">Cantidad</label>
                <input
                  type="number"
                  name="cantidad"
                  className="form-control"
                  min="1"
                  step="1"
                  value={form.cantidad}
                  onChange={handleChange}
                  required
                  disabled={!libroInicial}
                />
              </div>
            </div>

            <div className="form-group">
              <label className="form-label">Disponibilidad</label>
              <select
                name="disponibilidad"
                className="form-control"
                value={form.disponibilidad}
                onChange={handleChange}
                disabled={!libroInicial}
              >
                <option value="disponible">Disponible</option>
                <option value="no_disponible">No disponible</option>
              </select>
            </div>

            <div className="form-actions">
              <button type="submit" className="btn-save" disabled={!libroInicial}>
                Actualizar
              </button>

              <Link to="/books" className="btn-cancel">
                Cancelar
              </Link>
            </div>
          </form>
        </div>
      </main>
    </div>
  );
}