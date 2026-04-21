import { useMemo, useState } from "react";
import { Link, useParams } from "react-router-dom";
import Sidebar from "../../components/layout/Sidebar";
import "./LoanEdit.css";

export default function LoanEdit() {
  const { id } = useParams();

  const authUser = {
    id_usuario: 2,
    nombre: "Carlos",
    apellido: "Ramírez",
    rol: "Administrador",
  };

  const rol = authUser.rol.toLowerCase();
  const puedeCambiarUsuario = ["administrador", "bibliotecario"].includes(rol);

  const usuarios = [
    { id_usuario: 1, nombre: "Diana", apellido: "Sterpin" },
    { id_usuario: 2, nombre: "Carlos", apellido: "Ramírez" },
    { id_usuario: 3, nombre: "Laura", apellido: "Gómez" },
  ];

  const libros = [
    { id_libro: 1, titulo: "Clean Code" },
    { id_libro: 2, titulo: "Cien años de soledad" },
    { id_libro: 3, titulo: "Introducción a la Historia" },
  ];

  const prestamosMock = [
    {
      id_prestamo: 1,
      id_usuario: 1,
      id_libro: 1,
      fecha_prestamo: "2026-04-20",
      fecha_devolucion: "2026-04-28",
      estado: "prestado",
    },
    {
      id_prestamo: 2,
      id_usuario: 2,
      id_libro: 2,
      fecha_prestamo: "2026-04-10",
      fecha_devolucion: "2026-04-17",
      estado: "devuelto",
    },
    {
      id_prestamo: 3,
      id_usuario: 3,
      id_libro: 3,
      fecha_prestamo: "2026-04-01",
      fecha_devolucion: "",
      estado: "atrasado",
    },
  ];

  const prestamoInicial = useMemo(() => {
    return (
      prestamosMock.find(
        (prestamo) => String(prestamo.id_prestamo) === String(id)
      ) || null
    );
  }, [id]);

  const [form, setForm] = useState(
    prestamoInicial || {
      id_usuario: String(authUser.id_usuario),
      id_libro: "",
      fecha_prestamo: "",
      fecha_devolucion: "",
      estado: "prestado",
    }
  );

  const [error, setError] = useState(
    prestamoInicial ? "" : "No se encontró el préstamo a editar."
  );
  const [success, setSuccess] = useState("");

  const handleChange = (e) => {
    const { name, value } = e.target;

    setForm((prev) => ({
      ...prev,
      [name]: value,
    }));

    if (error === "No se encontró el préstamo a editar.") return;

    if (error) setError("");
    if (success) setSuccess("");
  };

  const handleSubmit = (e) => {
    e.preventDefault();

    if (!form.id_usuario) {
      setError("Debe seleccionar un usuario.");
      return;
    }

    if (!form.id_libro) {
      setError("Debe seleccionar un libro.");
      return;
    }

    if (!form.fecha_prestamo) {
      setError("Debe seleccionar la fecha de préstamo.");
      return;
    }

    if (
      form.fecha_devolucion &&
      form.fecha_devolucion < form.fecha_prestamo
    ) {
      setError(
        "La fecha de devolución no puede ser menor que la fecha de préstamo."
      );
      return;
    }

    if (!form.estado) {
      setError("Debe seleccionar un estado.");
      return;
    }

    setError("");
    setSuccess("Préstamo actualizado correctamente.");
  };

  return (
    <div className="loan-edit-layout">
      <Sidebar />

      <main className="loan-edit-content">
        <div className="loan-edit-card">
          <h2 className="loan-edit-title">Editar Préstamo</h2>

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
              <label htmlFor="id_usuario" className="form-label">
                Usuario
              </label>

              {puedeCambiarUsuario ? (
                <select
                  name="id_usuario"
                  id="id_usuario"
                  className="form-control"
                  value={form.id_usuario}
                  onChange={handleChange}
                  required
                  disabled={!prestamoInicial}
                >
                  {usuarios.map((usuario) => (
                    <option key={usuario.id_usuario} value={usuario.id_usuario}>
                      {usuario.nombre} {usuario.apellido}
                    </option>
                  ))}
                </select>
              ) : (
                <>
                  <input
                    type="hidden"
                    name="id_usuario"
                    value={form.id_usuario}
                  />

                  <select className="form-control" disabled>
                    {usuarios
                      .filter(
                        (usuario) =>
                          String(usuario.id_usuario) ===
                          String(prestamoInicial?.id_usuario)
                      )
                      .map((usuario) => (
                        <option key={usuario.id_usuario}>
                          {usuario.nombre} {usuario.apellido}
                        </option>
                      ))}
                  </select>
                </>
              )}
            </div>

            <div className="form-group">
              <label htmlFor="id_libro" className="form-label">
                Libro
              </label>
              <select
                name="id_libro"
                id="id_libro"
                className="form-control"
                value={form.id_libro}
                onChange={handleChange}
                required
                disabled={!prestamoInicial}
              >
                {libros.map((libro) => (
                  <option key={libro.id_libro} value={libro.id_libro}>
                    {libro.titulo}
                  </option>
                ))}
              </select>
            </div>

            <div className="form-group">
              <label htmlFor="fecha_prestamo" className="form-label">
                Fecha de Préstamo
              </label>
              <input
                type="date"
                name="fecha_prestamo"
                id="fecha_prestamo"
                className="form-control"
                value={form.fecha_prestamo}
                onChange={handleChange}
                required
                disabled={!prestamoInicial}
              />
            </div>

            <div className="form-group">
              <label htmlFor="fecha_devolucion" className="form-label">
                Fecha de Devolución
              </label>
              <input
                type="date"
                name="fecha_devolucion"
                id="fecha_devolucion"
                className="form-control"
                value={form.fecha_devolucion}
                onChange={handleChange}
                disabled={!prestamoInicial}
              />
            </div>

            <div className="form-group">
              <label htmlFor="estado" className="form-label">
                Estado
              </label>
              <select
                name="estado"
                id="estado"
                className="form-control"
                value={form.estado}
                onChange={handleChange}
                disabled={!prestamoInicial}
              >
                <option value="prestado">Prestado</option>
                <option value="devuelto">Devuelto</option>
                <option value="atrasado">Atrasado</option>
              </select>
            </div>

            <div className="form-actions">
              <button
                type="submit"
                className="btn-save"
                disabled={!prestamoInicial}
              >
                Actualizar
              </button>

              <Link to="/loans" className="btn-cancel">
                Cancelar
              </Link>
            </div>
          </form>
        </div>
      </main>
    </div>
  );
}