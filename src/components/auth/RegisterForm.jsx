import InputField from "../ui/InputField";
import AlertMessage from "../ui/AlertMessage";

/**
 * RegisterForm – formulario de registro de nueva cuenta.
 */
export default function RegisterForm({
  form,
  onChange,
  onSubmit,
  loading,
  error,
  success,
  onSwitchToLogin,
}) {
  return (
    <form className="login-form" onSubmit={onSubmit}>
      <div className="login-row">
        <InputField
          label="Nombres"
          name="nombre"
          placeholder="Ana María"
          value={form.nombre}
          onChange={onChange}
          autoComplete="given-name"
        />
        <InputField
          label="Apellidos"
          name="apellido"
          placeholder="García López"
          value={form.apellido}
          onChange={onChange}
          autoComplete="family-name"
        />
      </div>

      <InputField
        label="Nombre de usuario"
        name="username"
        placeholder="ana.garcia"
        value={form.username}
        onChange={onChange}
        autoComplete="username"
      />

      <InputField
        label="Correo electrónico"
        name="correo"
        type="email"
        placeholder="ana@correo.com"
        value={form.correo}
        onChange={onChange}
        autoComplete="email"
      />

      <InputField
        label="Contraseña"
        name="contrasena"
        type="password"
        placeholder="••••••••"
        value={form.contrasena}
        onChange={onChange}
        autoComplete="new-password"
      />

      <AlertMessage type="danger" message={error} />
      <AlertMessage type="success" message={success} />

      <button type="submit" disabled={loading}>
        {loading ? "ENVIANDO..." : "CREAR CUENTA"}
      </button>

      <p className="login-switch">
        ¿Ya tienes cuenta?{" "}
        <a onClick={onSwitchToLogin} role="button" tabIndex={0}>
          Inicia sesión
        </a>
      </p>
    </form>
  );
}
