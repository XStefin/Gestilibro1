import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { FaUserPlus } from "react-icons/fa6";
import PageLayout from "../../components/layout/PageLayout";
import FormCard from "../../components/ui/FormCard";
import AlertMessage from "../../components/ui/AlertMessage";
import InputField from "../../components/ui/InputField";
import SelectField from "../../components/ui/SelectField";
import CheckboxField from "../../components/ui/CheckboxField";
import FormActions from "../../components/ui/FormActions";
import { useForm } from "../../hooks/useForm";
import { apiRequest } from "../../services/api";
import "./UserCreate.css";

const ROL_OPTIONS = [
  { value: "Administrador", label: "Administrador" },
  { value: "Bibliotecario", label: "Bibliotecario" },
  { value: "Estudiante", label: "Estudiante" },
];

const EMAIL_REGEX = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

export default function UserCreate() {
  const navigate = useNavigate();
  const { form, handleChange, resetForm, error, setError, success, setSuccess } = useForm({
    nombre: "", apellido: "", correo: "", username: "",
    contrasena: "", rol: "", pin: "", active: true,
  });
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!form.nombre.trim()) return setError("El nombre es obligatorio.");
    if (!form.apellido.trim()) return setError("El apellido es obligatorio.");
    if (!form.correo.trim()) return setError("El correo es obligatorio.");
    if (!EMAIL_REGEX.test(form.correo)) return setError("Debe ingresar un correo válido.");
    if (!form.username.trim()) return setError("El username es obligatorio.");
    if (!form.contrasena.trim()) return setError("La contraseña es obligatoria.");
    if (!form.rol) return setError("Debe seleccionar un rol.");
    if (form.pin && !/^\d{4}$/.test(form.pin)) return setError("El PIN debe tener exactamente 4 dígitos.");

    try {
      setLoading(true); setError(""); setSuccess("");
      await apiRequest("/usuarios", {
        method: "POST",
        body: JSON.stringify({
          nombre: form.nombre.trim(), apellido: form.apellido.trim(),
          correo: form.correo.trim(), username: form.username.trim(),
          contrasena: form.contrasena, rol: form.rol,
          pin: form.pin || "", active: form.active ? 1 : 0, esRegistro: false,
        }),
      });
      setSuccess("Usuario guardado correctamente.");
      resetForm();
      setTimeout(() => navigate("/users"), 1000);
    } catch (err) {
      setError(err.message || "No fue posible guardar el usuario.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <PageLayout>
      <FormCard icon={<FaUserPlus />} title="Nuevo Usuario">
        <AlertMessage type="danger" message={error} onClose={() => setError("")} />
        <AlertMessage type="success" message={success} onClose={() => setSuccess("")} />

        <form onSubmit={handleSubmit} className="user-form">
          <InputField label="Nombre" name="nombre" value={form.nombre} onChange={handleChange} required />
          <InputField label="Apellido" name="apellido" value={form.apellido} onChange={handleChange} required />
          <InputField label="Correo" name="correo" type="email" value={form.correo} onChange={handleChange} required />
          <InputField label="Username" name="username" value={form.username} onChange={handleChange} required />
          <InputField label="Contraseña" name="contrasena" type="password" value={form.contrasena} onChange={handleChange} required />
          <SelectField label="Rol" name="rol" value={form.rol} onChange={handleChange} required placeholder="Seleccione un rol" options={ROL_OPTIONS} />
          <InputField label="PIN" name="pin" maxLength="4" placeholder="Ej: 1234" value={form.pin} onChange={handleChange} />
          <CheckboxField label="Activo" name="active" id="active" checked={form.active} onChange={handleChange} />

          <FormActions cancelTo="/users" submitLabel="Guardar" loadingLabel="Guardando..." loading={loading} />
        </form>
      </FormCard>
    </PageLayout>
  );
}
