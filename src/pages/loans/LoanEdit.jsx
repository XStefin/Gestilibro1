import { useEffect, useMemo, useState } from "react";
import { useNavigate, useParams } from "react-router-dom";
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
import "./LoanEdit.css";

const ESTADO_OPTIONS = [
  { value: "prestado", label: "Prestado" },
  { value: "devuelto", label: "Devuelto" },
  { value: "atrasado", label: "Atrasado" },
];

export default function LoanEdit() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { authUser, canManage } = useAuthUser();

  const { form, setForm, handleChange, error, setError, success, setSuccess } = useForm({
    id_usuario: authUser.id ? String(authUser.id) : "",
    id_libro: "", fecha_prestamo: "", fecha_devolucion: "", estado: "prestado",
  });
  const [loadingLoan, setLoadingLoan] = useState(true);
  const [saving, setSaving] = useState(false);
  const [loanFound, setLoanFound] = useState(true);

  const { data: usuarios, loading: loadingUsers } = useApiList("/usuarios");
  const { data: libros, loading: loadingBooks } = useApiList("/libros");

  const usuarioOptions = useMemo(
    () => usuarios.map((u) => ({ value: u.id_usuario, label: `${u.nombre} ${u.apellido}` })),
    [usuarios]
  );
  const libroOptions = useMemo(
    () => libros.map((l) => ({ value: l.id_libro, label: l.titulo })),
    [libros]
  );

  const usuarioActual = useMemo(
    () => usuarios.find((u) => String(u.id_usuario) === String(form.id_usuario)) || null,
    [usuarios, form.id_usuario]
  );

  useEffect(() => {
    (async () => {
      try {
        setLoadingLoan(true);
        const data = await apiRequest(`/prestamos/${id}`);
        setForm({
          id_usuario: data.id_usuario ? String(data.id_usuario) : "",
          id_libro: data.id_libro ? String(data.id_libro) : "",
          fecha_prestamo: data.fecha_prestamo || "",
          fecha_devolucion: data.fecha_devolucion || "",
          estado: data.estado || "prestado",
        });
        setLoanFound(true);
      } catch (err) {
        setLoanFound(false);
        setError(err.message || "No se encontró el préstamo a editar.");
      } finally {
        setLoadingLoan(false);
      }
    })();
  }, [id]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!form.id_usuario) return setError("Debe seleccionar un usuario.");
    if (!form.id_libro) return setError("Debe seleccionar un libro.");
    if (!form.fecha_prestamo) return setError("Debe seleccionar la fecha de préstamo.");
    if (form.fecha_devolucion && form.fecha_devolucion < form.fecha_prestamo)
      return setError("La fecha de devolución no puede ser menor que la fecha de préstamo.");
    if (!form.estado) return setError("Debe seleccionar un estado.");

    try {
      setSaving(true); setError(""); setSuccess("");
      await apiRequest(`/prestamos/${id}`, {
        method: "PUT",
        body: JSON.stringify({
          id_usuario: Number(form.id_usuario), id_libro: Number(form.id_libro),
          fecha_prestamo: form.fecha_prestamo,
          fecha_devolucion: form.fecha_devolucion || null,
          estado: form.estado,
        }),
      });
      setSuccess("Préstamo actualizado correctamente.");
      setTimeout(() => navigate("/loans"), 1000);
    } catch (err) {
      setError(err.message || "No fue posible actualizar el préstamo.");
    } finally {
      setSaving(false);
    }
  };

  return (
    <PageLayout>
      <FormCard icon={null} title="Editar Préstamo">
        <AlertMessage type="danger" message={error} onClose={() => setError("")} />
        <AlertMessage type="success" message={success} onClose={() => setSuccess("")} />

        {loadingLoan ? (
          <AlertMessage type="info" message="Cargando préstamo..." />
        ) : (
          <form onSubmit={handleSubmit}>
            {canManage ? (
              <SelectField
                label="Usuario"
                name="id_usuario"
                value={form.id_usuario}
                onChange={handleChange}
                required
                disabled={!loanFound || loadingUsers}
                placeholder={loadingUsers ? "Cargando usuarios..." : "Seleccione un usuario"}
                options={usuarioOptions}
              />
            ) : (
              <>
                <input type="hidden" name="id_usuario" value={form.id_usuario} />
                <InputField
                  label="Usuario"
                  name="_usuario_display"
                  value={
                    usuarioActual
                      ? `${usuarioActual.nombre} ${usuarioActual.apellido}`
                      : `${authUser.nombre} ${authUser.apellido}`.trim()
                  }
                  disabled
                />
              </>
            )}

            <SelectField
              label="Libro"
              name="id_libro"
              value={form.id_libro}
              onChange={handleChange}
              required
              disabled={!loanFound || loadingBooks}
              placeholder={loadingBooks ? "Cargando libros..." : "Seleccione un libro"}
              options={libroOptions}
            />

            <InputField
              label="Fecha de Préstamo"
              name="fecha_prestamo"
              type="date"
              value={form.fecha_prestamo}
              onChange={handleChange}
              required
              disabled={!loanFound}
            />
            <InputField
              label="Fecha de Devolución"
              name="fecha_devolucion"
              type="date"
              value={form.fecha_devolucion}
              onChange={handleChange}
              min={form.fecha_prestamo || undefined}
              disabled={!loanFound}
            />

            <SelectField
              label="Estado"
              name="estado"
              value={form.estado}
              onChange={handleChange}
              disabled={!loanFound}
              options={ESTADO_OPTIONS}
              placeholder={null}
            />

            <FormActions
              cancelTo="/loans"
              submitLabel="Actualizar"
              loadingLabel="Actualizando..."
              loading={saving}
              disabled={!loanFound}
            />
          </form>
        )}
      </FormCard>
    </PageLayout>
  );
}
