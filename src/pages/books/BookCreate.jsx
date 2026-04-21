import { useState } from "react";
import { Link } from "react-router-dom";
import { FaPlus } from "react-icons/fa6";
import Sidebar from "../../components/layout/Sidebar";
import "./BookCreate.css";

export default function BookCreate() {
  const categorias = [
    { id_categoria: 1, nombre: "Tecnología" },
    { id_categoria: 2, nombre: "Novela" },
    { id_categoria: 3, nombre: "Historia" },
    { id_categoria: 4, nombre: "Educación" },
  ];

  const [form, setForm] = useState({
    titulo: "",
    autor: "",
    editorial: "",
    anio: "",
    id_categoria: "",
    cantidad: 1,
  });

  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");

  const handleChange = (e) => {
    const { name, value } = e.target;

    if (name === "anio") {
      const onlyNumbers = value.replace(/\D/g, "").slice(0, 4);
      setForm((prev) => ({ ...prev, [name]: onlyNumbers }));
    } else {
      setForm((prev) => ({ ...prev, [name]: value }));
    }

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
    setSuccess("Libro guardado correctamente.");

    setForm({
      titulo: "",
      autor: "",
      editorial: "",
      anio: "",
      id_categoria: "",
      cantidad: 1,
    });
  };

  return (
    <div className="book-create-layout">
      <Sidebar />

      <main className="book-create-content">
        <div className="book-create-card">
          <h2 className="book-create-title">
            <FaPlus />
            <span>Añadir Libro</span>
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
                  placeholder="Ej: 2026"
                  value={form.anio}
                  onChange={handleChange}
                  required
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
                >
                  <option value="">Seleccione una categoría</option>
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
                />
              </div>
            </div>

            <div className="form-actions">
              <button type="submit" className="btn-save">
                Guardar
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