import { useEffect, useMemo, useState } from "react";
import { useNavigate, useParams } from "react-router-dom";
import { FaPenToSquare } from "react-icons/fa6";
import PageLayout from "../../components/layout/PageLayout";
import FormCard from "../../components/ui/FormCard";
import AlertMessage from "../../components/ui/AlertMessage";
import InputField from "../../components/ui/InputField";
import SelectField from "../../components/ui/SelectField";
import FormActions from "../../components/ui/FormActions";
import { useForm } from "../../hooks/useForm";
import { useApiList } from "../../hooks/useApiList";
import { apiRequest } from "../../services/api";
import "./BookEdit.css";

const maxDate = (() => {
  const now = new Date();
  return new Date(now.getTime() - now.getTimezoneOffset() * 60000)
    .toISOString()
    .split("T")[0];
})();

export default function BookEdit() {
  const { id } = useParams();
  const navigate = useNavigate();

  const { form, setForm, handleChange, error, setError, success, setSuccess } = useForm({
    titulo: "", autor: "", editorial: "", anio: "",
    id_categoria: "", cantidad: 1, disponibilidad: "disponible",
  });
  const [loadingBook, setLoadingBook] = useState(true);
  const [saving, setSaving] = useState(false);
  const [bookFound, setBookFound] = useState(true);

  const { data: categorias, loading: loadingCategorias } = useApiList("/categorias");
  const categoriaOptions = useMemo(
    () => categorias.map((c) => ({ value: c.id_categoria, label: c.nombre })),
    [categorias]
  );

  useEffect(() => {
    (async () => {
      try {
        setLoadingBook(true);
        const data = await apiRequest(`/libros/${id}`);
        setForm({
          titulo: data.titulo || "",
          autor: data.autor || "",
          editorial: data.editorial || "",
          anio: data.anio ? `${data.anio}-01-01` : "",
          id_categoria: data.id_categoria ? String(data.id_categoria) : "",
          cantidad: data.cantidad ?? 1,
          disponibilidad: data.disponibilidad || "disponible",
        });
        setBookFound(true);
      } catch (err) {
        setBookFound(false);
        setError(err.message || "No se encontró el libro a editar.");
      } finally {
        setLoadingBook(false);
      }
    })();
  }, [id]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!form.titulo.trim()) return setError("El título es obligatorio.");
    if (!form.autor.trim()) return setError("El autor es obligatorio.");
    if (!form.anio) return setError("Debe seleccionar una fecha.");
    if (form.anio > maxDate) return setError("No puede seleccionar un año superior al actual.");
    if (!form.id_categoria) return setError("Debe seleccionar una categoría.");
    if (Number(form.cantidad) < 1) return setError("La cantidad debe ser mayor o igual a 1.");

    try {
      setSaving(true);
      setError(""); setSuccess("");
      await apiRequest(`/libros/${id}`, {
        method: "PUT",
        body: JSON.stringify({
          titulo: form.titulo.trim(),
          autor: form.autor.trim(),
          editorial: form.editorial.trim(),
          anio: Number(form.anio.split("-")[0]),
          id_categoria: Number(form.id_categoria),
          cantidad: Number(form.cantidad),
          disponibilidad: form.disponibilidad,
        }),
      });
      setSuccess("Libro actualizado correctamente.");
      setTimeout(() => navigate("/books"), 1000);
    } catch (err) {
      setError(err.message || "No fue posible actualizar el libro.");
    } finally {
      setSaving(false);
    }
  };

  const disponibilidadOptions = [
    { value: "disponible", label: "Disponible" },
    { value: "no_disponible", label: "No disponible" },
  ];

  return (
    <PageLayout>
      <FormCard icon={<FaPenToSquare />} title="Editar Libro">
        <AlertMessage type="danger" message={error} onClose={() => setError("")} />
        <AlertMessage type="success" message={success} onClose={() => setSuccess("")} />

        {loadingBook ? (
          <AlertMessage type="info" message="Cargando libro..." />
        ) : (
          <form onSubmit={handleSubmit}>
            <InputField label="Título" name="titulo" value={form.titulo} onChange={handleChange} required disabled={!bookFound} />
            <InputField label="Autor" name="autor" value={form.autor} onChange={handleChange} required disabled={!bookFound} />
            <InputField label="Editorial" name="editorial" value={form.editorial} onChange={handleChange} disabled={!bookFound} />

            <div className="form-grid">
              <InputField label="Fecha de publicación" name="anio" type="date" value={form.anio} onChange={handleChange} max={maxDate} required disabled={!bookFound} />
              <SelectField label="Categoría" name="id_categoria" value={form.id_categoria} onChange={handleChange} required disabled={!bookFound || loadingCategorias} placeholder={loadingCategorias ? "Cargando..." : "Seleccione una categoría"} options={categoriaOptions} />
              <InputField label="Cantidad" name="cantidad" type="number" min="1" step="1" value={form.cantidad} onChange={handleChange} required disabled={!bookFound} />
            </div>

            <SelectField label="Disponibilidad" name="disponibilidad" value={form.disponibilidad} onChange={handleChange} disabled={!bookFound} options={disponibilidadOptions} placeholder={null} />

            <FormActions cancelTo="/books" submitLabel="Actualizar" loadingLabel="Actualizando..." loading={saving} disabled={!bookFound} />
          </form>
        )}
      </FormCard>
    </PageLayout>
  );
}
