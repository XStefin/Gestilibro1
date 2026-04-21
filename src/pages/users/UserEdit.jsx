import { useMemo, useState } from "react";
import { Link, useParams } from "react-router-dom";
import Sidebar from "../../components/layout/Sidebar";
import "./UserEdit.css";

export default function UserEdit() {
  const { id } = useParams();

  const usersMock = [
    {
      id_usuario: 1,
      nombre: "Diana",
      apellido: "Sterpin",
      correo: "diana@example.com",
      username: "dsterpin",
      rol: "Administrador",
      pin: "1234",
      active: true,
    },
    {
      id_usuario: 2,
      nombre: "Carlos",
      apellido: "Ramírez",
      correo: "carlos@example.com",
      username: "cramirez",
      rol: "Bibliotecario",
      pin: "5678",
      active: true,
    },
    {
      id_usuario: 3,
      nombre: "Laura",
      apellido: "Gómez",
      correo: "laura@example.com",
      username: "lgomez",
      rol: "Estudiante",
      pin: "9876",
      active: false,
    },
  ];

  const usuarioInicial = useMemo(() => {
    return usersMock.find((u) => String(u.id_usuario) === String(id)) || null;
  }, [id]);

  const [form, setForm] = useState(
    usuarioInicial || {
      nombre: "",
      apellido: "",
      correo: "",
      username: "",
      contrasena: "",
      rol: "Estudiante",
      pin: "",
      active: false,
    }
  );

  const [error, setError] = useState(
    usuarioInicial ? "" : "No se encontró el usuario a editar."
  );
  const [success, setSuccess] = useState("");

  const handleChange = (e) => {
    const { name, value, type, checked } = e.target;

    if (name === "pin") {
      const onlyNumbers = value.replace(/\D/g, "").slice(0, 4);
      setForm((prev) => ({
        ...prev,
        [name]: onlyNumbers,
      }));
    } else if (type === "checkbox") {
      setForm((prev) => ({
        ...prev,
        [name]: checked,
      }));
    } else {
      setForm((prev) => ({
        ...prev,
        [name]: value,
      }));
    }

    if (error === "No se encontró el usuario a editar.") return;

    if (error) setError("");
    if (success) setSuccess("");
  };

  const handleSubmit = (e) => {
    e.preventDefault();

    if (!form.nombre.trim()) {
      setError("El nombre es obligatorio.");
      return;
    }

    if (!form.apellido.trim()) {
      setError("El apellido es obligatorio.");
      return;
    }

    if (!form.correo.trim()) {
      setError("El correo es obligatorio.");
      return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(form.correo)) {
      setError("Debe ingresar un correo válido.");
      return;
    }

    if (!form.username.trim()) {
      setError("El username es obligatorio.");
      return;
    }

    if (!form.rol) {
      setError("Debe seleccionar un rol.");
      return;
    }

    if (form.pin && !/^\d{4}$/.test(form.pin)) {
      setError("El PIN debe tener exactamente 4 dígitos.");
      return;
    }

    setError("");
    setSuccess("Usuario actualizado correctamente.");
  };

  return (
    <div className="user-edit-layout">
      <Sidebar />

      <main className="user-edit-content">
        <div className="user-edit-card">
          <h4 className="user-edit-title">Editar Usuario</h4>

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

          <form onSubmit={handleSubmit} className="user-edit-form">
            <div className="form-group">
              <label className="form-label">Nombre</label>
              <input
                type="text"
                name="nombre"
                value={form.nombre}
                className="form-control"
                onChange={handleChange}
                required
                disabled={!usuarioInicial}
              />
            </div>

            <div className="form-group">
              <label className="form-label">Apellido</label>
              <input
                type="text"
                name="apellido"
                value={form.apellido}
                className="form-control"
                onChange={handleChange}
                required
                disabled={!usuarioInicial}
              />
            </div>

            <div className="form-group">
              <label className="form-label">Correo</label>
              <input
                type="email"
                name="correo"
                value={form.correo}
                className="form-control"
                onChange={handleChange}
                required
                disabled={!usuarioInicial}
              />
            </div>

            <div className="form-group">
              <label className="form-label">Username</label>
              <input
                type="text"
                name="username"
                value={form.username}
                className="form-control"
                onChange={handleChange}
                required
                disabled={!usuarioInicial}
              />
            </div>

            <div className="form-group">
              <label className="form-label">Nueva Contraseña (opcional)</label>
              <input
                type="password"
                name="contrasena"
                value={form.contrasena}
                className="form-control"
                onChange={handleChange}
                disabled={!usuarioInicial}
              />
            </div>

            <div className="form-group">
              <label className="form-label">Rol</label>
              <select
                name="rol"
                className="form-control"
                value={form.rol}
                onChange={handleChange}
                required
                disabled={!usuarioInicial}
              >
                <option value="Administrador">Administrador</option>
                <option value="Bibliotecario">Bibliotecario</option>
                <option value="Estudiante">Estudiante</option>
              </select>
            </div>

            <div className="form-group">
              <label className="form-label">PIN</label>
              <input
                type="text"
                name="pin"
                className="form-control"
                maxLength="4"
                value={form.pin}
                onChange={handleChange}
                disabled={!usuarioInicial}
              />
            </div>

            <div className="form-check">
              <input
                type="checkbox"
                name="active"
                id="active"
                className="form-check-input"
                checked={form.active}
                onChange={handleChange}
                disabled={!usuarioInicial}
              />
              <label htmlFor="active" className="form-check-label">
                Activo
              </label>
            </div>

            <div className="form-actions">
              <button className="btn-save" disabled={!usuarioInicial}>
                Actualizar
              </button>

              <Link to="/users" className="btn-cancel">
                Cancelar
              </Link>
            </div>
          </form>
        </div>
      </main>
    </div>
  );
}