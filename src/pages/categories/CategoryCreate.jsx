import { useState } from "react";
import { Link } from "react-router-dom";
import Sidebar from "../../components/layout/Sidebar";
import "./CategoryCreate.css";

export default function CategoryCreate() {
  const [form, setForm] = useState({
    nombre: "",
    descripcion: "",
  });

  const [warning, setWarning] = useState("");
  const [success, setSuccess] = useState("");

  const handleChange = (e) => {
    const { name, value } = e.target;

    setForm((prev) => ({
      ...prev,
      [name]: value,
    }));

    if (warning) setWarning("");
    if (success) setSuccess("");
  };

  const handleSubmit = (e) => {
    e.preventDefault();

    if (!form.nombre.trim()) {
      setWarning("El nombre de la categoría es obligatorio.");
      setSuccess("");
      return;
    }

    setWarning("");
    setSuccess("Categoría guardada correctamente.");

    setForm({
      nombre: "",
      descripcion: "",
    });
  };

  return (
    <div className="category-create-layout">
      <Sidebar />

      <main className="category-create-content">
        <div className="category-create-card">
          <h2 className="category-create-title">Añadir Categoría</h2>

          {warning && (
            <div className="alert-box alert-warning">
              <span>{warning}</span>
              <button type="button" onClick={() => setWarning("")}>
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
              <label>Nombre</label>
              <input
                type="text"
                name="nombre"
                className="form-control"
                value={form.nombre}
                onChange={handleChange}
                required
              />
            </div>

            <div className="form-group">
              <label>Descripción</label>
              <textarea
                name="descripcion"
                className="form-control"
                rows="4"
                value={form.descripcion}
                onChange={handleChange}
              />
            </div>

            <div className="form-actions">
              <Link to="/categories" className="btn-cancel">
                Cancelar
              </Link>

              <button type="submit" className="btn-save">
                Guardar
              </button>
            </div>
          </form>
        </div>
      </main>
    </div>
  );
}