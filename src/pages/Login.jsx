import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { FaBook } from "react-icons/fa";
import "./Login.css";

const APP_NAME = "Gestibiblio";

export default function Login() {
  const navigate = useNavigate();

  const [form, setForm] = useState({
    username: "",
    password: "",
  });

  const [loading, setLoading] = useState(false);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setForm((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);

    setTimeout(() => {
      setLoading(false);
      navigate("/dashboard");
    }, 500);
  };

  return (
    <div className="login-page">
      <div className="login-container">
        <h1>
          <FaBook className="login-icon" />
          {APP_NAME}
        </h1>

        <p>Ingresa tus credenciales</p>

        <form onSubmit={handleSubmit}>
          <input
            type="text"
            name="username"
            placeholder="Username"
            value={form.username}
            onChange={handleChange}
            required
          />

          <input
            type="password"
            name="password"
            placeholder="Contraseña"
            value={form.password}
            onChange={handleChange}
            required
          />

          <button type="submit" disabled={loading}>
            {loading ? "INGRESANDO..." : "INICIAR SESIÓN"}
          </button>
        </form>

        <a href="#">¿Olvidaste tu contraseña?</a>
      </div>
    </div>
  );
}