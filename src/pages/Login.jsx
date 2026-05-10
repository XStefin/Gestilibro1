import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { FaBook } from "react-icons/fa";
import { useForm } from "../hooks/useForm";
import { apiRequest } from "../services/api";
import LoginForm from "../components/auth/LoginForm";
import RegisterForm from "../components/auth/RegisterForm";
import PinModal from "../components/auth/PinModal";
import "./Login.css";

const APP_NAME = "Gestibiblio";

export default function Login() {
  const navigate = useNavigate();
  const [view, setView] = useState("login");

  // ── Formulario de login ──
  const {
    form: loginForm,
    handleChange: handleLoginChange,
    error: loginError,
    setError: setLoginError,
    loading: loginLoading,
    setLoading: setLoginLoading,
  } = useForm({ username: "", password: "" });

  const [loginLoading2, setLoginLoading2] = useState(false);
  const [loginError2, setLoginError2] = useState("");

  // ── Formulario de registro ──
  const {
    form: registerForm,
    handleChange: handleRegisterChange,
    resetForm: resetRegisterForm,
    error: registerError,
    setError: setRegisterError,
    success: registerSuccess,
    setSuccess: setRegisterSuccess,
  } = useForm({ nombre: "", apellido: "", username: "", correo: "", contrasena: "", esRegistro: true });

  const [regLoading, setRegLoading] = useState(false);

  // ── PIN modal ──
  const [showPin, setShowPin] = useState(false);
  const [pinLoading, setPinLoading] = useState(false);
  const [pinError, setPinError] = useState("");
  const [pinSuccess, setPinSuccess] = useState("");

  // ── Handlers login ──
  const handleLoginSubmit = async (e) => {
    e.preventDefault();
    setLoginLoading2(true);
    setLoginError2("");
    try {
      const result = await apiRequest("/login", {
        method: "POST",
        body: JSON.stringify({ username: loginForm.username, password: loginForm.password }),
      });
      localStorage.setItem("token", result.token);
      localStorage.setItem("user", JSON.stringify(result.user));
      const rol = (result.user?.rol || "").toLowerCase();
      if(result.user?.active == 0) {
        setLoginError2("No fue posible iniciar sesión");
      }else{
        navigate(rol === "administrador" || rol === "bibliotecario" ? "/dashboard" : "/books");
      }
    } catch (err) {
      setLoginError2(err.message || "No fue posible iniciar sesión");
    } finally {
      setLoginLoading2(false);
    }
  };

  // ── Handlers registro ──
  const handleRegisterSubmit = async (e) => {
    e.preventDefault();
    setRegLoading(true);
    setRegisterError("");
    setRegisterSuccess("");
    try {
      await apiRequest("/usuarios", { method: "POST", body: JSON.stringify(registerForm) });
      setShowPin(true);
    } catch (err) {
      setRegisterError(err.message || "No fue posible crear la cuenta");
    } finally {
      setRegLoading(false);
    }
  };

  // ── PIN handlers ──
  const handleVerifyPin = async (pin) => {
    setPinLoading(true);
    setPinError("");
    setPinSuccess("");
    try {
      await apiRequest("/verify-pin", {
        method: "POST",
        body: JSON.stringify({ correo: registerForm.correo, pin }),
      });
      setPinSuccess("¡Cuenta verificada! Redirigiendo al login…");
      setTimeout(() => {
        setShowPin(false);
        resetRegisterForm();
        switchView("login");
      }, 1800);
    } catch (err) {
      setPinError(err.message || "PIN incorrecto. Inténtalo de nuevo.");
    } finally {
      setPinLoading(false);
    }
  };

  const handleResendPin = async () => {
    setPinLoading(true);
    setPinError("");
    setPinSuccess("");
    try {
      await apiRequest("/resend-pin", {
        method: "POST",
        body: JSON.stringify({ correo: registerForm.correo }),
      });
      setPinSuccess("Código reenviado. Revisa tu correo.");
    } catch (err) {
      setPinError(err.message || "No se pudo reenviar el código.");
    } finally {
      setPinLoading(false);
    }
  };

  const switchView = (newView) => {
    setView(newView);
    setLoginError2("");
    setRegisterError("");
    setRegisterSuccess("");
  };

  return (
    <>
      {showPin && (
        <PinModal
          email={registerForm.correo}
          onVerify={handleVerifyPin}
          onResend={handleResendPin}
          onClose={() => { setShowPin(false); setPinError(""); setPinSuccess(""); }}
          loading={pinLoading}
          error={pinError}
          success={pinSuccess}
        />
      )}

      <div className="login-page">
        <div className="login-container">
          <h1>
            <FaBook className="login-icon" />
            {APP_NAME}
          </h1>
          <p>Sistema de Gestión Bibliotecaria</p>

          <div className="login-tabs">
            <button
              type="button"
              className={`login-tab ${view === "login" ? "login-tab--active" : ""}`}
              onClick={() => switchView("login")}
            >
              Iniciar sesión
            </button>
            <button
              type="button"
              className={`login-tab ${view === "register" ? "login-tab--active" : ""}`}
              onClick={() => switchView("register")}
            >
              Registrarse
            </button>
          </div>

          {view === "login" && (
            <LoginForm
              form={loginForm}
              onChange={handleLoginChange}
              onSubmit={handleLoginSubmit}
              loading={loginLoading2}
              error={loginError2}
              onSwitchToRegister={() => switchView("register")}
            />
          )}

          {view === "register" && (
            <RegisterForm
              form={registerForm}
              onChange={handleRegisterChange}
              onSubmit={handleRegisterSubmit}
              loading={regLoading}
              error={registerError}
              success={registerSuccess}
              onSwitchToLogin={() => switchView("login")}
            />
          )}
        </div>
      </div>
    </>
  );
}
