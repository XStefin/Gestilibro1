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
import { useAuthUser } from "../../hooks/useAuthUser";
import "./Sidebar.css";

/**
 * Sidebar – barra lateral de navegación.
 * Lee el usuario autenticado desde useAuthUser.
 */
export default function Sidebar() {
  const location = useLocation();
  const navigate = useNavigate();
  const { authUser, rol } = useAuthUser();

  const handleLogout = (e) => {
    e.preventDefault();
    localStorage.removeItem("token");
    localStorage.removeItem("user");
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
            src="/images/usuario.jpg"
            alt="Usuario"
            className="sidebar-user-image"
            onError={(e) => {
              e.target.src =
                "https://png.pngtree.com/png-clipart/20191120/original/pngtree-outline-user-icon-png-image_5045523.jpg";
            }}
          />
          <h6>{authUser.nombreCompleto}</h6>
          <small>{authUser.rol}</small>
        </div>

        <nav className="sidebar-nav">
          {(rol === "administrador" || rol === "bibliotecario") && (
            <Link
              to="/dashboard"
              className={isActive("/dashboard") ? "active" : ""}
            >
              <FaHouse />
              <span>Home</span>
            </Link>
          )}

          {rol === "administrador" && (
            <>
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
