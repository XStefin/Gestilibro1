import { useState } from "react";
import { Link } from "react-router-dom";
import { FaUserPlus } from "react-icons/fa6";
import Sidebar from "../../components/layout/Sidebar";
import "./UserCreate.css";

export default function UserCreate() {
  const [form, setForm] = useState({
    nombre: "",
    apellido: "",
    correo: "",
    username: "",
    contrasena: "",
    rol: "",
    pin: "",
    active: true,
  });

  const [error, setError] = useState("");
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

    if (!form.contrasena.trim()) {
      setError("La contraseña es obligatoria.");
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
    setSuccess("Usuario guardado correctamente.");

    setForm({
      nombre: "",
      apellido: "",
      correo: "",
      username: "",
      contrasena: "",
      rol: "",
      pin: "",
      active: true,
    });
  };

  return (
    <div className="user-create-layout">
      <Sidebar />

      <main className="user-create-content">
        <div className="user-create-card">
          <h4 className="user-create-title">
            <FaUserPlus />
            <span>Nuevo Usuario</span>
          </h4>

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

          <form onSubmit={handleSubmit} className="user-form">
            <div className="form-group">
              <label htmlFor="nombre" className="form-label">
                Nombre
              </label>
              <input
                type="text"
                name="nombre"
                id="nombre"
                className="form-control"
                value={form.nombre}
                onChange={handleChange}
                required
              />
            </div>

            <div className="form-group">
              <label htmlFor="apellido" className="form-label">
                Apellido
              </label>
              <input
                type="text"
                name="apellido"
                id="apellido"
                className="form-control"
                value={form.apellido}
                onChange={handleChange}
                required
              />
            </div>

            <div className="form-group">
              <label htmlFor="correo" className="form-label">
                Correo
              </label>
              <input
                type="email"
                name="correo"
                id="correo"
                className="form-control"
                value={form.correo}
                onChange={handleChange}
                required
              />
            </div>

            <div className="form-group">
              <label htmlFor="username" className="form-label">
                Username
              </label>
              <input
                type="text"
                name="username"
                id="username"
                className="form-control"
                value={form.username}
                onChange={handleChange}
                required
              />
            </div>

            <div className="form-group">
              <label htmlFor="contrasena" className="form-label">
                Contraseña
              </label>
              <input
                type="password"
                name="contrasena"
                id="contrasena"
                className="form-control"
                value={form.contrasena}
                onChange={handleChange}
                required
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
              >
                <option value="">Seleccione un rol</option>
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
                placeholder="Ej: 1234"
                value={form.pin}
                onChange={handleChange}
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
              />
              <label htmlFor="active" className="form-check-label">
                Activo
              </label>
            </div>

            <div className="form-actions">
              <button type="submit" className="btn-save">
                Guardar
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