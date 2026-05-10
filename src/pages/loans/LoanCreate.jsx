import { useMemo, useState } from "react";
import { useNavigate } from "react-router-dom";
import PageLayout from "../../components/layout/PageLayout";
import FormCard from "../../components/ui/FormCard";
import AlertMessage from "../../components/ui/AlertMessage";
import InputField from "../../components/ui/InputField";
import SelectField from "../../components/ui/SelectField";
import FormActions from "../../components/ui/FormActions";
import { useForm } from "../../hooks/useForm";
import { useApiList } from "../../hooks/useApiList";
import { useAuthUser } from "../../hooks/useAuthUser";
import { apiRequest } from "../../services/api";
import "./LoanCreate.css";

function localToday() {
  const now = new Date();
  return new Date(now.getTime() - now.getTimezoneOffset() * 60000)
    .toISOString().split("T")[0];
}
function maxLoanDate() {
  const now = new Date();
  now.setDate(now.getDate() + 21);
  return new Date(now.getTime() - now.getTimezoneOffset() * 60000)
    .toISOString().split("T")[0];
}

export default function LoanCreate() {
  const navigate = useNavigate();
  const { authUser, canManage } = useAuthUser();
  const today = useMemo(localToday, []);
  const maxDate = useMemo(maxLoanDate, []);

  const { form, handleChange, resetForm, error: formError, setError, success, setSuccess } = useForm({
    id_usuario: authUser.id ? String(authUser.id) : "",
    id_libro: "", fecha_prestamo: today, fecha_devolucion: "",
  });
  const [loading, setLoading] = useState(false);

  const { data: usuarios } = useApiList("/usuarios");
  const { data: libros } = useApiList("/libros");

  const usuarioOptions = useMemo(
    () => usuarios.map((u) => ({ value: u.id_usuario, label: `${u.nombre} ${u.apellido}` })),
    [usuarios]
  );
  const libroOptions = useMemo(
    () => libros.map((l) => ({ value: l.id_libro, label: `${l.titulo} (Disponibles: ${l.cantidad})` })),
    [libros]
  );

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!form.id_usuario) return setError("Debe seleccionar un usuario.");
    if (!form.id_libro) return setError("Debe seleccionar un libro.");
    if (!form.fecha_prestamo) return setError("Debe seleccionar la fecha de préstamo.");
    if (form.fecha_devolucion && form.fecha_devolucion < form.fecha_prestamo)
      return setError("La fecha de devolución no puede ser menor que la fecha de préstamo.");

    try {
      setLoading(true); setError(""); setSuccess("");
      await apiRequest("/prestamos", {
        method: "POST",
        body: JSON.stringify({
          id_usuario: form.id_usuario, id_libro: form.id_libro,
          fecha_prestamo: form.fecha_prestamo,
          fecha_devolucion: form.fecha_devolucion || null,
        }),
      });
      setSuccess("Préstamo guardado correctamente.");
      resetForm();
      setTimeout(() => navigate("/loans"), 1000);
    } catch (err) {
      setError(err.message || "Error al guardar el préstamo.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <PageLayout>
      <FormCard icon={null} title="Añadir Préstamo">
        <AlertMessage type="danger" message={formError} onClose={() => setError("")} />
        <AlertMessage type="success" message={success} onClose={() => setSuccess("")} />
        {libros.length === 0 && (
          <AlertMessage type="warning" message="No hay libros disponibles." />
        )}

        <form onSubmit={handleSubmit}>
          {canManage ? (
            <SelectField
              label="Usuario"
              name="id_usuario"
              value={form.id_usuario}
              onChange={handleChange}
              placeholder="Seleccione"
              options={usuarioOptions}
            />
          ) : (
            <InputField
              label="Usuario"
              name="id_usuario_display"
              value={`${authUser.nombre} ${authUser.apellido}`}
              disabled
            />
          )}

          <SelectField
            label="Libro"
            name="id_libro"
            value={form.id_libro}
            onChange={handleChange}
            placeholder="Seleccione"
            options={libroOptions}
          />

          <div className="loan-form-grid">
            <InputField
              label="Fecha de Préstamo"
              name="fecha_prestamo"
              type="date"
              value={form.fecha_prestamo}
              onChange={handleChange}
              min={today}
              max={maxDate}
              required
            />
            <InputField
              label="Fecha de Devolución"
              name="fecha_devolucion"
              type="date"
              value={form.fecha_devolucion}
              onChange={handleChange}
              min={form.fecha_prestamo || today}
              max={maxDate}
            />
          </div>

          <FormActions
            cancelTo="/loans"
            submitLabel="Guardar"
            loadingLabel="Guardando..."
            loading={loading}
            disabled={libros.length === 0}
          />
        </form>
      </FormCard>
    </PageLayout>
  );
}
