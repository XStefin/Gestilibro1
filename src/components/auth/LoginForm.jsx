import InputField from "../ui/InputField";
import AlertMessage from "../ui/AlertMessage";

/**
 * LoginForm – formulario de inicio de sesión.
 */
export default function LoginForm({ form, onChange, onSubmit, loading, error, onSwitchToRegister }) {
  return (
    <form className="login-form" onSubmit={onSubmit}>
      <InputField
        label="Usuario"
        name="username"
        placeholder="tu_usuario"
        value={form.username}
        onChange={onChange}
        autoComplete="username"
      />

      <InputField
        label="Contraseña"
        name="password"
        type="password"
        placeholder="••••••••"
        value={form.password}
        onChange={onChange}
        autoComplete="current-password"
      />

      <AlertMessage type="danger" message={error} />

      <button type="submit" disabled={loading}>
        {loading ? "INGRESANDO..." : "INICIAR SESIÓN"}
      </button>

      <p className="login-switch">
        ¿No tienes cuenta?{" "}
        <a onClick={onSwitchToRegister} role="button" tabIndex={0}>
          Regístrate aquí
        </a>
      </p>
    </form>
  );
}
