import { useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router-dom";
import PageLayout from "../../components/layout/PageLayout";
import FormCard from "../../components/ui/FormCard";
import AlertMessage from "../../components/ui/AlertMessage";
import InputField from "../../components/ui/InputField";
import SelectField from "../../components/ui/SelectField";
import CheckboxField from "../../components/ui/CheckboxField";
import FormActions from "../../components/ui/FormActions";
import { useForm } from "../../hooks/useForm";
import { apiRequest } from "../../services/api";
import "./UserEdit.css";

const ROL_OPTIONS = [
  { value: "Administrador", label: "Administrador" },
  { value: "Bibliotecario", label: "Bibliotecario" },
  { value: "Estudiante", label: "Estudiante" },
];
const EMAIL_REGEX = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

export default function UserEdit() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { form, setForm, handleChange, error, setError, success, setSuccess } = useForm({
    nombre: "", apellido: "", correo: "", username: "",
    contrasena: "", rol: "Estudiante", pin: "", active: false,
  });
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [userFound, setUserFound] = useState(true);

  useEffect(() => {
    (async () => {
      try {
        setLoading(true);
        const data = await apiRequest(`/usuarios/${id}`);
        setForm({
          nombre: data.nombre || "", apellido: data.apellido || "",
          correo: data.correo || "", username: data.username || "",
          contrasena: "", rol: data.rol || "Estudiante",
          pin: data.pin || "",
          active: data.active === true || data.active === 1 || data.active === "1",
        });
        setUserFound(true);
      } catch (err) {
        setUserFound(false);
        setError(err.message || "No se encontró el usuario a editar.");
      } finally {
        setLoading(false);
      }
    })();
  }, [id]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!form.nombre.trim()) return setError("El nombre es obligatorio.");
    if (!form.apellido.trim()) return setError("El apellido es obligatorio.");
    if (!form.correo.trim()) return setError("El correo es obligatorio.");
    if (!EMAIL_REGEX.test(form.correo)) return setError("Debe ingresar un correo válido.");
    if (!form.username.trim()) return setError("El username es obligatorio.");
    if (!form.rol) return setError("Debe seleccionar un rol.");
    if (form.pin && !/^\d{4}$/.test(form.pin)) return setError("El PIN debe tener exactamente 4 dígitos.");

    try {
      setSaving(true); setError(""); setSuccess("");
      const payload = {
        nombre: form.nombre.trim(), apellido: form.apellido.trim(),
        correo: form.correo.trim(), username: form.username.trim(),
        rol: form.rol, pin: form.pin || "", active: form.active ? 1 : 0,
      };
      if (form.contrasena.trim()) payload.contrasena = form.contrasena;

      await apiRequest(`/usuarios/${id}`, { method: "PUT", body: JSON.stringify(payload) });
      setSuccess("Usuario actualizado correctamente.");
      setTimeout(() => navigate("/users"), 1000);
    } catch (err) {
      setError(err.message || "No fue posible actualizar el usuario.");
    } finally {
      setSaving(false);
    }
  };

  return (
    <PageLayout>
      <FormCard icon={null} title="Editar Usuario">
        <AlertMessage type="danger" message={error} onClose={() => setError("")} />
        <AlertMessage type="success" message={success} onClose={() => setSuccess("")} />

        {loading ? (
          <AlertMessage type="info" message="Cargando usuario..." />
        ) : (
          <form onSubmit={handleSubmit} className="user-edit-form">
            <InputField label="Nombre" name="nombre" value={form.nombre} onChange={handleChange} required disabled={!userFound} />
            <InputField label="Apellido" name="apellido" value={form.apellido} onChange={handleChange} required disabled={!userFound} />
            <InputField label="Correo" name="correo" type="email" value={form.correo} onChange={handleChange} required disabled={!userFound} />
            <InputField label="Username" name="username" value={form.username} onChange={handleChange} required disabled={!userFound} />
            <InputField label="Nueva Contraseña (opcional)" name="contrasena" type="password" value={form.contrasena} onChange={handleChange} disabled={!userFound} />
            <SelectField label="Rol" name="rol" value={form.rol} onChange={handleChange} required disabled={!userFound} options={ROL_OPTIONS} placeholder={null} />
            <InputField label="PIN" name="pin" maxLength="4" value={form.pin} onChange={handleChange} disabled={!userFound} />
            <CheckboxField label="Activo" name="active" id="active" checked={form.active} onChange={handleChange} disabled={!userFound} />

            <FormActions cancelTo="/users" submitLabel="Actualizar" loadingLabel="Actualizando..." loading={saving} disabled={!userFound} />
          </form>
        )}
      </FormCard>
    </PageLayout>
  );
}
