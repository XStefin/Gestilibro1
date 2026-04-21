import { useMemo, useState } from "react";
import { Link } from "react-router-dom";
import Sidebar from "../../components/layout/Sidebar";
import "./LoanCreate.css";

export default function LoanCreate() {
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
    { id_libro: 1, titulo: "Clean Code", copias_disponibles: 5 },
    { id_libro: 2, titulo: "Introducción a la Historia", copias_disponibles: 2 },
    { id_libro: 3, titulo: "Patrones de Diseño", copias_disponibles: 1 },
  ];

  const today = useMemo(() => {
    const now = new Date();
    const offset = now.getTimezoneOffset();
    const localDate = new Date(now.getTime() - offset * 60000);
    return localDate.toISOString().split("T")[0];
  }, []);

  const maxDate = useMemo(() => {
    const now = new Date();
    now.setDate(now.getDate() + 21);
    const offset = now.getTimezoneOffset();
    const localDate = new Date(now.getTime() - offset * 60000);
    return localDate.toISOString().split("T")[0];
  }, []);

  const [form, setForm] = useState({
    id_usuario: String(authUser.id_usuario),
    id_libro: "",
    fecha_prestamo: today,
    fecha_devolucion: "",
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

    if (!form.id_usuario) {
      setWarning("Debe seleccionar un usuario.");
      return;
    }

    if (!form.id_libro) {
      setWarning("Debe seleccionar un libro.");
      return;
    }

    if (!form.fecha_prestamo) {
      setWarning("Debe seleccionar la fecha de préstamo.");
      return;
    }

    if (
      form.fecha_devolucion &&
      form.fecha_devolucion < form.fecha_prestamo
    ) {
      setWarning("La fecha de devolución no puede ser menor que la fecha de préstamo.");
      return;
    }

    setWarning("");
    setSuccess("Préstamo guardado correctamente.");

    setForm({
      id_usuario: String(authUser.id_usuario),
      id_libro: "",
      fecha_prestamo: today,
      fecha_devolucion: "",
    });
  };

  return (
    <div className="loan-create-layout">
      <Sidebar />

      <main className="loan-create-content">
        <div className="loan-create-card">
          <h2 className="loan-create-title">Añadir Préstamo</h2>

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

          {libros.length === 0 && (
            <div className="alert-box alert-warning simple-alert">
              <span>No hay libros disponibles para préstamo.</span>
            </div>
          )}

          <form onSubmit={handleSubmit}>
            <div className="form-group">
              <label className="form-label">Usuario</label>

              {puedeCambiarUsuario ? (
                <select
                  name="id_usuario"
                  className="form-control"
                  value={form.id_usuario}
                  onChange={handleChange}
                  required
                >
                  <option value="">Seleccione un usuario</option>
                  {usuarios.map((usuario) => (
                    <option key={usuario.id_usuario} value={usuario.id_usuario}>
                      {usuario.nombre} {usuario.apellido}
                    </option>
                  ))}
                </select>
              ) : (
                <>
                  <input type="hidden" name="id_usuario" value={form.id_usuario} />
                  <select className="form-control" disabled>
                    {usuarios
                      .filter(
                        (usuario) =>
                          String(usuario.id_usuario) === String(authUser.id_usuario)
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
              <label className="form-label">Libro</label>
              <select
                name="id_libro"
                className="form-control"
                value={form.id_libro}
                onChange={handleChange}
                required
              >
                <option value="">Seleccione un libro</option>
                {libros.map((libro) => (
                  <option key={libro.id_libro} value={libro.id_libro}>
                    {libro.titulo} (Disponibles: {libro.copias_disponibles})
                  </option>
                ))}
              </select>
            </div>

            <div className="loan-form-grid">
              <div className="form-group">
                <label className="form-label">Fecha de Préstamo</label>
                <input
                  type="date"
                  name="fecha_prestamo"
                  min={today}
                  max={maxDate}
                  className="form-control"
                  value={form.fecha_prestamo}
                  onChange={handleChange}
                  required
                />
              </div>

              <div className="form-group">
                <label className="form-label">Fecha de Devolución</label>
                <input
                  type="date"
                  name="fecha_devolucion"
                  min={form.fecha_prestamo || today}
                  max={maxDate}
                  className="form-control"
                  value={form.fecha_devolucion}
                  onChange={handleChange}
                />
              </div>
            </div>

            <div className="form-actions">
              <button
                type="submit"
                className="btn-save"
                disabled={libros.length === 0}
              >
                Guardar
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