import { Link, useLocation, useNavigate } from "react-router-dom";
import {
  FaBook,
  FaBookOpen,
  FaHandshake,
  FaHouse,
  FaRightFromBracket,
  FaTags,
  FaUser,
} from "react-icons/fa6";
import "./Sidebar.css";

export default function Sidebar() {
  const location = useLocation();
  const navigate = useNavigate();

  // Datos simulados por ahora
  const authUser = {
    nombreCompleto: "Diana Sterpin",
    rol: "Administrador",
    foto: "/images/usuario.jpg", // opcional si luego agregas la imagen en public/images
  };

  const rol = authUser.rol.toLowerCase();

  const handleLogout = (e) => {
    e.preventDefault();
    navigate("/login");
  };

  const isActive = (path) => location.pathname === path;

  return (
    <aside className="sidebar">
      <div>
        <h4 className="sidebar-title">
          <FaBook className="me-2" />
          <span>Gestibiblio</span>
        </h4>

        <div className="sidebar-user">
          <img
            src={authUser.foto}
            alt="Usuario"
            className="sidebar-user-image"
            onError={(e) => {
              e.target.src =
                "https://via.placeholder.com/80x80.png?text=User";
            }}
          />
          <h6>{authUser.nombreCompleto}</h6>
          <small>{authUser.rol}</small>
        </div>

        <nav className="sidebar-nav">
          {rol === "administrador" && (
            <>
              <Link
                to="/dashboard"
                className={isActive("/dashboard") ? "active" : ""}
              >
                <FaHouse />
                <span>Home</span>
              </Link>

              <Link to="/users" className={isActive("/users") ? "active" : ""}>
                <FaUser />
                <span>Usuarios</span>
              </Link>

              <Link
                to="/categories"
                className={isActive("/categories") ? "active" : ""}
              >
                <FaTags />
                <span>Categorías</span>
              </Link>
            </>
          )}

          <Link to="/books" className={isActive("/books") ? "active" : ""}>
            <FaBookOpen />
            <span>Libros</span>
          </Link>

          <Link to="/loans" className={isActive("/loans") ? "active" : ""}>
            <FaHandshake />
            <span>Préstamos</span>
          </Link>
        </nav>
      </div>

      <a href="/login" className="sidebar-logout" onClick={handleLogout}>
        <FaRightFromBracket />
        <span>Cerrar Sesión</span>
      </a>
    </aside>
  );
}