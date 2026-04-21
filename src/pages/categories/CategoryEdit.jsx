import { useMemo, useState } from "react";
import { Link, useParams } from "react-router-dom";
import Sidebar from "../../components/layout/Sidebar";
import "./CategoryEdit.css";

export default function CategoryEdit() {
  const { id } = useParams();

  const categoriesMock = [
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
  ];

  const categoriaInicial = useMemo(() => {
    return (
      categoriesMock.find(
        (categoria) => String(categoria.id_categoria) === String(id)
      ) || null
    );
  }, [id]);

  const [form, setForm] = useState(
    categoriaInicial || {
      nombre: "",
      descripcion: "",
    }
  );

  const [warning, setWarning] = useState(
    categoriaInicial ? "" : "No se encontró la categoría a editar."
  );
  const [success, setSuccess] = useState("");

  const handleChange = (e) => {
    const { name, value } = e.target;

    setForm((prev) => ({
      ...prev,
      [name]: value,
    }));

    if (warning === "No se encontró la categoría a editar.") return;

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
    setSuccess("Categoría actualizada correctamente.");
  };

  return (
    <div className="category-edit-layout">
      <Sidebar />

      <main className="category-edit-content">
        <div className="category-edit-card">
          <h2 className="category-edit-title">Editar Categoría</h2>

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
                disabled={!categoriaInicial}
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
                disabled={!categoriaInicial}
              />
            </div>

            <div className="form-actions">
              <button
                type="submit"
                className="btn-save"
                disabled={!categoriaInicial}
              >
                Actualizar
              </button>

              <Link to="/categories" className="btn-cancel">
                Cancelar
              </Link>
            </div>
          </form>
        </div>
      </main>
    </div>
  );
}